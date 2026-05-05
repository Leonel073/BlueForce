<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'PERSONA';
    protected $primaryKey = 'idPersona';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'cargo',
        'institucion',
        'tipo',
        'activo'
    ];
}