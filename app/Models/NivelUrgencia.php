<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelUrgencia extends Model
{
    protected $table = 'NIVEL_URGENCIA';
    protected $primaryKey = 'idUrgencia';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}
