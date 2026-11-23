<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetiroLote extends Model
{
    protected $table = 'retiros_lote';

    protected $fillable = ['lote_id', 'usuario_id', 'fecha', 'cantidad', 'observacion'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime'];
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(LotePeces::class, 'lote_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
