@extends('welcome')

@section('title', 'Gestión de Órdenes de Trabajo')

@section('content')
    {{-- (Alertas de success/error - sin cambios) --}}
    @if (session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
    @if (session('error')) <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestión de Órdenes de Trabajo</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrdenModal">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> Crear Nueva Orden
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado de Órdenes</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th># Orden</th><th>Fecha y Hora</th><th>Cliente</th><th>Vehículo</th><th>Estado</th><th>Monto Total</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ordenes as $orden)
                            <tr>
                                <th>{{ $orden->id }}</th>
                                <td>{{ $orden->fecha_hora->format('d/m/Y H:i') }}</td>
                                <td>{{ $orden->cliente->nombres ?? 'N/A' }} {{ $orden->cliente->apellidos ?? '' }}</td>
                                <td>{{ $orden->vehiculo->marca ?? 'N/A' }} {{ $orden->vehiculo->modelo ?? '' }} ({{ $orden->vehiculo->patente ?? 'N/A' }})</td>
                                <td>
                                    @if($orden->estado == 'pendiente') <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($orden->estado == 'en proceso') <span class="badge bg-info text-dark">En Proceso</span>
                                    @else <span class="badge bg-success">Finalizada</span>
                                    @endif
                                </td>
                                <td>${{ number_format($orden->monto_total, 0, ',', '.') }}</td>
                                <td>
                                    {{-- El botón "Gestionar" ahora abre un modal --}}
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#manageOrdenModal" data-orden="{{ json_encode($orden) }}" title="Gestionar Orden"><i class="bi bi-gear-fill"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrdenModal" data-orden-id="{{ $orden->id }}" data-orden-nombre="Orden #{{ $orden->id }}" title="Eliminar Orden"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No hay órdenes de trabajo registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($ordenes->hasPages())<div class="d-flex justify-content-center mt-3">{{ $ordenes->links() }}</div>@endif
        </div>
    </div>

    {{-- ================================= MODALES ================================== --}}

    <!-- Modal: CREAR Orden -->
    {{-- (Este modal se queda igual) --}}
    <div class="modal fade" id="createOrdenModal" tabindex="-1" aria-labelledby="createOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Crear Nueva Orden</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><form action="{{ route('ordenes.store') }}" method="POST"><div class="modal-body">@csrf @if ($errors->create->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->create->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<div class="mb-3"><label for="create_cliente_id" class="form-label">1. Cliente</label><select class="form-select" id="create_cliente_id" name="cliente_id" required><option value="" selected disabled>-- Clientes --</option>@foreach ($clientes as $cliente)<option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombres }} {{ $cliente->apellidos }}</option>@endforeach</select></div><div class="mb-3"><label for="create_vehiculo_id" class="form-label">2. Vehículo del Cliente</label><select class="form-select" id="create_vehiculo_id" name="vehiculo_id" required disabled><option value="" selected disabled>-- Primero seleccione un cliente --</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Crear Orden</button></div></form></div></div>
    </div>
    
    <!-- Modal: GESTIONAR Orden -->
    <div class="modal fade" id="manageOrdenModal" tabindex="-1" aria-labelledby="manageOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl"> {{-- Modal extra grande --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageOrdenModalLabel">Gestionar Orden #<span id="manage_orden_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 border-end">
                            <h6>Información General</h6>
                            <p class="mb-1"><strong>Cliente:</strong> <span id="manage_cliente_nombre"></span></p>
                            <p><strong>Vehículo:</strong> <span id="manage_vehiculo_info"></span></p>
                            <h6>Estado</h6>
                            <form id="manage_estado_form" method="POST">@csrf<div class="input-group mb-3"><select name="estado" id="manage_estado_select" class="form-select"><option value="pendiente">Pendiente</option><option value="en proceso">En Proceso</option><option value="finalizada">Finalizada</option></select><button type="submit" class="btn btn-success">Actualizar</button></div></form>
                            <hr>
                            <h6>Agregar Repuesto</h6>
                            <form id="manage_repuesto_form" method="POST">@csrf<div class="mb-2"><label for="manage_repuesto_id" class="form-label">Repuesto</label><select name="repuesto_id" id="manage_repuesto_id" class="form-select" required><option value="" disabled selected>Seleccione...</option>@foreach($repuestos as $repuesto)<option value="{{$repuesto->id}}">{{$repuesto->nombre}} (Stock: {{$repuesto->stock}})</option>@endforeach</select></div><div class="mb-2"><label for="manage_cantidad" class="form-label">Cantidad</label><input type="number" name="cantidad" id="manage_cantidad" class="form-control" value="1" min="1" required></div><button type="submit" class="btn btn-primary w-100">Agregar</button></form>
                        </div>
                        <div class="col-lg-8">
                            <h6>Repuestos en la Orden</h6>
                            <p class="text-end mb-1"><strong>Monto Total: <span id="manage_monto_total" class="h5"></span></strong></p>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Repuesto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead>
                                    <tbody id="manage_repuestos_tbody">
                                        {{-- El contenido se llenará con JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: ELIMINAR Orden -->
    {{-- (Este modal se queda igual) --}}
    <div class="modal fade" id="deleteOrdenModal" tabindex="-1" aria-labelledby="deleteOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title">Confirmar Eliminación</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><form id="deleteOrdenForm" method="POST">@csrf @method('DELETE')<div class="modal-body"><p>¿Estás seguro de que deseas eliminar la <strong id="delete_orden_nombre"></strong>?</p><p class="text-danger small">Esta acción no se puede deshacer.</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Sí, eliminar</button></div></form></div></div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- Lógica para el modal de CREACIÓN (dropdowns dinámicos) ----
    const createClienteSelect = document.getElementById('create_cliente_id');
    const createVehiculoSelect = document.getElementById('create_vehiculo_id');
    const todosLosVehiculos = @json($todosLosVehiculos ?? []);
    createClienteSelect.addEventListener('change', function() { /* ... (código sin cambios) ... */
        const clienteId = this.value; createVehiculoSelect.innerHTML = '<option value="" disabled selected>Cargando...</option>'; createVehiculoSelect.disabled = true; if (clienteId) { const vehiculosDelCliente = todosLosVehiculos.filter(v => v.cliente_id == clienteId); createVehiculoSelect.innerHTML = ''; if (vehiculosDelCliente.length > 0) { createVehiculoSelect.add(new Option('-- Seleccione un vehículo --', '')); vehiculosDelCliente.forEach(v => createVehiculoSelect.add(new Option(`${v.marca} ${v.modelo} (${v.patente})`, v.id))); createVehiculoSelect.disabled = false; } else { createVehiculoSelect.add(new Option('Este cliente no tiene vehículos', '')); } }
    });
    if (createClienteSelect.value) { createClienteSelect.dispatchEvent(new Event('change')); const oldVehiculoId = "{{ old('vehiculo_id') }}"; if(oldVehiculoId) { setTimeout(() => { createVehiculoSelect.value = oldVehiculoId; }, 100); } }

    // ---- Lógica para el modal de GESTIÓN ----
    const manageModal = document.getElementById('manageOrdenModal');
    manageModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const orden = JSON.parse(button.getAttribute('data-orden'));
        
        // Rellenar información estática
        document.getElementById('manage_orden_id').textContent = orden.id;
        document.getElementById('manage_cliente_nombre').textContent = `${orden.cliente.nombres} ${orden.cliente.apellidos}`;
        document.getElementById('manage_vehiculo_info').textContent = `${orden.vehiculo.marca} ${orden.vehiculo.modelo} (${orden.vehiculo.patente})`;
        document.getElementById('manage_monto_total').textContent = `$${new Intl.NumberFormat('es-CL').format(orden.monto_total)}`;

        // Configurar formularios
        document.getElementById('manage_estado_form').action = `{{ url('ordenes/actualizar-estado') }}/${orden.id}`;
        document.getElementById('manage_repuesto_form').action = `{{ url('ordenes/agregar-repuesto') }}/${orden.id}`;
        document.getElementById('manage_estado_select').value = orden.estado;

        // Rellenar tabla de repuestos
        const tbody = document.getElementById('manage_repuestos_tbody');
        tbody.innerHTML = ''; // Limpiar tabla
        if (orden.detalles.length > 0) {
            orden.detalles.forEach(detalle => {
                const subtotal = detalle.cantidad * detalle.precio_unitario;
                const row = `<tr>
                    <td>${detalle.repuesto.nombre}</td>
                    <td>${detalle.cantidad}</td>
                    <td>$${new Intl.NumberFormat('es-CL').format(detalle.precio_unitario)}</td>
                    <td>$${new Intl.NumberFormat('es-CL').format(subtotal)}</td>
                </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay repuestos en esta orden.</td></tr>';
        }
    });

    // ---- Lógica para el modal de ELIMINACIÓN ----
    const deleteModal = document.getElementById('deleteOrdenModal');
    deleteModal.addEventListener('show.bs.modal', function (event) { /* ... (código sin cambios) ... */
        const button = event.relatedTarget; const ordenId = button.getAttribute('data-orden-id'); const ordenNombre = button.getAttribute('data-orden-nombre'); const form = deleteModal.querySelector('form'); form.action = `{{ url('ordenes') }}/${ordenId}`; deleteModal.querySelector('#delete_orden_nombre').textContent = ordenNombre;
    });

    // ---- Lógica para REABRIR MODALES en caso de error ----
    @if ($errors->create->any())
        new bootstrap.Modal(document.getElementById('createOrdenModal')).show();
    @endif
    @if (session('error_modal_id'))
        // Simula un clic en el botón correcto para abrir el modal de gestión con los datos frescos
        const failedButton = document.querySelector(`button[data-orden*='"id":{{ session('error_modal_id') }}']`);
        if(failedButton) {
            new bootstrap.Modal(document.getElementById('manageOrdenModal')).show(failedButton);
        }
    @endif
});
</script>
@endsection