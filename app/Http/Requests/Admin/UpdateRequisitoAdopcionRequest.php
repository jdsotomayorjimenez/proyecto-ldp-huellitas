<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequisitoAdopcionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'obligatorio' => $this->boolean('obligatorio'),
        ]);
    }

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
                'max:100',
                Rule::unique('requisitos_adopcion', 'nombre')
                    ->where(
                        fn ($query) => $query->where(
                            'tipo_mascota_id',
                            $this->integer('tipo_mascota_id'),
                        ),
                    )
                    ->ignore($this->route('requisito')),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'obligatorio' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
