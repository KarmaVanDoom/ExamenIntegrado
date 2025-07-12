@extends('welcome')

@section('title', 'Gestión de Clientes')

@section('content')

    {{-- Bloque para mostrar mensajes de éxito o error --}}
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

    {{-- Encabezado y Botón para abrir el Modal de Creación --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestión de Clientes</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClienteModal">
            <i class="bi bi-plus-circle me-1"></i> Agregar Nuevo Cliente
        </button>
    </div>

    {{-- Tabla de Clientes --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado de Clientes</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">RUN</th>
                            <th scope="col">Nombre Completo</th>
                            <th scope="col">Correo Electrónico</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                            <tr>
                                <th scope="row">{{ $cliente->id }}</th>
                                <td>{{ $cliente->run }}</td>
                                <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
                                <td>{{ $cliente->correo }}</td>
                                <td>{{ $cliente->telefono }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editClienteModal" data-cliente="{{ json_encode($cliente) }}" title="Editar"><i class="bi bi-pencil-square"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteClienteModal" data-cliente-id="{{ $cliente->id }}" data-cliente-nombre="{{ $cliente->nombres }} {{ $cliente->apellidos }}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay clientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($clientes->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================================= MODALES ================================== --}}

    <!-- Modal 1: CREAR Cliente -->
    <div class="modal fade" id="createClienteModal" tabindex="-1" aria-labelledby="createClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="createClienteModalLabel">Agregar Nuevo Cliente</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><form action="{{ route('clientes.store') }}" method="POST"><div class="modal-body">@csrf @if ($errors->create->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->create->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<div class="mb-3"><label for="run" class="form-label">RUN</label><input type="text" class="form-control @error('run', 'create') is-invalid @enderror" id="run" name="run" value="{{ old('run') }}" placeholder="Ej: 12.345.678-9" required></div><div class="row"><div class="col-md-6 mb-3"><label for="nombres" class="form-label">Nombres</label><input type="text" class="form-control @error('nombres', 'create') is-invalid @enderror" id="nombres" name="nombres" value="{{ old('nombres') }}" required></div><div class="col-md-6 mb-3"><label for="apellidos" class="form-label">Apellidos</label><input type="text" class="form-control @error('apellidos', 'create') is-invalid @enderror" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required></div></div><div class="mb-3"><label for="telefono" class="form-label">Teléfono</label><input type="text" class="form-control @error('telefono', 'create') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}" required></div><div class="mb-3"><label for="direccion" class="form-label">Dirección (Opcional)</label><input type="text" class="form-control @error('direccion', 'create') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion') }}"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar Cliente</button></div></form></div></div>
    </div>

    <!-- Modal 2: EDITAR Cliente -->
    <div class="modal fade" id="editClienteModal" tabindex="-1" aria-labelledby="editClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="editClienteModalLabel">Editar Cliente</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><form id="editClienteForm" method="POST" action="">@csrf @method('PUT')<div class="modal-body">@if ($errors->edit->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->edit->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<div class="mb-3"><label for="edit_run" class="form-label">RUN (No editable)</label><input type="text" class="form-control" id="edit_run" name="run" readonly></div><div class="row"><div class="col-md-6 mb-3"><label for="edit_nombres" class="form-label">Nombres</label><input type="text" class="form-control @error('nombres', 'edit') is-invalid @enderror" id="edit_nombres" name="nombres" value="{{ old('nombres') }}" required></div><div class="col-md-6 mb-3"><label for="edit_apellidos" class="form-label">Apellidos</label><input type="text" class="form-control @error('apellidos', 'edit') is-invalid @enderror" id="edit_apellidos" name="apellidos" value="{{ old('apellidos') }}" required></div></div><div class="mb-3"><label for="edit_telefono" class="form-label">Teléfono</label><input type="text" class="form-control @error('telefono', 'edit') is-invalid @enderror" id="edit_telefono" name="telefono" value="{{ old('telefono') }}" required></div><div class="mb-3"><label for="edit_direccion" class="form-label">Dirección (Opcional)</label><input type="text" class="form-control @error('direccion', 'edit') is-invalid @enderror" id="edit_direccion" name="direccion" value="{{ old('direccion') }}"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar Cambios</button></div></form></div></div>
    </div>

    <!-- Modal 3: CONFIRMAR ELIMINACIÓN de Cliente -->
    <div class="modal fade" id="deleteClienteModal" tabindex="-1" aria-labelledby="deleteClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title" id="deleteClienteModalLabel">Confirmar Eliminación</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><form id="deleteClienteForm" method="POST" action="">@csrf @method('DELETE')<div class="modal-body"><p>¿Estás seguro de que deseas eliminar al cliente <strong id="delete_cliente_nombre"></strong>?</p><p class="text-danger small"><strong>Advertencia:</strong> Esta acción no se puede deshacer.</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Sí, eliminar</button></div></form></div></div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any())
        @if ($errors->hasBag('create'))
            new bootstrap.Modal(document.getElementById('createClienteModal')).show();
        @endif
        @if ($errors->hasBag('edit') && session('error_edit_cliente_id'))
            const editModalEl = document.getElementById('editClienteModal');
            if (editModalEl) {
                const form = editModalEl.querySelector('form');
                const clienteId = "{{ session('error_edit_cliente_id') }}";
                form.action = `{{ url('clientes') }}/${clienteId}`;
                @php $failedCliente = \App\Models\Cliente::find(session('error_edit_cliente_id')); @endphp
                const runInput = editModalEl.querySelector('#edit_run');
                if (runInput) { runInput.value = '{{ $failedCliente ? $failedCliente->run : "" }}'; }
                new bootstrap.Modal(editModalEl).show();
            }
        @endif
    @endif

    const editModal = document.getElementById('editClienteModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const cliente = JSON.parse(button.getAttribute('data-cliente'));
            const form = editModal.querySelector('form');
            form.action = `{{ url('clientes') }}/${cliente.id}`;
            form.querySelector('#edit_run').value = cliente.run;
            form.querySelector('#edit_nombres').value = cliente.nombres;
            form.querySelector('#edit_apellidos').value = cliente.apellidos;
            form.querySelector('#edit_telefono').value = cliente.telefono;
            form.querySelector('#edit_direccion').value = cliente.direccion || '';
        });
    }

    const deleteModal = document.getElementById('deleteClienteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const clienteId = button.getAttribute('data-cliente-id');
            const clienteNombre = button.getAttribute('data-cliente-nombre');
            const form = deleteModal.querySelector('form');
            form.action = `{{ url('clientes') }}/${clienteId}`;
            deleteModal.querySelector('#delete_cliente_nombre').textContent = clienteNombre;
        });
    }
});
</script>
@endsection