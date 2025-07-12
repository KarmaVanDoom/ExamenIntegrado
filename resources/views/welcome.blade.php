<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Taller Mecánico')</title>

    <!-- Bootstrap CSS y Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    

    @yield('styles')
</head>
<body class="bg-light">

    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-tools me-2"></i> Taller Mecánico Rapido y Furioso
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- Enlace a Clientes --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                            <i class="bi bi-people-fill me-1"></i> Clientes
                        </a>
                    </li>
                    
                    {{-- Enlace a Vehículos --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" href="{{ route('vehiculos.index') }}">
                            <i class="bi bi-car-front-fill me-1"></i> Vehículos
                        </a>
                    </li>

                    {{-- Enlace a Repuestos --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('repuestos.*') ? 'active' : '' }}" href="{{ route('repuestos.index') }}">
                            <i class="bi bi-box-seam me-1"></i> Repuestos
                        </a>
                    </li>
                    
                    {{-- Enlace a Órdenes de Trabajo --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}" href="{{ route('ordenes.index') }}">
                            <i class="bi bi-file-earmark-text me-1"></i> Órdenes
                        </a>
                    </li>

                    {{-- Enlace a Reportes --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-bar-graph-fill me-1"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <main>@yield('content')</main>
    </div>

    <!-- SCRIPTS -->
    {{-- He añadido jQuery aquí, ya que es necesario para DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts') 
</body>
</html>