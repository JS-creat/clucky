@extends('admin.layout')

@section('content')

    <div x-data="{ deleteModal: false, activeId: null, errorStockModal: false, errorHistorialModal: false }" class="bg-gray-100 min-h-screen -m-8 p-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-black tracking-tight">Productos</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona tu inventario, precios y disponibilidad.</p>
            </div>

            <a href="{{ route('admin.productos.create') }}"
                class="inline-flex items-center gap-3 bg-black hover:bg-gray-800 text-white px-7 py-4 rounded-2xl font-bold shadow-xl transition-all hover:-translate-y-1 active:scale-95">
                <x-heroicon-o-plus class="w-6 h-6" />
                Nuevo Producto
            </a>
        </div>

        {{-- Buscador --}}
        <form action="{{ route('admin.productos.index') }}" method="GET"
            class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-4 rounded-3xl border-2 border-gray-200 shadow-sm">
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre..."
                    class="w-full pl-12 pr-6 py-4 bg-white border-2 border-gray-200 rounded-2xl focus:border-black transition-all outline-none font-bold text-black">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-6 py-3 bg-black text-white rounded-xl font-bold hover:bg-gray-800 transition">
                    Buscar
                </button>

                <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>

                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Mostrar:</span>
                <select name="perPage" onchange="this.form.submit()"
                    class="bg-white border-2 border-gray-200 rounded-xl py-2.5 px-4 font-bold text-black focus:border-black outline-none cursor-pointer">
                    <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('perPage') == 10 || !request('perPage') ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
                </select>
            </div>
        </form>

        {{-- Contenedor de Resultados (Respuesta AJAX / Vista Normal) --}}
        <div id="tabla-resultados">
            @include('admin.productos.resultados')
        </div>

        {{-- Modal Eliminación --}}
        <template x-if="deleteModal">
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div @click="deleteModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-md"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center border-2 border-gray-100">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-rose-50 text-rose-500 mb-6 font-bold">
                        <x-heroicon-o-trash class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-black mb-3">¿Eliminar producto?</h3>
                    <p class="text-gray-500 mb-10 font-medium">
                        Esta acción no se puede deshacer. Solo úsala si el producto <span class="font-bold text-black">nunca tuvo ventas</span>.
                        Si ya se vendió alguna vez, es mejor <span class="font-bold text-black">desactivarlo</span>.
                    </p>
                    <div class="flex flex-col gap-3">
                        <form :action="'{{ route('admin.productos.index') }}/' + activeId" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full py-4 bg-rose-500 text-white font-bold rounded-2xl hover:bg-rose-600 transition">
                                Eliminar ahora
                            </button>
                        </form>
                        <button @click="deleteModal = false" class="w-full py-4 bg-white text-gray-600 font-bold rounded-2xl border-2 border-gray-200 hover:border-black hover:text-black transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Alerta de Stock --}}
        <template x-if="errorStockModal">
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
                <div @click="errorStockModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-md"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center border-2 border-gray-100">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-6">
                        <x-heroicon-o-exclamation-circle class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-black mb-3">Acción denegada</h3>
                    <p class="text-gray-500 mb-10 font-medium leading-relaxed">
                        No puedes eliminar un producto que aún tiene <span class="text-amber-600 font-bold">stock disponible</span>. Debes agotar el inventario o desactivarlo.
                    </p>
                    <button @click="errorStockModal = false" class="w-full py-4 bg-black text-white font-bold rounded-2xl hover:bg-gray-800 transition shadow-lg">
                        Entendido
                    </button>
                </div>
            </div>
        </template>

        {{-- Modal Alerta de Historial de Ventas --}}
        <template x-if="errorHistorialModal">
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
                <div @click="errorHistorialModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-md"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center border-2 border-gray-100">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-6">
                        <x-heroicon-o-shield-exclamation class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-black mb-3">Tiene historial de ventas</h3>
                    <p class="text-gray-500 mb-10 font-medium leading-relaxed">
                        Este producto ya fue vendido alguna vez, así que no puede eliminarse
                        (se perdería la trazabilidad de esos pedidos/comprobantes). Desactívalo en su lugar.
                    </p>
                    <button @click="errorHistorialModal = false" class="w-full py-4 bg-black text-white font-bold rounded-2xl hover:bg-gray-800 transition shadow-lg">
                        Entendido
                    </button>
                </div>
            </div>
        </template>

    </div>

@endsection