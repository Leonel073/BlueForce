<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnuncioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'asunto' => ['required', 'string', 'max:5000'],
            'archivo_pdf' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx',
                'max:' . config('app.max_pdf_size_kb', 10240),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',
            'asunto.required' => 'El contenido del anuncio es obligatorio.',
            'asunto.max' => 'El contenido no puede superar los 5000 caracteres.',
            'archivo_pdf.mimes' => 'Solo se permiten archivos PDF, Word o Excel.',
            'archivo_pdf.max' => 'El archivo no puede superar los 10 MB.',
        ];
    }
}
