<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ImportacionCsv;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportacionController extends Controller
{
    public function index()
    {
        return view('importaciones.index', [
            'importaciones' => ImportacionCsv::with('usuario')->latest()->take(15)->get(),
        ]);
    }

    public function plantilla()
    {
        $contenido = "codigo,nombre,categoria,proveedor,precio_venta,stock_minimo\n"
            . "ALI-011,Alimento Sera 50g,Alimentos,PRV-0002,38.00,5\n";

        return response($contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ]);
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'archivo.mimes' => 'El archivo debe ser un .csv. Usa la plantilla (6 columnas) y vuelve a intentarlo.',
            'archivo.required' => 'Selecciona un archivo .csv.',
        ]);

        $actualizar = $request->boolean('actualizar');
        $archivo = $request->file('archivo');
        $insertadas = $actualizadas = $conError = 0;
        $errores = [];

        $filas = array_map('str_getcsv', file($archivo->getRealPath()));

        // Saltar encabezado si la primera celda es "codigo"
        if (isset($filas[0][0]) && Str::lower(trim($filas[0][0])) === 'codigo') {
            array_shift($filas);
        }

        $proveedores = Proveedor::pluck('id', 'codigo');
        $categorias = Categoria::pluck('id', 'nombre');

        DB::transaction(function () use ($filas, $actualizar, $proveedores, $categorias, &$insertadas, &$actualizadas, &$conError, &$errores) {
            foreach ($filas as $i => $fila) {
                $nroFila = $i + 2; // +2 por encabezado y base 0

                if (count(array_filter($fila)) === 0) {
                    continue;
                }
                if (count($fila) < 6) {
                    $conError++;
                    $errores[] = "Fila {$nroFila}: se esperaban 6 columnas.";
                    continue;
                }

                [$codigo, $nombre, $categoria, $proveedor, $precio, $minimo] = array_map('trim', $fila);

                if ($codigo === '' || $nombre === '') {
                    $conError++;
                    $errores[] = "Fila {$nroFila}: código y nombre son obligatorios.";
                    continue;
                }
                if (! is_numeric($precio) || $precio < 0 || ! is_numeric($minimo) || $minimo < 0) {
                    $conError++;
                    $errores[] = "Fila {$nroFila}: precio_venta y stock_minimo deben ser números no negativos.";
                    continue;
                }
                if (! isset($proveedores[$proveedor])) {
                    $conError++;
                    $errores[] = "Fila {$nroFila}: el proveedor '{$proveedor}' no existe.";
                    continue;
                }
                if (! isset($categorias[$categoria])) {
                    $conError++;
                    $errores[] = "Fila {$nroFila}: la categoría '{$categoria}' no existe.";
                    continue;
                }

                $existente = Producto::where('codigo', $codigo)->first();

                if ($existente) {
                    if (! $actualizar) {
                        $conError++;
                        $errores[] = "Fila {$nroFila}: el producto '{$codigo}' ya existe (marca 'Actualizar productos existentes').";
                        continue;
                    }
                    $existente->update([
                        'nombre' => $nombre,
                        'categoria_id' => $categorias[$categoria],
                        'proveedor_id' => $proveedores[$proveedor],
                        'precio_venta' => $precio,
                        'stock_minimo' => $minimo,
                    ]);
                    $actualizadas++;
                } else {
                    Producto::create([
                        'codigo' => $codigo,
                        'nombre' => $nombre,
                        'categoria_id' => $categorias[$categoria],
                        'proveedor_id' => $proveedores[$proveedor],
                        'precio_venta' => $precio,
                        'stock_minimo' => $minimo,
                        'stock_actual' => 0,
                        'estado' => 'activo',
                    ]);
                    $insertadas++;
                }
            }

            ImportacionCsv::create([
                'archivo' => $archivo->getClientOriginalName(),
                'usuario_id' => auth()->id(),
                'insertadas' => $insertadas,
                'actualizadas' => $actualizadas,
                'con_error' => $conError,
                'detalle_errores' => $errores ? implode("\n", $errores) : null,
            ]);
        });

        $mensaje = "Importación terminada: {$insertadas} insertadas, {$actualizadas} actualizadas, {$conError} con error.";

        return redirect()->route('importaciones.index')
            ->with($conError ? 'error' : 'exito', $mensaje)
            ->with('errores_importacion', $errores);
    }
}
