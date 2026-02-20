@extends('admin.layout')

@section('content')

<div class="bg-gradient-to-r from-gray-900 to-gray-700 text-white p-6 rounded-lg mb-8">
    <h2 class="text-2xl font-semibold">
        Panel de administración
    </h2>
    <p class="text-gray-200 mt-1">
        Los cambios realizados aquí se reflejan directamente en la tienda
    </p>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.productos.create') }}"
           class="bg-white text-black px-5 py-2 rounded font-medium hover:bg-gray-200 transition">
            Agregar producto
        </a>

        <a href="{{ route('home') }}" target="_blank"
           class="border border-white px-5 py-2 rounded hover:bg-white hover:text-black transition">
            Ver tienda
        </a>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    <div class="bg-white p-6 rounded shadow border-l-4 border-black">
        <p class="text-sm text-gray-500">Gestión de catálogo</p>
        <p class="text-lg font-semibold mt-2">
            Administra productos, variantes e imágenes
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow border-l-4 border-black">
        <p class="text-sm text-gray-500">Vista del cliente</p>
        <p class="text-lg font-semibold mt-2">
            Revisa cómo los clientes ven la tienda
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow border-l-4 border-black">
        <p class="text-sm text-gray-500">Estado del sistema</p>
        <p class="text-lg font-semibold mt-2">
            Panel operativo y actualizado
        </p>
    </div>

</div>

@endsection
