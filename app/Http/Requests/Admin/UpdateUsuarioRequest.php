<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para actualizar un Usuario (Admin)
 * 
 * Validaciones:
 * - Email único (ignorando usuario actual)
 * - Contraseña opcional pero si se proporciona debe ser fuerte
 * - Rol válido
 * - Persona asignada debe existir
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo administradores pueden actualizar usuarios
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
        $idUsuario = $this->route('usuario') ?? $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($idUsuario, 'id'),
            ],

            'password' => [
                'nullable',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
            ],

            'idRol' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('ROL', 'idRol'),
            ],

            // idPersona NO se permite cambiar en la edición
            // para mantener integridad referencial

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
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',

            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'email.unique' => 'Este email ya está registrado en el sistema.',

            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener mínimo 12 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener: mayúscula, minúscula, número y carácter especial (@$!%*?&).',

            'idRol.integer' => 'El ID del rol debe ser un número entero.',
            'idRol.exists' => 'El rol especificado no existe.',

            'idPersona.integer' => 'La persona debe ser válida.',
            'idPersona.exists' => 'La persona especificada no existe.',

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
