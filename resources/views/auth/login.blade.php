@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-huellitas">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Iniciar sesión</h1>
                    <p class="texto-secundario">Ingresa con tu cuenta de Huellitas.</p>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email"
                                   type="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Contraseña</label>
                            <input class="form-control @error('password') is-invalid @enderror" id="password"
                                   type="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="remember" type="checkbox" name="remember" value="1">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        <button class="btn btn-huellitas w-100" type="submit">Ingresar</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
