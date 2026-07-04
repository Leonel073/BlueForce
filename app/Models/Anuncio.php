<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anuncio extends Model
{
    protected $table = 'ANUNCIO';

    protected $primaryKey = 'idAnuncio';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'asunto',
        'archivo_pdf',
        'ruta_pdf',
        'mime_type',
        'tamano_archivo',
        'activo',
        'idUsuarioCreador',
        'fechaCreacion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fechaCreacion' => 'datetime',
        'tamano_archivo' => 'integer',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuarioCreador', 'id');
    }

    public function vistas(): HasMany
    {
        return $this->hasMany(AnuncioVisto::class, 'idAnuncio', 'idAnuncio');
    }

    public function tienePdf(): bool
    {
        return !empty($this->ruta_pdf);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function fueVistoPor(int $userId): bool
    {
        return $this->vistas()->where('idUsuario', $userId)->exists();
    }
}
