@extends('layouts.app')

@section('title', 'Login - B-EDEN')

@section('content')
<div class="flex justify-center items-center py-20 px-4">
    <div class="flex flex-col md:flex-row gap-8 max-w-5xl w-full">
        <div class="bg-white p-10 md:p-12 rounded shadow flex-1">
            <h2 class="text-lg font-semibold mb-4">Iniciar sesión</h2>

            {{-- Mensaje de error general si las credenciales fallan --}}
            @if ($errors->has('correo'))
                <div class="text-red-500 text-sm mb-4">
                    {{ $errors->first('correo') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Input Correo con persistencia --}}
                <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo electrónico"
                    class="w-full border px-4 py-2 mb-3 rounded @error('correo') border-red-500 @enderror" required autofocus>

                {{-- Input Contraseña --}}
                <input type="password" name="contrasena" placeholder="Contraseña"
                    class="w-full border px-4 py-2 mb-4 rounded @error('correo') border-red-500 @enderror" required>

                <button type="submit" class="w-full bg-black text-white py-2 rounded">
                    Iniciar sesión
                </button>
            </form>
        </div>

        <div class="bg-white p-10 md:p-12 rounded shadow text-center flex-1">
            <h2 class="text-lg font-semibold mb-4">¿Eres nuevo en B-EDEN?</h2>
            <p class="text-gray-600 mb-6">
                Crea una cuenta para comprar más rápido y acceder a ofertas especiales
            </p>
            <a href="{{ route('register') }}" class="bg-black text-white px-6 py-2 rounded inline-block">
                Crear cuenta
            </a>
        </div>
    </div>
</div>
@endsection
