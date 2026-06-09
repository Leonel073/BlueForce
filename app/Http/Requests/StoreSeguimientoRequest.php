<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para crear un Seguimiento
 * 
 * Validaciones:
 * - Documento debe existir
 * - Fecha debe ser válida y no en el futuro
 * - Ubicación es obligatoria
 * - Estado debe existir (opcional)
 * 
 * Seguridad:
 * - Solo usuarios autenticados
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class StoreSeguimientoRequest extends FormRequest
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
            'idDocumento' => [
                'required',
                'integer',
                Rule::exists('CORRESPONDENCIA', 'idDocumento'),
            ],

            'fecha' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'before_or_equal:now',
            ],

            'ubicacion' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s\.\,\-\(\)\/]+$/u',
            ],

            'idEstado' => [
                'nullable',
                'integer',
                Rule::exists('ESTADO_DOCUMENTO', 'idEstado'),
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
            'idDocumento.required' => 'El documento es obligatorio.',
            'idDocumento.integer' => 'El ID del documento debe ser un número entero.',
            'idDocumento.exists' => 'El documento especificado no existe.',

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'fecha.date_format' => 'La fecha debe tener formato Y-m-d H:i:s.',
            'fecha.before_or_equal' => 'La fecha no puede ser en el futuro.',

            'ubicacion.required' => 'La ubicación es obligatoria.',
            'ubicacion.string' => 'La ubicación debe ser un texto válido.',
            'ubicacion.max' => 'La ubicación no puede exceder 255 caracteres.',
            'ubicacion.regex' => 'La ubicación contiene caracteres no permitidos.',

            'idEstado.integer' => 'El ID del estado debe ser un número entero.',
            'idEstado.exists' => 'El estado especificado no existe.',

            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ubicacion' => trim($this->ubicacion ?? ''),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
