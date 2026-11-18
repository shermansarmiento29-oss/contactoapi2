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
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del contacto no puede estar vacío.',
            'email.email' => 'El formato del correo electrónico es inválido.',
            'avatar.image' => 'El archivo debe ser una imagen válida.',
            'avatar.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o webp.',
            'avatar.max' => 'La imagen no debe pesar más de 2 MB.',
        ];
    }
}
