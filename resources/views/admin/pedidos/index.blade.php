@extends('admin.layout')

@section('content')
<div class="space-y-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Pedidos</h1>
            <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona y supervisa el flujo de ventas de tu tienda.</p>
            <div class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black uppercase tracking-wider">
                <x-heroicon-o-shopping-cart class="w-4 h-4" />
                {{ $pedidos->total() }} registros totales
            </div>
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- TABLA  --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
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
                <tr class="group hover:bg-indigo-50/30 transition-all duration-300">
                    {{-- ID --}}
                    <td class="px-8 py-8">
                        <span class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors tracking-tight">
                            #{{ $pedido->numero_pedido }}
                        </span>
                    </td>

                    {{-- Cliente --}}
                    <td class="px-8 py-8">
                        <div class="flex flex-col">
                            <span class="text-base font-bold text-gray-800 leading-tight">
                                {{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium italic mt-1 leading-none">
                                {{ $pedido->usuario->correo }}
                            </span>
                        </div>
                    </td>

                    {{-- Fecha --}}
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

                    {{-- Total --}}
                    <td class="px-8 py-8 text-center">
                        <span class="inline-block text-lg font-black text-gray-900 bg-gray-50 px-5 py-2 rounded-2xl border border-gray-100 shadow-inner">
                            S/ {{ number_format($pedido->total_pedido, 2) }}
                        </span>
                    </td>

                    {{-- Estado --}}
                    <td class="px-8 py-8 text-center">
                        @php
                            $colores = [
                                'Pendiente'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                'Pagado'     => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Enviado'    => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'En Agencia' => 'bg-purple-50 text-purple-600 border-purple-100',
                                'Entregado'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'Cancelado'  => 'bg-rose-50 text-rose-600 border-rose-100',
                            ];
                            $color = $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                        @endphp
                        <span class="inline-block px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $color }} shadow-sm">
                            {{ $pedido->estado_pedido }}
                        </span>
                    </td>

                    {{-- Acción --}}
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
                        <h3 class="text-xl font-black text-gray-400 italic">No hay pedidos registrados</h3>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="py-4">
        {{ $pedidos->links() }}
    </div>

</div>
@endsection
