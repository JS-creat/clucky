@extends('layouts.app')

@section('content')

    <div class="min-h-screen bg-gray-50/80 py-6 sm:py-8">

        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            @php
                $colores = [
                    'Pendiente'          => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Confirmado'         => 'bg-sky-50 text-sky-700 border-sky-200',
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

            {{-- VOLVER --}}
            <div class="mb-6">

                <a
                    href="{{ route('perfil.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    <span class="text-base">←</span>
                    Volver a mi perfil
                </a>

            </div>

            {{-- TÍTULO --}}
            <div class="mb-6">

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Mis pedidos
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Consulta el estado y los detalles de tus compras.
                </p>

            </div>

            @if ($pedidos->isEmpty())

                {{-- SIN PEDIDOS --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 sm:p-16 text-center">

                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg
                            class="w-7 h-7 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h13m-6-4v4"
                            />
                        </svg>
                    </div>

                    <h2 class="text-base font-semibold text-gray-800">
                        Aún no tienes pedidos
                    </h2>

                    <p class="text-sm text-gray-400 mt-1">
                        Cuando realices una compra aparecerá aquí.
                    </p>

                </div>

            @else

                {{-- LISTA --}}
                <div class="space-y-5">

                    @foreach ($pedidos as $pedido)

                        <div
                            x-data="{ open: false }"
                            class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden
                                   hover:shadow-md hover:border-gray-300 transition-all duration-200"
                        >

                            {{-- HEADER --}}
                            <button
                                @click="open = !open"
                                class="w-full px-5 sm:px-6 py-5 flex flex-col sm:flex-row
                                       sm:items-center sm:justify-between gap-4
                                       text-left hover:bg-gray-50/70 transition"
                            >

                                {{-- PEDIDO --}}
                                <div>

                                    <div class="flex items-center gap-3">

                                        <span class="font-bold text-gray-900">
                                            #{{ $pedido->numero_pedido }}
                                        </span>

                                        <span
                                            class="px-2.5 py-1 rounded-full border text-[11px] font-medium
                                            {{ $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}"
                                        >
                                            {{ $pedido->estado_pedido }}
                                        </span>

                                    </div>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Realizado el {{ $pedido->fecha_pedido->format('d/m/Y') }}
                                    </p>

                                </div>

                                {{-- TOTAL --}}
                                <div class="flex items-center justify-between sm:justify-end gap-4">

                                    <div class="text-left sm:text-right">

                                        <p class="text-[11px] uppercase tracking-wide text-gray-400">
                                            Total
                                        </p>

                                        <p class="font-bold text-gray-900">
                                            S/ {{ number_format($pedido->total_pedido, 2) }}
                                        </p>

                                    </div>

                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">

                                        <svg
                                            :class="open ? 'rotate-180' : ''"
                                            class="w-4 h-4 text-gray-500 transition-transform"
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

                                </div>

                            </button>

                            {{-- CONTENIDO --}}
                            <div
                                x-show="open"
                                x-collapse
                                class="border-t border-gray-200"
                            >

                                <div class="p-5 sm:p-6 space-y-6">

                                    {{-- PROGRESO --}}
                                    <div>

                                        <div class="flex items-center justify-between mb-4">

                                            <h3 class="text-sm font-semibold text-gray-800">
                                                Estado del pedido
                                            </h3>

                                            <span class="text-xs text-gray-400">
                                                Seguimiento
                                            </span>

                                        </div>

                                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 overflow-x-auto">

                                            <div class="flex min-w-[520px]">

                                                @foreach ($pasos as $i => $paso)

                                                    <div class="flex-1 relative text-center">

                                                        {{-- LÍNEA --}}
                                                        @if ($i < count($pasos) - 1)

                                                            <div class="absolute top-3 left-1/2 w-full h-px bg-gray-200"></div>

                                                        @endif

                                                        {{-- CÍRCULO --}}
                                                        <div
                                                            class="relative z-10 w-6 h-6 mx-auto rounded-full flex items-center justify-center
                                                            text-[10px] font-semibold border
                                                            {{ $pedido->estado_pedido === $paso
                                                                ? 'bg-gray-900 text-white border-gray-900'
                                                                : 'bg-white text-gray-400 border-gray-300' }}"
                                                        >
                                                            {{ $i + 1 }}
                                                        </div>

                                                        <p class="mt-2 text-[10px] text-gray-500 whitespace-nowrap">
                                                            {{ $paso }}
                                                        </p>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    </div>

                                    {{-- INFORMACIÓN --}}
                                    <div>

                                        <h3 class="text-sm font-semibold text-gray-800 mb-3">
                                            Información de entrega
                                        </h3>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                                            {{-- TIPO --}}
                                            <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">

                                                <p class="text-[11px] uppercase tracking-wide text-gray-400">
                                                    Tipo de entrega
                                                </p>

                                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                                    {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                                </p>

                                            </div>

                                            @if ($pedido->agencia)

                                                {{-- AGENCIA --}}
                                                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 sm:col-span-2">

                                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">
                                                        Punto de entrega
                                                    </p>

                                                    <p class="text-sm font-semibold text-gray-800 mt-1">
                                                        {{ $pedido->agencia->nombre_agencia }}
                                                    </p>

                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $pedido->agencia->direccion }}
                                                    </p>

                                                </div>

                                                {{-- ENVÍO --}}
                                                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">

                                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">
                                                        Costo de envío
                                                    </p>

                                                    <p class="text-sm font-semibold text-gray-800 mt-1">

                                                        @if ($pedido->agencia->costo_envio > 0)

                                                            S/ {{ number_format($pedido->agencia->costo_envio, 2) }}

                                                        @else

                                                            <span class="text-emerald-600">
                                                                Gratis
                                                            </span>

                                                        @endif

                                                    </p>

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                    {{-- FECHAS --}}
                                    <div>

                                        <h3 class="text-sm font-semibold text-gray-800 mb-3">
                                            Fechas
                                        </h3>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                            <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">

                                                <p class="text-[11px] uppercase tracking-wide text-blue-500">
                                                    Fecha de envío
                                                </p>

                                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                                    {{ $pedido->fecha_envio
                                                        ? \Carbon\Carbon::parse($pedido->fecha_envio)->format('d/m/Y')
                                                        : '—' }}
                                                </p>

                                            </div>

                                            <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-4">

                                                <p class="text-[11px] uppercase tracking-wide text-amber-600">
                                                    Entrega estimada
                                                </p>

                                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                                    {{ $pedido->fecha_entrega_estimada
                                                        ? \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y')
                                                        : '—' }}
                                                </p>

                                            </div>

                                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">

                                                <p class="text-[11px] uppercase tracking-wide text-emerald-600">
                                                    Entregado el
                                                </p>

                                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                                    {{ $pedido->fecha_entrega_real
                                                        ? \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d/m/Y')
                                                        : '—' }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                    {{-- PRODUCTOS --}}
                                    <div>

                                        <div class="flex items-center justify-between mb-3">

                                            <h3 class="text-sm font-semibold text-gray-800">
                                                Productos
                                            </h3>

                                            <span class="text-xs text-gray-400">
                                                {{ $pedido->detalles->count() }}
                                                {{ $pedido->detalles->count() === 1 ? 'producto' : 'productos' }}
                                            </span>

                                        </div>

                                        <div class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">

                                            @foreach ($pedido->detalles as $detalle)

                                                <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4">

                                                    {{-- IMAGEN --}}
                                                    <img
                                                        src="{{ asset('productos/' . ($detalle->variante->producto->imagen ?? '')) }}"
                                                        alt="{{ $detalle->variante->producto->nombre_producto ?? 'Producto' }}"
                                                        class="w-14 h-14 rounded-lg object-cover flex-shrink-0 border border-gray-100"
                                                    >

                                                    {{-- INFO --}}
                                                    <div class="flex-1 min-w-0">

                                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                                            {{ $detalle->variante->producto->nombre_producto ?? '' }}
                                                        </p>

                                                        <p class="text-xs text-gray-400 mt-1">
                                                            Cantidad: {{ $detalle->cantidad }}
                                                        </p>

                                                    </div>

                                                    {{-- SUBTOTAL --}}
                                                    <p class="text-sm font-semibold text-gray-800 whitespace-nowrap">
                                                        S/ {{ number_format($detalle->subtotal, 2) }}
                                                    </p>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                    {{-- TOTAL --}}
                                    <div class="border-t border-gray-200 pt-4 flex items-center justify-between">

                                        <div>

                                            <p class="text-xs text-gray-400">
                                                Total del pedido
                                            </p>

                                            <p class="text-lg font-bold text-gray-900">
                                                S/ {{ number_format($pedido->total_pedido, 2) }}
                                            </p>

                                        </div>

                                        <span class="text-xs text-gray-400">
                                            Pedido #{{ $pedido->numero_pedido }}
                                        </span>

                                    </div>

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

    </div>

@endsection