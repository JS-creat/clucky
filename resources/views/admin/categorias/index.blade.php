@extends('admin.layout')

@section('content')

<div x-data="{ createModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Categorías</h1>
            <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona los segmentos de tu catálogo.</p>
        </div>

        <button @click="createModal = true"
           class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
            <x-heroicon-o-plus class="w-6 h-6" />
            Nueva Categoría
        </button>
    </div>

    {{-- GRID DE CATEGORÍAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($categorias as $categoria)
            <div x-data="{ confirmModal: false, editModal: false }"
                 class="group relative bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-2xl hover:border-indigo-100 transition-all duration-500">

                {{-- Badge de Estado --}}
                <div class="absolute top-8 right-8">
                    @if($categoria->estado_categoria)
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-100">
                            Inactivo
                        </span>
                    @endif
                </div>

                <div class="mb-10 text-center sm:text-left">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 mx-auto sm:ml-0">
                        <x-heroicon-o-folder class="w-8 h-8" />
                    </div>

                    <h3 class="text-2xl font-black text-gray-800 leading-tight group-hover:text-indigo-600 transition-colors">
                        {{ $categoria->nombre_categoria }}
                    </h3>

                    <p class="mt-3 text-gray-400 font-medium flex items-center justify-center sm:justify-start gap-2">
                        <x-heroicon-o-cube class="w-5 h-5 text-indigo-400" />
                        <span class="text-indigo-600 font-bold leading-none">{{ $categoria->productos->count() }}</span> productos
                    </p>
                </div>

                {{-- Footer Acciones --}}
                <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                    <button @click="editModal = true"
                       class="flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-indigo-600 transition group/link">
                        <div class="bg-gray-50 p-2.5 rounded-xl group-hover/link:bg-indigo-50 transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </div>
                        Editar
                    </button>

                    <button @click="confirmModal = true"
                            class="flex items-center gap-2 text-sm font-black px-4 py-2 rounded-xl transition {{ $categoria->estado_categoria ? 'text-rose-500 hover:bg-rose-50' : 'text-emerald-500 hover:bg-emerald-50' }}">
                        <x-heroicon-o-power class="w-5 h-5" />
                        {{ $categoria->estado_categoria ? 'Desactivar' : 'Activar' }}
                    </button>
                </div>

                {{-- MODAL EDITAR --}}
                <template x-if="editModal">
                    <div class="fixed inset-0 z-[90] flex items-center justify-center p-4">
                        <div @click="editModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
                        <div class="relative bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-200">
                            <div class="flex justify-between items-center mb-10">
                                <div class="text-left">
                                    <h2 class="text-3xl font-black text-gray-900 leading-tight">Editar Categoría</h2>
                                </div>
                                <button @click="editModal = false" class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                                    <x-heroicon-o-x-mark class="w-6 h-6" />
                                </button>
                            </div>

                            <form action="{{ route('admin.categorias.update', $categoria->id_categoria) }}" method="POST" class="space-y-8">
                                @csrf @method('PUT')
                                <div class="text-left">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nombre</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-5 flex items-center text-gray-300">
                                            <x-heroicon-o-tag class="w-6 h-6" />
                                        </span>
                                        <input type="text" name="nombre_categoria" value="{{ old('nombre_categoria', $categoria->nombre_categoria) }}" required
                                               class="w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none text-lg font-bold shadow-inner">
                                    </div>
                                </div>

                                <div class="flex gap-4 pt-4">
                                    <button type="button" @click="editModal = false" class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                                    <button type="submit" class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- MODAL CONFIRMACIÓN (ACTIVAR/DESACTIVAR) --}}
                <template x-if="confirmModal">
                    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div @click="confirmModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
                        <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center">
                            <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-6">
                                <x-heroicon-o-exclamation-triangle class="w-10 h-10" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">¿Confirmar cambio?</h3>
                            <p class="text-gray-500 mb-10 leading-relaxed">Estás a punto de cambiar la visibilidad de <strong>"{{ $categoria->nombre_categoria }}"</strong>.</p>
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('admin.categorias.toggle', $categoria->id_categoria) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition shadow-lg">Sí, confirmar</button>
                                </form>
                                <button @click="confirmModal = false" class="w-full py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center bg-gray-50/50 rounded-[4rem] border-4 border-dashed border-gray-100">
                <x-heroicon-o-archive-box class="w-24 h-24 text-gray-200 mb-6" />
                <h3 class="text-2xl font-bold text-gray-400 text-center">No hay categorías</h3>
            </div>
        @endforelse
    </div>

    {{-- MODAL CREACIÓN --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[90] flex items-center justify-center p-4">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>

            <div class="relative bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl animate-in zoom-in-95 duration-200">
                <div class="flex justify-between items-center mb-10">
                    <div class="text-left">
                        <h2 class="text-3xl font-black text-gray-900 leading-tight">Nueva Categoría</h2>
                        <p class="text-gray-400 mt-1 font-medium italic">Agrega un nuevo segmento al catálogo.</p>
                    </div>
                    <button @click="createModal = false" class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <form action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="text-left">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nombre</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-5 flex items-center text-gray-300">
                                <x-heroicon-o-tag class="w-6 h-6" />
                            </span>
                            <input type="text" name="nombre_categoria" required autofocus
                                   class="w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-transparent rounded-[1.5rem] focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none text-lg font-bold placeholder:text-gray-300 shadow-inner"
                                   placeholder="Ej: Accesorios Deportivos">
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-6 bg-indigo-50/30 rounded-[2rem] border border-indigo-50">
                        <div class="flex gap-4 items-center">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-indigo-500">
                                <x-heroicon-o-eye class="w-6 h-6" />
                            </div>
                            <p class="font-bold text-gray-800 text-lg leading-tight">Estado Activo</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="estado_categoria" value="1" checked class="sr-only peer">
                            <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all shadow-inner"></div>
                        </label>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="createModal = false"
                                class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95">
                            Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

@endsection
