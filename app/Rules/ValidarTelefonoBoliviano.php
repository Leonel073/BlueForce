<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Valida teléfonos bolivianos
 *
 * Formatos válidos:
 * - Celular: 6XXXXXXX o 7XXXXXXX
 * - Fijo:
 *      2XXXXXXX -> La Paz
 *      3XXXXXXX -> Cochabamba / Santa Cruz
 *      4XXXXXXX -> Chuquisaca / Oruro / Potosí / Tarija
 *
 * También acepta:
 * - +591XXXXXXXX
 * - espacios
 * - guiones
 * - paréntesis
 *
 * Ejemplos válidos:
 * - 71234567
 * - 21234567
 * - +591 71234567
 * - +591-2-1234567
 *
 * Ejemplos inválidos:
 * - 222222222222222222
 * - 123456
 * - 999999999
 * - abc123
 */
class ValidarTelefonoBoliviano implements Rule
{
    /**
     * Validar el teléfono Boliviano.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return false;
        }

        // Eliminar espacios, guiones y paréntesis
        $telefono = preg_replace('/[\s\-\(\)]+/', '', $value);

        // Eliminar prefijo internacional +591 o 591
        $telefono = preg_replace('/^(\+)?591/', '', $telefono);

        /**
         * Reglas:
         * - Fijos: 2,3,4 + 7 dígitos = 8 dígitos
         * - Celulares: 6 o 7 + 7 dígitos = 8 dígitos
         */

        $patron = '/^([2-4]\d{7}|[67]\d{7})$/';

        return preg_match($patron, $telefono) === 1;
    }

    /**
     * Mensaje de error.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El teléfono no es válido. '
            . 'Formatos aceptados: '
            . '71234567, 21234567, +59171234567. '
            . 'Se aceptan espacios, guiones y paréntesis.';
    }
}