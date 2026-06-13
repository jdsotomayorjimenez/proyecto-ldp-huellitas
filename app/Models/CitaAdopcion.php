<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaAdopcion extends Model
{
    protected $table = 'citas_adopcion';

    protected $fillable = [
        'solicitud_adopcion_id',
        'fecha',
        'hora',
        'lugar',
        'indicaciones',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function solicitudAdopcion()
    {
        return $this->belongsTo(SolicitudAdopcion::class, 'solicitud_adopcion_id');
    }
}
