@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">

            <!-- Header - AHORA EN NEGRO -->
            <div class="bg-black px-8 py-8 text-white">
                <h1 class="text-3xl font-bold">
                    Editar perfil
                </h1>
                <p class="opacity-90 mt-1">
                    Actualiza tu información personal
                </p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('perfil.update') }}" class="px-8 py-10 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-gray-500 text-sm">Nombres</p>
                        <input type="text" name="nombres" value="{{ auth()->user()->nombres }}"
                            class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('nombres')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">Apellidos</p>
                        <input type="text" name="apellidos" value="{{ auth()->user()->apellidos }}"
                            class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('apellidos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="max-w-md">
                    <p class="text-gray-500 text-sm">Teléfono</p>
                    <input type="text" name="telefono" value="{{ auth()->user()->telefono }}"
                        class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('telefono')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acciones -->
                <div class="border-t pt-6 flex justify-between items-center">
                    <a href="{{ route('perfil.index') }}" class="text-indigo-600 hover:underline">
                        ← Volver al perfil
                    </a>

                    <button type="submit"
                        class="bg-black text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-800 transition">
                        Guardar cambios
                    </button>
                </div>

            </form>

        </div>

    </div>
@endsection