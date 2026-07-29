@extends('layouts.app')

@section('title', 'Login - B-EDEN')

@section('content')
    <div class="flex-grow flex justify-center items-center px-4 py-12 sm:py-20">
        <div class="flex flex-col md:flex-row gap-8 max-w-5xl w-full items-stretch">

            <!-- Columna: Iniciar sesión -->
            <div class="bg-white p-8 md:p-12 rounded shadow flex-1 flex flex-col justify-center">
                <h2 class="text-lg font-semibold mb-6">Iniciar sesión</h2>

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('correo'))
                    <div class="text-red-500 text-sm mb-4">
                        {{ $errors->first('correo') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo electrónico"
                            class="w-full border border-gray-300 px-4 py-3 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent @error('correo') border-red-500 @enderror"
                            required autofocus>
                    </div>

                    <div class="mb-4">
                        <input type="password" name="contrasena" placeholder="Contraseña"
                            class="w-full border border-gray-300 px-4 py-3 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent @error('contrasena') border-red-500 @enderror"
                            required>

                        <div class="flex justify-end mt-2">
                            <a href="{{ route('password.request') }}"
                                class="text-xs text-gray-500 underline hover:text-black transition">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-black text-white py-3 rounded font-medium hover:bg-gray-800 transition cursor-pointer">
                        Iniciar sesión
                    </button>
                    {{-- Separador --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500">o</span>
                        </div>
                    </div>

                    {{-- Botón Google --}}
                    <a href="{{ route('google.login') }}"
                        class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 py-2.5 rounded font-medium hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"
                                fill="#4285F4" />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853" />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                fill="#FBBC05" />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335" />
                        </svg>
                        Google
                    </a>
                </form>
            </div>

            <!-- Columna: Crear cuenta -->
            <div class="bg-white p-8 md:p-12 rounded shadow flex-1 flex flex-col justify-center items-center text-center">
                <h2 class="text-lg font-semibold mb-4">¿Eres nuevo en B-EDEN?</h2>
                <p class="text-gray-600 mb-6 max-w-sm">
                    Crea una cuenta para comprar más rápido y acceder a ofertas especiales
                </p>
                <a href="{{ route('register') }}"
                    class="bg-black text-white px-8 py-3 rounded inline-block font-medium hover:bg-gray-800 transition">
                    Crear cuenta
                </a>
            </div>

        </div>
    </div>
@endsection
