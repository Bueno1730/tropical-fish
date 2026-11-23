<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = ['codigo', 'ci', 'nombre', 'telefono', 'estado'];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public static function proximoCodigo(): string
    {
        $max = static::max('id') ?? 0;
        return 'CLI-' . str_pad($max + 1, 5, '0', STR_PAD_LEFT);
    }
}
