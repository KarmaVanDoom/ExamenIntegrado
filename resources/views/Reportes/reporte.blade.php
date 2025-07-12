@extends('welcome')

@section('title', 'Reportes del Taller')

{{-- Añadimos los estilos de DataTables en el head --}}
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <h1 class="mb-4">Emisión de Reportes</h1>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Recaudado</h5>
                    <p class="card-text fs-4">${{ number_format($totalRecaudado, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-dark bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Órdenes Pendientes</h5>
                    <p class="card-text fs-4">{{ $ordenesPorEstado['pendiente'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Órdenes en Proceso</h5>
                    <p class="card-text fs-4">{{ $ordenesPorEstado['en proceso'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes en Tablas -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Repuestos más Utilizados</div>
                <div class="card-body">
                    <table id="repuestos-datatable" class="table table-striped" style="width:100%">
                        <thead><tr><th>Repuesto</th><th>Cantidad Usada</th></tr></thead>
                        <tbody>
                            @foreach ($repuestosMasUtilizados as $repuesto)
                            <tr><td>{{ $repuesto->nombre }}</td><td>{{ $repuesto->total_usado }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Órdenes de Trabajo por Cliente</div>
                <div class="card-body">
                    <form action="{{ route('reportes.ordenesPorCliente') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <select name="cliente_id" class="form-select">
                                <option disabled selected>Seleccione un cliente...</option>
                                @foreach($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->nombres}} {{$cliente->apellidos}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                    
                    @if(isset($ordenesCliente))
                    <table id="ordenes-cliente-datatable" class="table table-striped" style="width:100%">
                        <thead><tr><th># Orden</th><th>Vehículo</th><th>Fecha</th><th>Estado</th></tr></thead>
                        <tbody>
                            @foreach ($ordenesCliente as $orden)
                            <tr><td>{{$orden->id}}</td><td>{{$orden->vehiculo->patente ?? 'N/A'}}</td><td>{{$orden->fecha_hora->format('d/m/Y')}}</td><td>{{ ucfirst($orden->estado) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{-- Scripts para DataTables (jQuery ya está en welcome.blade.php) --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Objeto de configuración para el idioma español de DataTables
        const languageConfig = {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        };

        // Inicializar la tabla de repuestos con el idioma español
        $('#repuestos-datatable').DataTable({
            language: languageConfig
        });

        // Inicializar la tabla de órdenes por cliente con el idioma español
        $('#ordenes-cliente-datatable').DataTable({
            language: languageConfig
        });
    });
</script>
@endsection