@extends('layouts.admin')

@section('title', 'Nueva mascota')

@section('content')
    <div class="card card-huellitas">
        <div class="card-body p-4">
            <h1 class="h3 mb-4">Nueva mascota</h1>
            <form method="POST" action="{{ route('admin.mascotas.store') }}">
                @csrf
                @include('admin.mascotas._form')
                <div class="mt-4">
                    <button class="btn btn-huellitas" type="submit">Guardar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.mascotas.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
