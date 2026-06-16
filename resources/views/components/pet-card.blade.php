@props(['mascota'])

@php
    $imagen = $mascota->imagenes->firstWhere('es_principal', true) ?? $mascota->imagenes->first();
    $estadoSlug = str_replace('_', '-', $mascota->estado);
    $estadoLabel = ucfirst(str_replace('_', ' ', $mascota->estado));
    $tamanio = ucfirst(str_replace('pequeno', 'pequeño', $mascota->tamanio));
@endphp

<a class="pet-card d-block text-decoration-none" href="{{ route('mascotas.show.public', $mascota) }}">
    <div class="pet-card-media">
        @if ($imagen)
            <img src="{{ asset($imagen->ruta) }}" alt="Foto de {{ $mascota->nombre }}">
        @else
            <div class="pet-empty"><i class="bi bi-image fs-3"></i></div>
        @endif
        <span class="badge rounded-pill badge-{{ $estadoSlug }}">{{ $estadoLabel }}</span>
    </div>
    <div class="pet-card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h3 class="pet-name h5 mb-0">{{ $mascota->nombre }}</h3>
            <i class="bi bi-{{ $mascota->genero === 'hembra' ? 'gender-female' : 'gender-male' }} fs-5"
               style="color: var(--color-principal);"
               title="{{ ucfirst($mascota->genero) }}"></i>
        </div>
        <p class="pet-meta mb-3">
            {{ $mascota->raza->tipoMascota->nombre }} · {{ $mascota->raza->nombre }}
        </p>
        <div class="d-flex flex-wrap gap-2">
            <span class="pet-chip"><i class="bi bi-rulers"></i>{{ $tamanio }}</span>
            <span class="pet-chip">
                <i class="bi bi-calendar3"></i>
                {{ $mascota->edad_legible }}
            </span>
        </div>
    </div>
</a>
