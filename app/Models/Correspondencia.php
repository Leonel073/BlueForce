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

    /**
     * Relación: Correspondencia pertenece a un Tipo de Documento
     */
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'idTipoDocumento', 'idTipoDocumento');
    }

    /**
     * Relación: Correspondencia pertenece a un Estado
     */
    public function estado()
    {
        return $this->belongsTo(EstadoDocumento::class, 'idEstado', 'idEstado');
    }

    /**
     * Relación: Correspondencia pertenece a un Nivel de Urgencia
     */
    public function urgencia()
    {
        return $this->belongsTo(NivelUrgencia::class, 'idUrgencia', 'idUrgencia');
    }

    /**
     * Relación: Correspondencia tiene un Remitente (Persona)
     */
    public function remitente()
    {
        return $this->belongsTo(Persona::class, 'idRemitente', 'idPersona');
    }

    /**
     * Relación: Correspondencia tiene muchos Destinatarios
     */
    public function destinatarios()
    {
        return $this->hasMany(CorrespondenciaDestinatario::class, 'idDocumento', 'idDocumento');
    }

    /*Creacion para poder usar datos del usuario */
    public function usuario()
    {
    return $this->belongsTo(User::class, 'idUsuario');
    }
    public function correspondencias()
    {
        return $this->hasMany(
            Correspondencia::class,
            'idUsuario'
        );
    }
    public function seguimientos()
    {
    return $this->hasMany(Seguimiento::class, 'idDocumento', 'idDocumento');
    }
}
