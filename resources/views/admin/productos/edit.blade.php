@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Editar Producto</h1>

{{-- ERRORES --}}
@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('admin.productos.update', $producto->id_producto) }}"
    method="POST"
    enctype="multipart/form-data"
    x-data="editProductoForm({{ json_encode($producto->variantes) }})"
    class="space-y-8 bg-white p-6 rounded shadow"
>
    @csrf
    @method('PUT')

    {{-- DATOS DEL PRODUCTO --}}
    <section>
        <h2 class="text-lg font-semibold mb-4">Datos del producto</h2>

        <div class="grid grid-cols-2 gap-4">
            <input
                name="nombre_producto"
                value="{{ old('nombre_producto', $producto->nombre_producto) }}"
                placeholder="Nombre del producto"
                class="border p-2 rounded"
                required
            >

            <input
                name="marca"
                value="{{ old('marca', $producto->marca) }}"
                placeholder="Marca"
                class="border p-2 rounded"
            >

            <input
                name="precio"
                type="number"
                step="0.01"
                value="{{ old('precio', $producto->precio) }}"
                placeholder="Precio"
                class="border p-2 rounded"
                required
            >

            <input
                name="precio_oferta"
                type="number"
                step="0.01"
                value="{{ old('precio_oferta', $producto->precio_oferta) }}"
                placeholder="Precio oferta"
                class="border p-2 rounded"
            >

            <select name="id_genero" class="border p-2 rounded">
                @foreach($generos as $g)
                    <option value="{{ $g->id_genero }}" {{ $producto->id_genero == $g->id_genero ? 'selected' : '' }}>
                        {{ $g->nombre_genero }}
                    </option>
                @endforeach
            </select>

            <select name="id_categoria" class="border p-2 rounded">
                @foreach($categorias as $c)
                    <option value="{{ $c->id_categoria }}" {{ $producto->id_categoria == $c->id_categoria ? 'selected' : '' }}>
                        {{ $c->nombre_categoria }}
                    </option>
                @endforeach
            </select>
        </div>

        <textarea
            name="descripcion"
            class="border p-2 rounded w-full mt-4"
            placeholder="Descripción del producto"
        >{{ old('descripcion', $producto->descripcion) }}</textarea>
    </section>

    {{-- IMAGEN --}}
    <section>
        <h2 class="text-lg font-semibold mb-4">Imagen</h2>

        <div class="flex items-center gap-6">
            <img
                src="{{ asset('productos/'.$producto->imagen) }}"
                class="w-32 h-32 object-cover rounded border"
            >

            <div class="flex-1">
                <label class="block font-medium mb-2">Cambiar imagen</label>
                <input
                    type="file"
                    name="imagen"
                    accept="image/*"
                    @change="previewImage"
                    class="border p-2 rounded w-full"
                >

                <img
                    x-show="imagePreview"
                    :src="imagePreview"
                    class="w-32 h-32 mt-4 object-cover rounded border"
                >
            </div>
        </div>
    </section>
    {{-- GALERÍA DE IMÁGENES --}}
    <section>
        <h2 class="text-lg font-semibold mb-4">Galería de imágenes</h2>

        {{-- IMÁGENES ACTUALES --}}
        @if(!empty($producto->galeria))
            <div class="grid grid-cols-5 gap-4 mb-4">
                @foreach($producto->galeria as $index => $img)
                    <div class="relative border rounded overflow-hidden">
                        <img
                            src="{{ asset('productos/'.$img) }}"
                            class="w-full h-28 object-cover"
                        >

                        {{-- marcar para eliminar --}}
                        <input
                            type="checkbox"
                            name="eliminar_galeria[]"
                            value="{{ $img }}"
                            class="absolute top-2 left-2"
                            title="Eliminar imagen"
                        >
                    </div>
                @endforeach
            </div>

            <p class="text-sm text-gray-600 mb-3">
                Marca las imágenes que deseas eliminar
            </p>
        @else
            <p class="text-gray-500 mb-4">Este producto no tiene imágenes en la galería</p>
        @endif

        {{-- AGREGAR NUEVAS --}}
        <div>
            <label class="block font-medium mb-2">Agregar nuevas imágenes</label>
            <input
                type="file"
                name="galeria[]"
                multiple
                accept="image/*"
                class="border p-2 rounded w-full"
            >
        </div>
    </section>

    {{-- VARIANTES --}}
    <section class="border-t pt-6">
        <h2 class="text-lg font-semibold mb-4">Variantes</h2>

        <template x-for="(variante, index) in variantes" :key="variante.id_variante ?? index">
            <div class="grid grid-cols-6 gap-3 mb-3 items-end bg-gray-50 p-3 rounded">

                {{-- ID OCULTO --}}
                <input
                    type="hidden"
                    :name="`variantes[${index}][id_variante]`"
                    x-model="variante.id_variante"
                >

                <input
                    type="text"
                    :name="`variantes[${index}][talla]`"
                    x-model="variante.talla"
                    placeholder="Talla"
                    class="border p-2 rounded"
                    required
                >

                <input
                    type="text"
                    :name="`variantes[${index}][color]`"
                    x-model="variante.color"
                    placeholder="Color"
                    class="border p-2 rounded"
                >

                <input
                    type="number"
                    :name="`variantes[${index}][stock]`"
                    x-model="variante.stock"
                    placeholder="Stock"
                    min="0"
                    class="border p-2 rounded"
                    required
                >

                <input
                    type="text"
                    :name="`variantes[${index}][sku]`"
                    x-model="variante.sku"
                    placeholder="SKU (único por variante)"
                    class="border p-2 rounded"
                >

                <button
                    type="button"
                    @click="removeVariante(index)"
                    class="text-red-600 font-semibold hover:text-red-800"
                    title="Eliminar variante"
                >
                    ✕
                </button>
            </div>
        </template>

        <button
            type="button"
            @click="addVariante"
            class="mt-3 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded"
        >
            + Agregar variante
        </button>
    </section>

    {{-- ACCIONES --}}
    <div class="flex justify-end gap-4 pt-4 border-t">
        <a
            href="{{ route('admin.productos.index') }}"
            class="px-6 py-2 rounded border"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
        >
            Guardar cambios
        </button>
    </div>
</form>

<script>
function editProductoForm(initialVariantes) {
    return {
        variantes: initialVariantes.length
            ? initialVariantes
            : [{ id_variante:null, talla:'', color:'', stock:'', sku:'' }],

        imagePreview: null,

        addVariante() {
            this.variantes.push({
                id_variante: null,
                talla: '',
                color: '',
                stock: '',
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
@endsection
