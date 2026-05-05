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
    ];

    // Relaciones
    public function tipo()
    {
        return $this->belongsTo(TipoDocumento::class, 'idTipoDocumento');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoDocumento::class, 'idEstado');
    }

    public function urgencia()
    {
        return $this->belongsTo(NivelUrgencia::class, 'idUrgencia');
    }

    public function remitente()
    {
        return $this->belongsTo(Persona::class, 'idRemitente');
    }
}
