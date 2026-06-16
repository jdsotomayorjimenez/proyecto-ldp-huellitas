<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResponderSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdministrador() ?? false;
    }

    public function rules(): array
    {
        return [
            'resultado' => ['required', 'in:aprobada,rechazada'],
            'respuesta' => ['required', 'string', 'max:800'],
            'fecha' => ['required_if:resultado,aprobada', 'nullable', 'date', 'after_or_equal:today'],
            'hora' => ['required_if:resultado,aprobada', 'nullable', 'date_format:H:i'],
            'lugar' => ['required_if:resultado,aprobada', 'nullable', 'string', 'max:150'],
            'indicaciones' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'respuesta.required' => 'Escribe un mensaje de respuesta para el adoptante.',
            'fecha.required_if' => 'Indica la fecha de la cita para aprobar la solicitud.',
            'hora.required_if' => 'Indica la hora de la cita para aprobar la solicitud.',
            'lugar.required_if' => 'Indica el lugar de la cita para aprobar la solicitud.',
        ];
    }
}
