@extends('layouts.public')

@section('title', 'Catálogo de mascotas')

@section('content')
    <section class="container">
        <div class="text-center mb-4">
            <p class="section-eyebrow mb-1">Adopta hoy</p>
            <h1 class="section-title">Catálogo de mascotas</h1>
            <p class="texto-secundario mb-0">{{ $mascotas->total() }} mascota(s) encontradas</p>
        </div>

        <form method="GET" action="{{ route('mascotas.catalogo') }}" class="card-huellitas p-3 p-lg-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small fw-semibold" for="q">Buscar por nombre</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input class="form-control" type="search" id="q" name="q"
                               value="{{ $busqueda }}" placeholder="Ej. Luna, Max...">
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <label class="form-label small fw-semibold" for="genero">Género</label>
                    <select class="form-select" id="genero" name="genero">
                        <option value="">Todos</option>
                        <option value="macho" @selected($generoSeleccionado === 'macho')>Macho</option>
                        <option value="hembra" @selected($generoSeleccionado === 'hembra')>Hembra</option>
                    </select>
                </div>
                <div class="col-lg-3 col-6">
                    <label class="form-label small fw-semibold" for="orden">Ordenar por</label>
                    <select class="form-select" id="orden" name="orden">
                        <option value="">Destacados</option>
                        <option value="edad_asc" @selected($orden === 'edad_asc')>Menor edad</option>
                        <option value="edad_desc" @selected($orden === 'edad_desc')>Mayor edad</option>
                    </select>
                </div>
                <div class="col-lg-2 d-grid gap-2">
                    <button class="btn-cta" type="submit">Filtrar</button>
                    @if ($busqueda || $generoSeleccionado || $tipoSeleccionado || $orden)
                        <a class="btn-cta-ghost text-center" href="{{ route('mascotas.catalogo') }}">Limpiar</a>
                    @endif
                </div>
            </div>

            <input type="hidden" name="tipo" value="{{ $tipoSeleccionado }}">
        </form>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <a class="filter-chip {{ ! $tipoSeleccionado ? 'active' : '' }}"
               href="{{ route('mascotas.catalogo', array_filter(['q' => $busqueda, 'genero' => $generoSeleccionado, 'orden' => $orden])) }}">
                Todos
            </a>
            @foreach ($tipos as $tipo)
                <a class="filter-chip {{ $tipoSeleccionado === $tipo->id ? 'active' : '' }}"
                   href="{{ route('mascotas.catalogo', array_filter(['tipo' => $tipo->id, 'q' => $busqueda, 'genero' => $generoSeleccionado, 'orden' => $orden])) }}">
                    {{ $tipo->nombre }}
                </a>
            @endforeach
        </div>

        @if ($mascotas->isEmpty())
            <div class="card-huellitas p-5 text-center">
                <i class="bi bi-search fs-1 texto-secundario"></i>
                <h2 class="h5 mt-3">No encontramos mascotas</h2>
                <p class="texto-secundario mb-3">Prueba ajustando los filtros de búsqueda.</p>
                <a class="btn-cta d-inline-block" href="{{ route('mascotas.catalogo') }}">Ver todo el catálogo</a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($mascotas as $mascota)
                    <div class="col-sm-6 col-lg-4">
                        <x-pet-card :mascota="$mascota" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $mascotas->links() }}
            </div>
        @endif
    </section>
@endsection
