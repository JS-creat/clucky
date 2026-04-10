@extends('layouts.app')

@section('title', 'Editar Perfil - C\'Lucky')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">

            <div class="bg-black px-8 py-8 text-white">
                <h1 class="text-3xl font-bold" style="font-family:'Playfair Display',serif">
                    Editar perfil
                </h1>
                <p class="opacity-80 mt-1" style="font-family:'DM Sans',sans-serif">
                    Actualiza tu información personal y de contacto
                </p>
            </div>

            <form method="POST" action="{{ route('perfil.update') }}" class="px-8 py-10 space-y-8" style="font-family:'DM Sans',sans-serif">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Nombres --}}
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-400 block mb-2">Nombres</label>
                        <input type="text" name="nombres" value="{{ old('nombres', auth()->user()->nombres) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-black focus:bg-white focus:outline-none transition">
                        @error('nombres')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-400 block mb-2">Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', auth()->user()->apellidos) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-black focus:bg-white focus:outline-none transition">
                        @error('apellidos')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-400 block mb-2">Teléfono móvil</label>
                        <input type="text" name="telefono"
                            value="{{ old('telefono', auth()->user()->telefono) }}"
                            maxlength="9"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            placeholder="Ej. 912345678"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-black focus:bg-white focus:outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1 italic">Solo 9 dígitos numéricos</p>
                        @error('telefono')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Documento de Identidad --}}
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-400 block mb-2">Documento de Identidad</label>
                        <input type="text" name="numero_documento"
                            value="{{ old('numero_documento', auth()->user()->numero_documento) }}"
                            maxlength="12"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            placeholder="DNI o Pasaporte"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 focus:ring-2 focus:ring-black focus:bg-white focus:outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1 italic">Solo caracteres numéricos</p>
                        @error('numero_documento')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('perfil.index') }}" class="text-sm font-medium text-gray-400 hover:text-black transition">
                        ← Cancelar y volver
                    </a>

                    <button type="submit"
                        class="w-full sm:w-auto bg-black text-white px-10 py-3.5 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition-all shadow-md active:scale-95">
                        Actualizar mis datos
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection
