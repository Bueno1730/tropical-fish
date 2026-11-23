<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotePeces extends Model
{
    protected $table = 'lotes_peces';

    protected $fillable = [
        'codigo', 'producto_id', 'proveedor_id', 'fecha_recepcion',
        'cantidad_inicial', 'total_retirado', 'ph', 'temperatura',
        'amoniaco', 'nitritos', 'dureza', 'observaciones',
        'precio_compra', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'date',
            'ph' => 'decimal:2',
            'temperatura' => 'decimal:1',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function tratamientos(): HasMany
    {
        return $this->hasMany(Tratamiento::class, 'lote_id');
    }

    public function retiros(): HasMany
    {
        return $this->hasMany(RetiroLote::class, 'lote_id');
    }

    public function disponibles(): int
    {
        return max(0, $this->cantidad_inicial - $this->total_retirado);
    }

    public function puedeEliminarse(): bool
    {
        return ! $this->retiros()->exists() && ! $this->tratamientos()->exists();
    }

    public static function proximoCodigo(): string
    {
        $anio = date('Y');
        $ultimo = static::where('codigo', 'like', "L-{$anio}-%")->max('codigo');
        $nro = $ultimo ? ((int) substr($ultimo, -3)) + 1 : 27; // continúa la numeración del mockup
        return "L-{$anio}-" . str_pad($nro, 3, '0', STR_PAD_LEFT);
    }
}
