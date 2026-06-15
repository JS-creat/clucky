@extends('layouts.app')

@section('title', 'Recuperar contraseña - B-EDEN')

@section('content')
<div class="flex justify-center items-center py-20 px-4">
    <div class="w-full max-w-xl">
        <div class="bg-white p-10 md:p-12 rounded shadow">
            <h2 class="text-lg font-semibold mb-4">¿Olvidaste tu contraseña?</h2>

            <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                No te preocupes. Ingresa tu correo electrónico y te enviaremos un enlace seguro para que puedas restablecerla y elegir una nueva.
            </p>

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded text-green-700 text-sm font-medium text-center">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('correo'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-red-700 text-sm font-medium">
                    {{ $errors->first('correo') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
                    <input type="email" name="correo" value="{{ old('correo') }}"
                        placeholder="Correo electrónico"
                        class="w-full border px-4 py-2 rounded @error('correo') border-red-500 @enderror"
                        required autofocus
                        {{ session('status') ? 'disabled' : '' }}>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-black text-white py-2 rounded font-medium hover:bg-gray-800 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ session('status') ? 'disabled' : '' }}>
                    {{ session('status') ? 'Reenviar en 60s' : 'Enviar enlace de recuperación' }}
                </button>
            </form>

            @if (session('status'))
            <script>
                const btn = document.getElementById('submit-btn');
                let seconds = 60;

                const interval = setInterval(() => {
                    seconds--;
                    btn.textContent = `Reenviar en ${seconds}s`;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        btn.disabled = false;
                        btn.textContent = 'Reenviar enlace';
                        document.querySelector('input[name="correo"]').disabled = false;
                    }
                }, 1000);
            </script>
            @endif

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-xs text-gray-500 underline hover:text-black transition">
                    Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
