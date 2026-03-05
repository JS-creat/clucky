@extends('admin.layout')

@section('content')
    <div x-data="{ createModal: false, createGeneroModal: false, tab: 'categorias' }">

        {{-- HEADER Y NAVEGACIÓN --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Gestión de Catálogo</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Administra las clasificaciones de tus productos.</p>

                <div class="flex gap-2 mt-8 p-1.5 bg-gray-100 w-fit rounded-[2rem] border border-gray-200">
                    <button @click="tab = 'categorias'"
                        :class="tab === 'categorias' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-3 rounded-[1.5rem] font-black transition-all duration-300 flex items-center gap-2">
                        <x-heroicon-o-folder class="w-5 h-5" />
                        Categorías
                    </button>
                    <button @click="tab = 'generos'"
                        :class="tab === 'generos' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-3 rounded-[1.5rem] font-black transition-all duration-300 flex items-center gap-2">
                        <x-heroicon-o-users class="w-5 h-5" />
                        Géneros
                    </button>
                </div>
            </div>

            <div class="pb-2">
                {{-- Botón para Categorías --}}
                <button x-show="tab === 'categorias'" @click="createModal = true"
                    class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
                    <x-heroicon-o-plus class="w-6 h-6" />
                    Nueva Categoría
                </button>

                {{-- Botón para Géneros --}}
                <button x-show="tab === 'generos'" @click="createGeneroModal = true"
                    class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95"
                    x-cloak>
                    <x-heroicon-o-plus class="w-6 h-6" />
                    Nuevo Género
                </button>
            </div>
        </div>

        <hr class="border-gray-100 mb-10">

        {{-- SECCIÓN CATEGORÍAS --}}
        <div x-show="tab === 'categorias'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($categorias as $categoria)
                    <div x-data="{ confirmModal: false, editModal: false }"
                        class="group relative bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-2xl transition-all duration-500">

                        {{-- Estado --}}
                        <div class="absolute top-8 right-8">
                            <span
                                class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider {{ $categoria->estado_categoria ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-400' }}">
                                {{ $categoria->estado_categoria ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <div class="mb-10">
                            <div
                                class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                                <x-heroicon-o-folder class="w-8 h-8" />
                            </div>
                            <h3 class="text-2xl font-black text-gray-800 leading-tight">{{ $categoria->nombre_categoria }}</h3>
                            <p class="mt-2 text-gray-400 font-medium italic">{{ $categoria->productos->count() }} productos</p>
                        </div>

                        <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                            <button @click="editModal = true"
                                class="font-bold text-gray-600 hover:text-indigo-600 flex items-center gap-2">
                                <x-heroicon-o-pencil-square class="w-5 h-5" /> Editar
                            </button>
                            <button @click="confirmModal = true"
                                class="font-bold {{ $categoria->estado_categoria ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ $categoria->estado_categoria ? 'Desactivar' : 'Activar' }}
                            </button>
                            {{-- MODAL CONFIRMACIÓN ESTADO --}}
                            <template x-if="confirmModal">
                                <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                                    <div @click="confirmModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm">
                                    </div>
                                    <div class="relative bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center">
                                        <div
                                            class="w-20 h-20 {{ $categoria->estado_categoria ? 'bg-rose-50 text-rose-500' : 'bg-emerald-50 text-emerald-500' }} rounded-full flex items-center justify-center mx-auto mb-6">
                                            <x-heroicon-o-exclamation-triangle class="w-10 h-10" />
                                        </div>
                                        <h3 class="text-2xl font-black text-gray-900 mb-2">¿Estás seguro?</h3>
                                        <p class="text-gray-500 font-medium mb-8">
                                            Vas a {{ $categoria->estado_categoria ? 'desactivar' : 'activar' }} la categoría
                                            <span class="font-bold text-gray-800">"{{ $categoria->nombre_categoria }}"</span>.
                                        </p>
                                        <form action="{{ route('admin.categorias.toggle', $categoria->id_categoria) }}"
                                            method="POST" class="flex gap-3">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" @click="confirmModal = false"
                                                class="flex-1 py-3 bg-gray-100 text-gray-500 font-bold rounded-xl">No,
                                                volver</button>
                                            <button type="submit"
                                                class="flex-1 py-3 {{ $categoria->estado_categoria ? 'bg-rose-500' : 'bg-emerald-500' }} text-white font-bold rounded-xl shadow-lg">Sí,
                                                continuar</button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                        {{-- MODAL EDITAR CATEGORÍA --}}
                        <template x-if="editModal">
                            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                                <div @click="editModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md"></div>
                                <div class="relative bg-white rounded-[2.5rem] p-10 max-w-lg w-full shadow-2xl">
                                    <h2 class="text-3xl font-black text-gray-900 mb-6">Editar Categoría</h2>
                                    <form action="{{ route('admin.categorias.update', $categoria) }}" method="POST"
                                        class="space-y-6">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Nuevo
                                                Nombre</label>
                                            <input type="text" name="nombre_categoria"
                                                value="{{ $categoria->nombre_categoria }}" required
                                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-lg font-bold">
                                        </div>
                                        <div class="flex gap-4">
                                            <button type="button" @click="editModal = false"
                                                class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-xl">Cancelar</button>
                                            <button type="submit"
                                                class="flex-[2] py-4 bg-indigo-600 text-white font-black rounded-xl shadow-lg">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </div>
                @empty
                    <div
                        class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-bold">No hay categorías registradas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- SECCIÓN GENEROS --}}
        <div x-show="tab === 'generos'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-cloak>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($generos as $genero)
                    <div
                        class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm flex items-center justify-between group hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                <x-heroicon-o-user class="w-6 h-6" />
                            </div>
                            <span class="text-xl font-black text-gray-800">{{ $genero->nombre_genero }}</span>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
                            <button class="p-2 text-gray-400 hover:text-indigo-600"><x-heroicon-o-pencil
                                    class="w-5 h-5" /></button>
                            <button class="p-2 text-gray-400 hover:text-rose-500"><x-heroicon-o-trash
                                    class="w-5 h-5" /></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- MODAL CREACIÓN CATEGORIA --}}
        <template x-if="createModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div @click="createModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
                <div
                    class="relative bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-200">
                    <div class="flex justify-between items-center mb-10 text-left">
                        <div>
                            <h2 class="text-3xl font-black text-gray-900">Nueva Categoría</h2>
                            <p class="text-gray-400 mt-1 font-medium italic">Segmenta tu catálogo.</p>
                        </div>
                        <button @click="createModal = false"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>
                    <form action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-8 text-left">
                        @csrf
                        <div>
                            <label
                                class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nombre</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-5 flex items-center text-gray-300">
                                    <x-heroicon-o-tag class="w-6 h-6" />
                                </span>
                                <input type="text" name="nombre_categoria" required autofocus placeholder="Ej: Poleras"
                                    class="w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-indigo-500 transition-all outline-none text-lg font-bold shadow-inner">
                            </div>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="createModal = false"
                                class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                            <button type="submit"
                                class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95">Crear
                                Categoría</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- MODAL CREACION GÉNERO --}}
        <template x-if="createGeneroModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div @click="createGeneroModal = false"
                    class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl transition-opacity"></div>
                <div
                    class="relative bg-white rounded-[3rem] p-10 max-w-md w-full shadow-2xl animate-in zoom-in-95 duration-200 text-left">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-3xl font-black text-gray-900 leading-tight">Nuevo Género</h2>
                        <button @click="createGeneroModal = false"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>
                    <form action="{{ route('admin.generos.store') }}" method="POST" class="space-y-6 text-left">
                        @csrf
                        <div>
                            <label
                                class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nombre</label>
                            <input type="text" name="nombre_genero" required placeholder="Ej: Niños"
                                class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 transition-all outline-none text-lg font-bold">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="createGeneroModal = false"
                                class="flex-1 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-center">Cancelar</button>
                            <button type="submit"
                                class="flex-[2] py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg text-center">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

@endsection
