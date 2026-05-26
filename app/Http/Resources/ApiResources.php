<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource
 * Recurso para serializar modelo User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'idPersona' => $this->idPersona,
            'idRol' => $this->idRol,
            'activo' => (bool)$this->activo,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

/**
 * CorrespondenciaResource
 * Recurso para serializar modelo Correspondencia
 */
class CorrespondenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idDocumento' => $this->idDocumento,
            'cite' => $this->cite,
            'asunto' => $this->asunto,
            'fecha' => $this->fecha,
            'idTipoDocumento' => $this->idTipoDocumento,
            'tipoDocumento' => $this->whenLoaded('tipoDocumento', [
                'id' => $this->tipoDocumento?->idTipoDocumento,
                'nombre' => $this->tipoDocumento?->nombre,
            ]),
            'idEstado' => $this->idEstado,
            'estado' => $this->whenLoaded('estado', [
                'id' => $this->estado?->idEstado,
                'nombre' => $this->estado?->nombre,
            ]),
            'idUrgencia' => $this->idUrgencia,
            'urgencia' => $this->whenLoaded('urgencia', [
                'id' => $this->urgencia?->idUrgencia,
                'nombre' => $this->urgencia?->nombre,
            ]),
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->whenLoaded('usuario', new UserResource($this->usuario)),
            'idRemitente' => $this->idRemitente,
            'remitente' => $this->whenLoaded('remitente', [
                'id' => $this->remitente?->idPersona,
                'nombre' => $this->remitente?->nombre,
            ]),
            'activo' => (bool)$this->activo,
        ];
    }
}

/**
 * DerivacionResource
 * Recurso para serializar modelo Derivacion
 */
class DerivacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idDerivacion' => $this->idDerivacion,
            'idDocumento' => $this->idDocumento,
            'documento' => $this->whenLoaded('documento', new CorrespondenciaResource($this->documento)),
            'orden' => $this->orden,
            'idDepartamentoOrigen' => $this->idDepartamentoOrigen,
            'departamentoOrigen' => $this->whenLoaded('departamentoOrigen', [
                'id' => $this->departamentoOrigen?->idDepartamento,
                'nombre' => $this->departamentoOrigen?->nombre,
            ]),
            'idDepartamentoDestino' => $this->idDepartamentoDestino,
            'departamentoDestino' => $this->whenLoaded('departamentoDestino', [
                'id' => $this->departamentoDestino?->idDepartamento,
                'nombre' => $this->departamentoDestino?->nombre,
            ]),
            'idUsuarioAsignado' => $this->idUsuarioAsignado,
            'usuarioAsignado' => $this->whenLoaded('usuarioAsignado', new UserResource($this->usuarioAsignado)),
            'idUsuarioEnvio' => $this->idUsuarioEnvio,
            'usuarioEnvio' => $this->whenLoaded('usuarioEnvio', new UserResource($this->usuarioEnvio)),
            'instruccion' => $this->instruccion,
            'fechaEnvio' => $this->fechaEnvio,
            'fechaRecepcion' => $this->fechaRecepcion,
            'activo' => (bool)$this->activo,
        ];
    }
}

/**
 * PersonaResource
 * Recurso para serializar modelo Persona
 */
class PersonaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idPersona' => $this->idPersona,
            'nombre' => $this->nombre,
            'ci' => $this->ci ?? null,
            'cargo' => $this->cargo ?? null,
            'email' => $this->email ?? null,
            'telefono' => $this->telefono ?? null,
            'activo' => (bool)$this->activo,
        ];
    }
}

/**
 * DepartamentoResource
 * Recurso para serializar modelo Departamento
 */
class DepartamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idDepartamento' => $this->idDepartamento,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion ?? null,
            'activo' => (bool)$this->activo,
        ];
    }
}

/**
 * SeguimientoResource
 * Recurso para serializar modelo Seguimiento
 */
class SeguimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idSeguimiento' => $this->idSeguimiento,
            'idDocumento' => $this->idDocumento,
            'documento' => $this->whenLoaded('documento', new CorrespondenciaResource($this->documento)),
            'estado' => $this->estado,
            'fecha' => $this->fecha,
            'observacion' => $this->observacion ?? null,
        ];
    }
}

/**
 * AuditoriaResource
 * Recurso para serializar modelo Auditoria
 */
class AuditoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idAuditoria' => $this->idAuditoria,
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->whenLoaded('usuario', new UserResource($this->usuario)),
            'tabla' => $this->tabla,
            'operacion' => $this->operacion,
            'registro_id' => $this->registro_id,
            'datos_anteriores' => $this->datos_anteriores ? json_decode($this->datos_anteriores, true) : null,
            'datos_nuevos' => $this->datos_nuevos ? json_decode($this->datos_nuevos, true) : null,
            'fecha' => $this->fecha,
            'ip' => $this->ip ?? null,
        ];
    }
}
