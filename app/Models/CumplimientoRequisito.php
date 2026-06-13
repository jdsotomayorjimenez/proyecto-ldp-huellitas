<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CumplimientoRequisito extends Model
{
    protected $table = 'cumplimientos_requisitos';

    protected $fillable = [
        'solicitud_adopcion_id',
        'requisito_adopcion_id',
        'estado',
        'observacion',
        'fecha_revision',
    ];

    protected function casts(): array
    {
        return [
            'fecha_revision' => 'date',
        ];
    }

    public function solicitudAdopcion()
    {
        return $this->belongsTo(SolicitudAdopcion::class, 'solicitud_adopcion_id');
    }

    public function requisitoAdopcion()
    {
        return $this->belongsTo(RequisitoAdopcion::class, 'requisito_adopcion_id');
    }
}
