@extends('admin.layout')

@section('content')
    <div x-data="editProductoForm(@js(old('variantes') ?? $producto->variantes ?? []), '{{ $producto->id_producto }}', @js($errors->toArray()))"
        class="bg-gray-100 min-h-screen -m-8 p-8 relative">

        {{-- Toast flotante --}}
        <div class="fixed top-6 right-6 z-50 w-full max-w-sm"
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4 translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl border-l-4 p-5 flex items-start gap-3"
                :class="toast.type === 'success' ? 'border-black' : 'border-rose-500'">
                <div class="p-1.5 rounded-full flex-shrink-0"
                    :class="toast.type === 'success' ? 'bg-black' : 'bg-rose-500'">
                    <template x-if="toast.type === 'success'">
                        <x-heroicon-o-check class="w-4 h-4 text-white" />
                    </template>
                    <template x-if="toast.type === 'error'">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-white" />
                    </template>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black uppercase tracking-wide"
                        :class="toast.type === 'success' ? 'text-black' : 'text-rose-600'"
                        x-text="toast.type === 'success' ? 'Listo' : 'Falta algo'"></p>
                    <p class="text-sm font-semibold text-gray-700 mt-0.5" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false"
                    class="text-gray-300 hover:text-black transition-colors">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-10">
            <a href="{{ route('admin.productos.index') }}"
                class="p-3 bg-white border-2 border-gray-200 rounded-2xl text-gray-600 hover:border-black hover:text-black transition-all shadow-sm">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h1 class="text-4xl font-extrabold text-black tracking-tight">Editar Producto</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Modifica la información, variantes y galería.</p>
            </div>
        </div>

        <form x-ref="productForm" @submit.prevent="submitForm"
            action="{{ route('admin.productos.update', $producto->id_producto) }}"
            method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            @method('PUT')

            {{-- COLUMNA IZQUIERDA --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Card de Información General --}}
                <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-black rounded-lg text-white">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-black">Información del Producto</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nombre --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Nombre <span class="text-rose-500">*</span>
                            </label>
                            <input name="nombre_producto"
                                value="{{ old('nombre_producto', $producto->nombre_producto) }}" required
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.nombre_producto ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @input="clearError('nombre_producto')">
                            <template x-if="errors.nombre_producto">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.nombre_producto[0]"></span>
                            </template>
                        </div>

                        {{-- Marca --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Marca <span class="text-gray-400 normal-case font-semibold">(opcional)</span>
                            </label>
                            <input name="marca" value="{{ old('marca', $producto->marca) }}"
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.marca ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @input="clearError('marca')">
                            <template x-if="errors.marca">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.marca[0]"></span>
                            </template>
                        </div>

                        {{-- Precio --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Precio (S/) <span class="text-rose-500">*</span>
                            </label>
                            <input name="precio" type="number" step="0.01"
                                value="{{ old('precio', $producto->precio) }}" required
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.precio ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @input="clearError('precio')">
                            <template x-if="errors.precio">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.precio[0]"></span>
                            </template>
                        </div>

                        {{-- Precio oferta --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Precio Oferta <span class="text-gray-400 normal-case font-semibold">(opcional)</span>
                            </label>
                            <input name="precio_oferta" type="number" step="0.01"
                                value="{{ old('precio_oferta', $producto->precio_oferta) }}"
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.precio_oferta ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @input="clearError('precio_oferta')">
                            <template x-if="errors.precio_oferta">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.precio_oferta[0]"></span>
                            </template>
                        </div>

                        {{-- Género --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Género <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_genero" required
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.id_genero ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @change="clearError('id_genero')">
                                <option value="">Seleccionar Género</option>
                                @foreach($generos as $g)
                                    <option value="{{ $g->id_genero }}"
                                        {{ old('id_genero', $producto->id_genero) == $g->id_genero ? 'selected' : '' }}>
                                        {{ $g->nombre_genero }}
                                    </option>
                                @endforeach
                            </select>
                            <template x-if="errors.id_genero">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.id_genero[0]"></span>
                            </template>
                        </div>

                        {{-- Categoría --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Categoría <span class="text-rose-500">*</span>
                            </label>
                            <select name="id_categoria" required
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.id_categoria ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @change="clearError('id_categoria')">
                                <option value="">Seleccionar Categoría</option>
                                @foreach($categorias as $c)
                                    <option value="{{ $c->id_categoria }}"
                                        {{ old('id_categoria', $producto->id_categoria) == $c->id_categoria ? 'selected' : '' }}>
                                        {{ $c->nombre_categoria }}
                                    </option>
                                @endforeach
                            </select>
                            <template x-if="errors.id_categoria">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.id_categoria[0]"></span>
                            </template>
                        </div>

                        {{-- Estado --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                Estado <span class="text-rose-500">*</span>
                            </label>
                            <select name="estado_producto" required
                                class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all"
                                :class="errors.estado_producto ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                                @change="clearError('estado_producto')">
                                <option value="1"
                                    {{ old('estado_producto', $producto->estado_producto) == 1 ? 'selected' : '' }}>
                                    Activo</option>
                                <option value="0"
                                    {{ old('estado_producto', $producto->estado_producto) == 0 ? 'selected' : '' }}>
                                    Inactivo</option>
                            </select>
                            <template x-if="errors.estado_producto">
                                <span class="text-[11px] font-bold text-rose-500 ml-1"
                                    x-text="errors.estado_producto[0]"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Descripción <span class="text-gray-400 normal-case font-semibold">(opcional)</span>
                        </label>
                        <textarea name="descripcion" rows="4" maxlength="1000"
                            class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-medium text-sm text-black outline-none transition-all"
                            :class="errors.descripcion ? 'border-rose-400 focus:border-rose-500' : 'border-gray-200 focus:border-black'"
                            @input="clearError('descripcion')">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        <template x-if="errors.descripcion">
                            <span class="text-[11px] font-bold text-rose-500 ml-1"
                                x-text="errors.descripcion[0]"></span>
                        </template>
                    </div>
                </div>

                {{-- Card de Variantes --}}
                <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-black rounded-lg text-white">
                                <x-heroicon-o-swatch class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-black">Variantes de Stock</h2>
                        </div>
                        <button type="button" @click="addVariante"
                            class="flex items-center gap-2 text-xs font-black text-black border-2 border-black px-4 py-2 rounded-xl hover:bg-black hover:text-white transition-all">
                            <x-heroicon-o-plus-circle class="w-5 h-5" />
                            Añadir Variante
                        </button>
                    </div>

                    {{-- Hint sobre stock 0 --}}
                    <div class="mb-4 flex items-center gap-2 text-[11px] font-bold text-gray-500 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-gray-400 flex-shrink-0" />
                        <span>Para desactivar una variante existente, establece su stock en <strong class="text-black">0</strong>. No se eliminan variantes para preservar el historial de pedidos.</span>
                    </div>

                    <template x-if="errors.variantes">
                        <div
                            class="mb-4 p-3 bg-rose-50 border-2 border-rose-300 rounded-xl text-rose-600 text-xs font-bold">
                            <span x-text="errors.variantes[0]"></span>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <template x-for="(variante, index) in variantes" :key="variante.uid">
                            <div class="relative grid grid-cols-1 gap-3 p-5 rounded-2xl transition-all border-2"
                                :class="[
                                    isDuplicated(index) ? 'bg-rose-50 border-rose-300' : 'bg-gray-50 border-gray-200',
                                    variante.stock == 0 && variante.id_variante ? 'opacity-60' : ''
                                ]"
                                :style="variante.id_variante ? 'grid-template-columns: repeat(1, minmax(0, 1fr))' : ''"
                                x-data="{ isExisting: variante.id_variante !== null }">

                                <input type="hidden" :name="`variantes[${index}][id_variante]`"
                                    x-model="variante.id_variante">
                                <input type="hidden" :name="`variantes[${index}][sku]`" x-model="variante.sku">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3"
                                    :class="isExisting ? 'md:grid-cols-4' : 'md:grid-cols-4'">
                                    <div class="space-y-1" :class="isExisting ? 'md:col-span-1' : 'md:col-span-1'">
                                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                            Talla <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" :name="`variantes[${index}][talla]`"
                                            x-model="variante.talla" required
                                            @input="clearError(`variantes.${index}.talla`)"
                                            :disabled="isExisting"
                                            class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                                            :class="(errors[`variantes.${index}.talla`] || isDuplicated(index)) ? 'border-rose-300 text-rose-600' : 'border-gray-200 focus:border-black text-black'">
                                        <template x-if="errors[`variantes.${index}.talla`]">
                                            <span class="text-[10px] font-bold text-rose-500"
                                                x-text="errors[`variantes.${index}.talla`][0]"></span>
                                        </template>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                            Color <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" :name="`variantes[${index}][color]`"
                                            x-model="variante.color" required
                                            @input="clearError(`variantes.${index}.color`)"
                                            :disabled="isExisting"
                                            class="w-full px-4 py-3 rounded-xl border-2 font-bold text-sm outline-none transition-all bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                                            :class="(errors[`variantes.${index}.color`] || isDuplicated(index)) ? 'border-rose-300 text-rose-600' : 'border-gray-200 focus:border-black text-black'">
                                        <template x-if="errors[`variantes.${index}.color`]">
                                            <span class="text-[10px] font-bold text-rose-500"
                                                x-text="errors[`variantes.${index}.color`][0]"></span>
                                        </template>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                                            Stock <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" :name="`variantes[${index}][stock]`"
                                                x-model="variante.stock" required min="0"
                                                @input="clearError(`variantes.${index}.stock`)"
                                                class="w-full bg-white px-4 py-3 rounded-xl border-2 font-bold text-sm text-black outline-none transition-all"
                                                :class="[
                                                    errors[`variantes.${index}.stock`] ? 'border-rose-300' : 'border-gray-200 focus:border-black',
                                                    variante.stock == 0 && isExisting ? 'text-rose-500' : ''
                                                ]">
                                            {{-- Badge sin stock --}}
                                            <template x-if="variante.stock == 0 && isExisting">
                                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-black uppercase bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full">
                                                    Sin stock
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="errors[`variantes.${index}.stock`]">
                                            <span class="text-[10px] font-bold text-rose-500"
                                                x-text="errors[`variantes.${index}.stock`][0]"></span>
                                        </template>
                                    </div>

                                    {{-- Botón eliminar: SOLO para variantes nuevas --}}
                                    <div class="flex items-end justify-center pb-0.5"
                                        :class="isExisting ? 'hidden' : 'block'">
                                        <button type="button" @click="removeVariante(index)"
                                            class="p-3 text-rose-500 hover:text-white hover:bg-rose-500 border-2 border-rose-200 hover:border-rose-500 rounded-xl transition-all">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </div>

                                    {{-- Indicador para variantes existentes (ocupa el espacio del botón) --}}
                                    <div class="flex items-end justify-center pb-2"
                                        :class="isExisting ? 'block' : 'hidden'">
                                        <span class="text-[9px] font-black uppercase text-gray-400 tracking-wider">
                                            Guardada
                                        </span>
                                    </div>
                                </div>

                                <template x-if="isDuplicated(index)">
                                    <div
                                        class="flex items-center gap-1 text-[10px] font-black text-rose-600 uppercase mt-1 ml-1">
                                        <x-heroicon-s-exclamation-triangle class="w-4 h-4" />
                                        <span>Talla y Color repetidos</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="space-y-8">
                {{-- Imagen Principal --}}
                <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm space-y-4">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">
                        Imagen Principal
                    </label>
                    <div class="relative group aspect-square rounded-2xl overflow-hidden bg-gray-50 border-2 border-dashed transition-all flex items-center justify-center"
                        :class="errors.imagen ? 'border-rose-400' : 'border-gray-300 hover:border-black'">

                        {{-- Preview nueva imagen --}}
                        <template x-if="imgPrincipalPreview">
                            <img :src="imgPrincipalPreview" class="w-full h-full object-cover">
                        </template>

                        {{-- Imagen actual del producto --}}
                        <template x-if="!imgPrincipalPreview">
                            @if($producto->imagen)
                                <img src="{{ asset('productos/' . $producto->imagen) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="text-center p-4">
                                    <x-heroicon-o-camera class="w-10 h-10 text-gray-400 mx-auto mb-2" />
                                    <span class="text-xs font-bold text-gray-500">Subir foto principal</span>
                                </div>
                            @endif
                        </template>

                        {{-- Overlay hover --}}
                        <div
                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all bg-black/20 pointer-events-none">
                            <x-heroicon-o-camera class="w-10 h-10 text-white" />
                        </div>

                        <input type="file" name="imagen" @change="previewPrincipal"
                            class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                    <template x-if="errors.imagen">
                        <span class="text-[11px] font-bold text-rose-500 ml-1"
                            x-text="errors.imagen[0]"></span>
                    </template>
                </div>

                {{-- Galería de Imágenes --}}
                <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm space-y-6">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-500 ml-1">Galería de
                            Imágenes</label>
                        <span class="text-xs font-black text-black"
                            x-text="`${galeriaExistenteCount + galeriaFiles.length} foto(s)`"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        {{-- Imágenes existentes --}}
                        @forelse($producto->galeria ?? [] as $img)
                            <div
                                class="relative aspect-square rounded-xl overflow-hidden group border-2 border-gray-200 shadow-sm">
                                <img src="{{ asset('productos/' . $img) }}" class="w-full h-full object-cover">
                                <label
                                    class="absolute inset-0 bg-rose-500/80 opacity-0 group-hover:opacity-100 transition-all cursor-pointer flex flex-col items-center justify-center text-white text-center p-1">
                                    <input type="checkbox" name="galeria_eliminar[]" value="{{ $img }}"
                                        class="hidden peer">
                                    <x-heroicon-o-trash class="w-5 h-5 mb-1" />
                                    <span
                                        class="text-[8px] font-black uppercase peer-checked:hidden">Eliminar</span>
                                    <span
                                        class="hidden peer-checked:block text-[8px] font-black uppercase">¡Marcado!</span>
                                </label>
                            </div>
                        @empty
                        @endforelse

                        {{-- Nuevas fotos agregadas dinámicamente --}}
                        <template x-for="(item, index) in galeriaFiles" :key="item.id">
                            <div
                                class="relative aspect-square rounded-xl overflow-hidden border-2 border-black shadow-sm">
                                <img :src="item.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeGaleriaFile(index)"
                                    class="absolute top-1 right-1 p-1 bg-black text-white rounded-full opacity-80 hover:opacity-100 transition-all shadow-md">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <div
                        class="relative w-full py-6 border-2 border-dashed border-gray-300 rounded-2xl hover:border-black hover:bg-gray-50 transition-all text-center">
                        <x-heroicon-o-plus class="w-6 h-6 text-gray-500 mx-auto mb-1" />
                        <span class="text-[10px] font-black text-gray-500 uppercase">Añadir fotos a la
                            galería</span>
                        <input type="file" x-ref="galeriaInput" multiple @change="addGaleriaFiles"
                            class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" :disabled="hasErrors() || submitting"
                        :class="(hasErrors() || submitting) ? 'bg-gray-300 cursor-not-allowed' : 'bg-black hover:bg-gray-800'"
                        class="w-full py-5 text-white font-black rounded-2xl shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <template x-if="submitting">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <span
                            x-text="submitting ? 'Guardando...' : (hasErrors() ? 'Corrige los errores' : 'Guardar Cambios')"></span>
                    </button>
                    <a href="{{ route('admin.productos.index') }}"
                        class="w-full py-5 bg-white text-gray-600 font-bold rounded-2xl text-center border-2 border-gray-200 hover:border-black hover:text-black transition-all">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>

        <script>
            function editProductoForm(initialVariantes = [], productoId = '', initialErrors = {}) {
                return {
                    variantes: [],
                    imgPrincipalPreview: null,
                    galeriaFiles: [],
                    galeriaExistenteCount: {{ count($producto->galeria ?? []) }},
                    errors: initialErrors,
                    submitting: false,
                    toast: { show: false, type: 'error', message: '' },
                    _toastTimer: null,

                    init() {
                        if (Array.isArray(initialVariantes) && initialVariantes.length > 0) {
                            this.variantes = initialVariantes.map(v => ({
                                uid: crypto.randomUUID(),
                                id_variante: v.id_variante ?? null,
                                talla: v.talla ?? '',
                                color: v.color ?? '',
                                stock: v.stock ?? 0,
                                sku: v.sku && v.sku.trim() !== '' ? v.sku : this.generarSkuCorto(productoId)
                            }));
                        } else {
                            this.addVariante();
                        }
                    },
                    generarSkuCorto(id = '') {
                        const idProd = id ? id : 'NEW';
                        const randomHash = Math.random().toString(36).substring(2, 6).toUpperCase();
                        return `PROD-${idProd}-${randomHash}`;
                    },
                    previewPrincipal(event) {
                        if (this.imgPrincipalPreview) URL.revokeObjectURL(this.imgPrincipalPreview);
                        const file = event.target.files[0];
                        if (file) this.imgPrincipalPreview = URL.createObjectURL(file);
                        this.clearError('imagen');
                    },
                    addGaleriaFiles(event) {
                        const files = Array.from(event.target.files);
                        if (this.galeriaFiles.length + files.length > 10) {
                            this.showToast('error', 'Máximo 10 imágenes nuevas en la galería.');
                            return;
                        }
                        files.forEach(file => {
                            this.galeriaFiles.push({
                                id: crypto.randomUUID(),
                                file: file,
                                url: URL.createObjectURL(file)
                            });
                        });
                        this.syncGaleriaInput();
                        event.target.value = '';
                    },
                    removeGaleriaFile(index) {
                        URL.revokeObjectURL(this.galeriaFiles[index].url);
                        this.galeriaFiles.splice(index, 1);
                        this.syncGaleriaInput();
                    },
                    syncGaleriaInput() {
                        const dt = new DataTransfer();
                        this.galeriaFiles.forEach(item => dt.items.add(item.file));
                        this.$refs.galeriaInput.files = dt.files;
                    },
                    addVariante() {
                        this.variantes.push({
                            uid: crypto.randomUUID(),
                            id_variante: null,
                            talla: '',
                            color: '',
                            stock: 0,
                            sku: this.generarSkuCorto(productoId)
                        });
                    },
                    removeVariante(index) {
                        // Solo permite eliminar variantes NUEVAS (sin id_variante)
                        if (this.variantes[index].id_variante === null && this.variantes.length > 1) {
                            this.variantes.splice(index, 1);
                        }
                    },
                    isDuplicated(index) {
                        const current = this.variantes[index];
                        if (!current.talla.trim() || !current.color.trim()) return false;
                        return this.variantes.some((v, i) => i !== index &&
                            v.talla.toLowerCase().trim() === current.talla.toLowerCase().trim() &&
                            v.color.toLowerCase().trim() === current.color.toLowerCase().trim());
                    },
                    hasErrors() {
                        return this.variantes.some((_, i) => this.isDuplicated(i));
                    },
                    clearError(key) {
                        if (this.errors[key]) {
                            const copy = { ...this.errors };
                            delete copy[key];
                            this.errors = copy;
                        }
                    },
                    showToast(type, message) {
                        this.toast = { show: true, type, message };
                        clearTimeout(this._toastTimer);
                        this._toastTimer = setTimeout(() => { this.toast.show = false; }, 5000);
                    },
                    scrollToFirstError() {
                        const firstKey = Object.keys(this.errors)[0];
                        if (!firstKey) return;
                        const el = document.querySelector(`[name="${firstKey}"], [name^="${firstKey.split('.')[0]}"]`);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    },
                    async submitForm() {
                        if (this.hasErrors()) {
                            this.showToast('error', 'Corrige las variantes duplicadas antes de guardar.');
                            return;
                        }

                        this.submitting = true;

                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfMeta) {
                            this.submitting = false;
                            this.showToast('error', 'Falta el meta tag csrf-token en el layout. Avisa al desarrollador.');
                            return;
                        }

                        const formData = new FormData(this.$refs.productForm);
                        formData.append('_method', 'PUT');

                        try {
                            const response = await fetch(this.$refs.productForm.action, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfMeta.content,
                                },
                                body: formData,
                            });

                            const data = await response.json();

                            if (response.ok) {
                                this.showToast('success', data.message ?? 'Cambios guardados correctamente.');
                                setTimeout(() => { window.location.href = data.redirect; }, 900);
                                return;
                            }

                            if (response.status === 422) {
                                this.errors = data.errors || {};
                                const primerError = Object.values(this.errors)[0]?.[0];
                                this.showToast('error', primerError ?? 'Revisa los campos marcados en rojo.');
                                this.$nextTick(() => this.scrollToFirstError());
                            } else {
                                console.error('Server error:', response.status, data);
                                this.showToast('error', 'Ocurrió un error inesperado. Intenta nuevamente.');
                            }
                        } catch (e) {
                            console.error(e);
                            this.showToast('error', 'No se pudo conectar con el servidor.');
                        } finally {
                            this.submitting = false;
                        }
                    }
                }
            }
        </script>
    </div>
@endsection