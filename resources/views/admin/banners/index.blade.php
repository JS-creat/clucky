@extends('admin.layout')

@section('content')
<div x-data="{ createModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Marketing</p>
            <h1 class="text-3xl font-light text-gray-900 tracking-tight">Banners</h1>
            <p class="text-gray-500 mt-1 text-sm">Gestiona los banners del carrusel principal.</p>
        </div>
        <button @click="createModal = true"
            class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm w-full sm:w-auto">
            <x-heroicon-o-plus class="w-4 h-4" />
            Nuevo Banner
        </button>
    </div>

    <hr class="border-gray-200 mb-8">

    {{-- GRID DE BANNERS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($banners as $banner)
            <div x-data="{ confirmDelete: false }"
                class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-gray-400 transition-all duration-300">

                {{-- Imagen --}}
                <div class="relative h-44 sm:h-48 bg-gray-50 border-b border-gray-100 overflow-hidden">
                    <img src="{{ asset('banners/' . $banner->imagen) }}"
                         alt="{{ $banner->titulo }}"
                         class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
                         onerror="this.src='https://placehold.co/800x400/f9fafb/9ca3af?text=Sin+imagen'">

                    {{-- Badges --}}
                    <div class="absolute top-3 left-3 flex gap-2">
                        <span class="bg-gray-900/80 backdrop-blur-md text-white text-[11px] font-medium px-2.5 py-1 rounded-md">
                            # {{ $banner->orden }}
                        </span>
                    </div>

                    {{-- Estado --}}
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-[11px] font-medium backdrop-blur-md border border-gray-200/50
                            {{ $banner->estado ? 'bg-white/90 text-gray-900' : 'bg-gray-900/80 text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $banner->estado ? 'bg-black' : 'bg-white' }}"></span>
                            {{ $banner->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="p-5">
                    <h3 class="text-base font-semibold text-gray-900 leading-snug">{{ $banner->titulo }}</h3>
                    @if($banner->subtitulo)
                        <p class="text-gray-500 text-xs mt-0.5 font-normal">{{ $banner->subtitulo }}</p>
                    @endif
                    @if($banner->descripcion)
                        <p class="text-gray-600 text-xs mt-2 line-clamp-2 leading-relaxed">{{ $banner->descripcion }}</p>
                    @endif

                    {{-- Acciones --}}
                    <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                        <button @click="$dispatch('open-edit-banner', {{ $banner->toJson() }})"
                            class="font-medium text-gray-700 hover:text-black flex items-center gap-1.5 transition-colors">
                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                            Editar
                        </button>
                        <div class="flex items-center gap-3">
                            <form action="{{ route('admin.banners.toggle', $banner->id_banner) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="font-medium text-gray-500 hover:text-black transition-colors">
                                    {{ $banner->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <span class="text-gray-200">|</span>
                            <button @click="confirmDelete = true"
                                class="font-medium text-gray-400 hover:text-red-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Confirmar Eliminar --}}
                <template x-if="confirmDelete">
                    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                        <div @click="confirmDelete = false" class="absolute inset-0 bg-gray-900/20 backdrop-blur-xs"></div>
                        <div class="relative bg-white border border-gray-200 rounded-xl p-6 max-w-sm w-full shadow-xl text-center mx-4">
                            <div class="w-10 h-10 bg-gray-50 border border-gray-200 text-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">¿Eliminar banner?</h3>
                            <p class="text-gray-500 font-normal mb-5 text-xs">
                                Estás a punto de eliminar <span class="font-semibold text-gray-800">"{{ $banner->titulo }}"</span>. Esta acción no se puede deshacer.
                            </p>
                            <form action="{{ route('admin.banners.destroy', $banner->id_banner) }}" method="POST" class="flex gap-2">
                                @csrf @method('DELETE')
                                <button type="button" @click="confirmDelete = false"
                                    class="flex-1 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-200 rounded-lg text-xs transition-all">Cancelar</button>
                                <button type="submit"
                                    class="flex-1 py-2 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-xl border border-gray-200 border-dashed">
                <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center mx-auto mb-3 border border-gray-100">
                    <x-heroicon-o-photo class="w-6 h-6" />
                </div>
                <p class="text-gray-900 font-medium text-sm">No hay banners registrados</p>
                <p class="text-gray-400 text-xs mt-0.5">Crea tu primer banner para el carrusel.</p>
            </div>
        @endforelse
    </div>

    {{-- ===================== MODAL CREAR BANNER ===================== --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/30 backdrop-blur-xs"></div>
            <div class="relative bg-white border border-gray-200 rounded-t-xl sm:rounded-xl p-6 sm:p-8 w-full sm:max-w-xl shadow-2xl max-h-[92vh] overflow-y-auto">

                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Nuevo Banner</h2>
                        <p class="text-gray-400 text-xs mt-0.5">Configura las opciones básicas del banner.</p>
                    </div>
                    <button @click="createModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-black hover:bg-gray-50 transition">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Título *</label>
                            <input type="text" name="titulo" required placeholder="Ej: Nueva Colección Verano"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Subtítulo <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="subtitulo" placeholder="Ej: Descubre lo nuevo"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Orden <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="number" name="orden" value="0" min="0"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <textarea name="descripcion" rows="2" placeholder="Descripción breve del banner..."
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm resize-none transition-colors"></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                Estado <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <select name="estado"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Imagen *</label>
                            <label x-data="{ fileName: '' }"
                                class="flex flex-col items-center justify-center w-full h-24 bg-gray-50/50 border border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-500 hover:bg-gray-50 transition-all">
                                <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-gray-400 mb-1" />
                                <span class="text-xs font-medium text-gray-600 px-4 text-center"
                                    x-text="fileName || 'Seleccionar archivo de imagen'"></span>
                                <span class="text-[10px] text-gray-400 mt-0.5">JPG, PNG, WEBP — Max 2MB</span>
                                <input type="file" name="imagen" required accept="image/*" class="hidden"
                                    @change="fileName = $event.target.files[0]?.name || ''">
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="createModal = false"
                            class="flex-1 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-200 text-xs transition-all">Cancelar</button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Crear Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ===================== MODAL EDITAR BANNER ===================== --}}
    <div x-data="{ editModal: false, banner: {} }"
         @open-edit-banner.window="banner = $event.detail; editModal = true">
        <template x-if="editModal">
            <div class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center">
                <div @click="editModal = false" class="absolute inset-0 bg-gray-900/30 backdrop-blur-xs"></div>
                <div class="relative bg-white border border-gray-200 rounded-t-xl sm:rounded-xl p-6 sm:p-8 w-full sm:max-w-xl shadow-2xl max-h-[92vh] overflow-y-auto">

                    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Editar Banner</h2>
                            <p class="text-gray-400 text-xs mt-0.5">Modifica los detalles del banner seleccionado.</p>
                        </div>
                        <button @click="editModal = false"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-black hover:bg-gray-50 transition">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>

                    <form :action="`/admin/banners/${banner.id_banner}`" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Título *</label>
                                <input type="text" name="titulo" :value="banner.titulo" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Subtítulo <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <input type="text" name="subtitulo" :value="banner.subtitulo"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Orden <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <input type="number" name="orden" :value="banner.orden" min="0"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <textarea name="descripcion" rows="2"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm resize-none transition-colors"
                                    x-init="$el.value = banner.descripcion ?? ''"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Estado <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <select name="estado"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors"
                                    x-init="$el.value = banner.estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Imagen actual</label>
                                <div class="relative w-full h-28 rounded-lg border border-gray-200 overflow-hidden bg-gray-50">
                                    <img :src="`/banners/${banner.imagen}`"
                                         :alt="banner.titulo"
                                         class="w-full h-full object-cover">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                    Reemplazar Imagen <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <label x-data="{ fileName: '' }"
                                    class="flex flex-col items-center justify-center w-full h-20 bg-gray-50/50 border border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-500 hover:bg-gray-50 transition-all">
                                    <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-400 mb-1" />
                                    <span class="text-xs font-medium text-gray-600 px-4 text-center"
                                        x-text="fileName || 'Cambiar imagen'"></span>
                                    <input type="file" name="imagen" accept="image/*" class="hidden"
                                        @change="fileName = $event.target.files[0]?.name || ''">
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="editModal = false"
                                class="flex-1 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-200 text-xs transition-all">Cancelar</button>
                            <button type="submit"
                                class="flex-1 py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection