<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correspondencia extends Model
{
    protected $table = 'CORRESPONDENCIA';

    protected $primaryKey = 'idDocumento';

    public $timestamps = false;

    protected $fillable = [

        'cite',
        'asunto',
        'fecha',
        'idTipoDocumento',
        'idEstado',
        'idUrgencia',
        'idUsuario',
        'idRemitente',
        'activo',

        // Archivo PDF adjunto
        'archivo_pdf',
        'ruta_pdf',
        'mime_type',
        'tamano_archivo',
        'fecha_subida',
        'idUsuarioPdf',

    ];

    protected $casts = [
        'fecha'          => 'datetime',
        'tamano_archivo' => 'integer',
        'fecha_subida'   => 'datetime',
        'activo'         => 'boolean',
    ];

    /**
     * Indica si este documento tiene un PDF adjunto.
     */
    public function getTieneArchivoAttribute(): bool
    {
        return !is_null($this->ruta_pdf);
    }

    public function getEsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getArchivoIconoAttribute(): string
    {
        if ($this->es_pdf) {
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

    public function getArchivoTipoLabelAttribute(): string
    {
        if ($this->es_pdf) {
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

    /**
     * Tamaño del archivo formateado para mostrar en vistas.
     */
    public function getTamanoFormateadoAttribute(): string
    {
        if (!$this->tamano_archivo) {
            return '—';
        }
        $kb = $this->tamano_archivo / 1024;
        if ($kb < 1024) {
            return round($kb, 1) . ' KB';
        }
        return round($kb / 1024, 2) . ' MB';
    }

    /*
    |--------------------------------------------------------------------------
    | TIPO DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function tipoDocumento()
    {
        return $this->belongsTo(
            TipoDocumento::class,
            'idTipoDocumento',
            'idTipoDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ESTADO
    |--------------------------------------------------------------------------
    */

    public function estado()
    {
        return $this->belongsTo(
            EstadoDocumento::class,
            'idEstado',
            'idEstado'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | URGENCIA
    |--------------------------------------------------------------------------
    */

    public function urgencia()
    {
        return $this->belongsTo(
            NivelUrgencia::class,
            'idUrgencia',
            'idUrgencia'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REMITENTE
    |--------------------------------------------------------------------------
    */

    public function remitente()
    {
        return $this->belongsTo(
            Persona::class,
            'idRemitente',
            'idPersona'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO CREADOR
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->belongsTo(
            User::class,
            'idUsuario',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTINATARIOS
    |--------------------------------------------------------------------------
    */

    public function destinatarios()
    {
        return $this->hasMany(
            CorrespondenciaDestinatario::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DERIVACIONES
    |--------------------------------------------------------------------------
    */

    public function derivaciones()
    {
        return $this->hasMany(
            Derivacion::class,
            'idDocumento',
            'idDocumento'
        );
    }
        /*
    |--------------------------------------------------------------------------
    | ÚLTIMA DERIVACIÓN
    |--------------------------------------------------------------------------
    */

    public function ultimaDerivacion()
    {
        return $this->hasOne(
            Derivacion::class,
            'idDocumento',
            'idDocumento'
        )->latestOfMany('orden');
    }
        /*
    |--------------------------------------------------------------------------
    | UBICACIÓN ACTUAL
    |--------------------------------------------------------------------------
    */

    public function ubicacionActual()
    {
        $ultima = $this->ultimaDerivacion;

        if (!$ultima) {
            return 'Sin derivación';
        }

        return $ultima->departamentoDestino->nombre ?? 'N/A';
    }

        /*
    |--------------------------------------------------------------------------
    | ESTADO FÍSICO
    |--------------------------------------------------------------------------
    */

    public function estadoFisico()
    {
        $ultima = $this->ultimaDerivacion;

        if (!$ultima) {
            return 'Registrado';
        }

        if ($ultima->fechaRecepcion) {
            return 'Recibido';
        }

        return 'En tránsito';
    }

    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTOS
    |--------------------------------------------------------------------------
    */

    public function seguimientos()
    {
        return $this->hasMany(
            Seguimiento::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE SUBIÓ EL PDF
    |--------------------------------------------------------------------------
    */

    public function usuarioPdf()
    {
        return $this->belongsTo(
            User::class,
            'idUsuarioPdf',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE TRANSICIONES DE ESTADO
    |--------------------------------------------------------------------------
    */

    public function transiciones()
    {
        return $this->hasMany(
            EstadoTransicion::class,
            'idDocumento',
            'idDocumento'
        );
    }

    /**
     * Obtiene todas las transiciones de estado, ordenadas por fecha descendente
     */
    public function obtenerHistorialTransiciones()
    {
        return $this->transiciones()
            ->orderByDesc('fecha')
            ->get();
    }

    /**
     * Obtiene la última transición de estado
     */
    public function obtenerUltimaTransicion()
    {
        return $this->transiciones()
            ->orderByDesc('fecha')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE CAMBIO DE ESTADO
    |--------------------------------------------------------------------------
    */

    /**
     * Cambiar estado de forma genérica
     * 
     * @param string $nuevoEstado Nombre del estado o ID
     * @param string $accion Tipo de acción (CREAR, DERIVAR, RECIBIR, ATENDER, ARCHIVAR)
     * @param string|null $observacion Observación adicional
     * @param int|null $idUsuario ID del usuario que realiza la acción
     * @return bool
     */
    public function cambiarEstado($nuevoEstado, $accion, $observacion = null, $idUsuario = null)
    {
        try {
            $idUsuarioActual = $idUsuario ?? \Illuminate\Support\Facades\Auth::id();

            // Obtener el estado nuevo por nombre o ID
            if (is_numeric($nuevoEstado)) {
                $estado = EstadoDocumento::find($nuevoEstado);
            } else {
                $estado = EstadoDocumento::where('nombre', $nuevoEstado)->first();
            }

            if (!$estado) {
                throw new \Exception("Estado '{$nuevoEstado}' no encontrado");
            }

            $idEstadoNuevo = $estado->idEstado;
            $idEstadoAnterior = $this->idEstado;

            // No hacer nada si el estado es el mismo
            if ($idEstadoAnterior == $idEstadoNuevo) {
                return false;
            }

            // Actualizar el estado del documento
            $this->idEstado = $idEstadoNuevo;
            $this->save();

            // Registrar la transición en el historial
            EstadoTransicion::create([
                'idDocumento' => $this->idDocumento,
                'idUsuario' => $idUsuarioActual,
                'idEstadoAnterior' => $idEstadoAnterior,
                'idEstadoNuevo' => $idEstadoNuevo,
                'accion' => $accion,
                'observacion' => $observacion,
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error("Error al cambiar estado: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Cambiar a estado RECIBIDO
     * Solo es posible desde PENDIENTE
     */
    public function cambiarARecibido($observacion = null, $idUsuario = null): bool
    {
        if (!$this->puedeRecibirse()) {
            throw new \Exception("El documento no puede pasar a RECIBIDO desde el estado actual");
        }

        return $this->cambiarEstado('Recibido', 'RECIBIR', $observacion, $idUsuario);
    }

    /**
     * Cambiar a estado ATENDIDO
     * Solo es posible desde RECIBIDO
     */
    public function cambiarAAtendido($observacion = null, $idUsuario = null): bool
    {
        if (!$this->puedeAtenderse()) {
            throw new \Exception("El documento no puede pasar a ATENDIDO desde el estado actual");
        }

        return $this->cambiarEstado('Atendido', 'ATENDER', $observacion, $idUsuario);
    }

    /**
     * Cambiar a estado ARCHIVADO
     * Solo es posible desde ATENDIDO
     */
    public function cambiarAArchivado($observacion = null, $idUsuario = null): bool
    {
        if (!$this->puedeArchivarse()) {
            throw new \Exception("El documento no puede pasar a ARCHIVADO desde el estado actual");
        }

        return $this->cambiarEstado('Archivado', 'ARCHIVAR', $observacion, $idUsuario);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDACIONES DE TRANSICIÓN
    |--------------------------------------------------------------------------
    */

    /**
     * Valida si el documento puede cambiar a estado RECIBIDO
     */
    public function puedeRecibirse(): bool
    {
        // Solo desde PENDIENTE
        return $this->estado?->nombre === 'Pendiente';
    }

    /**
     * Valida si el documento puede cambiar a estado ATENDIDO
     */
    public function puedeAtenderse(): bool
    {
        // Solo desde RECIBIDO
        return $this->estado?->nombre === 'Recibido';
    }

    /**
     * Valida si el documento puede cambiar a estado ARCHIVADO
     */
    public function puedeArchivarse(): bool
    {
        // Solo desde ATENDIDO
        return $this->estado?->nombre === 'Atendido';
    }

    /**
     * Obtiene los estados a los que puede transicionar
     */
    public function obtenerTransicionesPermitidas(): array
    {
        $transiciones = [];

        if ($this->puedeRecibirse()) {
            $transiciones[] = [
                'nombre' => 'Recibir',
                'accion' => 'cambiarARecibido',
                'metodo' => 'RECIBIR',
                'clase' => 'btn-success',
            ];
        }

        if ($this->puedeAtenderse()) {
            $transiciones[] = [
                'nombre' => 'Atender',
                'accion' => 'cambiarAAtendido',
                'metodo' => 'ATENDER',
                'clase' => 'btn-primary',
            ];
        }

        if ($this->puedeArchivarse()) {
            $transiciones[] = [
                'nombre' => 'Archivar',
                'accion' => 'cambiarAArchivado',
                'metodo' => 'ARCHIVAR',
                'clase' => 'btn-secondary',
            ];
        }

        return $transiciones;
    }
}
