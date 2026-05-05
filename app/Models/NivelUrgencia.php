<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelUrgencia extends Model
{
    protected $table = 'NIVEL_URGENCIA';
    protected $primaryKey = 'idUrgencia';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    /**
     * Relación: Un nivel de urgencia puede estar en muchas correspondencias
     */
    public function correspondencias()
    {
        return $this->hasMany(Correspondencia::class, 'idUrgencia', 'idUrgencia');
    }
}
