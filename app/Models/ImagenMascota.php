<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenMascota extends Model
{
    protected $table = 'imagenes_mascotas';

    protected $fillable = [
        'mascota_id',
        'ruta',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascota_id');
    }
}
