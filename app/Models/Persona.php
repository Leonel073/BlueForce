<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'PERSONA';
    
    protected $primaryKey = 'idPersona';
    
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS ASIGNABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nombre',
        'correo',
        'telefono_celular',
        'telefono_fijo',
        'ci',
        'institucion',
        'tipo',
        'tipo_persona',
        'idDepartamento',
        'idCargo',
        'activo',
        'fecha_creacion',
        'fecha_deshabilitacion',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Una persona pertenece a UN cargo (o ninguno)
     * 
     * @return BelongsTo
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(
            Cargo::class,
            'idCargo',
            'idCargo'
        );
    }

    /**
     * Relación: Una persona pertenece a UN departamento
     * 
     * @return BelongsTo
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(
            Departamento::class,
            'idDepartamento',
            'idDepartamento'
        );
    }

    /**
     * Relación: Una persona puede tener múltiples responsabilidades en departamentos
     * 
     * @return HasMany
     */
    public function responsabilidades()
    {
        return $this->hasMany(
            'App\Models\DepartamentoResponsable',
            'idPersona',
            'idPersona'
        );
    }

    /**
     * Relación: Una persona puede ser remitente de muchas correspondencias
     * 
     * @return HasMany
     */
    public function correspondenciasComoRemitente(): HasMany
    {
        return $this->hasMany(
            Correspondencia::class, 
            'idRemitente', 
            'idPersona'
        );
    }

    /**
     * Relación: Una persona puede ser destinataria de muchas correspondencias
     * 
     * @return HasMany
     */
    public function correspondenciasComoDestinatario(): HasMany
    {
        return $this->hasMany(
            CorrespondenciaDestinatario::class, 
            'idPersona', 
            'idPersona'
        );
    }

    /**
     * Relación: Una persona trabajador puede tener un único usuario
     * Una persona externa NUNCA tendrá usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function usuario()
    {
        return $this->hasOne(
            User::class,
            'idPersona',
            'idPersona'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES PARA FILTROS
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Personas activas (no deshabilitadas)
     */
    public function scopeActivas($query)
    {
        return $query->whereNull('fecha_deshabilitacion');
    }

    /**
     * Scope: Personas deshabilitadas
     */
    public function scopeDeshabilitadas($query)
    {
        return $query->whereNotNull('fecha_deshabilitacion');
    }

    /**
     * Scope: Solo trabajadores (pueden tener cuenta de usuario)
     */
    public function scopeTrabajadores($query)
    {
        return $query->where('tipo_persona', 'trabajador');
    }

    /**
     * Scope: Solo externos (no pueden tener cuenta de usuario)
     */
    public function scopeExternos($query)
    {
        return $query->where('tipo_persona', 'externo');
    }

    /**
     * Scope: Trabajadores activos sin usuario asignado
     */
    public function scopeTrabajadoresSinUsuario($query)
    {
        return $query
            ->where('tipo_persona', 'trabajador')
            ->whereNull('fecha_deshabilitacion')
            ->whereDoesntHave('usuario');
    }

    /**
     * Scope: Personas que trabajan en la institución (asignadas a departamento)
     */
    public function scopeConDepartamento($query)
    {
        return $query->whereNotNull('idDepartamento')->whereNull('fecha_deshabilitacion');
    }

    /**
     * Scope: Personas remitentes (no asignadas a departamento)
     */
    public function scopeRemitentes($query)
    {
        return $query->whereNull('idDepartamento')->whereNull('fecha_deshabilitacion');
    }

    /**
     * Scope: Filtrar por CI
     */
    public function scopePorCI($query, $ci)
    {
        return $query->where('ci', 'like', "%{$ci}%");
    }

    /**
     * Scope: Filtrar por nombre
     */
    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    /**
     * Scope: Filtrar por departamento
     */
    public function scopePorDepartamento($query, $idDepartamento)
    {
        return $query->where('idDepartamento', $idDepartamento);
    }

    /**
     * Scope: Filtrar por cargo
     */
    public function scopePorCargo($query, $idCargo)
    {
        return $query->where('idCargo', $idCargo);
    }

    /**
     * Scope: Filtrar por tipo (INTERNO/EXTERNO)
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Filtrar por celular
     */
    public function scopePorCelular($query, $celular)
    {
        return $query->where('telefono_celular', 'like', "%{$celular}%");
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS ÚTILES
    |--------------------------------------------------------------------------
    */

    /**
     * Deshabilitar persona (borrado lógico)
     */
    public function deshabilitar()
    {
        $this->fecha_deshabilitacion = now();
        $this->save();
    }

    /**
     * Reactivar persona
     */
    public function reactivar()
    {
        $this->fecha_deshabilitacion = null;
        $this->save();
    }

    /**
     * Verificar si es responsable de algún departamento actualmente
     */
    public function esResponsableActual()
    {
        return $this->responsabilidades()
                    ->where('activo', true)
                    ->whereNull('fecha_declinacion')
                    ->exists();
    }

    /**
     * Obtener departamentos donde es responsable actualmente
     */
    public function departamentosResponsables()
    {
        return $this->responsabilidades()
                    ->where('activo', true)
                    ->whereNull('fecha_declinacion')
                    ->get();
    }

    /**
     * ¿Puede esta persona tener una cuenta de usuario?
     */
    public function puedeSerUsuario(): bool
    {
        return $this->tipo_persona === 'trabajador';
    }

    /**
     * ¿Tiene usuario asignado?
     */
    public function tieneUsuario(): bool
    {
        return $this->usuario()->exists();
    }
}
