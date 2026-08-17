@extends('admin.layout')

@section('content')

<div x-data="{
        buscar: '{{ request('buscar') }}',
        perPage: '{{ request('perPage', 10) }}',
        tipo: '{{ request('tipo') }}',
        cargando: false,
        buscarTimeout: null,
        actualizar(pagina = null) {
            this.cargando = true;
            const params = new URLSearchParams();
            if (this.buscar) params.set('buscar', this.buscar);
            if (this.perPage) params.set('perPage', this.perPage);
            if (this.tipo) params.set('tipo', this.tipo);
            if (pagina) params.set('page', pagina);

            const url = '{{ route('admin.movimientos.index') }}?' + params.toString();

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('resultados-movimientos').innerHTML = html;
                    window.history.replaceState({}, '', url);
                })
                .finally(() => this.cargando = false);
        }
     }"
     @click="if ($event.target.closest('#resultados-movimientos a')) { $event.preventDefault(); actualizar(new URL($event.target.closest('a').href).searchParams.get('page')); }">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Movimientos de Stock</h1>
            <p class="text-gray-500 mt-1 font-medium">Historial de entradas, salidas y ajustes de inventario.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-3 rounded-3xl border border-gray-100 shadow-sm">
        <div class="relative w-full md:max-w-md">
            <span class="absolute inset-y-0 left-4 flex items-center text-gray-300">
                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
            </span>
            <input type="text"
                x-model="buscar"
                @input="clearTimeout(buscarTimeout); buscarTimeout = setTimeout(() => actualizar(), 400)"
                placeholder="Buscar por producto..."
                class="w-full pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-black transition-all outline-none font-medium text-gray-700 placeholder-gray-300 text-sm">
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <select x-model="tipo" @change="actualizar()"
                class="bg-gray-50 border-none rounded-xl py-2 px-4 font-bold text-gray-900 text-sm focus:ring-0 cursor-pointer">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
            </select>

            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Mostrar:</span>
            <select x-model="perPage" @change="actualizar()"
                class="bg-gray-50 border-none rounded-xl py-2 px-4 font-bold text-gray-900 text-sm focus:ring-0 cursor-pointer">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
        </div>
    </div>

    {{-- Resultados asíncronos --}}
    <div class="relative min-h-[300px]">
        <div id="resultados-movimientos" :class="cargando ? 'opacity-40 pointer-events-none' : 'opacity-100'" class="transition-opacity">
            @include('admin.movimientos.resultados')
        </div>
    </div>

</div>

@endsection