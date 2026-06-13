<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMascota extends Model
{
    protected $table = 'tipos_mascotas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function razas()
    {
        return $this->hasMany(Raza::class, 'tipo_mascota_id');
    }

    public function requisitosAdopcion()
    {
        return $this->hasMany(RequisitoAdopcion::class, 'tipo_mascota_id');
    }
}
