<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrespondenciaDestinatario extends Model
{
    protected $table = 'CORRESPONDENCIA_DESTINATARIO';
    public $timestamps = false;

    protected $fillable = [
        'idDocumento',
        'idPersona',
        'activo',
    ];

    /**
     * Relación: Destinatario pertenece a una Correspondencia
     */
    public function correspondencia()
    {
        return $this->belongsTo(Correspondencia::class, 'idDocumento', 'idDocumento');
    }

    /**
     * Relación: Destinatario es una Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }
}
