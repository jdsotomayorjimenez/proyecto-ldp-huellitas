@extends('layouts.admin')

@section('title', 'Editar mascota')

@section('content')
    <div class="card card-huellitas mb-4">
        <div class="card-body p-4">
            <h1 class="h3 mb-4">Editar mascota</h1>
            <form method="POST" action="{{ route('admin.mascotas.update', $mascota) }}">
                @csrf
                @method('PUT')
                @include('admin.mascotas._form')
                <div class="mt-4">
                    <button class="btn btn-huellitas" type="submit">Actualizar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.mascotas.index') }}">Volver</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-huellitas">
        <div class="card-body p-4">
            <h2 class="h4">Imágenes simples</h2>
            <p class="texto-secundario">
                Primero agrega el archivo en <code>public/img/mascotas/</code> y registra su ruta relativa.
            </p>
            <form class="row g-3 align-items-end" method="POST"
                  action="{{ route('admin.mascotas.imagenes.store', $mascota) }}">
                @csrf
                <div class="col-md-7">
                    <label class="form-label" for="ruta">Ruta</label>
                    <input class="form-control" id="ruta" name="ruta"
                           placeholder="img/mascotas/ejemplo.jpg" value="{{ old('ruta') }}" required>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" id="es_principal" type="checkbox"
                               name="es_principal" value="1">
                        <label class="form-check-label" for="es_principal">Imagen principal</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-huellitas w-100" type="submit">Asociar</button>
                </div>
            </form>

            <div class="row g-3 mt-3">
                @forelse ($mascota->imagenes as $imagen)
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100">
                            <img class="card-img-top pet-image-admin" src="{{ asset($imagen->ruta) }}"
                                 alt="Foto de {{ $mascota->nombre }}">
                            <div class="card-body">
                                <small class="d-block text-break">{{ $imagen->ruta }}</small>
                                @if ($imagen->es_principal)
                                    <span class="badge text-bg-success my-2">Principal</span>
                                @endif
                                <form method="POST" action="{{ route('admin.imagenes.destroy', $imagen) }}"
                                      onsubmit="return confirm('¿Quitar esta imagen del registro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Quitar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="texto-secundario mb-0">Esta mascota todavía no tiene imágenes.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
