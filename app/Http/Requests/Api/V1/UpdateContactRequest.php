<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del contacto no puede estar vacío.',
            'email.email' => 'El formato del correo electrónico es inválido.',
        ];
    }
}