<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tratamiento extends Model
{
    protected $table = 'tratamientos';

    protected $fillable = [
        'lote_id', 'fecha_inicio', 'fecha_fin', 'sintomas',
        'producto', 'dosis', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(LotePeces::class, 'lote_id');
    }
}
