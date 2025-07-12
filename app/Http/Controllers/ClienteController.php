<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de los clientes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtenemos los clientes de forma paginada
        $clientes = Cliente::orderBy('id', 'desc')->paginate(10);

        // ===== CORRECCIÓN AQUÍ =====
        // Cambiamos 'clientes.index' por 'Cliente.cliente' para que coincida con tu archivo.
        return view('Cliente.cliente', compact('clientes'));
    }

    /**
     * Almacena un nuevo cliente en la base de datos usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // La lógica de validación y almacenamiento no necesita cambios.
        $validator = Validator::make($request->all(), [
            'run' => [
                'required',
                'string',
                'regex:/^[0-9]{1,2}\\.?[0-9]{3}\\.?[0-9]{3}-[0-9Kk]$/',
                Rule::unique('JC_Clientes', 'run')
            ],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:15',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'create')
                ->withInput();
        }

        try {
            DB::statement(
                'CALL JC_InsertarCliente(?, ?, ?, ?, ?)',
                [
                    $request->input('run'),
                    $request->input('nombres'),
                    $request->input('apellidos'),
                    $request->input('telefono'),
                    $request->input('direccion')
                ]
            );

            return redirect()->route('home')->with('success', 'Cliente creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Actualiza un cliente existente usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // La lógica de validación y actualización no necesita cambios.
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:15',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'edit')
                ->withInput()
                ->with('error_edit_cliente_id', $id);
        }

        try {
            DB::statement(
                'CALL JC_ActualizarCliente(?, ?, ?, ?, ?)',
                [
                    $id,
                    $request->input('nombres'),
                    $request->input('apellidos'),
                    $request->input('telefono'),
                    $request->input('direccion')
                ]
            );

            return redirect()->route('home')->with('success', 'Cliente actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina un cliente de la base de datos usando el procedimiento almacenado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // La lógica de eliminación no necesita cambios.
        try {
            DB::statement('CALL JC_EliminarCliente(?)', [$id]);
            return redirect()->route('home')->with('success', 'Cliente eliminado exitosamente.');
        
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}