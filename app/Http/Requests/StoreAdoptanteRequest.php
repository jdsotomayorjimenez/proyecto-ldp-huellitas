<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreAdoptanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null || $this->user()->esAdministrador();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'cedula' => ['nullable', 'digits:10', 'unique:users,cedula'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'telefono' => ['nullable', 'digits:10'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
