<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400">Producto</th>
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">Variantes</th>
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">Precio</th>
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">Stock Total</th>
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">Estado</th>
                    <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($productos as $producto)
                    @php $stockTotal = $producto->variantes->sum('stock'); @endphp
                    <tr class="group hover:bg-gray-50/80 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-gray-50 flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                                    <img src="{{ asset('productos/' . $producto->imagen) }}"
                                         alt="{{ $producto->nombre_producto }}" class="w-full h-full object-cover"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($producto->nombre_producto) }}&color=000000&background=F3F4F6'">
                                </div>
                                <div>
                                    <p class="font-extrabold text-gray-900 text-base leading-tight">
                                        {{ $producto->nombre_producto }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-medium mt-1">
                                        {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-bold">
                                {{ $producto->variantes->count() }} {{ Str::plural('variante', $producto->variantes->count()) }}
                            </span>
                        </td>

                        <td class="px-8 py-6 text-center">
                            @if($producto->precio_oferta && $producto->precio_oferta > 0)
                                <div class="flex flex-col items-center">
                                    <span class="font-black text-gray-900 text-lg">
                                        S/ {{ number_format($producto->precio_oferta, 2) }}
                                    </span>
                                    <span class="text-xs text-gray-400 line-through font-semibold">
                                        S/ {{ number_format($producto->precio, 2) }}
                                    </span>
                                </div>
                            @else
                                <span class="font-black text-gray-900 text-lg">
                                    S/ {{ number_format($producto->precio, 2) }}
                                </span>
                            @endif
                        </td>

                        <td class="px-8 py-6 text-center">
                            @if($stockTotal <= 0)
                                <span class="inline-flex items-center gap-1 text-rose-600 font-bold bg-rose-50 px-3 py-1 rounded-lg text-xs">
                                    <x-heroicon-s-x-circle class="w-4 h-4" /> Agotado
                                </span>
                            @elseif($stockTotal <= 10)
                                <div class="flex flex-col items-center">
                                    <span class="text-amber-600 font-black text-base">{{ $stockTotal }}</span>
                                    <span class="text-[10px] uppercase font-black text-amber-500 tracking-tighter">Bajo stock</span>
                                </div>
                            @else
                                <span class="text-gray-900 font-black text-base">{{ $stockTotal }}</span>
                            @endif
                        </td>

                        <td class="px-8 py-6 text-center">
                            @if($producto->estado_producto)
                                <span class="inline-flex items-center gap-1.5 py-1.5 px-3.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-black text-white">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-1.5 px-3.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-400">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td class="px-8 py-6">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.productos.edit', $producto->id_producto) }}"
                                   class="p-2.5 bg-gray-50 text-gray-600 hover:bg-black hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                </a>

                                <button type="button"
                                    @click="if({{ $stockTotal }} > 0) { errorStockModal = true } else { deleteModal = true; activeId = {{ $producto->id_producto }} }"
                                    class="p-2.5 bg-gray-50 text-gray-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-16 text-center">
                            <x-heroicon-o-archive-box class="w-10 h-10 text-gray-200 mx-auto mb-3" />
                            <p class="text-gray-400 font-bold text-sm">No se encontraron productos.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8">
    {{ $productos->links() }}
</div>