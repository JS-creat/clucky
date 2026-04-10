@extends('layouts.app')

@section('title', 'Mi Perfil - C\'Lucky')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="min-h-screen bg-stone-50" style="font-family:'DM Sans',sans-serif">

    {{-- NAVBAR --}}
    <div class="sticky top-0 bg-white border-b border-gray-100 z-40">
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <span class="font-semibold text-xs tracking-[0.2em] uppercase text-gray-900">
                    Mi perfil
                </span>

                <span class="text-sm text-gray-500">
                    Hola, <strong class="text-gray-900">{{ auth()->user()->nombres }}</strong>
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8 sm:py-10">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-5 mb-8">

            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-2xl bg-gray-900 flex items-center justify-center text-white text-xl font-bold shrink-0"
                style="font-family:'Playfair Display',serif">
                {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                {{ strtoupper(substr(auth()->user()->apellidos, 0, 1)) }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 truncate"
                    style="font-family:'Playfair Display',serif">
                    {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                </h1>

                <p class="text-sm text-gray-400 truncate">
                    {{ auth()->user()->correo }}
                </p>
            </div>

            {{-- Acciones --}}
            <div class="flex gap-2 w-full sm:w-auto">

                <a href="{{ route('perfil.edit') }}"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 bg-gray-900 text-white text-xs font-semibold uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-gray-800 transition">
                    Editar
                </a>

                <a href="{{ route('perfil.pedidos.index') }}"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 border border-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-widest px-4 py-2.5 rounded-xl hover:border-gray-900 hover:bg-gray-50 transition">
                    Mis pedidos
                </a>

            </div>
        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-6 sm:px-8 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
                    Información personal
                </h2>
            </div>

            <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label class="label">Nombres</label>
                    <div class="input">{{ auth()->user()->nombres }}</div>
                </div>

                <div>
                    <label class="label">Apellidos</label>
                    <div class="input">{{ auth()->user()->apellidos }}</div>
                </div>

                <div>
                    <label class="label">Correo</label>
                    <div class="input truncate">{{ auth()->user()->correo }}</div>
                </div>

                <div>
                    <label class="label">Teléfono</label>
                    <div class="input {{ auth()->user()->telefono ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
                        {{ auth()->user()->telefono ?? 'No registrado' }}
                    </div>
                </div>

                <div>
                    <label class="label">Documento</label>
                    <div class="input {{ auth()->user()->numero_documento ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
                        {{ auth()->user()->numero_documento ?? 'No registrado' }}
                    </div>
                </div>

            </div>
        </div>

        {{-- FOOTER --}}
        <div class="flex flex-col sm:flex-row sm:justify-between gap-4 mt-6">

            <a href="{{ route('home') }}"
                class="text-sm text-gray-400 hover:text-gray-700 font-medium">
                ← Volver al inicio
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-rose-400 hover:text-rose-600 font-semibold uppercase tracking-widest">
                    Cerrar sesión
                </button>
            </form>

        </div>

    </div>
</div>

{{-- COMPONENTES REUTILIZABLES --}}
<style>
    .label {
        @apply text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5;
    }
    .input {
        @apply bg-gray-50 rounded-xl px-4 py-3 text-sm border border-gray-100;
    }
</style>

@endsection
