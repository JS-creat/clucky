@extends('layouts.app')

@section('title', 'Login - C Lucky')

@section('content')
<div class="flex justify-center items-center py-20 px-4">
    <div class="flex flex-col md:flex-row gap-8 max-w-5xl w-full">
        <!-- LOGIN -->
        <div class="bg-white p-10 md:p-12 rounded shadow flex-1">
            <h2 class="text-lg font-semibold mb-4">Iniciar sesión</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="correo" placeholder="Correo electrónico"
                    class="w-full border px-4 py-2 mb-3 rounded" required>
                <input type="password" name="contrasena" placeholder="Contraseña"
                    class="w-full border px-4 py-2 mb-4 rounded" required>
                <button class="w-full bg-black text-white py-2 rounded">
                    Iniciar sesión
                </button>
            </form>
        </div>

        <!-- REGISTER -->
        <div class="bg-white p-10 md:p-12 rounded shadow text-center flex-1">
            <h2 class="text-lg font-semibold mb-4">¿Eres nuevo en C’Lucky?</h2>
            <p class="text-gray-600 mb-6">
                Crea una cuenta para comprar más rápido y acceder a ofertas especiales
            </p>
            <a href="/register" class="bg-black text-white px-6 py-2 rounded inline-block">
                Crear cuenta
            </a>
        </div>
    </div>
</div>
@endsection