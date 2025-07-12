@extends('welcome')

@section('title', 'Gestión de Vehículos')

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Vehículos</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVehiculoModal">
        <i class="bi bi-car-front-fill me-1"></i> Agregar Nuevo Vehículo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Listado de Vehículos</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Patente</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Tipo</th>
                        <th>Propietario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $vehiculo)
                        <tr>
                            <td>{{ $vehiculo->id }}</td>
                            <td>{{ $vehiculo->patente }}</td>
                            <td>{{ $vehiculo->marca }}</td>
                            <td>{{ $vehiculo->modelo }}</td>
                            <td>{{ $vehiculo->año }}</td>
                            <td>{{ Str::ucfirst($vehiculo->tipo) }}</td>
                            <td>{{ $vehiculo->cliente->nombres ?? 'N/A' }} {{ $vehiculo->cliente->apellidos ?? '' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVehiculoModal" onclick='loadEditData(@json($vehiculo))'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('vehiculos.destroy', $vehiculo->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirma que deseas eliminar este vehículo.')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay vehículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($vehiculos->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $vehiculos->links() }}
            </div>
        @endif
    </div>
</div>

@php $tiposVehiculo = ['sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep']; @endphp

{{-- Modal CREAR --}}
<div class="modal fade" id="createVehiculoModal" tabindex="-1" aria-labelledby="createVehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($errors->create->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->create->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Propietario</label>
                        <select class="form-select" name="cliente_id" required>
                            <option value="" disabled selected>Seleccione un cliente...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombres }} {{ $cliente->apellidos }} ({{ $cliente->run }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patente</label>
                        <input type="text" class="form-control" name="patente" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="año" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" required>
                                <option value="" disabled selected>Seleccione un tipo...</option>
                                @foreach ($tiposVehiculo as $tipo)
                                    <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal EDITAR --}}
<div class="modal fade" id="editVehiculoModal" tabindex="-1" aria-labelledby="editVehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editVehiculoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Propietario</label>
                        <input type="text" class="form-control" id="edit_propietario" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patente</label>
                        <input type="text" class="form-control" id="edit_patente" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" id="edit_marca" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" id="edit_modelo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="año" id="edit_año" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" id="edit_tipo" required>
                                @foreach ($tiposVehiculo as $tipo)
                                    <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function loadEditData(data) {
    const form = document.getElementById('editVehiculoForm');
    form.action = `/vehiculos/${data.id}`;
    document.getElementById('edit_propietario').value = data.cliente?.nombres + ' ' + data.cliente?.apellidos;
    document.getElementById('edit_patente').value = data.patente;
    document.getElementById('edit_marca').value = data.marca;
    document.getElementById('edit_modelo').value = data.modelo;
    document.getElementById('edit_año').value = data.año;
    document.getElementById('edit_tipo').value = data.tipo;
}
</script>
@endsection