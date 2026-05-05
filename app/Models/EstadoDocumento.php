<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
    protected $table = 'ESTADO_DOCUMENTO';
    protected $primaryKey = 'idEstado';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    /**
     * Relación: Un estado puede estar en muchas correspondencias
     */
    public function correspondencias()
    {
        return $this->hasMany(Correspondencia::class, 'idEstado', 'idEstado');
    }
}
