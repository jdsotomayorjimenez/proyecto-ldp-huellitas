@extends(auth()->user()->rol->nombre === 'Administrador' ? 'layouts.admin' : 'layouts.public')

@section('title', 'Adopción · Acta '.$adopcion->numero_acta)

@section('content')
    @php
        $mascota = $solicitud->mascota;
        $fecha = \Illuminate\Support\Carbon::parse($adopcion->fecha_adopcion)
            ->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    @endphp

    <style>
        .text-principal { color: var(--color-principal); }
    </style>

    <div class="mx-auto d-print-none mb-4" style="max-width: 820px;">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm border">
            @if (auth()->user()->rol->nombre === 'Administrador')
                <a class="text-decoration-none fw-bold text-secondary d-flex align-items-center" href="{{ route('admin.adopciones.index') }}">
                    <i class="bi bi-arrow-left-circle-fill fs-5 me-2 text-principal"></i> 
                    <span>Volver a adopciones</span>
                </a>
            @else
                <a class="text-decoration-none fw-bold text-secondary d-flex align-items-center" href="{{ route('solicitudes.mis') }}">
                    <i class="bi bi-arrow-left-circle-fill fs-5 me-2 text-principal"></i> 
                    <span>Volver a mis solicitudes</span>
                </a>
            @endif
            
            <button class="btn btn-huellitas px-4 fw-bold rounded-pill shadow-sm d-flex align-items-center" type="button" onclick="window.print()">
                <i class="bi bi-printer-fill me-2"></i> Imprimir certificado
            </button>
        </div>
    </div>

    <div class="comprobante-hoja mx-auto">
        <div class="cert-inner">
            <div class="cert-header">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('img/logo-huellitas-sf.png') }}" alt="Huellitas" width="48" height="48">
                    <div>
                        <div class="cert-marca">Huellitas</div>
                        <div class="cert-sub">Refugio de adopción de mascotas</div>
                    </div>
                </div>
                <div class="cert-acta">
                    Acta N.º
                    <strong>{{ $adopcion->numero_acta }}</strong>
                </div>
            </div>

            <div class="cert-cuerpo">
                <div class="text-center">
                    <span class="cert-sello"><i class="bi bi-patch-check-fill"></i> ADOPTADO</span>
                    <h1 class="cert-title">Certificado de Adopción</h1>
                    <p class="cert-narrativa">
                        El refugio <strong>Huellitas</strong> certifica que
                        <strong>{{ $mascota->nombre }}</strong>
                        ({{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }})
                        fue adoptado(a) de forma responsable por
                        <strong>{{ $solicitud->usuario->name }}</strong>
                        el <strong>{{ $fecha }}</strong>, encontrando así un hogar lleno de amor y cuidados.
                    </p>
                </div>

                <div class="cert-datos">
                    <div><span>Mascota</span>{{ $mascota->nombre }}</div>
                    <div><span>Tipo y raza</span>{{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }}</div>
                    <div><span>Adoptante</span>{{ $solicitud->usuario->name }}</div>
                    <div><span>Correo</span>{{ $solicitud->usuario->email }}</div>
                    <div><span>Fecha de adopción</span>{{ $fecha }}</div>
                    <div><span>Número de acta</span>{{ $adopcion->numero_acta }}</div>
                </div>

                @if ($adopcion->observaciones)
                    <div class="cert-obs">
                        <span>Observaciones</span>
                        <p class="mb-0">{{ $adopcion->observaciones }}</p>
                    </div>
                @endif
            </div>

            <div class="cert-firmas">
                <div class="text-center">
                    <div class="signature-font mb-0" style="font-family: 'Dancing Script', cursive; font-size: 1.8rem; color: #2c3e50;">
                        Juan Administrador
                    </div>
                    <hr class="mt-0 mb-1" style="width: 200px; margin: 0 auto;">
                    <div class="small text-uppercase fw-bold">Juan - Director del Refugio</div>
                    <div class="small text-muted">Refugio Huellitas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cargar fuente para la firma --}}
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
@endsection
