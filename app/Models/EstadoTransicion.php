<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoTransicion extends Model
{
    protected $table = 'ESTADO_TRANSICION';

    protected $primaryKey = 'idTransicion';

    public $timestamps = false;

    protected $fillable = [
        'idDocumento',
        'idUsuario',
        'idEstadoAnterior',
        'idEstadoNuevo',
        'accion',
        'observacion',
        'fecha',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'activo' => 'boolean',
    ];

    // ==============================
    // RELACIONES
    // ==============================

    /**
     * Relación con el documento de correspondencia
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(
            Correspondencia::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /**
     * Relación con el usuario que realizó la acción
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'idUsuario',
            'id'
        );
    }

    /**
     * Relación con el estado anterior
     */
    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstadoAnterior',
            'idEstado'
        );
    }

    /**
     * Relación con el estado nuevo
     */
    public function estadoNuevo(): BelongsTo
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstadoNuevo',
            'idEstado'
        );
    }

    // ==============================
    // ACCESORIOS
    // ==============================

    /**
     * Obtiene el nombre de la acción en formato legible
     */
    public function getAccionLegibleAttribute(): string
    {
        $acciones = [
            'CREAR' => 'Documento Creado',
            'DERIVAR' => 'Derivación',
            'RECIBIR' => 'Recepción',
            'ATENDER' => 'Atención',
            'ARCHIVAR' => 'Archivado',
        ];

        return $acciones[$this->accion] ?? $this->accion;
    }

    /**
     * Badge HTML para la acción
     */
    public function getAccionBadgeAttribute(): string
    {
        $colores = [
            'CREAR' => 'info',
            'DERIVAR' => 'warning',
            'RECIBIR' => 'success',
            'ATENDER' => 'primary',
            'ARCHIVAR' => 'secondary',
        ];

        $icono = match ($this->accion) {
            'CREAR' => '<i class="bi bi-plus-circle"></i>',
            'DERIVAR' => '<i class="bi bi-arrow-right-circle"></i>',
            'RECIBIR' => '<i class="bi bi-check-circle"></i>',
            'ATENDER' => '<i class="bi bi-hand-thumbs-up"></i>',
            'ARCHIVAR' => '<i class="bi bi-archive"></i>',
            default => '<i class="bi bi-question-circle"></i>',
        };

        $color = $colores[$this->accion] ?? 'secondary';

        return sprintf(
            '<span class="badge bg-%s">%s %s</span>',
            $color,
            $icono,
            $this->getAccionLegibleAttribute()
        );
    }

    /**
     * Formato de fecha para mostrar (fecha y hora)
     */
    public function getFechaFormateadaAttribute(): string
    {
        return $this->fecha->format('d/m/Y H:i:s');
    }

    /**
     * Información de transición resumida
     */
    public function getResumenAttribute(): string
    {
        $estadoAnterior = $this->estadoAnterior?->nombre ?? 'N/A';
        $estadoNuevo = $this->estadoNuevo?->nombre ?? 'N/A';
        
        return "{$estadoAnterior} → {$estadoNuevo}";
    }
}
