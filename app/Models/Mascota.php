<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    public function getEdadLegibleAttribute(): string
    {
        if (! $this->fecha_nacimiento) {
            return 'Edad no disponible';
        }

        $nacimiento = Carbon::parse($this->fecha_nacimiento)->startOfDay();
        $hoy = now()->startOfDay();

        if ($nacimiento->isFuture()) {
            return 'Edad no disponible';
        }

        $diferencia = $nacimiento->diff($hoy);

        if ($diferencia->y > 0) {
            $edad = $diferencia->y.' '.($diferencia->y === 1 ? 'año' : 'años');

            if ($diferencia->m > 0) {
                $edad .= ' y '.$diferencia->m.' '.($diferencia->m === 1 ? 'mes' : 'meses');
            }

            return $edad;
        }

        if ($diferencia->m > 0) {
            return $diferencia->m.' '.($diferencia->m === 1 ? 'mes' : 'meses');
        }

        return max(1, $diferencia->d).' '.($diferencia->d === 1 ? 'día' : 'días');
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
