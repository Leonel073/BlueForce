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
        'idDepartamento',
        'idCargo',
        'activo',
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
}

