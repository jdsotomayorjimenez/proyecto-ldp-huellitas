@extends('layouts.admin')

@section('title', 'Mascotas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Mascotas</h1>
            <p class="texto-secundario mb-0">Registro base de mascotas e imágenes.</p>
        </div>
        <a class="btn btn-huellitas" href="{{ route('admin.mascotas.create') }}">Nueva mascota</a>
    </div>

    <div class="card card-huellitas">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mascota</th>
                        <th>Tipo / raza</th>
                        <th>Género</th>
                        <th>Tamaño</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mascotas as $mascota)
                        @php
                            $imagen = $mascota->imagenes->firstWhere('es_principal', true)
                                ?? $mascota->imagenes->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($imagen)
                                        <img class="pet-thumbnail" src="{{ asset($imagen->ruta) }}"
                                             alt="Foto de {{ $mascota->nombre }}">
                                    @else
                                        <div class="pet-thumbnail pet-thumbnail-empty">Sin foto</div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $mascota->nombre }}</div>
                                        <small class="texto-secundario">{{ $mascota->descripcion }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $mascota->raza->tipoMascota->nombre }} / {{ $mascota->raza->nombre }}</td>
                            <td>{{ ucfirst($mascota->genero) }}</td>
                            <td>{{ ucfirst(str_replace('pequeno', 'pequeño', $mascota->tamanio)) }}</td>
                            <td>
                                <span class="badge badge-{{ str_replace('_', '-', $mascota->estado) }}">
                                    {{ ucfirst(str_replace('_', ' ', $mascota->estado)) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.mascotas.edit', $mascota) }}">Editar</a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.mascotas.destroy', $mascota) }}"
                                      onsubmit="return confirm('¿Eliminar esta mascota?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center py-4" colspan="6">No hay mascotas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($mascotas->hasPages())
            <div class="card-footer">{{ $mascotas->links() }}</div>
        @endif
    </div>
@endsection
