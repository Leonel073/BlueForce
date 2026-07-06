<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cargo extends Model
{
    protected $table = 'CARGO';
    
    protected $primaryKey = 'idCargo';
    
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS ASIGNABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un cargo puede tener muchas personas (legacy - mantener compatibilidad)
     * 
     * @return HasMany
     */
    public function personas(): HasMany
    {
        return $this->hasMany(
            Persona::class,
            'idCargo',
            'idCargo'
        );
    }

    /**
     * Relación: Un cargo puede estar asignado a muchas personas (N:N via pivote)
     * 
     * @return BelongsToMany
     */
    public function personasAsignadas(): BelongsToMany
    {
        return $this->belongsToMany(
            Persona::class,
            'PERSONA_CARGO',
            'idCargo',
            'idPersona'
        )
        ->withPivot('activo', 'principal', 'fecha_asignacion')
        ->wherePivot('activo', true);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Filtrar solo cargos activos
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Filtrar por nivel jerárquico
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $nivel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }
}
