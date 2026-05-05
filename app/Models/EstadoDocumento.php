<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
    protected $table = 'ESTADO_DOCUMENTO';
    protected $primaryKey = 'idEstado';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}