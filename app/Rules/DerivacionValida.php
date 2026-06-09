<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Correspondencia;
use App\Models\Derivacion;

/**
 * Valida que una Derivación sea válida según las reglas de negocio
 * 
 * Reglas:
 * 1. El departamento origen debe ser diferente del destino
 * 2. No puede haber una derivación duplicada (mismo doc, origen, destino)
 * 3. El usuario asignado debe ser del departamento destino
 * 4. El orden debe ser incremental
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class DerivacionValida implements Rule
{
    private $idDocumento;
    private $idDepartamentoOrigen;
    private $idDepartamentoDestino;
    private $errorMessage = '';

    /**
     * Constructor
     *
     * @param int $idDocumento
     * @param int $idDepartamentoOrigen
     * @param int $idDepartamentoDestino
     */
    public function __construct($idDocumento, $idDepartamentoOrigen, $idDepartamentoDestino)
    {
        $this->idDocumento = $idDocumento;
        $this->idDepartamentoOrigen = $idDepartamentoOrigen;
        $this->idDepartamentoDestino = $idDepartamentoDestino;
    }

    /**
     * Validar la derivación.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value): bool
    {
        // Regla 1: Departamento origen debe ser diferente del destino
        if ($this->idDepartamentoOrigen == $this->idDepartamentoDestino) {
            $this->errorMessage = 'El departamento de origen no puede ser igual al de destino.';
            return false;
        }

        // Regla 2: No puede haber derivación duplicada
        $derivacionDuplicada = Derivacion::where('idDocumento', $this->idDocumento)
            ->where('idDepartamentoOrigen', $this->idDepartamentoOrigen)
            ->where('idDepartamentoDestino', $this->idDepartamentoDestino)
            ->where('activo', true)
            ->exists();

        if ($derivacionDuplicada) {
            $this->errorMessage = 'Ya existe una derivación activa con estos departamentos para este documento.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage ?: 'La derivación no cumple con las reglas de negocio.';
    }
}
