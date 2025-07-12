<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Prepara los datos para todos los reportes y los muestra en la vista principal.
     */
    public function index()
    {
        // 1. Reporte: Total Recaudado (solo de órdenes finalizadas)
        $totalRecaudado = OrdenTrabajo::where('estado', 'finalizada')->sum('monto_total');

        // 2. Reporte: Conteo de Órdenes por Estado
        $ordenesPorEstado = OrdenTrabajo::select('estado', DB::raw('count(*) as total'))
                                        ->groupBy('estado')
                                        ->pluck('total', 'estado');

        // 3. Reporte: Repuestos más Utilizados
        $repuestosMasUtilizados = DB::table('JC_Detalle_Orden as DO')
            ->join('JC_Repuestos as R', 'DO.repuesto_id', '=', 'R.id')
            ->select('R.nombre', DB::raw('SUM(DO.cantidad) as total_usado'))
            ->groupBy('R.nombre')
            ->orderByDesc('total_usado')
            ->limit(10) // Mostramos el top 10
            ->get();

        // 4. Reporte: Órdenes por Cliente (datos para el formulario de búsqueda)
        $clientes = Cliente::orderBy('nombres')->get();
        
        // La vista buscará los resultados en la sesión si se envió el formulario
        $ordenesCliente = session('ordenes_cliente');
        
        // ===== CORRECCIÓN AQUÍ =====
        // Se cambia el nombre de la vista a 'Reportes.reporte' para que coincida
        // con tu archivo en resources/views/Reportes/reporte.blade.php
        return view('Reportes.reporte', compact(
            'totalRecaudado',
            'ordenesPorEstado',
            'repuestosMasUtilizados',
            'clientes',
            'ordenesCliente'
        ));
    }

    /**
     * Procesa la búsqueda de órdenes por cliente y redirige de vuelta
     * a la página de reportes con los resultados.
     */
    public function ordenesPorCliente(Request $request)
    {
        $request->validate(['cliente_id' => 'required|exists:JC_Clientes,id']);

        $ordenes = OrdenTrabajo::with('vehiculo')
            ->where('cliente_id', $request->cliente_id)
            ->orderBy('fecha_hora', 'desc')
            ->get();
            
        // Guardamos los resultados en la sesión para mostrarlos en la misma página de reportes
        return redirect()->route('reportes.index')->with('ordenes_cliente', $ordenes);
    }
}