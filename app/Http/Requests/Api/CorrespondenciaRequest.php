<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * LoginRequest
 * Validación para login en API
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422)
        );
    }
}

/**
 * RegisterRequest
 * Validación para registro de usuario
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'idPersona' => 'required|exists:PERSONA,idPersona',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.unique' => 'El email ya está registrado',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'idPersona.required' => 'La persona es requerida',
            'idPersona.exists' => 'La persona no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422)
        );
    }
}

/**
 * StoreCorrespondenciaRequest
 * Validación para crear correspondencia
 */
class StoreCorrespondenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cite' => 'required|string|unique:CORRESPONDENCIA,cite|max:50',
            'asunto' => 'required|string|max:255',
            'fecha' => 'required|date_format:Y-m-d|before_or_equal:today',
            'idTipoDocumento' => 'required|exists:TIPO_DOCUMENTO,idTipoDocumento',
            'idEstado' => 'required|exists:ESTADO_DOCUMENTO,idEstado',
            'idUrgencia' => 'required|exists:NIVEL_URGENCIA,idUrgencia',
            'idRemitente' => 'required|exists:PERSONA,idPersona',
        ];
    }

    public function messages(): array
    {
        return [
            'cite.required' => 'El CITE es requerido',
            'cite.unique' => 'El CITE ya existe',
            'asunto.required' => 'El asunto es requerido',
            'fecha.required' => 'La fecha es requerida',
            'fecha.date_format' => 'La fecha debe estar en formato YYYY-MM-DD',
            'fecha.before_or_equal' => 'La fecha no puede ser mayor a hoy',
            'idTipoDocumento.required' => 'El tipo de documento es requerido',
            'idTipoDocumento.exists' => 'El tipo de documento no existe',
            'idEstado.required' => 'El estado es requerido',
            'idEstado.exists' => 'El estado no existe',
            'idUrgencia.required' => 'El nivel de urgencia es requerido',
            'idUrgencia.exists' => 'El nivel de urgencia no existe',
            'idRemitente.required' => 'El remitente es requerido',
            'idRemitente.exists' => 'El remitente no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422)
        );
    }
}

/**
 * UpdateCorrespondenciaRequest
 * Validación para actualizar correspondencia
 */
class UpdateCorrespondenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $docId = $this->route('id');
        
        return [
            'cite' => "required|string|unique:CORRESPONDENCIA,cite,{$docId},idDocumento|max:50",
            'asunto' => 'required|string|max:255',
            'fecha' => 'required|date_format:Y-m-d|before_or_equal:today',
            'idTipoDocumento' => 'required|exists:TIPO_DOCUMENTO,idTipoDocumento',
            'idEstado' => 'required|exists:ESTADO_DOCUMENTO,idEstado',
            'idUrgencia' => 'required|exists:NIVEL_URGENCIA,idUrgencia',
            'idRemitente' => 'required|exists:PERSONA,idPersona',
        ];
    }

    public function messages(): array
    {
        return [
            'cite.required' => 'El CITE es requerido',
            'cite.unique' => 'El CITE ya existe',
            'asunto.required' => 'El asunto es requerido',
            'fecha.required' => 'La fecha es requerida',
            'fecha.date_format' => 'La fecha debe estar en formato YYYY-MM-DD',
            'fecha.before_or_equal' => 'La fecha no puede ser mayor a hoy',
            'idTipoDocumento.required' => 'El tipo de documento es requerido',
            'idTipoDocumento.exists' => 'El tipo de documento no existe',
            'idEstado.required' => 'El estado es requerido',
            'idEstado.exists' => 'El estado no existe',
            'idUrgencia.required' => 'El nivel de urgencia es requerido',
            'idUrgencia.exists' => 'El nivel de urgencia no existe',
            'idRemitente.required' => 'El remitente es requerido',
            'idRemitente.exists' => 'El remitente no existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422)
        );
    }
}
