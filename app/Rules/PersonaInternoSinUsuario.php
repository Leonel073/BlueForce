<?php

namespace App\Rules;

use Closure;
use App\Models\Persona;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que la persona seleccionada para crear un usuario:
 * 1. Exista en el sistema
 * 2. Sea de tipo = 'INTERNO'
 * 3. No tenga ya un usuario asignado
 *
 * @author Sistema de Correspondencia
 */
class PersonaInternoSinUsuario implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $persona = Persona::find($value);

        if (!$persona) {
            $fail('La persona especificada no existe en el sistema.');
            return;
        }

        if ($persona->tipo !== 'INTERNO') {
            $fail('Solo las personas internas pueden tener una cuenta de usuario. Las personas externas no pueden acceder al sistema.');
            return;
        }

        if ($persona->usuario()->exists()) {
            $fail('Esta persona ya tiene un usuario asignado. No se pueden crear dos cuentas para la misma persona.');
        }
    }
}
