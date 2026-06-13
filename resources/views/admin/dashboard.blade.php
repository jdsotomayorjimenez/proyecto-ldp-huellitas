@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="texto-secundario mb-0">Resumen de la base administrativa de Huellitas.</p>
        </div>
    </div>

    <div class="row g-3">
        @foreach ([
            ['Tipos', $totalTipos, 'admin.tipos-mascotas.index', 'bi-tags'],
            ['Razas', $totalRazas, 'admin.razas.index', 'bi-diagram-3'],
            ['Mascotas', $totalMascotas, 'admin.mascotas.index', 'bi-heart'],
            ['Disponibles', $mascotasDisponibles, 'admin.mascotas.index', 'bi-check-circle'],
            ['Requisitos', $totalRequisitos, 'admin.requisitos.index', 'bi-card-checklist'],
        ] as [$titulo, $total, $ruta, $icono])
            <div class="col-sm-6 col-xl">
                <a class="text-decoration-none" href="{{ route($ruta) }}">
                    <div class="card card-huellitas h-100">
                        <div class="card-body">
                            <i class="bi {{ $icono }} fs-2 text-warning"></i>
                            <p class="texto-secundario mb-1 mt-3">{{ $titulo }}</p>
                            <p class="display-6 fw-bold mb-0">{{ $total }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
