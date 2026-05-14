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

}
