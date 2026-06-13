<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    protected $table = 'mascotas';

    protected $fillable = [
        'raza_id',
        'nombre',
        'fecha_nacimiento',
        'genero',
        'tamanio',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class, 'raza_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenMascota::class, 'mascota_id');
    }

    public function solicitudesAdopcion()
    {
        return $this->hasMany(SolicitudAdopcion::class, 'mascota_id');
    }
}
