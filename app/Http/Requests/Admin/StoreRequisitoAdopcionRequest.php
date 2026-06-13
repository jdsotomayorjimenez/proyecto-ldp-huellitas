<?php

namespace App\Http\Requests\Admin;

use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRequisitoAdopcionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'obligatorio' => $this->boolean('obligatorio'),
            'obligatorios' => collect($this->input('obligatorios', []))
                ->map(fn ($valor) => filter_var($valor, FILTER_VALIDATE_BOOLEAN))
                ->all(),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->esAdministrador() === true;
    }

    public function rules(): array
    {
        return [
            'alcance' => ['required', Rule::in(['tipo', 'general'])],
            'tipo_mascota_id' => [
                Rule::requiredIf($this->input('alcance') === 'tipo'),
                'nullable',
                'exists:tipos_mascotas,id',
            ],
            'tipos' => [
                Rule::requiredIf($this->input('alcance') === 'general'),
                'array',
                'min:1',
            ],
            'tipos.*' => ['integer', 'distinct', 'exists:tipos_mascotas,id'],
            'obligatorios' => ['nullable', 'array'],
            'obligatorios.*' => ['boolean'],
            'nombre' => [
                'required',
                'string',
                'max:100',
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'obligatorio' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $tipoIds = $this->input('alcance') === 'general'
                    ? collect($this->input('tipos', []))->map(fn ($id) => (int) $id)
                    : collect([$this->integer('tipo_mascota_id')]);

                $duplicados = RequisitoAdopcion::query()
                    ->whereIn('tipo_mascota_id', $tipoIds)
                    ->where('nombre', $this->string('nombre')->trim())
                    ->with('tipoMascota')
                    ->get();

                if ($duplicados->isNotEmpty()) {
                    $tipos = $duplicados->pluck('tipoMascota.nombre')->join(', ');
                    $validator->errors()->add(
                        'nombre',
                        "Ya existe este requisito para: {$tipos}.",
                    );
                }

                if ($this->input('alcance') === 'general') {
                    $tiposActuales = TipoMascota::pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
                    $tiposEnviados = $tipoIds->sort()->values();

                    if ($tiposActuales->all() !== $tiposEnviados->all()) {
                        $validator->errors()->add(
                            'tipos',
                            'El requisito general debe incluir todos los tipos de mascotas actuales.',
                        );
                    }
                }
            },
        ];
    }
}
