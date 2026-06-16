<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudAdopcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->esAdministrador();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'vivienda_propia' => $this->boolean('vivienda_propia'),
            'tiene_mascotas' => $this->boolean('tiene_mascotas'),
        ]);
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', 'string', 'max:255'],
            'experiencia' => ['nullable', 'string', 'max:255'],
            'tipo_vivienda' => ['required', 'in:casa,departamento,finca,otro'],
            'vivienda_propia' => ['boolean'],
            'tiene_mascotas' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'Cuéntanos por qué quieres adoptar a esta mascota.',
            'tipo_vivienda.required' => 'Selecciona tu tipo de vivienda.',
            'tipo_vivienda.in' => 'El tipo de vivienda seleccionado no es válido.',
        ];
    }
}
