@extends('layouts.app')

@section('title', 'Registro - B-EDEN')

@section('content')

<div class="py-10 md:py-20 flex justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white p-6 md:p-8 rounded shadow-lg">

            <h2 class="text-xl md:text-2xl font-semibold text-center mb-8">
                Registrarse
            </h2>
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Nombres --}}
                <div class="mb-4">
                    <input
                        class="w-full border rounded-lg px-4 py-3 text-base"
                        type="text"
                        name="nombres"
                        placeholder="Nombres"
                        value="{{ old('nombres') }}"
                        required
                        autofocus
                    >

                    @error('nombres')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Apellidos --}}
                <div class="mb-4">
                    <input
                        class="w-full border rounded-lg px-4 py-3 text-base"
                        type="text"
                        name="apellidos"
                        placeholder="Apellidos"
                        value="{{ old('apellidos') }}"
                        required
                    >

                    @error('apellidos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Correo --}}
                <div class="mb-4">
                    <input
                        class="w-full border rounded-lg px-4 py-3 text-base"
                        type="email"
                        name="correo"
                        placeholder="Correo electrónico"
                        value="{{ old('correo') }}"
                        required
                    >

                    @error('correo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="relative mb-1">
                    <input
                        id="password"
                        class="w-full border rounded-lg px-4 py-3 text-base pr-12"
                        type="password"
                        name="password"
                        placeholder="Contraseña"
                        required
                    >

                    <button
                        type="button"
                        onclick="togglePass('password', 'eye-password')"
                        class="absolute right-3 top-3.5 text-gray-400 hover:text-black transition-colors"
                    >
                        <svg
                            id="eye-password"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs mb-4 pl-2">{{ $message }}</p>
                @enderror

                {{-- Confirmar contraseña --}}
                <div class="relative mb-6">
                    <input
                        id="password_confirmation"
                        class="w-full border rounded-lg px-4 py-3 text-base pr-12"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirmar contraseña"
                        required
                    >

                    <button
                        type="button"
                        onclick="togglePass('password_confirmation', 'eye-confirmation')"
                        class="absolute right-3 top-3.5 text-gray-400 hover:text-black transition-colors"
                    >
                        <svg
                            id="eye-confirmation"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>
                    </button>
                </div>

                {{-- Términos y condiciones --}}
                <div class="mb-6">
                    <label class="flex items-start gap-3 text-sm text-gray-700">
                        <input
                            type="checkbox"
                            name="acepta_terminos"
                            value="1"
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-black focus:ring-black"
                            {{ old('acepta_terminos') ? 'checked' : '' }}
                            required
                        >

                        <span>
                            He leído y acepto los
                            <a
                                href="{{ route('terminos') }}"
                                target="_blank"
                                class="font-medium text-black underline hover:text-gray-600"
                            >
                                Términos y Condiciones
                            </a>
                            y la
                            <a
                                href="{{ route('politica-privacidad') }}"
                                target="_blank"
                                class="font-medium text-black underline hover:text-gray-600"
                            >
                                Política de Privacidad
                            </a>.
                        </span>
                    </label>

                    @error('acepta_terminos')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón registro --}}
                <button
                    class="w-full bg-black text-white py-3 rounded-lg text-base font-bold hover:bg-gray-800 transition"
                >
                    Crear cuenta
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-6">
                ¿Ya tienes una cuenta?

                <a
                    href="{{ route('login') }}"
                    class="text-black underline font-medium hover:text-gray-600 transition"
                >
                    Iniciar sesión
                </a>
            </p>

            {{-- Separador --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>

                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">
                            o continúa con
                        </span>
                    </div>
                </div>

                {{-- Google --}}
                <a
                    href="{{ route('google.login') }}"
                    class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 py-2.5 rounded font-medium hover:bg-gray-50 transition"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path
                            fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"
                        />
                        <path
                            fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        />
                        <path
                            fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        />
                        <path
                            fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        />
                    </svg>

                    Google
                </a>

        </div>
    </div>
</div>

<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';

            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
            `;
        } else {
            input.type = 'password';

            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            `;
        }
    }
</script>

@endsection
