{{-- Extendemos de nuestra plantilla principal (welcome.blade.php) --}}
@extends('welcome')

@section('title', 'Gestión de Vehículos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestión de Vehículos</h1>
        <a href="#" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Agregar Nuevo Vehículo
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado de Vehículos</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Patente</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Año</th>
                            <th scope="col">Dueño (Cliente)</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Aquí usamos el bucle de Blade para iterar sobre los datos del controlador --}}
                        @forelse ($vehiculos as $vehiculo)
                            <tr>
                                <th scope="row">{{ $vehiculo->patente }}</th>
                                <td>{{ $vehiculo->marca }}</td>
                                <td>{{ $vehiculo->modelo }}</td>
                                <td>{{ $vehiculo->año }}</td>
                                {{-- Gracias a la relación, podemos acceder al nombre del cliente fácilmente --}}
                                <td>{{ $vehiculo->cliente->nombres ?? 'Sin asignar' }} {{ $vehiculo->cliente->apellidos ?? '' }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    <a href="#" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay vehículos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection