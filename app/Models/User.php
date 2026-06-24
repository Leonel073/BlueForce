<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Rol;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'idPersona',
        'idRol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT — protección de integridad en capa de modelo
    |--------------------------------------------------------------------------
    */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            // Bloquear creación de usuario sin persona asignada
            // (excepto en entorno de testing donde factories pueden omitir esto)
            if (!app()->runningUnitTests() && empty($user->idPersona)) {
                throw new \InvalidArgumentException(
                    'No se puede crear un usuario sin persona asignada. Todo usuario debe pertenecer a una persona trabajadora.'
                );
            }

            // Bloquear si la persona es externa
            if (!empty($user->idPersona)) {
                $persona = Persona::find($user->idPersona);
                if ($persona && $persona->tipo_persona === 'externo') {
                    throw new \InvalidArgumentException(
                        'Las personas externas no pueden tener cuenta de usuario.'
                    );
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function correspondencias()
    {
        return $this->hasMany(
            \App\Models\Correspondencia::class,
            'idUsuario',
            'id'
        );
    }

    public function persona()
    {
        return $this->belongsTo(
            Persona::class,
            'idPersona',
            'idPersona'
        );
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE AUTORIZACIÓN
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return (int) $this->idRol === 1;
    }

    public function isActive(): bool
    {
        return (bool) $this->activo;
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function getNombreRol(): ?string
    {
        return $this->rol?->nombre;
    }

    public function hasDepartamento(): bool
    {
        return $this->persona?->idDepartamento !== null;
    }

    public function getDepartamentoId(): ?int
    {
        return $this->persona?->idDepartamento;
    }
}
