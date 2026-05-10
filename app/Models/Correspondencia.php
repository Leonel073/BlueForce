<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correspondencia extends Model
{
    protected $table = 'CORRESPONDENCIA';

    protected $primaryKey = 'idDocumento';

    public $timestamps = false;

    protected $fillable = [

        'cite',
        'asunto',
        'fecha',
        'idTipoDocumento',
        'idEstado',
        'idUrgencia',
        'idUsuario',
        'idRemitente',
        'activo',

    ];

    /*
    |--------------------------------------------------------------------------
    | TIPO DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function tipoDocumento()
    {
        return $this->belongsTo(
            TipoDocumento::class,
            'idTipoDocumento',
            'idTipoDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ESTADO
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstado',
            'idEstado'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | URGENCIA
    |--------------------------------------------------------------------------
    */

    public function urgencia()
    {
        return $this->belongsTo(
            NivelUrgencia::class,
            'idUrgencia',
            'idUrgencia'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REMITENTE
    |--------------------------------------------------------------------------
    */

    public function remitente()
    {
        return $this->belongsTo(
            Persona::class,
            'idRemitente',
            'idPersona'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO CREADOR
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->belongsTo(
            User::class,
            'idUsuario',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTINATARIOS
    |--------------------------------------------------------------------------
    */

    public function destinatarios()
    {
        return $this->hasMany(
            CorrespondenciaDestinatario::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DERIVACIONES
    |--------------------------------------------------------------------------
    */

    public function derivaciones()
    {
        return $this->hasMany(
            Derivacion::class,
            'idDocumento',
            'idDocumento'
        );
    }
        /*
    |--------------------------------------------------------------------------
    | ÚLTIMA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    public function ultimaDerivacion()
    {
        return $this->hasOne(
            Derivacion::class,
            'idDocumento',
            'idDocumento'
        )->latestOfMany('orden');
    }
        /*
    |--------------------------------------------------------------------------
    | UBICACIÓN ACTUAL
    |--------------------------------------------------------------------------
    */

    public function ubicacionActual()
    {
        $ultima = $this->ultimaDerivacion;

        if (!$ultima) {
            return 'Sin derivación';
        }

        return $ultima->departamentoDestino->nombre ?? 'N/A';
    }

        /*
    |--------------------------------------------------------------------------
    | ESTADO FÍSICO
    |--------------------------------------------------------------------------
    */

    public function estadoFisico()
    {
        $ultima = $this->ultimaDerivacion;

        if (!$ultima) {
            return 'Registrado';
        }

        if ($ultima->fechaRecepcion) {
            return 'Recibido';
        }

        return 'En tránsito';
    }

    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTOS
    |--------------------------------------------------------------------------
    */

    public function seguimientos()
    {
        return $this->hasMany(
            Seguimiento::class,
            'idDocumento',
            'idDocumento'
        );
    }
}