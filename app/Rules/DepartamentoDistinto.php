<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Valida que dos departamentos sean diferentes (para origen y destino)
 * 
 * Útil para derivaciones y asignaciones donde se requiere que el origen
 * sea diferente del destino.
 * 
 * Uso en Form Request:
 * 'idDepartamentoDestino' => [
 *     'required',
 *     new DepartamentoDistinto(request('idDepartamentoOrigen'))
 * ]
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class DepartamentoDistinto implements Rule
{
    private $departamentoComparar;

    /**
     * Constructor
     *
     * @param mixed $departamentoComparar
     */
    public function __construct($departamentoComparar)
    {
        $this->departamentoComparar = $departamentoComparar;
    }

    /**
     * Validar que los departamentos sean distintos.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value): bool
    {
        return (int) $value !== (int) $this->departamentoComparar;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El departamento de destino debe ser diferente al de origen.';
    }
}
