<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntradaMercaderia extends Model
{
    protected $table = 'entradas_mercaderia';

    protected $fillable = [
        'codigo', 'fecha', 'proveedor_id', 'nro_documento',
        'usuario_id', 'items_count', 'total', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'total' => 'decimal:2',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleEntrada::class, 'entrada_id');
    }

    public function estaRegistrada(): bool
    {
        return $this->estado === 'registrada';
    }

    public static function proximoCodigo(): string
    {
        $max = static::max('id') ?? 0;
        return 'ENT-' . str_pad($max + 55, 5, '0', STR_PAD_LEFT);
    }
}
