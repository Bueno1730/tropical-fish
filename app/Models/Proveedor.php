<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'codigo', 'nit_ci', 'nombre', 'telefono', 'correo', 'direccion', 'tipo', 'estado',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }

    public function entradas(): HasMany
    {
        return $this->hasMany(EntradaMercaderia::class, 'proveedor_id');
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(LotePeces::class, 'proveedor_id');
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function tieneDependencias(): bool
    {
        return $this->productos()->exists()
            || $this->entradas()->exists()
            || $this->lotes()->exists();
    }

    public static function proximoCodigo(): string
    {
        $max = static::max('id') ?? 0;
        return 'PRV-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}
