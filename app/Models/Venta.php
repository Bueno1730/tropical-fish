<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'codigo', 'fecha', 'usuario_id', 'cliente_id',
        'forma_pago', 'canal', 'unidades', 'total', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'total' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function estaRegistrada(): bool
    {
        return $this->estado === 'registrada';
    }

    public static function proximoCodigo(): string
    {
        $max = static::max('id') ?? 0;
        return 'VEN-' . str_pad($max + 139, 5, '0', STR_PAD_LEFT);
    }
}
