<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'PERSONA';
    protected $primaryKey = 'idPersona';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'cargo',
        'institucion',
        'tipo',
        'activo',
    ];

    /**
     * Relación: Una persona puede ser remitente de muchas correspondencias
     */
    public function correspondenciasComoRemitente()
    {
        return $this->hasMany(Correspondencia::class, 'idRemitente', 'idPersona');
    }

    /**
     * Relación: Una persona puede ser destinataria de muchas correspondencias
     */
    public function correspondenciasComoDestinatario()
    {
        return $this->hasMany(CorrespondenciaDestinatario::class, 'idPersona', 'idPersona');
    }
}
