<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidarCIBoliviano;
use App\Rules\ValidarTelefonoBoliviano;

/**
 * Form Request para crear una Persona (Admin)
 * 
 * Validaciones de negocio:
 * 1. CI válido y único
 * 2. Nombre obligatorio
 * 3. Teléfono válido
 * 4. Email válido (si se proporciona)
 * 5. Tipo solo INTERNO o EXTERNO
 * 6. Si es INTERNO: cargo y departamento obligatorios
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class StorePersonaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo administradores pueden crear personas
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
            /*
            |--------------------------------------------------------------------------
            | DATOS PERSONALES
            |--------------------------------------------------------------------------
            */
            'nombre' => [
                'required',
                'string',
                'max:200',
                'min:3',
                'regex:/^[\pL\s]+$/u',
            ],

            'ci' => [
                'required',
                'string',
                new ValidarCIBoliviano(),
                Rule::unique('PERSONA', 'ci'),
            ],

            'tipo' => [
                'required',
                'in:INTERNO,EXTERNO',
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */
            'telefono_celular' => [
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

            /*
            |--------------------------------------------------------------------------
            | INSTITUCIÓN
            |--------------------------------------------------------------------------
            */
            'institucion' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s]+$/u',
            ],

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN - VALIDACIÓN CONDICIONAL (OPCIONAL PARA INTERNOS)
            |--------------------------------------------------------------------------
            */
            'idCargo' => [
                'nullable',
                'integer',
                Rule::exists('CARGO', 'idCargo')->where('activo', 1),
            ],

            'idDepartamento' => [
                'nullable',
                'integer',
                Rule::exists('DEPARTAMENTO', 'idDepartamento')->where('activo', 1),
            ],

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */
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
            // Datos personales
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

            // CI
            'ci.required' => 'El carnet de identidad es obligatorio.',
            'ci.string' => 'El CI debe ser un texto válido.',
            'ci.unique' => 'Este CI ya está registrado en el sistema.',

            // Tipo
            'tipo.required' => 'El tipo de persona es obligatorio.',
            'tipo.in' => 'El tipo debe ser INTERNO o EXTERNO.',

            // Teléfono
            'telefono_celular.required' => 'El teléfono celular es obligatorio.',

            // Email
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.max' => 'El correo no puede exceder 150 caracteres.',

            // Institución
            'institucion.string' => 'La institución debe ser un texto válido.',
            'institucion.max' => 'La institución no puede exceder 200 caracteres.',

            // Cargo (opcional)
            'idCargo.exists' => 'El cargo especificado no existe o está inactivo.',

            // Departamento (opcional)
            'idDepartamento.exists' => 'El departamento especificado no existe o está inactivo.',

            // Estado
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
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
