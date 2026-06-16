@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-page-heading mb-4">
        <div>
            <p class="admin-eyebrow mb-1">Panel de control</p>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="texto-secundario mb-0">Resumen general del refugio Huellitas.</p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['Mascotas disponibles', $mascotasDisponibles, 'bi-check-circle-fill', 'verde'],
            ['Tipos de mascota', $totalTipos, 'bi-tags', 'naranja'],
            ['Solicitudes pendientes', $solicitudesPendientes, 'bi-inbox-fill', 'terracota'],
            ['Adopciones', $totalAdopciones, 'bi-house-heart-fill', 'cafe'],
        ] as [$titulo, $total, $icono, $variante])
            <div class="col-6 col-xl-3">
                <div class="kpi-card kpi-{{ $variante }} h-100">
                    <div class="kpi-icon">
                        <i class="bi {{ $icono }}"></i>
                    </div>
                    <div class="kpi-body">
                        <span class="kpi-label">{{ $titulo }}</span>
                        <span class="kpi-value">{{ $total }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráficos (todos del mismo tamaño) --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h2 class="chart-title"><i class="bi bi-graph-up-arrow me-2"></i>Adopciones por semana</h2>
                    <span class="chart-subtitle">Último mes</span>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="chartAdopciones"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h2 class="chart-title"><i class="bi bi-pie-chart-fill me-2"></i>Mascotas por estado</h2>
                    <span class="chart-subtitle">Distribución actual</span>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="chartEstados"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h2 class="chart-title"><i class="bi bi-bar-chart-fill me-2"></i>Mascotas por tipo</h2>
                    <span class="chart-subtitle">Inventario del refugio</span>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="chartTipos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <h2 class="chart-title"><i class="bi bi-clipboard-data-fill me-2"></i>Solicitudes por estado</h2>
                    <span class="chart-subtitle">Embudo de adopción</span>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="chartSolicitudes"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const paleta = {
                terracota: '#e96732',
                terracotaVivo: '#cf4f24',
                cafe: '#704536',
                cafeSuave: '#9b7668',
                acento: '#f4c9ae',
                verde: '#15803d',
                info: '#b86f3f',
                crema: '#f6e9df',
            };

            Chart.defaults.font.family = "'Open Sans', sans-serif";
            Chart.defaults.color = '#806f68';

            const adopciones = @json($adopcionesPorMes);
            const estados = @json($mascotasPorEstado);
            const tipos = @json($mascotasPorTipo);
            const solicitudes = @json($solicitudesPorEstado);

            // Adopciones por mes (línea)
            const ctxAdop = document.getElementById('chartAdopciones');
            const degradado = ctxAdop.getContext('2d').createLinearGradient(0, 0, 0, 260);
            degradado.addColorStop(0, 'rgba(233, 103, 50, 0.28)');
            degradado.addColorStop(1, 'rgba(233, 103, 50, 0)');
            new Chart(ctxAdop, {
                type: 'line',
                data: {
                    labels: adopciones.labels,
                    datasets: [{
                        label: 'Adopciones',
                        data: adopciones.data,
                        borderColor: paleta.terracota,
                        backgroundColor: degradado,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: paleta.terracotaVivo,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0e7e1' } },
                        x: { grid: { display: false } },
                    },
                },
            });

            // Mascotas por estado (dona)
            new Chart(document.getElementById('chartEstados'), {
                type: 'doughnut',
                data: {
                    labels: estados.labels,
                    datasets: [{
                        data: estados.data,
                        backgroundColor: [paleta.verde, paleta.terracota, paleta.info, paleta.cafeSuave],
                        borderColor: '#fff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: { legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true } } },
                },
            });

            // Mascotas por tipo (barras)
            new Chart(document.getElementById('chartTipos'), {
                type: 'bar',
                data: {
                    labels: tipos.labels,
                    datasets: [{
                        label: 'Mascotas',
                        data: tipos.data,
                        backgroundColor: [
                            paleta.terracota, paleta.cafe, paleta.verde, paleta.info, paleta.cafeSuave, paleta.acento,
                        ],
                        borderRadius: 8,
                        maxBarThickness: 48,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0e7e1' } },
                        x: { grid: { display: false } },
                    },
                },
            });

            // Solicitudes por estado (barras)
            new Chart(document.getElementById('chartSolicitudes'), {
                type: 'bar',
                data: {
                    labels: solicitudes.labels,
                    datasets: [{
                        label: 'Solicitudes',
                        data: solicitudes.data,
                        backgroundColor: [paleta.terracota, paleta.verde, paleta.cafeSuave, paleta.cafe],
                        borderRadius: 8,
                        maxBarThickness: 48,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0e7e1' } },
                        x: { grid: { display: false } },
                    },
                },
            });
        })();
    </script>
@endpush
