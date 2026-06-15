@extends('layouts.app')

@section('title', 'Restablecer contraseña - B-EDEN')

@section('content')
<div class="flex justify-center items-center py-20 px-4">
    <div class="w-full max-w-xl">
        <div class="bg-white p-10 md:p-12 rounded shadow">
            <h2 class="text-lg font-semibold mb-6">Crea tu nueva contraseña</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-red-700 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1 font-medium">Correo electrónico</label>
                    <input type="email" name="correo"
                        value="{{ old('correo', request()->query('email')) }}"
                        required readonly
                        class="w-full border px-4 py-2 bg-gray-50 text-gray-400 rounded outline-none cursor-not-allowed">
                </div>

                <div class="mb-4">
                    <input type="password" name="contrasena" placeholder="Nueva contraseña"
                        class="w-full border px-4 py-2 rounded" required autofocus autocomplete="new-password">
                </div>

                <div class="mb-6">
                    <input type="password" name="contrasena_confirmation" placeholder="Confirmar nueva contraseña"
                        class="w-full border px-4 py-2 rounded" required autocomplete="new-password">
                </div>

                <button type="submit"
                    class="w-full bg-black text-white py-2 rounded font-medium hover:bg-gray-800 transition cursor-pointer">
                    Restablecer contraseña
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
