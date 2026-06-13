@extends('layouts.admin')

@section('title', 'Nuevo tipo')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Nuevo tipo de mascota</h1>
                    <form method="POST" action="{{ route('admin.tipos-mascotas.store') }}">
                        @csrf
                        @include('admin.tipos_mascotas._form')
                        <button class="btn btn-huellitas" type="submit">Guardar</button>
                        <a class="btn btn-outline-secondary"
                           href="{{ route('admin.tipos-mascotas.index') }}">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
