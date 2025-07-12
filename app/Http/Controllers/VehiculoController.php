<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Muestra una lista de los vehículos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $vehiculos = Vehiculo::with('cliente')->orderBy('id', 'desc')->paginate(10);
        

        $clientes = Cliente::orderBy('nombres')->get();

        return view('Vehiculo.vehiculo', compact('vehiculos', 'clientes'));
    }

    /**
     * Almacena un nuevo vehículo en la base de datos usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
{
    $tiposPermitidos = ['sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep'];

    $validator = Validator::make(
        $request->all(),
        [
            'patente' => [
                'required', 'string', 'max:10',
                'regex:/^[ABCDFGHJKLMNPQRSTVWXYZ]{4}[-.]?[0-9]{2}$|^[A-Z]{2}[-.]?[0-9]{4}$/i',
                Rule::unique('JC_Vehiculos', 'patente')
            ],
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'año' => 'required|integer|digits:4|min:1950|max:' . (date('Y') + 1),
            'tipo' => ['required', Rule::in($tiposPermitidos)],
            'cliente_id' => 'required|integer|exists:JC_Clientes,id',
        ],
        [
            'patente.regex' => 'La patente debe tener el formato válido: ABCD-12 o AB-1234.',
            'patente.required' => 'La patente es obligatoria.',
            'patente.unique' => 'Esta patente ya está registrada.',
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'año.required' => 'El año es obligatorio.',
            'tipo.required' => 'El tipo de vehículo es obligatorio.',
            'cliente_id.required' => 'Debe seleccionar un propietario.',
        ]
    );

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'create')
            ->withInput();
    }

    try {
        DB::statement(
            'CALL JC_InsertarVehiculo(?, ?, ?, ?, ?, ?)',
            [
                $request->input('patente'),
                $request->input('marca'),
                $request->input('modelo'),
                $request->input('año'),
                $request->input('tipo'),
                $request->input('cliente_id')
            ]
        );

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo creado exitosamente.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error al crear el vehículo: ' . $e->getMessage())
            ->withInput();
    }
}



    /**
     * Actualiza un vehículo existente usando el procedimiento almacenado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $tiposPermitidos = ['sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep'];
        
        $validator = Validator::make($request->all(), [
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'año' => 'required|integer|digits:4|min:1950|max:' . (date('Y') + 1),
            'tipo' => ['required', Rule::in($tiposPermitidos)],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'edit')
                ->withInput()
                ->with('error_edit_vehiculo_id', $id);
        }

        try {
            DB::statement(
                'CALL JC_ActualizarVehiculo(?, ?, ?, ?, ?)',
                [
                    $id,
                    $request->input('marca'),
                    $request->input('modelo'),
                    $request->input('año'),
                    $request->input('tipo')
                ]
            );

            return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina un vehículo de la base de datos usando el procedimiento almacenado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::statement('CALL JC_EliminarVehiculo(?)', [$id]);
            return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente.');
        
        } catch (\Exception $e) {
            return redirect()->route('vehiculos.index')->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }
}