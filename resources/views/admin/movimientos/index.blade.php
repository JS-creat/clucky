@extends('admin.layout')

@section('content')
<div>

    {{-- Header --}}
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Movimientos de Stock</h1>
        <p class="text-gray-500 font-medium">Historial de entradas y salidas de inventario.</p>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-xs font-black uppercase tracking-widest">
                        <th class="px-6 py-5 text-left">Fecha</th>
                        <th class="px-6 py-5 text-left">Producto</th>
                        <th class="px-6 py-5 text-left">Variante</th>
                        <th class="px-6 py-5 text-left">Tipo</th>
                        <th class="px-6 py-5 text-left">Cantidad</th>
                        <th class="px-6 py-5 text-left">Motivo</th>
                        <th class="px-6 py-5 text-left">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($movimientos as $mov)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-5 text-gray-500 font-medium whitespace-nowrap">
                                {{ $mov->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-5 font-bold text-gray-800">
                                {{ $mov->variante->producto->nombre_producto ?? '—' }}
                            </td>
                            <td class="px-6 py-5 text-gray-500 font-medium">
                                {{ $mov->variante->talla ?? '' }}
                                @if($mov->variante->color)
                                    / {{ $mov->variante->color }}
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                @if($mov->tipo === 'entrada')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-black uppercase">
                                        <x-heroicon-o-arrow-up class="w-3 h-3" />
                                        Entrada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-black uppercase">
                                        <x-heroicon-o-arrow-down class="w-3 h-3" />
                                        Salida
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 font-black text-gray-900">
                                {{ $mov->cantidad }}
                            </td>
                            <td class="px-6 py-5 text-gray-500 font-medium capitalize">
                                {{ str_replace('_', ' ', $mov->motivo) }}
                            </td>
                            <td class="px-6 py-5 text-gray-500 font-medium">
                                {{ $mov->usuario->correo ?? 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <x-heroicon-o-archive-box class="w-10 h-10 text-gray-200 mx-auto mb-3" />
                                <p class="text-gray-400 font-bold text-sm">Aún no hay movimientos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-8">
        {{ $movimientos->links() }}
    </div>

</div>
@endsection
