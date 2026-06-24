<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\PersonaTrabajadorSinUsuario;

/**
 * Crea un usuario vinculado a una persona trabajadora.
 * Solo administradores pueden crear usuarios.
 */
class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->idRol == 1;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
            ],
            'idRol' => [
                'required',
                'integer',
                Rule::exists('ROL', 'idRol'),
            ],
            'idPersona' => [
                'required',
                'integer',
                new PersonaTrabajadorSinUsuario(),
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre del usuario es obligatorio.',
            'name.regex'         => 'El nombre solo puede contener letras y espacios.',
            'name.max'           => 'El nombre no puede exceder 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El correo debe tener un formato válido.',
            'email.unique'       => 'Este correo ya está registrado en el sistema.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener mínimo 12 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex'     => 'La contraseña debe contener: mayúscula, minúscula, número y carácter especial (@$!%*?&).',
            'idRol.required'     => 'Debe seleccionar un rol.',
            'idRol.exists'       => 'El rol seleccionado no existe.',
            'idPersona.required' => 'Debe seleccionar una persona trabajadora.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'   => trim($this->name ?? ''),
            'email'  => strtolower(trim($this->email ?? '')),
            'activo' => filter_var($this->activo ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
