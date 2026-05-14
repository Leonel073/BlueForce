<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('admin');
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
                'regex:/^[\pL\s]+$/u'
            ],

            'ci' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Za-z\-]+$/',
                'unique:PERSONA,ci'
            ],

            'tipo' => 
                'required|in:INTERNO,EXTERNO',

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */

            'telefono_celular' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\-\s]+$/'
            ],

            'telefono_fijo' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\+\-\s]+$/'
            ],

            'correo' => 
                'nullable|email|max:150',

            /*
            |--------------------------------------------------------------------------
            | INSTITUCIÓN
            |--------------------------------------------------------------------------
            */

            'institucion' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s]+$/u'
            ],

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            'idCargo' => 
                'required|exists:CARGO,idCargo',

            'idDepartamento' => 
                'required|exists:DEPARTAMENTO,idDepartamento',

            'es_responsable' => 
                'nullable|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',

            'ci.required' => 'El carnet de identidad es obligatorio.',
            'ci.unique' => 'Este CI ya existe en el sistema.',
            'ci.regex' => 'El CI solo puede contener números, letras y guiones.',

            'tipo.required' => 'Debe seleccionar el tipo de persona (INTERNO o EXTERNO).',
            'tipo.in' => 'El tipo debe ser INTERNO o EXTERNO.',

            'telefono_celular.required' => 'El teléfono celular es obligatorio.',
            'telefono_celular.regex' => 'El teléfono debe contener solo números, espacios, guiones o +.',

            'telefono_fijo.regex' => 'El teléfono fijo debe ser válido.',

            'correo.email' => 'El correo debe ser una dirección válida.',

            'institucion.regex' => 'La institución solo puede contener letras, números y espacios.',

            'idCargo.required' => 'Debe asignar un cargo a la persona.',
            'idCargo.exists' => 'El cargo seleccionado no existe.',

            'idDepartamento.required' => 'Debe asignar un departamento.',
            'idDepartamento.exists' => 'El departamento no existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'Nombre',
            'ci' => 'Carnet de Identidad',
            'tipo' => 'Tipo de Persona',
            'telefono_celular' => 'Teléfono Celular',
            'telefono_fijo' => 'Teléfono Fijo',
            'correo' => 'Correo',
            'institucion' => 'Institución',
            'idCargo' => 'Cargo',
            'idDepartamento' => 'Departamento',
            'es_responsable' => 'Responsable',
        ];
    }
}
