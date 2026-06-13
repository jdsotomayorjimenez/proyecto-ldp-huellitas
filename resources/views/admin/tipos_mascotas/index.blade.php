@extends('layouts.admin')

@section('title', 'Tipos de mascotas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tipos de mascotas</h1>
            <p class="texto-secundario mb-0">Categorías generales para razas y requisitos.</p>
        </div>
        <a class="btn btn-huellitas" href="{{ route('admin.tipos-mascotas.create') }}">Nuevo tipo</a>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Razas</th>
                        <th>Requisitos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipos as $tipo)
                        <tr>
                            <td class="fw-semibold">{{ $tipo->nombre }}</td>
                            <td>{{ $tipo->descripcion ?: 'Sin descripción' }}</td>
                            <td>{{ $tipo->razas_count }}</td>
                            <td>{{ $tipo->requisitos_adopcion_count }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.tipos-mascotas.edit', $tipo) }}">Editar</a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.tipos-mascotas.destroy', $tipo) }}"
                                      onsubmit="return confirm('¿Eliminar este tipo de mascota?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="5">No hay tipos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tipos->hasPages())
            <div class="card-footer">{{ $tipos->links() }}</div>
        @endif
    </div>
@endsection
