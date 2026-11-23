<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $fillable = ['usuario_id', 'ip', 'user_agent', 'inicio', 'ultima_actividad', 'fin'];

    protected function casts(): array
    {
        return [
            'inicio' => 'datetime',
            'ultima_actividad' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
