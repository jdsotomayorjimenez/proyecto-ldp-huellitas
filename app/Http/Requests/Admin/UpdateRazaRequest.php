<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRazaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdministrador() === true;
    }

    public function rules(): array
    {
        return [
            'tipo_mascota_id' => ['required', 'exists:tipos_mascotas,id'],
            'nombre' => [
                'required',
                'string',
                'max:80',
                Rule::unique('razas', 'nombre')
                    ->where(
                        fn ($query) => $query->where(
                            'tipo_mascota_id',
                            $this->integer('tipo_mascota_id'),
                        ),
                    )
                    ->ignore($this->route('raza')),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
