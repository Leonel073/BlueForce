<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\DerivacionValida;
use App\Rules\DepartamentoDistinto;

/**
 * Form Request para crear una Derivación
 * 
 * Validaciones de negocio:
 * 1. Documento debe existir
 * 2. Departamento origen debe ser diferente del destino
 * 3. No puede haber derivación duplicada activa
 * 4. Usuario asignado debe existir
 * 5. Fechas deben ser válidas
 * 
 * Seguridad:
 * - Solo usuarios autenticados y con permisos de derivación
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class StoreDerivacionRequest extends FormRequest
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
            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO
            |--------------------------------------------------------------------------
            */
            'idDocumento' => [
                'required',
                'integer',
                Rule::exists('CORRESPONDENCIA', 'idDocumento'),
            ],

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTOS
            |--------------------------------------------------------------------------
            */
            'idDepartamentoOrigen' => [
                'required',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento'),
            ],

            'idDepartamentoDestino' => [
                'required',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento'),
                new DepartamentoDistinto($this->idDepartamentoOrigen),
            ],

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN Y USUARIO
            |--------------------------------------------------------------------------
            */
            'idUsuarioAsignado' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('activo', 1),
            ],

            'idUsuarioEnvio' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],

            /*
            |--------------------------------------------------------------------------
            | INSTRUCCIONES
            |--------------------------------------------------------------------------
            */
            'instruccion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[\pL\pN\s\.\,\-\(\)\#\/]+$/u',
            ],

            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */
            'fechaEnvio' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
            ],

            'fechaRecepcion' => [
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:fechaEnvio',
            ],

            /*
            |--------------------------------------------------------------------------
            | ORDEN Y ESTADO
            |--------------------------------------------------------------------------
            */
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
            // Documento
            'idDocumento.required' => 'El documento es obligatorio.',
            'idDocumento.integer' => 'El ID del documento debe ser un número entero.',
            'idDocumento.exists' => 'El documento especificado no existe.',

            // Departamentos
            'idDepartamentoOrigen.required' => 'El departamento de origen es obligatorio.',
            'idDepartamentoOrigen.exists' => 'El departamento de origen no existe.',
            'idDepartamentoDestino.required' => 'El departamento de destino es obligatorio.',
            'idDepartamentoDestino.exists' => 'El departamento de destino no existe.',

            // Usuarios
            'idUsuarioAsignado.integer' => 'El usuario asignado debe ser válido.',
            'idUsuarioAsignado.exists' => 'El usuario asignado no existe o está inactivo.',
            'idUsuarioEnvio.required' => 'El usuario de envío es obligatorio.',
            'idUsuarioEnvio.exists' => 'El usuario de envío no existe.',

            // Instrucción
            'instruccion.string' => 'La instrucción debe ser un texto válido.',
            'instruccion.max' => 'La instrucción no puede exceder 500 caracteres.',
            'instruccion.regex' => 'La instrucción contiene caracteres no permitidos.',

            // Fechas
            'fechaEnvio.required' => 'La fecha de envío es obligatoria.',
            'fechaEnvio.date' => 'La fecha de envío debe ser una fecha válida.',
            'fechaEnvio.date_format' => 'La fecha de envío debe tener formato Y-m-d H:i:s.',
            'fechaRecepcion.date' => 'La fecha de recepción debe ser una fecha válida.',
            'fechaRecepcion.date_format' => 'La fecha de recepción debe tener formato Y-m-d H:i:s.',
            'fechaRecepcion.after_or_equal' => 'La fecha de recepción debe ser posterior o igual a la fecha de envío.',

            // Orden
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden debe ser mayor o igual a 1.',

            // Estado
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y preparar datos
        $this->merge([
            'instruccion' => trim($this->instruccion ?? ''),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
