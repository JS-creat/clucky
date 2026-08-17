@extends('admin.layout')

@section('content')

<div x-data="{
        buscar: '{{ request('buscar') }}',
        perPage: '{{ request('perPage', 10) }}',
        deleteModal: false,
        activeId: null,
        errorStockModal: false,
        cargando: false,
        buscarTimeout: null,
        actualizar(pagina = null) {
            this.cargando = true;
            const params = new URLSearchParams();
            if (this.buscar) params.set('buscar', this.buscar);
            if (this.perPage) params.set('perPage', this.perPage);
            if (pagina) params.set('page', pagina);

            const url = '{{ route('admin.productos.index') }}?' + params.toString();

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('resultados-productos').innerHTML = html;
                    window.history.replaceState({}, '', url);
                })
                .finally(() => this.cargando = false);
        }
     }"
     @click="if ($event.target.closest('#resultados-productos a')) { $event.preventDefault(); actualizar(new URL($event.target.closest('a').href).searchParams.get('page')); }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Productos</h1>
            <p class="text-gray-500 mt-1 font-medium">Gestiona tu inventario, precios y disponibilidad.</p>
        </div>

        <a href="{{ route('admin.productos.create') }}"
            class="inline-flex items-center gap-3 bg-black hover:bg-gray-800 text-white px-6 py-3.5 rounded-2xl font-bold shadow-sm transition-all hover:-translate-y-0.5 active:scale-95">
            <x-heroicon-o-plus class="w-5 h-5" />
            Nuevo Producto
        </a>
    </div>

    {{-- Filtros (Sin recarga de página) --}}
    <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-3 rounded-3xl border border-gray-100 shadow-sm">
        <div class="relative w-full md:max-w-md">
            <span class="absolute inset-y-0 left-4 flex items-center text-gray-300">
                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
            </span>
            <input type="text"
                x-model="buscar"
                @input="clearTimeout(buscarTimeout); buscarTimeout = setTimeout(() => actualizar(), 400)"
                placeholder="Buscar por nombre..."
                class="w-full pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-black transition-all outline-none font-medium text-gray-700 placeholder-gray-300 text-sm">
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Mostrar:</span>
            <select x-model="perPage" @change="actualizar()"
                class="bg-gray-50 border-none rounded-xl py-2 px-4 font-bold text-gray-900 text-sm focus:ring-0 cursor-pointer">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
        </div>
    </div>

    {{-- Tabla Asíncrona --}}
    <div class="relative min-h-[300px]">
        <div id="resultados-productos" :class="cargando ? 'opacity-40 pointer-events-none' : 'opacity-100'" class="transition-opacity">
            @include('admin.productos.resultados')
        </div>
    </div>

    {{-- Modal Confirmar Eliminación --}}
    <template x-if="deleteModal">
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
            <div @click="deleteModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
            <div class="relative bg-white rounded-[2.5rem] p-8 max-w-sm w-full shadow-2xl text-center">
                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-rose-50 text-rose-500 mb-4 font-bold">
                    <x-heroicon-o-trash class="w-8 h-8" />
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">¿Eliminar producto?</h3>
                <p class="text-gray-500 mb-8 text-sm font-medium">Esta acción no se puede deshacer.</p>
                <div class="flex flex-col gap-2">
                    <form :action="'{{ route('admin.productos.index') }}/' + activeId" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-3.5 bg-black text-white font-bold rounded-2xl hover:bg-gray-800 transition">Eliminar ahora</button>
                    </form>
                    <button @click="deleteModal = false" class="w-full py-3.5 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Alerta Stock --}}
    <template x-if="errorStockModal">
        <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
            <div @click="errorStockModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
            <div class="relative bg-white rounded-[2.5rem] p-8 max-w-sm w-full shadow-2xl text-center">
                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-4">
                    <x-heroicon-o-exclamation-circle class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Acción denegada</h3>
                <p class="text-gray-500 mb-6 text-sm font-medium leading-relaxed">
                    No puedes eliminar un producto que aún tiene <span class="text-amber-600 font-bold">stock disponible</span>.
                </p>
                <button @click="errorStockModal = false" class="w-full py-3.5 bg-black text-white font-bold rounded-2xl hover:bg-gray-800 transition shadow-sm">
                    Entendido
                </button>
            </div>
        </div>
    </template>

</div>

@endsection