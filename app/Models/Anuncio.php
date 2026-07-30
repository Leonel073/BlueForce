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

    public function esPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function archivoIcono(): string
    {
        if ($this->esPdf()) {
            return 'bi-file-earmark-pdf-fill text-danger';
        }

        if (str_contains((string) $this->mime_type, 'word')) {
            return 'bi-file-earmark-word-fill text-primary';
        }

        if (str_contains((string) $this->mime_type, 'excel') || str_contains((string) $this->mime_type, 'spreadsheet')) {
            return 'bi-file-earmark-excel-fill text-success';
        }

        return 'bi-file-earmark-fill text-secondary';
    }

    public function archivoTipoLabel(): string
    {
        if ($this->esPdf()) {
            return 'PDF';
        }

        if (str_contains((string) $this->mime_type, 'word')) {
            return 'Word';
        }

        if (str_contains((string) $this->mime_type, 'excel') || str_contains((string) $this->mime_type, 'spreadsheet')) {
            return 'Excel';
        }

        return 'Archivo';
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
