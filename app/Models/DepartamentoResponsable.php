<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartamentoResponsable extends Model
{
    protected $table = 'DEPARTAMENTO_RESPONSABLE';
    
    protected $primaryKey = 'idResponsable';
    
    protected $fillable = [
        'idDepartamento',
        'idPersona',
        'fecha_asignacion',
        'fecha_declinacion',
        'activo',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_declinacion' => 'datetime',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Pertenece a un departamento
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
     * Relación: Pertenece a una persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(
            Persona::class,
            'idPersona',
            'idPersona'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Responsables activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true)
                     ->whereNull('fecha_declinacion');
    }

    /**
     * Scope: Por departamento
     */
    public function scopePorDepartamento($query, $idDepartamento)
    {
        return $query->where('idDepartamento', $idDepartamento);
    }
}
