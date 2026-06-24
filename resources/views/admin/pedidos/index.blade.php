@extends('admin.layout')

@section('content')
<div class="space-y-8 px-2 md:px-0">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Pedidos</h1>
            <p class="text-gray-500 mt-1 text-base md:text-lg font-medium">Gestiona y supervisa el flujo de ventas de tu tienda.</p>
            <div class="mt-3 inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black uppercase tracking-wider">
                <x-heroicon-o-shopping-cart class="w-4 h-4" />
                {{ $pedidos->total() }} registros totales
            </div>
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- BUSCADOR --}}
    <form method="GET" action="{{ route('admin.pedidos.index') }}" class="flex gap-2">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
            </div>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Buscar por N° pedido, cliente, correo o estado..."
                class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-gray-200 bg-white text-sm font-medium text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent shadow-sm transition"
            >
        </div>
        <button type="submit"
            class="px-5 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl shadow-sm transition-colors whitespace-nowrap">
            Buscar
        </button>
        @if(request('search'))
        <a href="{{ route('admin.pedidos.index') }}"
            class="px-4 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-2xl transition-colors whitespace-nowrap">
            Limpiar
        </a>
        @endif
    </form>

    @php
        $colores = [
            'Pendiente'   => 'bg-amber-50 text-amber-600 border-amber-100',
            'Pagado'      => 'bg-blue-50 text-blue-600 border-blue-100',
            'Enviado'     => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            'En Agencia'  => 'bg-purple-50 text-purple-600 border-purple-100',
            'Entregado'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'Cancelado'   => 'bg-rose-50 text-rose-600 border-rose-100',
            'Confirmado'  => 'bg-teal-50 text-teal-600 border-teal-100',
            'En camino'   => 'bg-sky-50 text-sky-600 border-sky-100',
            'Anulado'     => 'bg-rose-50 text-rose-600 border-rose-100',
        ];
    @endphp

    {{-- TABLA — solo en md+ --}}
    <div class="hidden md:block bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-50">
                    <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">N° Pedido</th>
                    <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Cliente / Correo</th>
                    <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Fecha y Hora</th>
                    <th class="px-8 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total</th>
                    <th class="px-8 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Estado</th>
                    <th class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pedidos as $pedido)
                @php $color = $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-500 border-gray-100'; @endphp
                <tr class="group hover:bg-indigo-50/30 transition-all duration-300">

                    <td class="px-8 py-8">
                        <span class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors tracking-tight">
                            #{{ $pedido->numero_pedido }}
                        </span>
                    </td>

                    <td class="px-8 py-8">
                        <div class="flex flex-col">
                            <span class="text-base font-bold text-gray-800 leading-tight">
                                @if($pedido->usuario)
                                    {{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}
                                @else
                                    <span class="text-rose-600 italic font-medium">Usuario Eliminado</span>
                                @endif
                            </span>
                            <span class="text-xs text-gray-400 font-medium italic mt-1 leading-none">
                                {{ $pedido->usuario?->correo ?? 'Sin correo registrado' }}
                            </span>
                        </div>
                    </td>

                    <td class="px-8 py-8">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 text-sm font-bold text-gray-600 leading-none">
                                <x-heroicon-o-calendar class="w-4 h-4 text-indigo-300" />
                                {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-400 leading-none">
                                <x-heroicon-o-clock class="w-4 h-4 text-gray-300" />
                                {{ \Carbon\Carbon::parse($pedido->created_at)->format('H:i') }}
                            </div>
                        </div>
                    </td>

                    <td class="px-8 py-8 text-center">
                        <span class="inline-block text-lg font-black text-gray-900 bg-gray-50 px-5 py-2 rounded-2xl border border-gray-100 shadow-inner">
                            S/ {{ number_format($pedido->total_pedido, 2) }}
                        </span>
                    </td>

                    <td class="px-8 py-8 text-center">
                        <span class="inline-block px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $color }} shadow-sm">
                            {{ $pedido->estado_pedido }}
                        </span>
                    </td>

                    <td class="px-8 py-8 text-right">
                        <a href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
                           class="inline-flex w-12 h-12 items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-400 shadow-sm group-hover:bg-indigo-600 group-hover:text-white group-hover:shadow-indigo-200 group-hover:shadow-lg transition-all duration-300">
                            <x-heroicon-o-eye class="w-6 h-6" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-32 text-center">
                        <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-gray-200" />
                        </div>
                        <h3 class="text-xl font-black text-gray-400 italic">
                            {{ request('search') ? 'Sin resultados para "' . request('search') . '"' : 'No hay pedidos registrados' }}
                        </h3>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARDS — solo en móvil --}}
    <div class="flex flex-col gap-4 md:hidden">
        @forelse($pedidos as $pedido)
        @php $color = $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-500 border-gray-100'; @endphp
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">

            {{-- Número + Estado --}}
            <div class="flex items-center justify-between mb-3">
                <span class="text-xl font-black text-gray-900 tracking-tight">#{{ $pedido->numero_pedido }}</span>
                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $color }}">
                    {{ $pedido->estado_pedido }}
                </span>
            </div>

            {{-- Cliente --}}
            <div class="mb-3">
                @if($pedido->usuario)
                    <p class="text-sm font-bold text-gray-800">{{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}</p>
                    <p class="text-xs text-gray-400 italic">{{ $pedido->usuario->correo }}</p>
                @else
                    <p class="text-sm font-medium text-rose-500 italic">Usuario Eliminado</p>
                    <p class="text-xs text-gray-400 italic">Sin correo registrado</p>
                @endif
            </div>

            {{-- Fecha + Total --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                    <x-heroicon-o-calendar class="w-3.5 h-3.5 text-indigo-300" />
                    {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}
                </div>
                <span class="text-base font-black text-gray-900 bg-gray-50 px-4 py-1.5 rounded-xl border border-gray-100">
                    S/ {{ number_format($pedido->total_pedido, 2) }}
                </span>
            </div>

            {{-- Botón --}}
            <a href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
               class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-indigo-50 text-indigo-600 text-sm font-black hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                <x-heroicon-o-eye class="w-4 h-4" />
                Ver pedido
            </a>
        </div>
        @empty
        <div class="py-20 text-center">
            <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-gray-200 mx-auto mb-4" />
            <h3 class="text-lg font-black text-gray-400 italic">
                {{ request('search') ? 'Sin resultados para "' . request('search') . '"' : 'No hay pedidos registrados' }}
            </h3>
        </div>
        @endforelse
    </div>

    {{-- PAGINACIÓN --}}
    <div class="py-4">
        {{ $pedidos->links() }}
    </div>

</div>
@endsection
