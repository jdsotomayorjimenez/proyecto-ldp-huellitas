@extends('layouts.admin')

@section('title', 'Citas')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Citas de adopción</h1>
        <p class="texto-secundario mb-0">Citas generadas al aprobar solicitudes.</p>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mascota</th>
                        <th>Adoptante</th>
                        <th>Fecha y hora</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th class="text-end">Cita</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citas as $cita)
                        <tr>
                            <td class="fw-semibold">{{ $cita->solicitudAdopcion->mascota->nombre }}</td>
                            <td>{{ $cita->solicitudAdopcion->usuario->name }}</td>
                            <td>
                                {{ \Illuminate\Support\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                {{ \Illuminate\Support\Carbon::parse($cita->hora)->format('H:i') }}
                            </td>
                            <td>{{ $cita->lugar }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $cita->estado) }}">
                                    {{ ucfirst($cita->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.citas.show', $cita) }}">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="6">No hay citas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($citas->hasPages())
            <div class="card-footer">{{ $citas->links() }}</div>
        @endif
    </div>
@endsection
