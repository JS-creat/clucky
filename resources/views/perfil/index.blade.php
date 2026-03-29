@extends('layouts.app')

@section('title', 'Mi Perfil - C\'Lucky')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

{{-- NAVBAR PERFIL --}}
<div class="sticky top-0 bg-white border-b border-gray-100 z-40">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <span class="font-sans font-semibold text-xs tracking-[0.2em] uppercase text-gray-900" style="font-family:'DM Sans',sans-serif">Mi perfil</span>
            <span class="text-sm text-gray-500" style="font-family:'DM Sans',sans-serif">
                Hola, <strong class="text-gray-900">{{ auth()->user()->nombres }}</strong>
            </span>
        </div>
    </div>
</div>

<div class="min-h-screen bg-stone-50" style="font-family:'DM Sans',sans-serif">
    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- HEADER --}}
        <div class="flex items-center gap-5 mb-10">
            <div class="w-16 h-16 rounded-2xl bg-gray-900 flex items-center justify-center text-white text-xl font-bold select-none shrink-0"
                style="font-family:'Playfair Display',serif">
                {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellidos, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900 truncate" style="font-family:'Playfair Display',serif">
                    {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                </h1>
                <p class="text-sm text-gray-400 mt-0.5 truncate">{{ auth()->user()->correo }}</p>
            </div>
            <a href="{{ route('perfil.edit') }}"
               class="shrink-0 inline-flex items-center gap-2 bg-gray-900 text-white text-xs font-semibold uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-gray-800 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
                Editar
            </a>
        </div>

        {{-- TABS --}}
        <div class="flex gap-2 mb-6">
            <button onclick="switchTab('datos')" id="tab-datos"
                class="tab-btn text-xs font-semibold uppercase tracking-widest px-5 py-2.5 rounded-xl border transition-all bg-gray-900 text-white border-gray-900">
                Mis datos
            </button>
            <button onclick="switchTab('pedidos')" id="tab-pedidos"
                class="tab-btn text-xs font-semibold uppercase tracking-widest px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:border-gray-400 transition-all">
                Mis pedidos
                @php $totalPedidos = auth()->user()->pedidos ? auth()->user()->pedidos->count() : 0; @endphp
                @if($totalPedidos > 0)
                    <span class="ml-1.5 bg-gray-900 text-white text-[9px] font-bold rounded-full px-1.5 py-0.5">
                        {{ $totalPedidos }}
                    </span>
                @endif
            </button>
        </div>

        {{-- ── PANEL DATOS ── --}}
        <div id="panel-datos">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Información personal</h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5">Nombres</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm font-semibold text-gray-800 border border-gray-100">
                            {{ auth()->user()->nombres }}
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5">Apellidos</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm font-semibold text-gray-800 border border-gray-100">
                            {{ auth()->user()->apellidos }}
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5">Correo electrónico</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm font-semibold text-gray-800 border border-gray-100 truncate">
                            {{ auth()->user()->correo }}
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5">Teléfono</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm border border-gray-100 {{ auth()->user()->telefono ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
                            {{ auth()->user()->telefono ?? 'No registrado' }}
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-widest block mb-1.5">Número de documento</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm border border-gray-100 {{ auth()->user()->numero_documento ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
                            {{ auth()->user()->numero_documento ?? 'No registrado' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-700 font-medium transition-colors">
                    ← Volver al inicio
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-rose-400 hover:text-rose-600 font-semibold uppercase tracking-widest transition-colors">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>

        {{-- ── PANEL PEDIDOS ── --}}
        <div id="panel-pedidos" class="hidden">

            @php
                $pedidos = auth()->user()
                    ->pedidos()
                    ->with(['detalles.variante.producto', 'tipoEntrega', 'distrito'])
                    ->latest()
                    ->get();

                $colores = [
                    'Pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Pagado'     => 'bg-blue-50 text-blue-700 border-blue-200',
                    'Enviado'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'En Agencia' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Entregado'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Cancelado'  => 'bg-rose-50 text-rose-700 border-rose-200',
                ];
                $pasos = ['Pendiente', 'Pagado', 'Enviado', 'En Agencia', 'Entregado'];
            @endphp

            @if($pedidos->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-gray-300 text-sm font-semibold uppercase tracking-widest">Sin pedidos aún</p>
                    <a href="{{ route('home') }}"
                        class="inline-block mt-4 bg-gray-900 text-white text-xs font-semibold uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-gray-800 transition-colors">
                        Explorar productos
                    </a>
                </div>
            @else

                {{-- Lista de pedidos (paginación JS) --}}
                <div id="lista-pedidos" class="space-y-4">
                    @foreach($pedidos as $i => $pedido)
                        @php
                            $color       = $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            $indexActual = array_search($pedido->estado_pedido, $pasos);
                            $esCancelado = $pedido->estado_pedido === 'Cancelado';

                            // Costo de envío: si tiene agencia, es envío; si no, es recojo gratis
                            $tieneEnvio  = $pedido->id_distrito && $pedido->id_tipo_entrega == 2;
                            $costoEnvio  = $tieneEnvio
                                ? ($pedido->total_pedido - $pedido->detalles->sum('subtotal'))
                                : 0;
                        @endphp

                        <div class="pedido-item bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-all hover:-translate-y-px"
                            data-index="{{ $i }}">

                            {{-- Cabecera clickeable --}}
                            <button onclick="toggleDetalle('pedido-{{ $pedido->id_pedido }}')"
                                class="w-full text-left px-6 py-5 flex items-center gap-4 hover:bg-gray-50 transition-colors">

                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900 text-sm">#{{ $pedido->numero_pedido }}</span>
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $pedido->detalles->sum('cantidad') }} artículo(s)
                                        · {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                    </p>
                                </div>

                                <div class="text-right shrink-0 flex items-center gap-3">
                                    <span class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-widest border {{ $color }}">
                                        {{ $pedido->estado_pedido }}
                                    </span>
                                    <p class="font-semibold text-gray-900 text-base" style="font-family:'Playfair Display',serif">
                                        S/ {{ number_format($pedido->total_pedido, 2) }}
                                    </p>
                                    <svg id="chevron-{{ $pedido->id_pedido }}"
                                        class="w-4 h-4 text-gray-300 transition-transform duration-200 shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Cuerpo desplegable --}}
                            <div id="pedido-{{ $pedido->id_pedido }}"
                                class="hidden border-t border-gray-100">
                                <div class="px-6 pb-6 pt-5 space-y-5">

                                    {{-- Badge móvil --}}
                                    <div class="sm:hidden">
                                        <span class="px-3 py-1.5 rounded-xl text-xs font-semibold uppercase tracking-widest border {{ $color }}">
                                            {{ $pedido->estado_pedido }}
                                        </span>
                                    </div>

                                    {{-- Barra de progreso --}}
                                    @if(!$esCancelado)
                                        <div class="flex items-start px-1 pt-1">
                                            @foreach($pasos as $si => $paso)
                                                @php
                                                    $sc = '';
                                                    if ($indexActual !== false) {
                                                        if ($si < $indexActual) $sc = 'done';
                                                        elseif ($si === $indexActual) $sc = 'active';
                                                    }
                                                @endphp
                                                <div class="flex-1 relative">
                                                    {{-- Línea conectora --}}
                                                    @if($si < count($pasos) - 1)
                                                        <div class="absolute top-[13px] left-1/2 w-full h-0.5 {{ $sc === 'done' ? 'bg-gray-900' : 'bg-gray-200' }}"></div>
                                                    @endif
                                                    <div class="relative z-10 flex flex-col items-center">
                                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-[9px] font-bold transition-all
                                                            {{ $sc === 'done'   ? 'bg-gray-900 border-gray-900 text-white' : '' }}
                                                            {{ $sc === 'active' ? 'bg-indigo-500 border-indigo-500 text-white' : '' }}
                                                            {{ $sc === ''       ? 'bg-white border-gray-200 text-gray-400' : '' }}">
                                                            @if($sc === 'done')
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            @else
                                                                {{ $si + 1 }}
                                                            @endif
                                                        </div>
                                                        <p class="text-center text-[8px] font-semibold text-gray-400 mt-1.5 uppercase tracking-wide leading-tight px-0.5">
                                                            {{ $paso }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-rose-50 border border-rose-100 rounded-xl px-4 py-3 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-rose-600 uppercase tracking-widest">Este pedido fue cancelado</span>
                                        </div>
                                    @endif

                                    {{-- INFO ENTREGA --}}
                                    <div class="grid grid-cols-2 gap-3">

                                        {{-- Tipo de entrega --}}
                                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Tipo de entrega</p>
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                            </p>
                                        </div>

                                        {{-- Costo de envío --}}
                                        <div class="rounded-xl p-3 border {{ $pedido->costo_envio > 0 ? 'bg-amber-50 border-amber-100' : 'bg-emerald-50 border-emerald-100' }}">
                                            <p class="text-xs font-semibold uppercase tracking-widest mb-1 {{ $pedido->costo_envio > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                                                Costo de envío
                                            </p>
                                            <p class="text-sm font-semibold {{ $pedido->costo_envio > 0 ? 'text-amber-800' : 'text-emerald-700' }}">
                                                @if($pedido->costo_envio > 0)
                                                    S/ {{ number_format($pedido->costo_envio, 2) }}
                                                @else
                                                    Gratis
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Distrito destino --}}
                                        @if($pedido->distrito)
                                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Distrito destino</p>
                                                <p class="text-sm font-semibold text-gray-800">
                                                    {{ $pedido->distrito->nombre_distrito }}
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Agencia --}}
                                        @if($pedido->nombre_agencia)
                                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Agencia de envío</p>
                                                <p class="text-sm font-semibold text-gray-800">{{ $pedido->nombre_agencia }}</p>
                                                @if($pedido->direccion_agencia)
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $pedido->direccion_agencia }}</p>
                                                @endif
                                            </div>
                                        @endif

                                    </div>

                                    {{-- FECHAS --}}
                                    @if($pedido->fecha_envio || $pedido->fecha_entrega_estimada || $pedido->fecha_entrega_real)
                                        <div class="grid grid-cols-2 gap-3">
                                            @if($pedido->fecha_envio)
                                                <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                                                    <p class="text-xs font-semibold text-indigo-400 uppercase tracking-widest mb-1">Fecha de envío</p>
                                                    <p class="text-sm font-semibold text-indigo-900">
                                                        {{ \Carbon\Carbon::parse($pedido->fecha_envio)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            @endif
                                            @if($pedido->fecha_entrega_estimada && $pedido->estado_pedido !== 'Entregado')
                                                <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                                                    <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Entrega estimada</p>
                                                    <p class="text-sm font-semibold text-amber-900">
                                                        {{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            @endif
                                            @if($pedido->fecha_entrega_real)
                                                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                                                    <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Entregado el</p>
                                                    <p class="text-sm font-semibold text-emerald-900">
                                                        {{ \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- PRODUCTOS --}}
                                    <div class="space-y-2">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Artículos</p>
                                        @foreach($pedido->detalles as $detalle)
                                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                                @if($detalle->variante?->producto?->imagen)
                                                    <img src="{{ asset('productos/' . $detalle->variante->producto->imagen) }}"
                                                        class="w-12 h-12 rounded-xl object-cover shrink-0 border border-gray-100">
                                                @else
                                                    <div class="w-12 h-12 rounded-xl bg-gray-200 shrink-0 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $detalle->variante?->producto?->nombre_producto ?? '—' }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        Talla {{ $detalle->variante?->talla }} · {{ $detalle->variante?->color }}
                                                    </p>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <p class="text-xs text-gray-400">x{{ $detalle->cantidad }}</p>
                                                    <p class="text-sm font-semibold text-gray-900">S/ {{ number_format($detalle->subtotal, 2) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- TOTALES --}}
                                    <div class="border-t border-gray-100 pt-4 space-y-2">
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>Subtotal productos</span>
                                            <span>S/ {{ number_format($pedido->detalles->sum('subtotal'), 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>Envío</span>
                                            @if($pedido->costo_envio > 0)
                                                <span class="text-amber-600 font-semibold">+ S/ {{ number_format($pedido->costo_envio, 2) }}</span>
                                            @else
                                                <span class="text-emerald-600 font-semibold">Gratis</span>
                                            @endif
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                            <span class="font-semibold text-gray-900" style="font-family:'Playfair Display',serif">Total pagado</span>
                                            <span class="text-xl font-semibold text-gray-900" style="font-family:'Playfair Display',serif">
                                                S/ {{ number_format($pedido->total_pedido, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- PAGINACIÓN JS --}}
                <div id="paginacion" class="flex items-center justify-between mt-6">
                    <p id="pag-info" class="text-xs text-gray-400"></p>
                    <div class="flex gap-2">
                        <button id="btn-prev" onclick="cambiarPagina(-1)"
                            class="px-4 py-2 text-xs font-semibold border border-gray-200 rounded-xl bg-white hover:border-gray-900 hover:bg-gray-50 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            ← Anterior
                        </button>
                        <button id="btn-next" onclick="cambiarPagina(1)"
                            class="px-4 py-2 text-xs font-semibold border border-gray-200 rounded-xl bg-white hover:border-gray-900 hover:bg-gray-50 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            Siguiente →
                        </button>
                    </div>
                </div>

            @endif
        </div>

    </div>
</div>

<script>
    // ── TABS
    function switchTab(tab) {
        ['datos', 'pedidos'].forEach(t => {
            document.getElementById('panel-' + t).classList.add('hidden');
            const btn = document.getElementById('tab-' + t);
            btn.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
            btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
        });
        document.getElementById('panel-' + tab).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.classList.add('bg-gray-900', 'text-white', 'border-gray-900');
        activeBtn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
    }

    // ── ACCORDION
    function toggleDetalle(id) {
        const body    = document.getElementById(id);
        const pedidoId = id.replace('pedido-', '');
        const chevron  = document.getElementById('chevron-' + pedidoId);
        const isOpen   = !body.classList.contains('hidden');

        body.classList.toggle('hidden', isOpen);
        chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    // ── PAGINACIÓN
    const POR_PAGINA = 5;
    let paginaActual = 1;

    function renderPaginacion() {
        const items = document.querySelectorAll('.pedido-item');
        const total = items.length;
        const totalPaginas = Math.ceil(total / POR_PAGINA);

        if (total <= POR_PAGINA) {
            document.getElementById('paginacion').classList.add('hidden');
            return;
        }

        const inicio = (paginaActual - 1) * POR_PAGINA;
        const fin    = inicio + POR_PAGINA;

        items.forEach((item, i) => {
            item.classList.toggle('hidden', i < inicio || i >= fin);
            // Cerrar acordeones al paginar
            const detalle = item.querySelector('[id^="pedido-"]');
            if (detalle) detalle.classList.add('hidden');
        });

        document.getElementById('pag-info').textContent =
            `Mostrando ${inicio + 1}–${Math.min(fin, total)} de ${total} pedidos`;

        document.getElementById('btn-prev').disabled = paginaActual === 1;
        document.getElementById('btn-next').disabled = paginaActual === totalPaginas;
    }

    function cambiarPagina(dir) {
        const items = document.querySelectorAll('.pedido-item');
        const totalPaginas = Math.ceil(items.length / POR_PAGINA);
        paginaActual = Math.max(1, Math.min(totalPaginas, paginaActual + dir));
        renderPaginacion();
        document.getElementById('panel-pedidos').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Inicializar paginación al cargar
    document.addEventListener('DOMContentLoaded', renderPaginacion);
</script>

@endsection
