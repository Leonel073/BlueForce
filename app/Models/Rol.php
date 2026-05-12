<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // Nombre exacto de la tabla en la base de datos
    protected $table = 'ROL';

    // Llave primaria de la tabla
    protected $primaryKey = 'idRol';

    // Desactivamos los timestamps porque tu migración no tiene created_at ni updated_at
    public $timestamps = false;

    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'nombre'
    ];
}