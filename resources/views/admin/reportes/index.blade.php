@extends('admin.layout')

@section('content')

    <div x-data="{ descargandoPedidos: false, descargandoProductos: false, descargandoClientes: false }">

        {{-- Header de la Sección (Igualito al de Productos) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <x-heroicon-o-document-chart-bar class="w-9 h-9 text-gray-900" />
                    Centro de Reportes
                </h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Monitorea el rendimiento de B-EDEN y exporta a Excel.</p>
            </div>
        </div>

        {{-- Rejilla de Tarjetas de Reportes --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                <div>
                    <div class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                        <x-heroicon-o-presentation-chart-bar class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Estado de Pedidos</h3>
                    <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                        Extrae un resumen del volumen de órdenes de compra. Clasifica cuántos pedidos se encuentran en fases clave como <span class="text-gray-700 font-bold">Pagado</span> o <span class="text-gray-700 font-bold">Entregado</span>.
                    </p>
                </div>

                <a href="{{ route('admin.reportes.pedidos') }}"
                   @click="descargandoPedidos = true; setTimeout(() => descargandoPedidos = false, 4000)"
                   class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                    <span x-show="!descargandoPedidos" class="flex items-center gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar Reporte
                    </span>
                    <span x-show="descargandoPedidos" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Procesando...
                    </span>
                </a>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                <div>
                    <div class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                        <x-heroicon-o-trophy class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Top 10 Productos</h3>
                    <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                        Identifica los artículos con mayor rotación en B-EDEN. Calcula el total de <span class="text-gray-700 font-bold">unidades vendidas</span> y los <span class="text-gray-700 font-bold">ingresos brutos (S/.)</span>.
                    </p>
                </div>

                <a href="{{ route('admin.reportes.productos') }}"
                   @click="descargandoProductos = true; setTimeout(() => descargandoProductos = false, 4000)"
                   class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                    <span x-show="!descargandoProductos" class="flex items-center gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar Reporte
                    </span>
                    <span x-show="descargandoProductos" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Procesando...
                    </span>
                </a>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                <div>
                    <div class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                        <x-heroicon-o-user-group class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Top 10 Clientes</h3>
                    <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                        Descubre a los compradores más leales. Evalúa el número acumulado de compras exitosas y el <span class="text-gray-700 font-bold">monto total invertido (S/.)</span>.
                    </p>
                </div>

                <a href="{{ route('admin.reportes.clientes') }}"
                   @click="descargandoClientes = true; setTimeout(() => descargandoClientes = false, 4000)"
                   class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                    <span x-show="!descargandoClientes" class="flex items-center gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar Reporte
                    </span>
                    <span x-show="descargandoClientes" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Procesando...
                    </span>
                </a>
            </div>

        </div>

        {{-- Banner inferior a juego con el diseño redondeado de tus tablas [rounded-[2.5rem]] --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-center sm:text-left">
                <h4 class="text-lg font-bold text-gray-900 tracking-tight">¿Necesitas un cruce de información personalizado?</h4>
                <p class="text-sm font-medium text-gray-500 mt-1">Las descargas actuales están optimizadas para auditorías estándar automáticas de la tienda.</p>
            </div>
            <div class="text-xs font-bold px-5 py-3.5 bg-gray-50 text-gray-500 rounded-xl border border-gray-100 flex-shrink-0">
                Soporte: admin@b-eden.com
            </div>
        </div>

    </div>

@endsection
