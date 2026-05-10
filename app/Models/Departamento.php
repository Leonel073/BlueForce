<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'DEPARTAMENTO';
    protected $primaryKey = 'idDepartamento';
    public $timestamps = false;

    protected $fillable = ['nombre'];

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
}
