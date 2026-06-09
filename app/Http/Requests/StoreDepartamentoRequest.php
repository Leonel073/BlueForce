<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para crear un Departamento
 * 
 * Validaciones:
 * - nombre: Obligatorio, string, máx 200 caracteres, único
 * - idPersonaEncargada: Opcional, debe existir en PERSONA
 * 
 * Seguridad:
 * - Solo administradores pueden crear departamentos
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class StoreDepartamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->idRol == 1;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s\-\.]+$/u',
                Rule::unique('DEPARTAMENTO', 'nombre'),
            ],
            
            'idPersonaEncargada' => [
                'nullable',
                'integer',
                Rule::exists('PERSONA', 'idPersona')->where('activo', 1),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del departamento es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'nombre.unique' => 'Ya existe un departamento con este nombre.',
            
            'idPersonaEncargada.integer' => 'La persona encargada debe ser válida.',
            'idPersonaEncargada.exists' => 'La persona encargada no existe en el sistema.',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim($this->nombre ?? ''),
        ]);
    }
}
