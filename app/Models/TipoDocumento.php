<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'TIPO_DOCUMENTO';
    protected $primaryKey = 'idTipoDocumento';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    /**
     * Relación: Un tipo de documento puede estar en muchas correspondencias
     */
    public function correspondencias()
    {
        return $this->hasMany(Correspondencia::class, 'idTipoDocumento', 'idTipoDocumento');
    }
}
