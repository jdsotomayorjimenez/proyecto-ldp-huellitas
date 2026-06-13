<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMascotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdministrador() === true;
    }

    public function rules(): array
    {
        return [
            'raza_id' => ['required', 'exists:razas,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'genero' => ['required', Rule::in(['macho', 'hembra'])],
            'tamanio' => ['required', Rule::in(['pequeno', 'mediano', 'grande'])],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => [
                'required',
                Rule::in(['disponible', 'en_proceso', 'adoptada', 'no_disponible']),
            ],
        ];
    }
}
