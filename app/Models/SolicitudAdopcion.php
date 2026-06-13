<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAdopcion extends Model
{
    protected $table = 'solicitudes_adopcion';

    protected $fillable = [
        'user_id',
        'mascota_id',
        'motivo',
        'experiencia',
        'tipo_vivienda',
        'vivienda_propia',
        'tiene_mascotas',
        'estado',
        'fecha_solicitud',
    ];

    protected function casts(): array
    {
        return [
            'vivienda_propia' => 'boolean',
            'tiene_mascotas' => 'boolean',
            'fecha_solicitud' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascota_id');
    }

    public function respuesta()
    {
        return $this->hasOne(RespuestaSolicitud::class, 'solicitud_adopcion_id');
    }

    public function cita()
    {
        return $this->hasOne(CitaAdopcion::class, 'solicitud_adopcion_id');
    }

    public function cumplimientosRequisitos()
    {
        return $this->hasMany(CumplimientoRequisito::class, 'solicitud_adopcion_id');
    }

    public function adopcion()
    {
        return $this->hasOne(Adopcion::class, 'solicitud_adopcion_id');
    }
}
