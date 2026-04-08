@extends('admin.layout')

@section('content')
<div x-data="{ createModal: false, deleteId: null, deleteModal: false, editBanner: null, editModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-indigo-500 mb-2">Marketing</p>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Banners</h1>
            <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona los banners del carrusel principal.</p>
        </div>
        <button @click="createModal = true"
            class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
            <x-heroicon-o-plus class="w-6 h-6" />
            Nuevo Banner
        </button>
    </div>

    <hr class="border-gray-100 mb-10">

    {{-- GRID DE BANNERS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($banners as $banner)
            <div x-data="{ confirmDelete: false, confirmEdit: false }"
                class="group relative bg-white border border-gray-100 rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500">

                {{-- Imagen --}}
                <div class="relative h-52 bg-gray-100 overflow-hidden">
                    <img src="{{ asset('storage/' . $banner->imagen) }}"
                         alt="{{ $banner->titulo }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

                    {{-- Overlay con orden --}}
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="bg-black/60 backdrop-blur text-white text-xs font-black px-3 py-1.5 rounded-full">
                            # {{ $banner->orden }}
                        </span>
                        @if($banner->etiqueta)
                        <span class="bg-indigo-600/90 backdrop-blur text-white text-xs font-black px-3 py-1.5 rounded-full">
                            {{ $banner->etiqueta }}
                        </span>
                        @endif
                    </div>

                    {{-- Estado --}}
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider
                            {{ $banner->estado ? 'bg-emerald-500/90 text-white' : 'bg-gray-400/80 text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $banner->estado ? 'bg-white' : 'bg-gray-200' }}"></span>
                            {{ $banner->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="p-7">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $banner->titulo }}</h3>
                    @if($banner->subtitulo)
                        <p class="text-gray-500 font-medium mt-1">{{ $banner->subtitulo }}</p>
                    @endif
                    @if($banner->descripcion)
                        <p class="text-gray-400 text-sm mt-2 line-clamp-2">{{ $banner->descripcion }}</p>
                    @endif

                    @if($banner->texto_boton)
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-xl">
                            <x-heroicon-o-cursor-arrow-rays class="w-4 h-4" />
                            {{ $banner->texto_boton }}
                        </span>
                    </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="pt-5 mt-5 border-t border-gray-50 flex items-center justify-between">
                        <button @click="$dispatch('open-edit-banner', {{ $banner->toJson() }})"
                            class="font-bold text-gray-600 hover:text-indigo-600 flex items-center gap-2 transition-colors">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                            Editar
                        </button>
                        <div class="flex items-center gap-4">
                            <form action="{{ route('admin.banners.toggle', $banner->id_banner) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="font-bold text-sm {{ $banner->estado ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600' }} transition-colors">
                                    {{ $banner->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <button @click="confirmDelete = true"
                                class="font-bold text-sm text-rose-500 hover:text-rose-600 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Confirmar Eliminar --}}
                <template x-if="confirmDelete">
                    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                        <div @click="confirmDelete = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                        <div class="relative bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center">
                            <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <x-heroicon-o-trash class="w-10 h-10" />
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-2">¿Eliminar banner?</h3>
                            <p class="text-gray-500 font-medium mb-8">
                                Estás a punto de eliminar <span class="font-bold text-gray-800">"{{ $banner->titulo }}"</span>. Esta acción no se puede deshacer.
                            </p>
                            <form action="{{ route('admin.banners.destroy', $banner->id_banner) }}" method="POST" class="flex gap-3">
                                @csrf @method('DELETE')
                                <button type="button" @click="confirmDelete = false"
                                    class="flex-1 py-3 bg-gray-100 text-gray-500 font-bold rounded-xl">Cancelar</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-rose-500 text-white font-bold rounded-xl shadow-lg">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @empty
            <div class="col-span-full py-24 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-photo class="w-10 h-10 text-gray-300" />
                </div>
                <p class="text-gray-400 font-bold text-lg">No hay banners registrados.</p>
                <p class="text-gray-300 font-medium mt-1">Crea tu primer banner para el carrusel.</p>
            </div>
        @endforelse
    </div>

    {{-- ===================== MODAL CREAR BANNER ===================== --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
            <div class="relative bg-white rounded-[3rem] p-10 max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900">Nuevo Banner</h2>
                        <p class="text-gray-400 mt-1 font-medium italic">Configura el banner para el carrusel.</p>
                    </div>
                    <button @click="createModal = false"
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Título *</label>
                            <input type="text" name="titulo" required placeholder="Ej: Nueva Colección Verano"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-bold transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Subtítulo</label>
                            <input type="text" name="subtitulo" placeholder="Ej: Descubre lo nuevo"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Etiqueta</label>
                            <input type="text" name="etiqueta" placeholder="Ej: ¡Nuevo!"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descripción</label>
                            <textarea name="descripcion" rows="3" placeholder="Descripción breve del banner..."
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-medium transition-all resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Texto del Botón</label>
                            <input type="text" name="texto_boton" placeholder="Ej: Ver colección"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">URL del Botón</label>
                            <input type="text" name="url_boton" placeholder="Ej: /productos"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Orden</label>
                            <input type="number" name="orden" value="0" min="0"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-bold transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Estado</label>
                            <select name="estado"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Imagen *</label>
                            <label class="flex flex-col items-center justify-center w-full h-36 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition-all group">
                                <x-heroicon-o-cloud-arrow-up class="w-10 h-10 text-gray-300 group-hover:text-indigo-400 transition-colors mb-2" />
                                <span class="text-sm font-bold text-gray-400 group-hover:text-indigo-500">Haz clic para subir imagen</span>
                                <span class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP — Max 2MB</span>
                                <input type="file" name="imagen" required accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="createModal = false"
                            class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                        <button type="submit"
                            class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95">Crear Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ===================== MODAL EDITAR BANNER ===================== --}}
    {{-- Se controla con un evento de Alpine --}}
    <div x-data="{ editModal: false, banner: {} }"
         @open-edit-banner.window="banner = $event.detail; editModal = true">
        <template x-if="editModal">
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div @click="editModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">

                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-3xl font-black text-gray-900">Editar Banner</h2>
                            <p class="text-gray-400 mt-1 font-medium italic">Modifica los datos del banner.</p>
                        </div>
                        <button @click="editModal = false"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <form :action="`/admin/banners/${banner.id_banner}`" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Título *</label>
                                <input type="text" name="titulo" :value="banner.titulo" required
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-bold transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Subtítulo</label>
                                <input type="text" name="subtitulo" :value="banner.subtitulo"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Etiqueta</label>
                                <input type="text" name="etiqueta" :value="banner.etiqueta"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descripción</label>
                                <textarea name="descripcion" rows="3" :value="banner.descripcion"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-medium transition-all resize-none"
                                    x-text="banner.descripcion"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Texto del Botón</label>
                                <input type="text" name="texto_boton" :value="banner.texto_boton"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">URL del Botón</label>
                                <input type="text" name="url_boton" :value="banner.url_boton"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Orden</label>
                                <input type="number" name="orden" :value="banner.orden" min="0"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-bold transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Estado</label>
                                <select name="estado"
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-base font-semibold transition-all">
                                    <option value="1" :selected="banner.estado == 1">Activo</option>
                                    <option value="0" :selected="banner.estado == 0">Inactivo</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nueva Imagen <span class="font-medium normal-case text-gray-300">(opcional)</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-28 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition-all group">
                                    <x-heroicon-o-arrow-path class="w-8 h-8 text-gray-300 group-hover:text-indigo-400 transition-colors mb-1" />
                                    <span class="text-sm font-bold text-gray-400 group-hover:text-indigo-500">Reemplazar imagen</span>
                                    <input type="file" name="imagen" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="editModal = false"
                                class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                            <button type="submit"
                                class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection