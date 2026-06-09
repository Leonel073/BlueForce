<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Rol;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
 protected $fillable = [
        'name',
        'email',
        'password',
        'idPersona',
        'idRol',    
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* Creacion para poder usar correspondencia*/
    public function correspondencias()
    {
        return $this->hasMany(
            \App\Models\Correspondencia::class,
            'idUsuario',
            'id'
        );
    }
    /* Creacion para poder usar persona */
    public function persona()
    {
        return $this->belongsTo(
            Persona::class,
            'idPersona',
            'idPersona'
        );
    }
    // Relación con la tabla ROL
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS HELPERS PARA AUTORIZACIÓN
    |--------------------------------------------------------------------------
    */

    /**
     * ¿Es el usuario administrador?
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (int) $this->idRol === 1;
    }

    /**
     * ¿Es el usuario activo?
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->activo;
    }

    /**
     * ¿El usuario está verificado?
     * 
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Obtener el rol del usuario
     * 
     * @return string|null
     */
    public function getNombreRol(): ?string
    {
        return $this->rol?->nombre;
    }

    /**
     * ¿El usuario tiene un departamento asignado?
     * 
     * @return bool
     */
    public function hasDepartamento(): bool
    {
        return $this->persona?->idDepartamento !== null;
    }

    /**
     * Obtener el ID del departamento del usuario
     * 
     * @return int|null
     */
    public function getDepartamentoId(): ?int
    {
        return $this->persona?->idDepartamento;
    }
}
