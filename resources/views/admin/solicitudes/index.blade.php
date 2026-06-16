@extends('layouts.admin')

@section('title', 'Solicitudes')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Solicitudes de adopción</h1>
        <p class="texto-secundario mb-0">Revisa, aprueba o rechaza las solicitudes de los adoptantes.</p>
    </div>

    @php
        $etiquetas = [
            'pendiente' => 'En proceso',
            'aprobada' => 'Aprobadas',
            'rechazada' => 'Rechazadas',
            'cancelada' => 'Canceladas',
        ];
    @endphp
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-sm {{ $estadoActivo ? 'btn-outline-secondary' : 'btn-huellitas' }}"
           href="{{ route('admin.solicitudes.index') }}">Todas</a>
        @foreach ($estados as $estado)
            <a class="btn btn-sm {{ $estadoActivo === $estado ? 'btn-huellitas' : 'btn-outline-secondary' }}"
               href="{{ route('admin.solicitudes.index', ['estado' => $estado]) }}">
                {{ $etiquetas[$estado] }}
            </a>
        @endforeach
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Adoptante</th>
                        <th>Mascota</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $solicitud->usuario->name }}</div>
                                <small class="texto-secundario">{{ $solicitud->usuario->email }}</small>
                            </td>
                            <td>{{ $solicitud->mascota->nombre }}</td>
                            <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $solicitud->estado) }}">
                                    {{ ucfirst($solicitud->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-huellitas"
                                   href="{{ route('admin.solicitudes.show', $solicitud) }}">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="5">No hay solicitudes registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($solicitudes->hasPages())
            <div class="card-footer">{{ $solicitudes->links() }}</div>
        @endif
    </div>
@endsection
