<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO
            |--------------------------------------------------------------------------
            */

            'asunto' => [
                'required',
                'string',
                'max:500',
                'regex:/^[\pL\pN\s\.\,\-\(\)\#\/]+$/u'
            ],

            'tipo_documento' => 
                'required|exists:TIPO_DOCUMENTO,idTipoDocumento',

            'nivel_urgencia' => 
                'required|exists:NIVEL_URGENCIA,idUrgencia',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE
            |--------------------------------------------------------------------------
            */

            'nombre_remitente' => [
                'required',
                'string',
                'max:200',
                'regex:/^[\pL\s]+$/u'
            ],

            'correo_remitente' => 
                'nullable|email|max:150',

            // Cargo es read-only y se valida en el servidor (no incluir validación aquí)

            'institucion_remitente' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s]+$/u'
            ],

            'tipo_remitente' => 
                'required|in:INTERNO,EXTERNO',

            'ci_remitente' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Za-z\-]+$/'
            ],

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

            /*
            |--------------------------------------------------------------------------
            | DESTINO
            |--------------------------------------------------------------------------
            */

            'departamento' => 
                'required|exists:DEPARTAMENTO,idDepartamento',

            'persona_destinataria' => 
                'nullable|exists:PERSONA,idPersona',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            
            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO - ASUNTO
            |--------------------------------------------------------------------------
            */
            'asunto.required' => 
                'El asunto es obligatorio. Por favor, ingrese una descripción del documento.',
            
            'asunto.string' => 
                'El asunto debe ser un texto válido.',
            
            'asunto.max' => 
                'El asunto no puede exceder 500 caracteres. Reduzca el texto.',
            
            'asunto.regex' => 
                'El asunto contiene caracteres especiales no permitidos.',

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO - TIPO
            |--------------------------------------------------------------------------
            */
            'tipo_documento.required' => 
                'Debe seleccionar un tipo de documento. Este campo es obligatorio.',
            
            'tipo_documento.exists' => 
                'El tipo de documento seleccionado no existe en el sistema.',

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO - URGENCIA
            |--------------------------------------------------------------------------
            */
            'nivel_urgencia.required' => 
                'Debe seleccionar un nivel de urgencia. Este campo es obligatorio.',
            
            'nivel_urgencia.exists' => 
                'El nivel de urgencia seleccionado no existe en el sistema.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - CI
            |--------------------------------------------------------------------------
            */
            'ci_remitente.required' => 
                'El carnet de identidad es obligatorio. Por favor, ingrese el CI del remitente.',
            
            'ci_remitente.string' => 
                'El CI debe ser un texto válido.',
            
            'ci_remitente.max' => 
                'El CI no puede exceder 20 caracteres.',
            
            'ci_remitente.regex' => 
                'El CI solo puede contener números, letras y guiones.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - NOMBRE
            |--------------------------------------------------------------------------
            */
            'nombre_remitente.required' => 
                'El nombre completo del remitente es obligatorio. Por favor, ingrese el nombre.',
            
            'nombre_remitente.string' => 
                'El nombre debe ser un texto válido.',
            
            'nombre_remitente.max' => 
                'El nombre no puede exceder 200 caracteres.',
            
            'nombre_remitente.regex' => 
                'El nombre solo puede contener letras y espacios.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TELÉFONO CELULAR
            |--------------------------------------------------------------------------
            */
            'telefono_celular.required' => 
                'El teléfono celular es obligatorio. Por favor, ingrese un número válido.',
            
            'telefono_celular.string' => 
                'El teléfono celular debe ser un texto válido.',
            
            'telefono_celular.max' => 
                'El teléfono celular no puede exceder 20 caracteres.',
            
            'telefono_celular.regex' => 
                'El teléfono celular debe contener solo números, espacios, guiones o signos +.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TELÉFONO FIJO
            |--------------------------------------------------------------------------
            */
            'telefono_fijo.string' => 
                'El teléfono fijo debe ser un texto válido.',
            
            'telefono_fijo.max' => 
                'El teléfono fijo no puede exceder 20 caracteres.',
            
            'telefono_fijo.regex' => 
                'El teléfono fijo debe contener solo números, espacios, guiones o signos +.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - CORREO
            |--------------------------------------------------------------------------
            */
            'correo_remitente.email' => 
                'El correo debe ser una dirección de correo válida (ej: usuario@ejemplo.com).',
            
            'correo_remitente.max' => 
                'El correo no puede exceder 150 caracteres.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - CARGO
            |--------------------------------------------------------------------------
            */
            'cargo_remitente.string' => 
                'El cargo debe ser un texto válido.',
            
            'cargo_remitente.max' => 
                'El cargo no puede exceder 150 caracteres.',
            
            'cargo_remitente.regex' => 
                'El cargo solo puede contener letras y espacios.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - INSTITUCIÓN
            |--------------------------------------------------------------------------
            */
            'institucion_remitente.string' => 
                'La institución debe ser un texto válido.',
            
            'institucion_remitente.max' => 
                'La institución no puede exceder 200 caracteres.',
            
            'institucion_remitente.regex' => 
                'La institución solo puede contener letras, números y espacios.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TIPO
            |--------------------------------------------------------------------------
            */
            'tipo_remitente.required' => 
                'Debe seleccionar el tipo de remitente (Interno o Externo). Este campo es obligatorio.',
            
            'tipo_remitente.in' => 
                'El tipo de remitente debe ser "INTERNO" o "EXTERNO".',

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTO DESTINO
            |--------------------------------------------------------------------------
            */
            'departamento.required' => 
                'Debe seleccionar un departamento destino. Este campo es obligatorio para el flujo.',
            
            'departamento.exists' => 
                'El departamento seleccionado no existe en el sistema.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'asunto' => 'Asunto',
            'tipo_documento' => 'Tipo de Documento',
            'nivel_urgencia' => 'Nivel de Urgencia',
            'nombre_remitente' => 'Nombre del Remitente',
            'ci_remitente' => 'Carnet de Identidad',
            'telefono_celular' => 'Teléfono Celular',
            'telefono_fijo' => 'Teléfono Fijo',
            'correo_remitente' => 'Correo del Remitente',
            'cargo_remitente' => 'Cargo del Remitente',
            'institucion_remitente' => 'Institución del Remitente',
            'tipo_remitente' => 'Tipo de Remitente',
            'departamento' => 'Departamento Destino',
        ];
    }
}
