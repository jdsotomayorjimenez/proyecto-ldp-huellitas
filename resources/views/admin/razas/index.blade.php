@extends('layouts.admin')

@section('title', 'Razas')

@section('content')
    <div class="admin-page-heading mb-4">
        <div>
            <p class="admin-eyebrow mb-1">Catálogo animal</p>
            <h1 class="h3 mb-1">Razas por tipo de mascota</h1>
            <p class="texto-secundario mb-0">Selecciona un tipo para consultar y administrar sus razas.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3 admin-side-col">
            <div class="card admin-panel-card admin-type-panel">
                <div class="card-header">
                    <span class="fw-bold">Tipos de mascota</span>
                </div>
                <div class="list-group list-group-flush admin-type-list">
                    @forelse ($tipos as $tipo)
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                  {{ $tipoSeleccionado?->id === $tipo->id ? 'active' : '' }}"
                           href="{{ route('admin.razas.index', ['tipo_mascota_id' => $tipo->id]) }}">
                            <span>
                                <i class="bi bi-folder2-open me-2"></i>{{ $tipo->nombre }}
                            </span>
                            <span class="admin-count">{{ $tipo->razas_count }}</span>
                        </a>
                    @empty
                        <div class="p-3 texto-secundario">No hay tipos registrados.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card admin-panel-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="admin-card-label">Tipo seleccionado</span>
                        <h2 class="h4 mb-0">{{ $tipoSeleccionado?->nombre ?? 'Sin tipo' }}</h2>
                    </div>
                    @if ($tipoSeleccionado)
                        <a class="btn btn-huellitas"
                           href="{{ route('admin.razas.create', ['tipo_mascota_id' => $tipoSeleccionado->id]) }}">
                            <i class="bi bi-plus-lg me-1"></i>Añadir raza
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Raza</th>
                                <th>Descripción</th>
                                <th>Mascotas</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($razas as $raza)
                                <tr>
                                    <td class="fw-semibold">{{ $raza->nombre }}</td>
                                    <td>{{ $raza->descripcion ?: 'Sin descripción' }}</td>
                                    <td><span class="admin-count">{{ $raza->mascotas_count }}</span></td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-cafe"
                                           href="{{ route('admin.razas.edit', $raza) }}">Editar</a>
                                        <form class="d-inline" method="POST"
                                              action="{{ route('admin.razas.destroy', $raza) }}"
                                              onsubmit="return confirm('¿Eliminar esta raza?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-terracota" type="submit">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center py-5" colspan="4">
                                        Este tipo todavía no tiene razas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
