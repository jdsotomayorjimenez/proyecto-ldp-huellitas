@extends('layouts.admin')

@section('title', 'Adopciones')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Adopciones registradas</h1>
        <p class="texto-secundario mb-0">Historial de adopciones completadas.</p>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Acta</th>
                        <th>Mascota</th>
                        <th>Adoptante</th>
                        <th>Fecha</th>
                        <th class="text-end">Adopción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adopciones as $adopcion)
                        <tr>
                            <td class="fw-semibold">{{ $adopcion->numero_acta }}</td>
                            <td>{{ $adopcion->solicitudAdopcion->mascota->nombre }}</td>
                            <td>{{ $adopcion->solicitudAdopcion->usuario->name }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($adopcion->fecha_adopcion)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.adopciones.show', $adopcion) }}">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="5">Aún no hay adopciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($adopciones->hasPages())
            <div class="card-footer">{{ $adopciones->links() }}</div>
        @endif
    </div>
@endsection
