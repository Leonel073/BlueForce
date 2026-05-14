<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Departamento extends Model
{
    protected $table = 'DEPARTAMENTO';
    protected $primaryKey = 'idDepartamento';
    public $timestamps = false;

    protected $fillable = [

    'nombre',

    'idPersonaEncargada',

    'activo',

];

    /**
     * Relación: Un departamento puede recibir muchas correspondencias
     */
    public function correspondenciasDestinatario()
    {
        return $this->hasMany(CorrespondenciaDestinatario::class, 'idPersona', 'idDepartamento');
    }
    public function encargado()
{
    return $this->belongsTo(
        Persona::class,
        'idPersonaEncargada',
        'idPersona'
    );
}
public function derivacionesDestino()
{
    return $this->hasMany(
        Derivacion::class,
        'idDepartamentoDestino',
        'idDepartamento'
    );
}

/**
 * Relación: Un departamento puede tener múltiples responsables (auditoría)
 */
public function personaEncargada()
{
    return $this->belongsTo(
        Persona::class,
        'idPersonaEncargada',
        'idPersona'
    );
}
/**
 * Relación: Personas que trabajan en este departamento
 */
public function personas()
{
    return $this->hasMany(
        Persona::class,
        'idDepartamento',
        'idDepartamento'
    );
}

/*
|--------------------------------------------------------------------------
| SCOPES
|--------------------------------------------------------------------------
*/

/**
 * Scope: Departamentos activos
 */
public function scopeActivos($query)
{
    return $query->where('activo', true);
}

/*
|--------------------------------------------------------------------------
| MÉTODOS
|--------------------------------------------------------------------------
*/

/**
 * Asignar persona como responsable del departamento
 */
public function asignarResponsable($idPersona)
{
    // Declinar responsables anteriores
    $this->responsables()
         ->where('activo', true)
         ->update([
             'fecha_declinacion' => now(),
             'activo' => false
         ]);

    // Crear nuevo responsable
    return $this->responsables()->create([
        'idPersona' => $idPersona,
        'fecha_asignacion' => now(),
        'activo' => true,
    ]);
}

/**
 * Declinar responsable actual
 */
public function declinarResponsable()
{
    return $this->responsables()
                ->where('activo', true)
                ->update([
                    'fecha_declinacion' => now(),
                    'activo' => false
                ]);
}

/**
 * Obtener responsable actual
 */
public function responsableActual()
{
    return $this->responsables()
                ->where('activo', true)
                ->whereNull('fecha_declinacion')
                ->with('persona')
                ->first();
}

}
