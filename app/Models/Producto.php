<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo', 'nombre', 'categoria_id', 'proveedor_id', 'precio_compra',
        'precio_venta', 'stock_actual', 'stock_minimo', 'unidad',
        'control_especial', 'nota_control', 'fecha_vencimiento', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'control_especial' => 'boolean',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(LotePeces::class, 'producto_id');
    }

    public function stockCritico(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function esPez(): bool
    {
        return (bool) $this->categoria?->maneja_lotes;
    }

    public function tieneDependencias(): bool
    {
        return $this->lotes()->exists()
            || DetalleVenta::where('producto_id', $this->id)->exists()
            || DetalleEntrada::where('producto_id', $this->id)->exists();
    }
}
