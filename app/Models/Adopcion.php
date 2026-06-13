<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adopcion extends Model
{
    protected $table = 'adopciones';

    protected $fillable = [
        'solicitud_adopcion_id',
        'fecha_adopcion',
        'numero_acta',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_adopcion' => 'date',
        ];
    }

    public function solicitudAdopcion()
    {
        return $this->belongsTo(SolicitudAdopcion::class, 'solicitud_adopcion_id');
    }
}
