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

    // 🔐 Laravel usará este campo como password
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // 👤 Campo de autenticación
    public function username()
    {
        return 'correo';
    }
    public function getAuthIdentifierName()
{
    return 'idUsuario';
}
public function persona()
{
    return $this->belongsTo(Persona::class, 'idPersona');
}

}