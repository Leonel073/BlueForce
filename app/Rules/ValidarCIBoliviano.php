<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Valida un Carnet de Identidad (CI) Boliviano
 * 
 * Formato válido:
 * - Entre 7 y 13 dígitos
 * - Puede incluir guion opcional como separador
 * - Formato con guion: XXXXXXX-X o XXXXXXXXXX-X
 * 
 * Ejemplos válidos:
 * - 1234567
 * - 12345678
 * - 1234567-1
 * - 12345678-9
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class ValidarCIBoliviano implements Rule
{
    /**
     * Validar el CI Boliviano.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value): bool
    {
        // Eliminar espacios en blanco
        $ci = trim($value ?? '');
        
        // Patrón para CI boliviano
        // Formatos válidos: 1234567, 12345678, 1234567-1, 12345678-9
        $patron = '/^[0-9]{7,8}(?:-[0-9])?$/';
        
        return (bool) preg_match($patron, $ci);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El carnet de identidad (CI) no es válido. '
            . 'Debe tener entre 7 y 13 caracteres (incluidos dígitos y guion opcional). '
            . 'Ej: 1234567 o 1234567-1';
    }
}
