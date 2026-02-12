@extends('layouts.app') {{-- si tienes layout --}}
@section('content')

<div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- IZQUIERDA --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- ENTREGA --}}
        <div class="bg-white border rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Tipo de Entrega</h2>

            <div class="space-y-3">
                <label class="flex items-center space-x-3">
                    <input type="radio" name="entrega" value="envio" checked>
                    <span>Envío a domicilio</span>
                </label>

                <label class="flex items-center space-x-3">
                    <input type="radio" name="entrega" value="retiro">
                    <span>Retiro en tienda</span>
                </label>
            </div>
        </div>

        {{-- DIRECCIÓN --}}
        <div class="bg-white border rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Dirección de envío</h2>

            <input type="text" placeholder="Dirección"
                class="w-full border rounded-lg p-3 mb-3">

            <div class="grid grid-cols-2 gap-4">
                <input type="text" placeholder="Ciudad"
                    class="border rounded-lg p-3">
                <input type="text" placeholder="Departamento"
                    class="border rounded-lg p-3">
            </div>
        </div>

        {{-- CUPÓN --}}
        <div class="bg-white border rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Cupón de descuento</h2>

            <div class="flex">
                <input type="text"
                    placeholder="Código"
                    class="flex-1 border rounded-l-lg p-3">
                <button class="bg-black text-white px-6 rounded-r-lg">
                    Aplicar
                </button>
            </div>
        </div>

    </div>

    {{-- DERECHA RESUMEN --}}
    <div class="bg-white border rounded-xl p-6 shadow-sm h-fit">

        <h2 class="text-lg font-semibold mb-4">Resumen</h2>

        @php
            $total = 0;
        @endphp

        @foreach($carrito as $item)
            @php
                $total += $item['precio'] * $item['cantidad'];
            @endphp

            <div class="flex justify-between text-sm mb-2">
                <span>{{ $item['nombre'] }} x{{ $item['cantidad'] }}</span>
                <span>S/ {{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
            </div>
        @endforeach

        <hr class="my-4">

        <div class="flex justify-between font-bold text-lg">
            <span>Total</span>
            <span>S/ {{ number_format($total, 2) }}</span>
        </div>

        <div class="mt-6">
            <label class="flex items-center space-x-2 text-sm mb-4">
                <input type="checkbox">
                <span>Acepto términos y condiciones</span>
            </label>

            <button class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition">
                Pagar
            </button>
        </div>

    </div>

</div>

@endsection
