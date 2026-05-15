@extends('layouts.app')

@section('title', 'Registro - B-EDEN')

@section('content')

<div class="py-10 md:py-20 flex justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white p-6 md:p-8 rounded shadow-lg">

            <h2 class="text-xl md:text-2xl font-semibold text-center mb-8">
                Registrarse
            </h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

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
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs mb-4 pl-2">{{ $message }}</p>
                @enderror

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
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                    </button>
                </div>

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
                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
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
