@extends('admin.layout')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Editar Variante</h2>
        <a href="{{ route('admin.productos.edit', $variante->id_producto) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            Volver al Producto
        </a>
    </div>
@endsection

@section('content')
<div class="py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.productos.variante.update', $variante->id_variante) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Talla</label>
                    <input type="text" name="talla" value="{{ $variante->talla }}"
                           class="w-full rounded-lg border-gray-300" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" value="{{ $variante->color }}"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" value="{{ $variante->stock }}"
                           class="w-full rounded-lg border-gray-300" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ $variante->sku }}"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.productos.edit', $variante->id_producto) }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
