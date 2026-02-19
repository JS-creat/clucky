@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Productos</h1>

    <a href="{{ route('admin.productos.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        + Nuevo producto
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Imagen</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Precio</th>
                <th class="px-4 py-3 text-center">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @foreach($productos as $producto)
            <tr class="hover:bg-gray-50">
                <!-- Imagen -->
                <td class="px-4 py-3">
                    <img
                        src="{{ asset('productos/' . $producto->imagen) }}"
                        alt="{{ $producto->nombre_producto }}"
                        class="w-16 h-16 object-cover rounded border"
                    >
                </td>

                <!-- Nombre -->
                <td class="px-4 py-3 font-medium text-gray-800">
                    {{ $producto->nombre_producto }}
                </td>

                <!-- Precio -->
                <td class="px-4 py-3">
                    S/ {{ number_format($producto->precio, 2) }}
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 text-center">
                    @if($producto->estado_producto)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                            Activo
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                            Inactivo
                        </span>
                    @endif
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 text-center space-x-3">
                    <a href="{{ route('admin.productos.edit', $producto->id_producto) }}"
                       class="text-blue-600 hover:underline">
                        Editar
                    </a>

                    <form action="{{ route('admin.productos.destroy', $producto->id_producto) }}"
                          method="POST"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            onclick="return confirm('¿Eliminar producto?')"
                            class="text-red-600 hover:underline">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
