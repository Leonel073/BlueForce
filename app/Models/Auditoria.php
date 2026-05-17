<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'AUDITORIA';

    protected $primaryKey = 'idAuditoria';

    protected $fillable = [
        'idUsuario',
        'modelo',
        'idRegistro',
        'accion',
        'datosAnteriores',
        'datosNuevos',
        'ip',
        'navegador',
        'ruta',
        'fecha',
    ];

    protected $casts = [
        'datosAnteriores' => 'array',
        'datosNuevos' => 'array',
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['accion_badge', 'cambios_resumo'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ==============================
    // RELACIONES
    // ==============================

    /**
     * Relación con el usuario que realizó la acción
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'id');
    }

    // ==============================
    // ACCESORIOS
    // ==============================

    /**
     * Badge HTML para mostrar la acción con color
     */
    public function getAccionBadgeAttribute(): string
    {
        $colors = [
            'CREATE' => 'success',
            'UPDATE' => 'warning',
            'DELETE' => 'danger',
        ];

        $color = $colors[$this->accion] ?? 'secondary';
        $icons = [
            'CREATE' => '<i class="bi bi-plus-circle"></i>',
            'UPDATE' => '<i class="bi bi-pencil-square"></i>',
            'DELETE' => '<i class="bi bi-trash"></i>',
        ];

        $icon = $icons[$this->accion] ?? '';

        return sprintf(
            '<span class="badge bg-%s">%s %s</span>',
            $color,
            $icon,
            $this->accion
        );
    }

    /**
     * Resumen de cambios para mostrar en lista
     */
   public function getCambiosResumoAttribute(): string
{
    if ($this->accion === 'CREATE') {
        return 'Nuevo registro creado';
    }

    if ($this->accion === 'DELETE') {
        return 'Registro eliminado';
    }

    $anteriores = is_array($this->datosAnteriores)
        ? $this->datosAnteriores
        : [];

    $nuevos = is_array($this->datosNuevos)
        ? $this->datosNuevos
        : [];

    if ($this->accion === 'UPDATE') {

        $cambios = [];

        foreach ($nuevos as $campo => $valor) {

            $anterior = $anteriores[$campo] ?? null;

            if ($anterior != $valor) {
                $cambios[] = $campo;
            }
        }

        return count($cambios)
            ? implode(', ', array_slice($cambios, 0, 3))
            : 'Sin cambios detectados';
    }

    return '';
}

    /**
     * Obtiene el nombre legible del modelo
     */
    public function getModeloLegibleAttribute(): string
    {
        return match ($this->modelo) {
            'Correspondencia' => 'Correspondencia',
            'User' => 'Usuario',
            'Derivacion' => 'Derivación',
            'Departamento' => 'Departamento',
            'Persona' => 'Persona',
            'EstadoDocumento' => 'Estado Documento',
            'NivelUrgencia' => 'Nivel Urgencia',
            'TipoDocumento' => 'Tipo Documento',
            'Seguimiento' => 'Seguimiento',
            default => $this->modelo,
        };
    }

    // ==============================
    // SCOPES
    // ==============================

    /**
     * Filtrar por usuario
     */
    public function scopeDelUsuario($query, $idUsuario)
    {
        return $query->where('idUsuario', $idUsuario);
    }

    /**
     * Filtrar por acción
     */
    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Filtrar por modelo
     */
    public function scopePorModelo($query, $modelo)
    {
        return $query->where('modelo', $modelo);
    }

    /**
     * Filtrar por fecha entre rango
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener últimos registros
     */
    public function scopeUltimos($query, $cantidad = 50)
    {
        return $query->orderByDesc('fecha')->limit($cantidad);
    }

    /**
     * Ordenar descendentemente por fecha
     */
    public function scopeOrdenadoPorFecha($query)
    {
        return $query->orderByDesc('fecha');
    }
};
