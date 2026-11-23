<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionCsv extends Model
{
    protected $table = 'importaciones_csv';

    protected $fillable = ['archivo', 'usuario_id', 'insertadas', 'actualizadas', 'con_error', 'detalle_errores'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
