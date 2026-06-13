<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    protected $table = 'razas';

    protected $fillable = [
        'tipo_mascota_id',
        'nombre',
        'descripcion',
    ];

    public function tipoMascota()
    {
        return $this->belongsTo(TipoMascota::class, 'tipo_mascota_id');
    }

    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'raza_id');
    }
}
