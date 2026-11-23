<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudClave extends Model
{
    protected $table = 'solicitudes_clave';

    protected $fillable = ['usuario_id', 'estado', 'atendida_por'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function atendidaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'atendida_por');
    }
}
