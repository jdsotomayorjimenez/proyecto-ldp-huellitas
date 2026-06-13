<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'role_id',
    'name',
    'email',
    'cedula',
    'fecha_nacimiento',
    'password',
    'telefono',
    'direccion',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_nacimiento' => 'date',
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function solicitudesAdopcion()
    {
        return $this->hasMany(SolicitudAdopcion::class, 'user_id');
    }

    public function respuestasAdministradas()
    {
        return $this->hasMany(RespuestaSolicitud::class, 'administrador_id');
    }

    public function esAdministrador(): bool
    {
        return $this->rol?->nombre === 'Administrador';
    }
}
