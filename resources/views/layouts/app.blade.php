<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - @yield('title', 'Super Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('superadmin.dashboard') }}">Super Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.tarifas') }}">Tarifas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.bahias') }}">Bahías</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.reportes') }}">Reportes</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('superadmin.configuracion') }}">Configuración</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>


    <main class="container mb-5">
        @yield('content')
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
