@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto px-4 py-8">

        @php
            $colores = [
                'Pendiente'          => 'bg-amber-50 text-amber-700 border-amber-200',
                'Confirmado'         => 'bg-blue-50 text-blue-700 border-blue-200',
                'En camino'          => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'Listo para recoger' => 'bg-purple-50 text-purple-700 border-purple-200',
                'Entregado'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Anulado'            => 'bg-rose-50 text-rose-700 border-rose-200',
            ];

            $pasos = [
                'Pendiente',
                'Confirmado',
                'En camino',
                'Listo para recoger',
                'Entregado',
            ];
        @endphp

        {{-- BOTÓN VOLVER --}}
        <div class="mb-4">
            <a href="{{ route('perfil.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-900 transition">
                ← Volver a mi perfil
            </a>
        </div>

        @if ($pedidos->isEmpty())

            {{-- SIN PEDIDOS --}}
            <div class="bg-white rounded-2xl border p-16 text-center">
                <p class="text-gray-400 text-sm uppercase">
                    Sin pedidos aún
                </p>
            </div>

        @else

            {{-- LISTA DE PEDIDOS --}}
            <div class="space-y-4">

                @foreach ($pedidos as $pedido)

                    <div x-data="{ open: false }"
                         class="bg-white rounded-2xl border shadow-sm overflow-hidden">

                        {{-- HEADER DEL PEDIDO --}}
                        <button
                            @click="open = !open"
                            class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition"
                        >
                            <div class="text-left">
                                <p class="font-semibold text-sm">
                                    #{{ $pedido->numero_pedido }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $pedido->fecha_pedido->format('d/m/Y') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3">

                                {{-- ESTADO --}}
                                <span
                                    class="px-3 py-1 text-xs rounded-xl border {{ $colores[$pedido->estado_pedido] ?? '' }}"
                                >
                                    {{ $pedido->estado_pedido }}
                                </span>

                                {{-- TOTAL --}}
                                <span class="font-semibold">
                                    S/ {{ number_format($pedido->total_pedido, 2) }}
                                </span>

                                {{-- ICONO --}}
                                <svg
                                    :class="open ? 'rotate-180' : ''"
                                    class="w-4 h-4 transition"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M19 9l-7 7-7-7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                    />
                                </svg>

                            </div>
                        </button>

                        {{-- CONTENIDO DEL PEDIDO --}}
                        <div
                            x-show="open"
                            x-collapse
                            class="border-t p-5 space-y-4"
                        >

                            {{-- PROGRESO DEL PEDIDO --}}
                            <div class="flex text-xs">

                                @foreach ($pasos as $i => $paso)

                                    <div class="flex-1 text-center">

                                        <div
                                            class="w-6 h-6 mx-auto rounded-full border flex items-center justify-center
                                            {{ $pedido->estado_pedido === $paso
                                                ? 'bg-black text-white'
                                                : 'bg-gray-100 text-gray-500' }}"
                                        >
                                            {{ $i + 1 }}
                                        </div>

                                        <p class="mt-1 text-[10px] text-gray-400">
                                            {{ $paso }}
                                        </p>

                                    </div>

                                @endforeach

                            </div>

                            {{-- INFORMACIÓN DE ENTREGA --}}
                            <div class="grid grid-cols-2 gap-3 text-sm">

                                {{-- TIPO DE ENTREGA --}}
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-400">
                                        Tipo entrega
                                    </p>

                                    <p class="font-semibold">
                                        {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                    </p>
                                </div>

                                @if ($pedido->agencia)

                                    {{-- AGENCIA --}}
                                    <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                                        <p class="text-xs text-gray-400">
                                            Entrega
                                        </p>

                                        <p class="font-semibold">
                                            {{ $pedido->agencia->nombre_agencia }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $pedido->agencia->direccion }}
                                        </p>
                                    </div>

                                    {{-- COSTO DE ENVÍO --}}
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-400">
                                            Envío
                                        </p>

                                        <p class="font-semibold">
                                            @if ($pedido->agencia->costo_envio > 0)
                                                S/ {{ number_format($pedido->agencia->costo_envio, 2) }}
                                            @else
                                                Gratis
                                            @endif
                                        </p>
                                    </div>

                                @endif

                            </div>

                            {{-- FECHAS --}}
                            <div class="grid grid-cols-2 gap-3 text-sm">

                                {{-- FECHA DE ENVÍO --}}
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-400">
                                        Fecha envío
                                    </p>

                                    <p class="font-semibold">
                                        {{ $pedido->fecha_envio
                                            ? \Carbon\Carbon::parse($pedido->fecha_envio)->format('d-m-y')
                                            : '—' }}
                                    </p>
                                </div>

                                {{-- ENTREGA ESTIMADA --}}
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-400">
                                        Entrega estimada
                                    </p>

                                    <p class="font-semibold">
                                        {{ $pedido->fecha_entrega_estimada
                                            ? \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d-m-y')
                                            : '—' }}
                                    </p>
                                </div>

                                {{-- ENTREGA REAL --}}
                                <div class="bg-green-50 p-3 rounded-lg col-span-2">
                                    <p class="text-xs text-gray-400">
                                        Entregado el
                                    </p>

                                    <p class="font-semibold">
                                        {{ $pedido->fecha_entrega_real
                                            ? \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d-m-y')
                                            : '—' }}
                                    </p>
                                </div>

                            </div>

                            {{-- PRODUCTOS --}}
                            <div class="space-y-3">

                                @foreach ($pedido->detalles as $detalle)

                                    <div class="flex gap-3 items-center">

                                        {{-- IMAGEN --}}
                                        <img
                                            src="{{ asset('productos/' . ($detalle->variante->producto->imagen ?? '')) }}"
                                            alt="{{ $detalle->variante->producto->nombre_producto ?? 'Producto' }}"
                                            class="w-12 h-12 rounded object-cover"
                                        >

                                        {{-- INFORMACIÓN --}}
                                        <div class="flex-1">

                                            <p class="text-sm font-semibold">
                                                {{ $detalle->variante->producto->nombre_producto ?? '' }}
                                            </p>

                                            <p class="text-xs text-gray-400">
                                                x{{ $detalle->cantidad }}
                                            </p>

                                        </div>

                                        {{-- SUBTOTAL --}}
                                        <p class="text-sm font-semibold">
                                            S/ {{ number_format($detalle->subtotal, 2) }}
                                        </p>

                                    </div>

                                @endforeach

                            </div>

                            {{-- TOTAL --}}
                            <div class="flex justify-between border-t pt-3">

                                <span class="font-semibold">
                                    Total
                                </span>

                                <span class="font-semibold">
                                    S/ {{ number_format($pedido->total_pedido, 2) }}
                                </span>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-6">
                {{ $pedidos->links() }}
            </div>

        @endif

    </div>

@endsection