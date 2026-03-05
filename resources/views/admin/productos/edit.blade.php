@extends('admin.layout')

@section('content')
<div x-data="editProductoForm(@js($producto->variantes ?? []))">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-12">
        <a href="{{ route('admin.productos.index') }}" class="p-3 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-indigo-600 transition-all shadow-sm">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Editar Producto</h1>
        </div>
    </div>

    {{-- Errores con estilo --}}
    @if ($errors->any())
        <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 rounded-2xl flex gap-4 items-start animate-pulse">
            <x-heroicon-s-x-circle class="w-6 h-6 text-rose-500 flex-shrink-0" />
            <div>
                <h3 class="font-bold text-rose-800">Hay errores en los datos:</h3>
                <ul class="text-rose-600 text-sm mt-1 list-disc pl-4 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

        {{-- COLUMNA IZQUIERDA: DATOS Y VARIANTES --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Card de Información General --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Información del Producto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Nombre</label>
                        <input name="nombre_producto" value="{{ old('nombre_producto', $producto->nombre_producto) }}"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Marca</label>
                        <input name="marca" value="{{ old('marca', $producto->marca) }}"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Precio (S/)</label>
                        <input name="precio" type="number" step="0.01" value="{{ old('precio', $producto->precio) }}"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold text-indigo-600" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Precio Oferta</label>
                        <input name="precio_oferta" type="number" step="0.01" value="{{ old('precio_oferta', $producto->precio_oferta) }}"
                               class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all outline-none font-bold text-rose-500">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Género</label>
                        <select name="id_genero" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 transition-all outline-none font-bold appearance-none">
                            @foreach($generos as $g)
                                <option value="{{ $g->id_genero }}" {{ $producto->id_genero == $g->id_genero ? 'selected' : '' }}>{{ $g->nombre_genero }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Categoría</label>
                        <select name="id_categoria" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 transition-all outline-none font-bold appearance-none">
                            @foreach($categorias as $c)
                                <option value="{{ $c->id_categoria }}" {{ $producto->id_categoria == $c->id_categoria ? 'selected' : '' }}>{{ $c->nombre_categoria }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Descripción</label>
                    <textarea name="descripcion" rows="4" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 transition-all outline-none font-medium text-gray-600">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>
            </div>

            {{-- Card de Variantes --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                            <x-heroicon-o-swatch class="w-5 h-5" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Variantes de Stock</h2>
                    </div>
                    <button type="button" @click="addVariante" class="flex items-center gap-2 text-sm font-black text-indigo-600 hover:text-indigo-700 transition">
                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                        Añadir Variante
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(variante, index) in variantes" :key="variante.uid">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 p-4 bg-gray-50 rounded-3xl items-end group">
                            <input type="hidden" :name="`variantes[${index}][id_variante]`" x-model="variante.id_variante">

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Talla</label>
                                <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Color</label>
                                <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color" class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-1">Stock</label>
                                <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock" class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-sm shadow-sm" min="0" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-1">SKU</label>
                                <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" class="w-full bg-white px-4 py-3 rounded-xl border-none font-bold text-[10px] shadow-sm tracking-tighter">
                            </div>

                            <div class="flex items-center justify-center pb-1">
                                <button type="button" @click="removeVariante(index)"
                                        class="p-3 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: IMÁGENES --}}
        <div class="space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-8">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <x-heroicon-o-photo class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Imágenes</h2>
                </div>

                {{-- Imagen Principal Actual --}}
                <div class="space-y-4 text-center">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Imagen Principal</label>
                    <div class="relative group mx-auto w-48 h-48">
                        <div class="w-full h-full rounded-[2rem] overflow-hidden border-4 border-white shadow-xl bg-gray-100">
                            <img :src="imagePreview ? imagePreview : '{{ asset('productos/' . $producto->imagen) }}'" class="w-full h-full object-cover transition-opacity duration-300">
                        </div>
                        <label class="absolute inset-0 flex items-center justify-center bg-indigo-600/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-[2rem] cursor-pointer">
                            <x-heroicon-o-camera class="w-10 h-10 text-white" />
                            <input type="file" name="imagen" class="hidden" @change="previewImage">
                        </label>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 italic">Clic sobre la imagen para cambiarla</p>
                </div>

                <hr class="border-gray-50">

                {{-- Galería Actual con Selección para Eliminar --}}
                <div class="space-y-4">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Galería Actual</label>
                    @if(!empty($producto->galeria))
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($producto->galeria as $img)
                                <div x-data="{ selected: false }" class="relative group aspect-square rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                                    <img src="{{ asset('productos/' . $img) }}" class="w-full h-full object-cover">
                                    <label class="absolute inset-0 flex items-center justify-center cursor-pointer transition-all"
                                           :class="selected ? 'bg-rose-500/80' : 'bg-transparent group-hover:bg-black/20'">
                                        <input type="checkbox" name="galeria_eliminar[]" value="{{ $img }}" @click="selected = !selected" class="hidden">
                                        <x-heroicon-o-trash x-show="!selected" class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                        <x-heroicon-s-trash x-show="selected" class="w-6 h-6 text-white" />
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[10px] font-bold text-rose-400 leading-tight">Haz clic en las fotos que quieras eliminar (se pondrán en rojo).</p>
                    @else
                        <div class="p-4 bg-gray-50 rounded-2xl text-center">
                            <p class="text-xs font-bold text-gray-400 uppercase">Sin imágenes extra</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-3 pt-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Agregar fotos nuevas</label>
                    <input type="file" name="galeria[]" multiple accept="image/*"
                           class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition">
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95">
                    Guardar Cambios
                </button>
                <a href="{{ route('admin.productos.index') }}" class="w-full py-5 bg-white text-gray-400 font-bold rounded-3xl text-center border border-gray-100 hover:bg-gray-50 transition">
                    Descartar Cambios
                </a>
            </div>
        </div>
    </form>

    <script>
        function editProductoForm(initialVariantes = []) {
            return {
                variantes: [],
                imagePreview: null,
                init() {
                    if (Array.isArray(initialVariantes) && initialVariantes.length > 0) {
                        this.variantes = initialVariantes.map(v => ({
                            uid: crypto.randomUUID(),
                            id_variante: v.id_variante ?? null,
                            talla: v.talla ?? '',
                            color: v.color ?? '',
                            stock: v.stock ?? 0,
                            sku: v.sku ?? ''
                        }))
                    } else {
                        this.addVariante()
                    }
                },
                addVariante() {
                    this.variantes.push({
                        uid: crypto.randomUUID(),
                        id_variante: null,
                        talla: '',
                        color: '',
                        stock: 0,
                        sku: ''
                    })
                },
                removeVariante(index) {
                    this.variantes.splice(index, 1)
                },
                previewImage(e) {
                    if (e.target.files.length) {
                        this.imagePreview = URL.createObjectURL(e.target.files[0])
                    }
                }
            }
        }
    </script>
</div>
@endsection
