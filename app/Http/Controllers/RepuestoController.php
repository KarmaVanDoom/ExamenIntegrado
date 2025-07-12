<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RepuestoController extends Controller
{
    /**
     * Muestra una lista de los repuestos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtenemos los repuestos de forma paginada
        $repuestos = Repuesto::orderBy('id', 'desc')->paginate(10);

        // Asegúrate de que el nombre de la vista coincida con tu archivo.
        // Si tu archivo está en /Repuesto/repuesto.blade.php, el nombre es 'Repuesto.repuesto'.
        // Si está en /repuestos/index.blade.php, el nombre es 'repuestos.index'.
        return view('Repuesto.repuesto', compact('repuestos'));
    }

    /**
     * Almacena un nuevo repuesto en la base de datos usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('JC_Repuestos', 'nombre')],
            'categoria' => 'nullable|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'create')
                ->withInput();
        }

        try {
            DB::statement(
                'CALL JC_InsertarRepuesto(?, ?, ?, ?)',
                [
                    $request->input('nombre'),
                    $request->input('categoria'),
                    $request->input('precio'),
                    $request->input('stock')
                ]
            );

            return redirect()->route('repuestos.index')->with('success', 'Repuesto creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el repuesto: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Actualiza un repuesto existente usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // El nombre debe ser único, pero ignorando el registro actual
            'nombre' => ['required', 'string', 'max:100', Rule::unique('JC_Repuestos', 'nombre')->ignore($id)],
            'categoria' => 'nullable|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'edit')
                ->withInput()
                ->with('error_edit_repuesto_id', $id); // Para reabrir el modal correcto
        }

        try {
            DB::statement(
                'CALL JC_ActualizarRepuesto(?, ?, ?, ?, ?)',
                [
                    $id,
                    $request->input('nombre'),
                    $request->input('categoria'),
                    $request->input('precio'),
                    $request->input('stock')
                ]
            );

            return redirect()->route('repuestos.index')->with('success', 'Repuesto actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el repuesto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina un repuesto de la base de datos usando el procedimiento almacenado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::statement('CALL JC_EliminarRepuesto(?)', [$id]);
            return redirect()->route('repuestos.index')->with('success', 'Repuesto eliminado exitosamente.');
        
        } catch (\Exception $e) {
            // Este error puede saltar si el repuesto está asociado a una orden y no hay ON DELETE SET NULL, etc.
            return redirect()->route('repuestos.index')->with('error', 'Error al eliminar el repuesto. Es posible que esté en uso en una orden de trabajo.');
        }
    }
}