<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'SEGUIMIENTO';

    protected $primaryKey = 'idSeguimiento';

    public $timestamps = false;

    protected $fillable = [
        'idDocumento',
        'fecha',
        'ubicacion',
        'idEstado',
        'activo',
    ];

    public function correspondencia()
    {
        return $this->belongsTo(
            Correspondencia::class,
            'idDocumento',
            'idDocumento'
        );
    }

    public function estado()
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstado',
            'idEstado'
        );
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }
}