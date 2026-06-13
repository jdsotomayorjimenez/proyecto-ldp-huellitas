@extends('layouts.admin')

@section('title', 'Razas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Razas</h1>
            <p class="texto-secundario mb-0">Razas organizadas por tipo de mascota.</p>
        </div>
        <a class="btn btn-huellitas" href="{{ route('admin.razas.create') }}">Nueva raza</a>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Raza</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Mascotas</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($razas as $raza)
                        <tr>
                            <td class="fw-semibold">{{ $raza->nombre }}</td>
                            <td>{{ $raza->tipoMascota->nombre }}</td>
                            <td>{{ $raza->descripcion ?: 'Sin descripción' }}</td>
                            <td>{{ $raza->mascotas_count }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.razas.edit', $raza) }}">Editar</a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.razas.destroy', $raza) }}"
                                      onsubmit="return confirm('¿Eliminar esta raza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="5">No hay razas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($razas->hasPages())
            <div class="card-footer">{{ $razas->links() }}</div>
        @endif
    </div>
@endsection
