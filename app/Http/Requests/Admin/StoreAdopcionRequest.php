<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdopcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdministrador() ?? false;
    }

    public function rules(): array
    {
        return [
            'fecha_adopcion' => ['required', 'date'],
            'numero_acta' => ['required', 'string', 'max:30', 'unique:adopciones,numero_acta'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_acta.unique' => 'Ya existe una adopción con ese número de acta.',
        ];
    }
}
