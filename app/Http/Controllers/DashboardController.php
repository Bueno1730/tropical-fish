<?php

namespace App\Http\Controllers;

use App\Models\LotePeces;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $ventasHoy = Venta::whereDate('fecha', $hoy)->where('estado', 'registrada');
        $stockCritico = DB::table('v_stock_critico')->get();
        $lotesActivos = LotePeces::whereIn('estado', ['activo', 'en_tratamiento']);

        return view('dashboard', [
            'ventasHoyTotal' => (clone $ventasHoy)->sum('total'),
            'ventasHoyCount' => (clone $ventasHoy)->count(),
            'stockCriticoCount' => $stockCritico->count(),
            'stockCriticoTop' => $stockCritico->take(4),
            'lotesActivos' => (clone $lotesActivos)->count(),
            'lotesEnTratamiento' => (clone $lotesActivos)->where('estado', 'en_tratamiento')->count(),
            'productosCount' => Producto::where('estado', 'activo')->count(),
            'categoriasCount' => Producto::where('estado', 'activo')->distinct('categoria_id')->count('categoria_id'),
            'ultimasVentas' => Venta::with('cliente')->where('estado', 'registrada')->latest('fecha')->take(4)->get(),
        ]);
    }
}
