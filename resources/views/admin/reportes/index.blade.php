@extends('admin.layout')

@section('content')

    <div x-data="reportes()" x-init="init()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <x-heroicon-o-document-chart-bar class="w-9 h-9 text-gray-900" />
                    Centro de Reportes
                </h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Exporta a Excel con filtros personalizados para B-EDEN.
                </p>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
        MODAL DE FILTROS (Alpine.js)
        ════════════════════════════════════════════════════ --}}
        <div x-show="modalAbierto" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="cerrarModal()"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-lg p-8 z-10"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Título del modal --}}
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight" x-text="reporteActivo?.titulo">
                        </h2>
                        <p class="text-sm text-gray-500 mt-1 font-medium" x-text="reporteActivo?.descripcion"></p>
                    </div>
                    <button @click="cerrarModal()"
                        class="ml-4 p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0">
                        <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500" />
                    </button>
                </div>

                {{-- ── Filtro: Rango de fechas (disponible si reporteActivo.filtros incluye 'fechas') ── --}}
                <template x-if="reporteActivo?.filtros?.includes('fechas')">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Rango de fechas</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Desde</span>
                                <input type="date" x-model="filtros.desde"
                                    class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Hasta</span>
                                <input type="date" x-model="filtros.hasta"
                                    class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            </div>
                        </div>
                        {{-- Atajos de rango --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            <button @click="setRango('hoy')"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">Hoy</button>
                            <button @click="setRango('semana')"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">Esta
                                semana</button>
                            <button @click="setRango('mes')"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">Este
                                mes</button>
                            <button @click="setRango('trimestre')"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">Trimestre</button>
                            <button @click="setRango('anio')"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">Este
                                año</button>
                        </div>
                    </div>
                </template>

                {{-- ── Filtro: Límite de registros ── --}}
                <template x-if="reporteActivo?.filtros?.includes('limite')">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Cantidad de registros: <span class="text-gray-900" x-text="filtros.limite"></span>
                        </label>
                        <input type="range" min="5" max="200" step="5" x-model="filtros.limite"
                            class="w-full accent-gray-900">
                        <div class="flex justify-between text-xs text-gray-400 mt-1 font-medium">
                            <span>5</span><span>50</span><span>100</span><span>150</span><span>200</span>
                        </div>
                    </div>
                </template>

                {{-- ── Filtro: Agrupación por período ── --}}
                <template x-if="reporteActivo?.filtros?.includes('agrupacion')">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Agrupar por</label>
                        <div class="grid grid-cols-3 gap-2">
                            <template
                                x-for="op in [{val:'dia',label:'Día'},{val:'semana',label:'Semana'},{val:'mes',label:'Mes'}]">
                                <button @click="filtros.agrupacion = op.val"
                                    :class="filtros.agrupacion === op.val ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="text-sm font-bold py-2.5 rounded-xl transition-colors" x-text="op.label">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- ── Filtro: Estado de pedido ── --}}
                <template x-if="reporteActivo?.filtros?.includes('estado')">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Estado del pedido</label>
                        <select x-model="filtros.estado"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Pagado">Pagado</option>
                            <option value="Confirmado">Confirmado</option>
                            <option value="En camino">En camino</option>
                            <option value="Listo para recoger">Listo para recoger</option>
                            <option value="Entregado">Entregado</option>
                            <option value="Anulado">Anulado</option>
                        </select>
                    </div>
                </template>

                {{-- ── Filtro: Umbral de stock ── --}}
                <template x-if="reporteActivo?.filtros?.includes('umbral')">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Mostrar productos con stock ≤ <span class="text-gray-900" x-text="filtros.umbral"></span>
                            unidades
                        </label>
                        <input type="range" min="1" max="50" step="1" x-model="filtros.umbral"
                            class="w-full accent-gray-900">
                        <div class="flex justify-between text-xs text-gray-400 mt-1 font-medium">
                            <span>1</span><span>10</span><span>20</span><span>30</span><span>50</span>
                        </div>
                    </div>
                </template>

                {{-- Botón de descarga --}}
                <button @click="descargar()" :disabled="descargando"
                    class="w-full bg-gray-900 hover:bg-black disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 mt-2">
                    <template x-if="!descargando">
                        <span class="flex items-center gap-2">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar y Descargar Excel
                        </span>
                    </template>
                    <template x-if="descargando">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Procesando...
                        </span>
                    </template>
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
        SECCIÓN 1: VENTAS Y PEDIDOS
        ════════════════════════════════════════════════════ --}}
        <div class="mb-10">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Ventas y Pedidos</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-presentation-chart-bar class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Estado de Pedidos</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Resumen del volumen de órdenes por fase: <strong class="text-gray-700">Pagado</strong>, <strong
                                class="text-gray-700">Entregado</strong>, <strong class="text-gray-700">Anulado</strong>,
                            etc. Incluye monto total y porcentaje.
                        </p>
                    </div>
                    <a href="{{ route('admin.reportes.pedidos') }}"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar Reporte
                    </a>
                </div>

                {{-- Tarjeta: Ventas por Período --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Ventas por Período</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Evolución de ingresos agrupada por <strong class="text-gray-700">día, semana o mes</strong>.
                            Incluye ticket promedio y clientes únicos por período.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Ventas por Período',
                            descripcion: 'Evolución de ingresos con agrupación a tu elección.',
                            ruta: '{{ route('admin.reportes.periodo') }}',
                            filtros: ['fechas', 'agrupacion']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

                {{-- Tarjeta: Pedidos Detallado --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Pedidos Detallado</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Lista completa de órdenes con <strong class="text-gray-700">cliente, tipo de entrega,
                                cupón</strong> y dirección. Filtra por estado y elige cuántos registros exportar.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Pedidos Detallado',
                            descripcion: 'Lista completa de órdenes con todos sus datos.',
                            ruta: '{{ route('admin.reportes.pedidos-detallado') }}',
                            filtros: ['fechas', 'estado', 'limite']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
        SECCIÓN 2: PRODUCTOS E INVENTARIO
        ════════════════════════════════════════════════════ --}}
        <div class="mb-10">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Productos e Inventario</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Top Productos --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-trophy class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Top Productos</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Los artículos con mayor rotación. Ahora incluye <strong class="text-gray-700">categoría y precio
                                promedio</strong>. Elige cuántos mostrar y el rango de fechas.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Top Productos',
                            descripcion: 'Productos con más unidades vendidas en el período elegido.',
                            ruta: '{{ route('admin.reportes.productos') }}',
                            filtros: ['fechas', 'limite']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

                {{-- Ventas por Categoría --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-tag class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Ventas por Categoría</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Compara el desempeño de cada <strong class="text-gray-700">categoría</strong>: unidades,
                            ingresos y porcentaje de participación en ventas totales.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Ventas por Categoría',
                            descripcion: 'Participación de cada categoría en las ventas totales.',
                            ruta: '{{ route('admin.reportes.categoria') }}',
                            filtros: ['fechas']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

                {{-- Stock Crítico --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Stock Crítico</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Variantes con <strong class="text-gray-700">inventario bajo</strong> que necesitan
                            reabastecimiento. Define tú mismo el umbral mínimo de alerta.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Stock Crítico',
                            descripcion: 'Variantes de productos con stock por debajo del umbral elegido.',
                            ruta: '{{ route('admin.reportes.stock') }}',
                            filtros: ['umbral']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════
        SECCIÓN 3: CLIENTES Y MARKETING
        ════════════════════════════════════════════════════ --}}
        <div class="mb-10">
            <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Clientes y Marketing</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Top Clientes --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Top Clientes</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Los compradores más leales con <strong class="text-gray-700">ticket promedio, teléfono y última
                                compra</strong>. Filtra por rango de fechas y elige cuántos mostrar.
                        </p>
                    </div>
                    <button @click="abrirModal({
                            titulo: 'Top Clientes',
                            descripcion: 'Clientes con mayor gasto acumulado en el período elegido.',
                            ruta: '{{ route('admin.reportes.clientes') }}',
                            filtros: ['fechas', 'limite']
                        })"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" /> Configurar y Exportar
                    </button>
                </div>

                {{-- Uso de Cupones --}}
                <div
                    class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group">
                    <div>
                        <div
                            class="p-4 bg-gray-50 text-gray-900 rounded-2xl w-fit mb-6 transition-colors group-hover:bg-indigo-600 group-hover:text-white duration-300">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Uso de Cupones</h3>
                        <p class="text-sm font-medium text-gray-500 mt-2 mb-8 leading-relaxed">
                            Auditoría de todos tus <strong class="text-gray-700">cupones de descuento</strong>: cuántas
                            veces se usó cada código y cuántas ventas generó. Sin filtros — muestra el total histórico.
                        </p>
                    </div>
                    <a href="{{ route('admin.reportes.cupones') }}"
                        class="w-full text-center bg-gray-900 hover:bg-black text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 text-sm flex items-center justify-center gap-2 shadow-sm">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Generar Reporte
                    </a>
                </div>

            </div>
        </div>

        {{-- Banner inferior --}}
        <div
            class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-center sm:text-left">
                <h4 class="text-lg font-bold text-gray-900 tracking-tight">¿Necesitas un cruce de información personalizado?
                </h4>
                <p class="text-sm font-medium text-gray-500 mt-1">Las descargas están optimizadas para auditorías estándar.
                    Usa los filtros para ajustar cada reporte a tu necesidad.</p>
            </div>
            <div
                class="text-xs font-bold px-5 py-3.5 bg-gray-50 text-gray-500 rounded-xl border border-gray-100 flex-shrink-0">
                Soporte: admin@b-eden.com
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════
    ALPINE.JS — Lógica de filtros y descarga
    ════════════════════════════════════════════════════ --}}
    <script>
        function reportes() {
            return {
                modalAbierto: false,
                descargando: false,
                reporteActivo: null,
                filtros: {
                    desde: '',
                    hasta: '',
                    limite: 10,
                    agrupacion: 'mes',
                    estado: '',
                    umbral: 5,
                },

                init() { },

                abrirModal(reporte) {
                    this.reporteActivo = reporte;
                    // Reset filtros a defaults
                    this.filtros = { desde: '', hasta: '', limite: 10, agrupacion: 'mes', estado: '', umbral: 5 };
                    this.descargando = false;
                    this.modalAbierto = true;
                },

                cerrarModal() {
                    if (this.descargando) return;
                    this.modalAbierto = false;
                    this.reporteActivo = null;
                },

                setRango(tipo) {
                    const hoy = new Date();
                    const fmt = d => d.toISOString().split('T')[0];
                    let desde, hasta = fmt(hoy);

                    switch (tipo) {
                        case 'hoy':
                            desde = fmt(hoy); break;
                        case 'semana': {
                            const ini = new Date(hoy);
                            ini.setDate(hoy.getDate() - hoy.getDay() + 1);
                            desde = fmt(ini); break;
                        }
                        case 'mes':
                            desde = fmt(new Date(hoy.getFullYear(), hoy.getMonth(), 1)); break;
                        case 'trimestre': {
                            const mes = hoy.getMonth();
                            const ini = new Date(hoy.getFullYear(), Math.floor(mes / 3) * 3, 1);
                            desde = fmt(ini); break;
                        }
                        case 'anio':
                            desde = fmt(new Date(hoy.getFullYear(), 0, 1)); break;
                    }
                    this.filtros.desde = desde;
                    this.filtros.hasta = hasta;
                },

                descargar() {
                    if (!this.reporteActivo) return;

                    const url = new URL(this.reporteActivo.ruta, window.location.origin);
                    const f = this.filtros;

                    if (f.desde) url.searchParams.set('desde', f.desde);
                    if (f.hasta) url.searchParams.set('hasta', f.hasta);
                    if (f.limite) url.searchParams.set('limite', f.limite);
                    if (f.agrupacion) url.searchParams.set('agrupacion', f.agrupacion);
                    if (f.estado) url.searchParams.set('estado', f.estado);
                    if (f.umbral) url.searchParams.set('umbral', f.umbral);

                    this.descargando = true;

                    // Usamos un iframe invisible para no redirigir la página
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = url.toString();
                    document.body.appendChild(iframe);

                    setTimeout(() => {
                        this.descargando = false;
                        document.body.removeChild(iframe);
                        this.cerrarModal();
                    }, 3500);
                },
            };
        }
    </script>

@endsection
