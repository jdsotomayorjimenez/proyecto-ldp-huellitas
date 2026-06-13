<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaSolicitud extends Model
{
    protected $table = 'respuestas_solicitud';

    protected $fillable = [
        'solicitud_adopcion_id',
        'administrador_id',
        'resultado',
        'respuesta',
        'fecha_respuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'date',
        ];
    }

    public function solicitudAdopcion()
    {
        return $this->belongsTo(SolicitudAdopcion::class, 'solicitud_adopcion_id');
    }

    public function administrador()
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }
}
