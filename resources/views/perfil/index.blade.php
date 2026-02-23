@extends('layouts.app')

@section('title', 'Mi Perfil - C\'Lucky')

@section('content')
    <div class="sticky top-16 bg-white border-b z-40 shadow-sm">
    {{-- Este contenedor copia exactamente la estructura del contenedor principal --}}
        <div class="max-w-5xl mx-auto px-4 w-full">
            <div class="flex justify-between items-center py-3">
                {{-- "Mi perfil" alineado con el contenido del cuadro --}}
                <span class="font-medium uppercase text-sm tracking-widest text-gray-900">
                    Mi perfil
                </span>
                
                {{-- Nombre alineado con el contenido del cuadro --}}
                <span class="text-sm text-gray-600">
                    Hola, {{ auth()->user()->nombres }}
                </span>
            </div>
        </div>
    </div>
    <!-- PERFIL -->
    <div class="min-h-screen bg-gray-100 flex justify-center items-start py-12 px-4">

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="w-full max-w-5xl bg-white rounded-xl shadow flex overflow-hidden">

            <!-- SIDEBAR -->
            <aside class="w-60 bg-black text-white flex flex-col justify-between">
                <div>
                    <div class="flex justify-center py-6">
                        <div class="w-14 h-14 rounded-full border flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>

                    <nav class="px-6 space-y-4 text-sm">
                        <a href="#" class="block border-b pb-2 font-medium">
                            Información de cuenta
                        </a>
                        <a href="#" class="block text-gray-300 hover:text-white">
                            Mis compras
                        </a>
                        <a href="#" class="block text-gray-300 hover:text-white">
                            Favoritos
                        </a>
                    </nav>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="p-6">
                    @csrf
                    <button class="w-full text-sm hover:text-red-400">
                        Cerrar sesión
                    </button>
                </form>
            </aside>

            <!-- CONTENIDO -->
            <main class="flex-1 p-8">

                <h2 class="font-semibold mb-6">Datos personales</h2>

                <!-- DATOS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="text-sm text-gray-500">Nombres</label>
                        <input disabled value="{{ auth()->user()->nombres }}"
                            class="w-full bg-gray-100 rounded px-4 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Apellidos</label>
                        <input disabled value="{{ auth()->user()->apellidos }}"
                            class="w-full bg-gray-100 rounded px-4 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Correo electrónico</label>
                        <input disabled value="{{ auth()->user()->correo }}"
                            class="w-full bg-gray-100 rounded px-4 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Teléfono</label>
                        <input disabled value="{{ auth()->user()->telefono ?? '' }}"
                            class="w-full bg-gray-100 rounded px-4 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Número de documento</label>
                        <input disabled value="{{ auth()->user()->numero_documento ?? '' }}"
                            class="w-full bg-gray-100 rounded px-4 py-2">
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('home') }}"
                        class="text-sm text-gray-600 hover:underline">
                        ← Volver
                    </a>

                    <a href="{{ route('perfil.edit') }}"
                        class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800 transition">
                        Editar perfil
                    </a>
                </div>

            </main>
        </div>
    </div>
@endsection