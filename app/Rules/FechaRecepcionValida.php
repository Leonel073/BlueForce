<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

/**
 * Valida que una fecha de recepción sea posterior a la fecha de envío
 * 
 * Reglas:
 * 1. La fecha de recepción debe ser posterior a la de envío
 * 2. Ambas deben ser fechas válidas
 * 3. No puede ser en el futuro (opcional, puede deshabilitarse)
 * 
 * Uso en Form Request:
 * 'fechaRecepcion' => [
 *     'nullable',
 *     'date',
 *     new FechaRecepcionValida(request('fechaEnvio'))
 * ]
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class FechaRecepcionValida implements Rule
{
    private $fechaEnvio;
    private $errorMessage = '';

    /**
     * Constructor
     *
     * @param string|null $fechaEnvio
     */
    public function __construct($fechaEnvio = null)
    {
        $this->fechaEnvio = $fechaEnvio;
    }

    /**
     * Validar la fecha de recepción.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value): bool
    {
        if (empty($value) || empty($this->fechaEnvio)) {
            return true; // Dejar que required/nullable valide
        }

        try {
            $fechaRecepcion = Carbon::parse($value);
            $fechaEnvio = Carbon::parse($this->fechaEnvio);

            // Regla 1: Recepción debe ser posterior a envío
            if ($fechaRecepcion->lte($fechaEnvio)) {
                $this->errorMessage = 'La fecha de recepción debe ser posterior a la fecha de envío.';
                return false;
            }

            // Regla 2: No puede ser en el futuro
            if ($fechaRecepcion->isFuture()) {
                $this->errorMessage = 'La fecha de recepción no puede ser en el futuro.';
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->errorMessage = 'La fecha no tiene un formato válido.';
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage ?: 'La fecha de recepción no es válida.';
    }
}
