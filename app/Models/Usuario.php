<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'USUARIO';
    protected $primaryKey = 'idUsuario';

    public $timestamps = false;

    protected $fillable = [
        'correo',
        'contrasena',
        'idPersona',
        'idRol',
        'idEstadoUsuario',
        'activo'
    ];

    protected $hidden = [
        'contrasena'
    ];


    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}