<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Seguimiento
 * 
 * Gestiona el historial de seguimiento de documentos
 * Registra cambios de ubicación, estado y fecha de cada evento
 * 
 * Tabla: SEGUIMIENTO
 * 
 * @property int $idSeguimiento
 * @property int $idDocumento
 * @property \Carbon\Carbon $fecha
 * @property string $ubicacion
 * @property int|null $idEstado
 * @property bool $activo
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class Seguimiento extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'SEGUIMIENTO';

    /**
     * Clave primaria
     */
    protected $primaryKey = 'idSeguimiento';

    /**
     * Tipo de clave primaria
     */
    protected $keyType = 'int';

    /**
     * Indica si se deben usar timestamps automáticos
     */
    public $timestamps = false;

    /**
     * Los atributos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'idDocumento',
        'fecha',
        'ubicacion',
        'idEstado',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha' => 'datetime',
        'activo' => 'boolean',
        'idDocumento' => 'int',
        'idEstado' => 'int',
    ];

    /**
     * Relación: Un seguimiento pertenece a una Correspondencia
     */
    public function correspondencia()
    {
        return $this->belongsTo(
            Correspondencia::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /**
     * Relación: Un seguimiento pertenece a un EstadoDocumento
     */
    public function estado()
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstado',
            'idEstado'
        );
    }

    /**
     * Scope: Obtener solo registros activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope: Obtener registros por documento
     */
    public function scopePorDocumento($query, $idDocumento)
    {
        return $query->where('idDocumento', $idDocumento);
    }

    /**
     * Scope: Ordenar por fecha descendente (más reciente primero)
     */
    public function scopeOrdenarReciente($query)
    {
        return $query->orderBy('fecha', 'desc');
    }

    /**
     * Obtener el seguimiento más reciente de un documento
     */
    public static function getUltimoSeguimiento($idDocumento)
    {
        return self::porDocumento($idDocumento)
            ->activos()
            ->ordenarReciente()
            ->first();
    }
}