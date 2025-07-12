@extends('welcome')

@section('title', 'Gestión de Repuestos')

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
        <h1 class="mb-0">Gestión de Repuestos</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRepuestoModal">
            <i class="bi bi-box-seam me-1"></i> Agregar Nuevo Repuesto
        </button>
    </div>

    {{-- Tabla de Repuestos --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado de Repuestos</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Categoría</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($repuestos as $repuesto)
                            <tr>
                                <th scope="row">{{ $repuesto->id }}</th>
                                <td>{{ $repuesto->nombre }}</td>
                                <td>{{ $repuesto->categoria ?? 'N/A' }}</td>
                                <td>${{ number_format($repuesto->precio, 0, ',', '.') }}</td>
                                <td>{{ $repuesto->stock }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRepuestoModal" data-repuesto="{{ json_encode($repuesto) }}" title="Editar"><i class="bi bi-pencil-square"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRepuestoModal" data-repuesto-id="{{ $repuesto->id }}" data-repuesto-nombre="{{ $repuesto->nombre }}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay repuestos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($repuestos->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $repuestos->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================================= MODALES ================================== --}}

    <!-- Modal 1: CREAR Repuesto -->
    <div class="modal fade" id="createRepuestoModal" tabindex="-1" aria-labelledby="createRepuestoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="createRepuestoModalLabel">Agregar Nuevo Repuesto</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <form action="{{ route('repuestos.store') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        @if ($errors->create->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->create->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                        
                        <div class="mb-3"><label for="nombre" class="form-label">Nombre del Repuesto</label><input type="text" class="form-control @error('nombre', 'create') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required></div>
                        <div class="mb-3"><label for="categoria" class="form-label">Categoría (Opcional)</label><input type="text" class="form-control @error('categoria', 'create') is-invalid @enderror" id="categoria" name="categoria" value="{{ old('categoria') }}"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="precio" class="form-label">Precio</label><input type="number" class="form-control @error('precio', 'create') is-invalid @enderror" id="precio" name="precio" value="{{ old('precio') }}" min="0" step="0.01" required></div>
                            <div class="col-md-6 mb-3"><label for="stock" class="form-label">Stock Inicial</label><input type="number" class="form-control @error('stock', 'create') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" min="0" step="1" required></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar Repuesto</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: EDITAR Repuesto -->
    <div class="modal fade" id="editRepuestoModal" tabindex="-1" aria-labelledby="editRepuestoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="editRepuestoModalLabel">Editar Repuesto</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <form id="editRepuestoForm" method="POST" action="">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        @if ($errors->edit->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->edit->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                        
                        <div class="mb-3"><label for="edit_nombre" class="form-label">Nombre del Repuesto</label><input type="text" class="form-control @error('nombre', 'edit') is-invalid @enderror" id="edit_nombre" name="nombre" required></div>
                        <div class="mb-3"><label for="edit_categoria" class="form-label">Categoría (Opcional)</label><input type="text" class="form-control @error('categoria', 'edit') is-invalid @enderror" id="edit_categoria" name="categoria"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="edit_precio" class="form-label">Precio</label><input type="number" class="form-control @error('precio', 'edit') is-invalid @enderror" id="edit_precio" name="precio" min="0" step="0.01" required></div>
                            <div class="col-md-6 mb-3"><label for="edit_stock" class="form-label">Stock</label><input type="number" class="form-control @error('stock', 'edit') is-invalid @enderror" id="edit_stock" name="stock" min="0" step="1" required></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar Cambios</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: CONFIRMAR ELIMINACIÓN de Repuesto -->
    <div class="modal fade" id="deleteRepuestoModal" tabindex="-1" aria-labelledby="deleteRepuestoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white"><h5 class="modal-title" id="deleteRepuestoModalLabel">Confirmar Eliminación</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <form id="deleteRepuestoForm" method="POST" action="">
                    @csrf @method('DELETE')
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el repuesto <strong id="delete_repuesto_nombre"></strong>?</p>
                        <p class="text-danger small"><strong>Advertencia:</strong> Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Sí, eliminar</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any())
        @if ($errors->hasBag('create'))
            new bootstrap.Modal(document.getElementById('createRepuestoModal')).show();
        @endif
        @if ($errors->hasBag('edit') && session('error_edit_repuesto_id'))
            const editModalEl = document.getElementById('editRepuestoModal');
            if (editModalEl) {
                const form = editModalEl.querySelector('form');
                const repuestoId = "{{ session('error_edit_repuesto_id') }}";
                form.action = `{{ url('repuestos') }}/${repuestoId}`;
                new bootstrap.Modal(editModalEl).show();
            }
        @endif
    @endif

    const editModal = document.getElementById('editRepuestoModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const repuesto = JSON.parse(button.getAttribute('data-repuesto'));
            const form = editModal.querySelector('form');
            form.action = `{{ url('repuestos') }}/${repuesto.id}`;
            form.querySelector('#edit_nombre').value = repuesto.nombre;
            form.querySelector('#edit_categoria').value = repuesto.categoria || '';
            form.querySelector('#edit_precio').value = repuesto.precio;
            form.querySelector('#edit_stock').value = repuesto.stock;
        });
    }

    const deleteModal = document.getElementById('deleteRepuestoModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const repuestoId = button.getAttribute('data-repuesto-id');
            const repuestoNombre = button.getAttribute('data-repuesto-nombre');
            const form = deleteModal.querySelector('form');
            form.action = `{{ url('repuestos') }}/${repuestoId}`;
            deleteModal.querySelector('#delete_repuesto_nombre').textContent = repuestoNombre;
        });
    }
});
</script>
@endsection