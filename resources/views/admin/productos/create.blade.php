@extends('admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Nuevo Producto</h1>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" x-data="variantesForm({{ json_encode(old('variantes', [
        ['talla' => '', 'color' => '', 'stock' => '', 'sku' => '']
    ])) }})" class="space-y-6 bg-white p-6 rounded shadow">
        @csrf

        {{-- DATOS DEL PRODUCTO --}}
        <div class="grid grid-cols-2 gap-4">
            <input name="nombre_producto" value="{{ old('nombre_producto') }}" placeholder="Nombre del producto"
                class="border p-2 rounded" required>

            <input name="marca" value="{{ old('marca') }}" placeholder="Marca" class="border p-2 rounded">

            <input name="precio" type="number" step="0.01" value="{{ old('precio') }}" placeholder="Precio"
                class="border p-2 rounded" required>

            <input name="precio_oferta" type="number" step="0.01" value="{{ old('precio_oferta') }}"
                placeholder="Precio oferta" class="border p-2 rounded">

            <select name="id_genero" class="border p-2 rounded">
                @foreach($generos as $g)
                    <option value="{{ $g->id_genero }}" {{ old('id_genero') == $g->id_genero ? 'selected' : '' }}>
                        {{ $g->nombre_genero }}
                    </option>
                @endforeach
            </select>

            <select name="id_categoria" class="border p-2 rounded">
                @foreach($categorias as $c)
                    <option value="{{ $c->id_categoria }}" {{ old('id_categoria') == $c->id_categoria ? 'selected' : '' }}>
                        {{ $c->nombre_categoria }}
                    </option>
                @endforeach
            </select>
        </div>

        <textarea name="descripcion" placeholder="Descripción"
            class="border p-2 rounded w-full">{{ old('descripcion') }}</textarea>

        {{-- IMAGEN --}}
        <div>
            <label class="block font-semibold mb-2">Imagen principal</label>

            <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" x-on:change="previewImage"
                class="border p-2 rounded w-full" required>

            <p class="text-sm text-gray-500 mt-1">
                Si hay un error, deberás volver a seleccionar la imagen.
            </p>

            <img x-show="imagePreview" :src="imagePreview" class="w-32 h-32 mt-3 object-cover rounded border">
        </div>

        <div>
            <label class="block font-semibold mb-2">
                Galería (imágenes secundarias)
            </label>

            <input type="file" name="galeria[]" multiple accept="image/*" class="border p-2 rounded w-full">
        </div>

        {{-- VARIANTES --}}
        <div class="border-t pt-4">
            <h2 class="text-xl font-semibold mb-4">Variantes</h2>

            <template x-for="(variante, index) in variantes" :key="index">
                <div class="grid grid-cols-5 gap-2 mb-3 items-center">
                    <input type="text" :name="`variantes[${index}][talla]`" x-model="variante.talla" placeholder="Talla"
                        class="border p-2 rounded" required>

                    <input type="text" :name="`variantes[${index}][color]`" x-model="variante.color" placeholder="Color"
                        class="border p-2 rounded">

                    <input type="number" :name="`variantes[${index}][stock]`" x-model="variante.stock" placeholder="Stock"
                        class="border p-2 rounded" min="0" required>

                    <input type="text" :name="`variantes[${index}][sku]`" x-model="variante.sku" placeholder="SKU"
                        class="border p-2 rounded">

                    <button type="button" @click="removeVariante(index)" class="text-red-600 font-bold"
                        x-show="variantes.length > 1">
                        ✕
                    </button>
                </div>
            </template>

            <button type="button" @click="addVariante" class="bg-gray-200 px-3 py-1 rounded">
                + Agregar variante
            </button>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Guardar producto
        </button>
    </form>

    <script>
        function variantesForm(initialVariantes) {
            return {
                variantes: initialVariantes,
                imagePreview: null,

                addVariante() {
                    this.variantes.push({ talla: '', color: '', stock: '', sku: '' })
                },

                removeVariante(index) {
                    this.variantes.splice(index, 1)
                },

                previewImage(e) {
                    const file = e.target.files[0]
                    if (!file) return

                    this.imagePreview = URL.createObjectURL(file)
                }
            }
        }
    </script>
@endsection
