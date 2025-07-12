{{-- 1. Extendemos la plantilla que ahora es welcome.blade.php --}}
@extends('welcome')

{{-- 2. Definimos el título específico para esta página --}}
@section('title', 'Dashboard')

{{-- 3. Definimos el contenido que se insertará en el @yield('content') --}}
@section('content')
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Panel de Administración</h1>
            <p class="col-md-8 fs-4">Utilice la barra de navegación superior para gestionar las diferentes secciones del sistema.</p>
            <hr class="my-4">
            <p>Aquí puede visualizar resúmenes, estadísticas y accesos directos a las tareas más comunes.</p>
        </div>
    </div>

    <div class="row align-items-md-stretch">
        <div class="col-md-6 mb-4">
            <div class="h-100 p-5 text-white bg-primary rounded-3">
                <h2>Gestionar Clientes</h2>
                <p>Ver, agregar o editar la información de los clientes del taller.</p>
                <a class="btn btn-outline-light" href="{{ url('/clientes') }}">Ir a Clientes</a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="h-100 p-5 bg-secondary text-white rounded-3">
                <h2>Crear Orden de Trabajo</h2>
                <p>Iniciar una nueva orden de trabajo para un vehículo existente.</p>
                <button class="btn btn-outline-light" type="button">Nueva Orden</button>
            </div>
        </div>
    </div>
@endsection