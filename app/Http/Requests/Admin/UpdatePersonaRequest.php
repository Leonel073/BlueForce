<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidarCIBoliviano;
use App\Rules\ValidarTelefonoBoliviano;

/**
 * Form Request para actualizar una Persona (Admin)
 * 
 * Validaciones:
 * - CI único (ignorando persona actual)
 * - Validación condicional: si es INTERNO, cargo y dept obligatorios
 * - Teléfonos válidos
 * - Email válido
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class UpdatePersonaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo administradores pueden actualizar personas
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
        $idPersona = $this->route('persona') ?? $this->route('id');

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:200',
                'min:3',
                'regex:/^[\pL\s]+$/u',
            ],

            'ci' => [
                'sometimes',
                'required',
                'string',
                new ValidarCIBoliviano(),
                Rule::unique('PERSONA', 'ci')
                    ->ignore($idPersona, 'idPersona'),
            ],

            'tipo' => [
                'sometimes',
                'required',
                'in:INTERNO,EXTERNO',
            ],

            'telefono_celular' => [
                'sometimes',
                'required',
                new ValidarTelefonoBoliviano(),
            ],

            'telefono_fijo' => [
                'nullable',
                new ValidarTelefonoBoliviano(),
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'institucion' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s]+$/u',
            ],

            'idCargo' => [
                'required_if:tipo,INTERNO',
                'nullable',
                'integer',
                Rule::exists('CARGO', 'idCargo')->where('activo', 1),
            ],

            'idDepartamento' => [
                'required_if:tipo,INTERNO',
                'nullable',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento')->where('activo', 1),
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
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

            'ci.string' => 'El CI debe ser un texto válido.',
            'ci.unique' => 'Este CI ya está registrado en el sistema.',

            'tipo.in' => 'El tipo debe ser INTERNO o EXTERNO.',

            'correo.email' => 'El correo debe tener un formato válido.',

            'idCargo.required_if' => 'El cargo es obligatorio para personas INTERNAS.',
            'idCargo.exists' => 'El cargo no existe o está inactivo.',

            'idDepartamento.required_if' => 'El departamento es obligatorio para personas INTERNAS.',
            'idDepartamento.exists' => 'El departamento no existe o está inactivo.',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim($this->nombre ?? ''),
            'ci' => trim($this->ci ?? ''),
            'correo' => strtolower(trim($this->correo ?? '')),
            'institucion' => trim($this->institucion ?? ''),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
