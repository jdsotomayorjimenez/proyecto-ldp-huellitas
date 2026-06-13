<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoAdopcion extends Model
{
    protected $table = 'requisitos_adopcion';

    protected $fillable = [
        'tipo_mascota_id',
        'nombre',
        'descripcion',
        'obligatorio',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'obligatorio' => 'boolean',
        ];
    }

    public function tipoMascota()
    {
        return $this->belongsTo(TipoMascota::class, 'tipo_mascota_id');
    }

    public function cumplimientos()
    {
        return $this->hasMany(CumplimientoRequisito::class, 'requisito_adopcion_id');
    }
}
