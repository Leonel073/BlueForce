<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para operaciones de Recibidas
 * 
 * Validaciones:
 * - ID del documento debe existir
 * - El documento debe estar en estado válido para la operación
 * 
 * @author Sistema de Correspondencia
 * @since 2026-05-26
 */
class RecibirDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Solo usuarios autenticados pueden recibir documentos
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
        // El ID puede venir en route o en request
        $id = $this->route('id') ?? $this->input('id');

        return [
            'id' => [
                'required_without:idDocumento',
                'integer',
                Rule::exists('CORRESPONDENCIA', 'idDocumento'),
            ],

            'idDocumento' => [
                'required_without:id',
                'integer',
                Rule::exists('CORRESPONDENCIA', 'idDocumento'),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'id.required_without' => 'El ID del documento es obligatorio.',
            'id.integer' => 'El ID del documento debe ser un número entero.',
            'id.exists' => 'El documento especificado no existe.',

            'idDocumento.required_without' => 'El ID del documento es obligatorio.',
            'idDocumento.integer' => 'El ID del documento debe ser un número entero.',
            'idDocumento.exists' => 'El documento especificado no existe.',
        ];
    }

    /**
     * Obtener el ID del documento (desde route o request)
     */
    public function getIdDocumento(): int
    {
        return $this->route('id') ?? $this->input('idDocumento') ?? $this->input('id');
    }
}
