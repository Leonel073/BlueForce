<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para crear un Usuario (Admin)
 * 
 * Validaciones de seguridad:
 * - Email único en el sistema
 * - Contraseña fuerte (mín 12 caracteres, confirmación requerida)
 * - Rol válido
 * - Persona asignada debe existir
 * 
 * Seguridad OWASP:
 * - Password mínimo 12 caracteres (OWASP recomendación)
 * - Confirmación de contraseña requerida
 * - Email único
 * - Rol restringido a valores predefinidos
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo administradores pueden crear usuarios
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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],

            /*
            |--------------------------------------------------------------------------
            | CREDENCIALES
            |--------------------------------------------------------------------------
            */
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:12',  // OWASP: Mínimo 12 caracteres
                'confirmed',  // Debe existir password_confirmation
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',  // Complejidad
            ],

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN
            |--------------------------------------------------------------------------
            */
            'idRol' => [
                'required',
                'integer',
                Rule::exists('ROL', 'idRol'),
            ],

            'idPersona' => [
                'nullable',
                'integer',
                Rule::exists('PERSONA', 'idPersona'),
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
            'name.required' => 'El nombre del usuario es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',

            // Email
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'email.unique' => 'Este email ya está registrado en el sistema.',

            // Contraseña
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener mínimo 12 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener: mayúscula, minúscula, número y carácter especial (@$!%*?&).',

            // Rol
            'idRol.required' => 'El rol es obligatorio.',
            'idRol.integer' => 'El ID del rol debe ser un número entero.',
            'idRol.exists' => 'El rol especificado no existe.',

            // Persona
            'idPersona.integer' => 'La persona debe ser válida.',
            'idPersona.exists' => 'La persona especificada no existe.',

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
            'name' => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
