<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnuncioVisto extends Model
{
    protected $table = 'ANUNCIO_VISTO';

    protected $primaryKey = 'idAnuncioVisto';

    public $timestamps = false;

    protected $fillable = [
        'idAnuncio',
        'idUsuario',
        'fechaVisto',
    ];

    protected $casts = [
        'fechaVisto' => 'datetime',
    ];

    public function anuncio(): BelongsTo
    {
        return $this->belongsTo(Anuncio::class, 'idAnuncio', 'idAnuncio');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'id');
    }
}
