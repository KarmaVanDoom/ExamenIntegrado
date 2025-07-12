<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrdenTrabajoController extends Controller
{
    public function index()
    {
        // Cargar las órdenes con sus relaciones para optimizar la vista
        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo', 'detalles.repuesto'])
                                 ->orderBy('id', 'desc')
                                 ->paginate(10);

        $clientes = Cliente::orderBy('nombres')->get();
        $todosLosVehiculos = Vehiculo::all();
        $repuestos = Repuesto::where('stock', '>', 0)->orderBy('nombre')->get();

        // Apuntamos a la vista principal
        return view('Ordentrabajo.ordentrabajo', compact('ordenes', 'clientes', 'todosLosVehiculos', 'repuestos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|integer|exists:JC_Clientes,id',
            'vehiculo_id' => 'required|integer|exists:JC_Vehiculos,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'create')->withInput();
        }

        try {
            DB::select('CALL JC_CrearOrdenTrabajo(?, ?)', [$request->cliente_id, $request->vehiculo_id]);
            
            // Redirigimos de vuelta al listado
            return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo creada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la orden: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // No tienes un SP para eliminar, así que lo hacemos con Eloquent
            $orden = OrdenTrabajo::findOrFail($id);
            $orden->delete();
            return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo eliminada exitosamente.');
        
        } catch (\Exception $e) {
            return redirect()->route('ordenes.index')->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }
     
    public function agregarRepuesto(Request $request, $orden_id)
    {
        $validator = Validator::make($request->all(), [
            'repuesto_id' => 'required|integer|exists:JC_Repuestos,id',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            // Añadimos el ID de la orden para saber qué modal reabrir
            return redirect()->back()->withErrors($validator)->with('error_modal_id', $orden_id);
        }
        
        try {
            DB::statement('CALL JC_AgregarDetalleOrden(?, ?, ?)', [$orden_id, $request->repuesto_id, $request->cantidad]);
            return redirect()->route('ordenes.index')->with('success', 'Repuesto agregado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ordenes.index')->with('error', 'Error al agregar repuesto: ' . $e->getMessage());
        }
    }
     
    public function actualizarEstado(Request $request, $orden_id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => ['required', Rule::in(['pendiente', 'en proceso', 'finalizada'])]
        ]);
        
        if ($validator->fails()) {
             // Añadimos el ID de la orden para saber qué modal reabrir
            return redirect()->back()->withErrors($validator)->with('error_modal_id', $orden_id);
        }
        
        try {
            DB::statement('CALL JC_ActualizarEstadoOrden(?, ?)', [$orden_id, $request->estado]);
            return redirect()->route('ordenes.index')->with('success', 'Estado de la orden actualizado.');
        } catch (\Exception $e) {
            return redirect()->route('ordenes.index')->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }
}