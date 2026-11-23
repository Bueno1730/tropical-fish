<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'codigo', 'ci', 'nombre', 'usuario', 'password', 'rol', 'estado', 'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class, 'usuario_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    public static function proximoCodigo(): string
    {
        $max = static::max('id') ?? 0;
        return 'USR-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}
