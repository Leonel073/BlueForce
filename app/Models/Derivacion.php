<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Derivacion extends Model
{
    protected $table = 'DERIVACION';

    protected $primaryKey = 'idDerivacion';

    public $timestamps = false;

    protected $fillable = [
        'idDocumento',
        'orden',
        'idDepartamentoOrigen',
        'idDepartamentoDestino',
        'idUsuarioAsignado',
        'instruccion',
        'fechaEnvio',
        'fechaRecepcion',
        'activo',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIÓN DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function documento()
    {
        return $this->belongsTo(
            Correspondencia::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DEPARTAMENTO ORIGEN
    |--------------------------------------------------------------------------
    */

    public function departamentoOrigen()
    {
        return $this->belongsTo(
            Departamento::class,
            'idDepartamentoOrigen',
            'idDepartamento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DEPARTAMENTO DESTINO
    |--------------------------------------------------------------------------
    */

    public function departamentoDestino()
    {
        return $this->belongsTo(
            Departamento::class,
            'idDepartamentoDestino',
            'idDepartamento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO ASIGNADO
    |--------------------------------------------------------------------------
    */

    public function usuarioAsignado()
    {
        return $this->belongsTo(
            User::class,
            'idUsuarioAsignado',
            'id'
        );
    }


    
}