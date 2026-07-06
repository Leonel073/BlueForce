<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * ✅ SEGURIDAD: Ahora valida que el usuario esté autenticado
     * Antes: return true ❌ (VULNERABILIDAD)
     * Ahora: Requiere usuario autenticado ✅
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            
            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO - SIEMPRE VALIDAR
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
            | DESTINO - SIEMPRE VALIDAR
            |--------------------------------------------------------------------------
            */

            'departamento' => 
                'required|exists:DEPARTAMENTO,idDepartamento',

            'persona_destinataria' => 
                'nullable|exists:PERSONA,idPersona',

            'responsable_destino' =>
                'required|exists:PERSONA,idPersona',

            /*
            |--------------------------------------------------------------------------
            | ARCHIVO PDF (OPCIONAL)
            |--------------------------------------------------------------------------
            */
            'archivo_pdf' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:' . config('app.max_pdf_size_kb', 10240),
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN CONDICIONAL SEGÚN OPCIÓN DE REMITENTE
        |--------------------------------------------------------------------------
        */

        $opcionRemitente = $this->input('opcion_remitente');

        if ($opcionRemitente === 'otra_persona') {
            /*
            |--------------------------------------------------------------------------
            | VALIDAR SOLO CUANDO SE SELECCIONA "OTRA PERSONA"
            |--------------------------------------------------------------------------
            */
            $rules['nombre_remitente'] = [
                'required',
                'string',
                'max:200',
                // Permitir: letras (incluyendo acentos, ñ), espacios
                'regex:/^[\pL\s\-áéíóúÁÉÍÓÚñÑ]+$/u'
            ];

            $rules['correo_remitente'] = 
                'nullable|email|max:150';

            $rules['institucion_remitente'] = [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\pL\pN\s\-áéíóúÁÉÍÓÚñÑ]+$/u'
            ];

            $rules['tipo_remitente'] = 
                'required|in:INTERNO,EXTERNO';

            $rules['ci_remitente'] = [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Za-z\-]+$/'
            ];

            $rules['telefono_celular'] = [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\-\s]+$/'
            ];

            $rules['telefono_fijo'] = [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\+\-\s]+$/'
            ];

        } else {
            /*
            |--------------------------------------------------------------------------
            | CUANDO SE SELECCIONA "YO MISMO"
            | No validar los campos del remitente, se usan datos del usuario autenticado
            |--------------------------------------------------------------------------
            */
            // Los campos del remitente son enviados como hidden inputs, no se validan
        }

        return $rules;
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
            | REMITENTE - NOMBRE
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'nombre_remitente.required' => 
                'El nombre completo del remitente es obligatorio.',
            
            'nombre_remitente.string' => 
                'El nombre debe ser un texto válido.',
            
            'nombre_remitente.max' => 
                'El nombre no puede exceder 200 caracteres.',
            
            'nombre_remitente.regex' => 
                'El nombre solo puede contener letras, espacios, acentos (á, é, í, ó, ú) y la letra ñ. Ejemplos válidos: Juan Pérez, María Fernanda López, José Luis García.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - CI
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'ci_remitente.required' => 
                'El carnet de identidad es obligatorio.',
            
            'ci_remitente.string' => 
                'El CI debe ser un texto válido.',
            
            'ci_remitente.max' => 
                'El CI no puede exceder 20 caracteres.',
            
            'ci_remitente.regex' => 
                'El CI solo puede contener números, letras y guiones.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TELÉFONO CELULAR
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'telefono_celular.required' => 
                'El teléfono celular es obligatorio.',
            
            'telefono_celular.string' => 
                'El teléfono celular debe ser un texto válido.',
            
            'telefono_celular.max' => 
                'El teléfono celular no puede exceder 20 caracteres.',
            
            'telefono_celular.regex' => 
                'El teléfono celular debe contener solo números, espacios, guiones o signos +.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TELÉFONO FIJO
            | Solo se valida cuando se selecciona "Otra Persona"
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
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'correo_remitente.email' => 
                'El correo debe ser una dirección de correo válida (ej: usuario@ejemplo.com).',
            
            'correo_remitente.max' => 
                'El correo no puede exceder 150 caracteres.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - INSTITUCIÓN
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'institucion_remitente.string' => 
                'La institución debe ser un texto válido.',
            
            'institucion_remitente.max' => 
                'La institución no puede exceder 200 caracteres.',
            
            'institucion_remitente.regex' => 
                'La institución solo puede contener letras, números, espacios, acentos y la letra ñ.',

            /*
            |--------------------------------------------------------------------------
            | REMITENTE - TIPO
            | Solo se valida cuando se selecciona "Otra Persona"
            |--------------------------------------------------------------------------
            */
            'tipo_remitente.required' => 
                'Debe seleccionar el tipo de remitente (Interno o Externo).',
            
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

            'responsable_destino.required' =>
                'Debe seleccionar un responsable destino. Este campo es obligatorio.',

            'responsable_destino.exists' =>
                'El responsable seleccionado no existe en el sistema.',

            /*
            |--------------------------------------------------------------------------
            | PDF
            |--------------------------------------------------------------------------
            */
            'archivo_pdf.file'   => 'El archivo debe ser un fichero válido.',
            'archivo_pdf.mimes'  => 'Solo se permiten archivos en formato PDF.',
            'archivo_pdf.max'    => 'El archivo PDF no puede superar los 10 MB.',
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
            'responsable_destino' => 'Responsable Destino',
        ];
    }
}
