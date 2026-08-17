{{-- Tabla para Pantallas Grandes (Desktop) --}}
<div class="hidden md:block bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
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
                            <p class="text-gray-400 font-bold text-sm">No se encontraron movimientos.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Vista en Tarjetas para Móviles --}}
<div class="md:hidden space-y-3">
    @forelse($movimientos as $mov)
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="font-bold text-gray-800">{{ $mov->variante->producto->nombre_producto ?? '—' }}</p>
                    <p class="text-xs text-gray-400 font-medium">
                        {{ $mov->variante->talla ?? '' }}
                        @if($mov->variante->color)
                            / {{ $mov->variante->color }}
                        @endif
                    </p>
                </div>
                @if($mov->tipo === 'entrada')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-black uppercase shrink-0">
                        <x-heroicon-o-arrow-up class="w-3 h-3" /> Entrada
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-black uppercase shrink-0">
                        <x-heroicon-o-arrow-down class="w-3 h-3" /> Salida
                    </span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-y-2 text-xs">
                <div>
                    <p class="text-gray-300 font-black uppercase tracking-wide">Cantidad</p>
                    <p class="text-gray-900 font-bold">{{ $mov->cantidad }}</p>
                </div>
                <div>
                    <p class="text-gray-300 font-black uppercase tracking-wide">Fecha</p>
                    <p class="text-gray-600 font-medium">{{ $mov->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-300 font-black uppercase tracking-wide">Motivo</p>
                    <p class="text-gray-600 font-medium capitalize">{{ str_replace('_', ' ', $mov->motivo) }}</p>
                </div>
                <div>
                    <p class="text-gray-300 font-black uppercase tracking-wide">Usuario</p>
                    <p class="text-gray-600 font-medium">{{ $mov->usuario->correo ?? 'Sistema' }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm py-16 text-center">
            <x-heroicon-o-archive-box class="w-10 h-10 text-gray-200 mx-auto mb-3" />
            <p class="text-gray-400 font-bold text-sm">No se encontraron movimientos.</p>
        </div>
    @endforelse
</div>

{{-- Paginación --}}
<div class="mt-8">
    {{ $movimientos->links() }}
</div>