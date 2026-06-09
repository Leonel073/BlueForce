<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\DepartamentoDistinto;

/**
 * Form Request para actualizar una Derivación
 * 
 * Validaciones:
 * - Los departamentos no pueden ser iguales
 * - Las fechas deben ser válidas (recepción >= envío)
 * - El usuario asignado debe existir
 * 
 * Seguridad:
 * - Solo usuarios autenticados
 * - Valida integridad de relaciones
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class UpdateDerivacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'idDepartamentoOrigen' => [
                'sometimes',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento'),
            ],

            'idDepartamentoDestino' => [
                'sometimes',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento'),
                new DepartamentoDistinto(
                    $this->idDepartamentoOrigen ?? $this->route('derivacion')?->idDepartamentoOrigen
                ),
            ],

            'idUsuarioAsignado' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('activo', 1),
            ],

            'idUsuarioEnvio' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'instruccion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[\pL\pN\s\.\,\-\(\)\#\/]+$/u',
            ],

            'fechaEnvio' => [
                'sometimes',
                'date',
                'date_format:Y-m-d H:i:s',
            ],

            'fechaRecepcion' => [
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:fechaEnvio',
            ],

            'orden' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'idDepartamentoOrigen.exists' => 'El departamento de origen no existe.',
            'idDepartamentoDestino.exists' => 'El departamento de destino no existe.',
            'idUsuarioAsignado.exists' => 'El usuario asignado no existe o está inactivo.',
            'idUsuarioEnvio.exists' => 'El usuario de envío no existe.',
            'instruccion.max' => 'La instrucción no puede exceder 500 caracteres.',
            'fechaEnvio.date_format' => 'La fecha de envío debe tener formato Y-m-d H:i:s.',
            'fechaRecepcion.after_or_equal' => 'La fecha de recepción debe ser posterior o igual a la fecha de envío.',
            'orden.min' => 'El orden debe ser mayor o igual a 1.',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'instruccion' => trim($this->instruccion ?? ''),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
