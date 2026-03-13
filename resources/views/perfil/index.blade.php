@extends('layouts.app')

@section('title', 'Mi Perfil - C\'Lucky')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;700&display=swap');

    .font-display { font-family: 'Bebas Neue', sans-serif; }
    .font-body    { font-family: 'DM Sans', sans-serif; }

    .tab-btn.active { background: #111; color: #fff; }
    .pedido-card { transition: all 0.2s ease; }
    .pedido-card:hover { transform: translateY(-1px); }

    .detalle-body { display: none; overflow: hidden; }
    .detalle-body.open {
        display: block;
        animation: slideDown 0.25s ease;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .step { flex: 1; position: relative; }
    .step::after {
        content: '';
        position: absolute;
        top: 35%;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    .step:last-child::after { display: none; }
    .step-dot {
        width: 26px; height: 26px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        background: white;
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 1;
        margin: 0 auto;
        font-size: 9px; font-weight: 800;
        color: #9ca3af;
    }
    .step.done .step-dot  { background: #111; border-color: #111; color: white; }
    .step.done::after     { background: #111; }
    .step.active .step-dot { background: #6366f1; border-color: #6366f1; color: white; }
</style>

<div class="sticky top-0 bg-white border-b z-40">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <span class="font-body font-bold uppercase text-xs tracking-[0.2em] text-gray-900">Mi perfil</span>
            <span class="font-body text-sm text-gray-500">Hola, <strong class="text-gray-900">{{ auth()->user()->nombres }}</strong></span>
        </div>
    </div>
</div>

<div class="min-h-screen bg-[#f5f5f3] font-body">
    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- HEADER --}}
        <div class="flex items-center gap-6 mb-10">
            <div class="w-16 h-16 rounded-2xl bg-black flex items-center justify-center text-white font-display text-2xl tracking-wider select-none">
                {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellidos, 0, 1)) }}
            </div>
            <div>
                <h1 class="font-display text-4xl tracking-wide text-gray-900 leading-none">
                    {{ strtoupper(auth()->user()->nombres . ' ' . auth()->user()->apellidos) }}
                </h1>
                <p class="text-sm text-gray-400 mt-1">{{ auth()->user()->correo }}</p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('perfil.edit') }}"
                   class="inline-flex items-center gap-2 bg-black text-white text-xs font-bold uppercase tracking-widest px-5 py-3 rounded-xl hover:bg-gray-800 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Editar
                </a>
            </div>
        </div>

        {{-- TABS --}}
        <div class="flex gap-2 mb-6">
            <button onclick="switchTab('datos')" id="tab-datos"
                class="tab-btn active font-bold text-xs uppercase tracking-widest px-5 py-2.5 rounded-xl border border-gray-200 transition-all">
                Mis datos
            </button>
            <button onclick="switchTab('pedidos')" id="tab-pedidos"
                class="tab-btn font-bold text-xs uppercase tracking-widest px-5 py-2.5 rounded-xl border border-gray-200 bg-white transition-all">
                Mis pedidos
                @if(auth()->user()->pedidos && auth()->user()->pedidos->count() > 0)
                    <span class="ml-1.5 bg-indigo-600 text-white text-[9px] font-black rounded-full px-1.5 py-0.5">
                        {{ auth()->user()->pedidos->count() }}
                    </span>
                @endif
            </button>
        </div>

        {{-- TAB DATOS --}}
        <div id="panel-datos">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="font-black text-xs uppercase tracking-[0.2em] text-gray-500">Información personal</h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5">Nombres</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 font-bold text-gray-800 border border-gray-100">{{ auth()->user()->nombres }}</div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5">Apellidos</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 font-bold text-gray-800 border border-gray-100">{{ auth()->user()->apellidos }}</div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5">Correo electrónico</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 font-bold text-gray-800 border border-gray-100 truncate">{{ auth()->user()->correo }}</div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5">Teléfono</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 font-bold border border-gray-100 {{ auth()->user()->telefono ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ auth()->user()->telefono ?? 'No registrado' }}
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1.5">Número de documento</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 font-bold border border-gray-100 {{ auth()->user()->numero_documento ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ auth()->user()->numero_documento ?? 'No registrado' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-700 font-medium transition">← Volver al inicio</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-rose-400 hover:text-rose-600 font-bold uppercase tracking-widest transition">Cerrar sesión</button>
                </form>
            </div>
        </div>

        {{-- TAB PEDIDOS --}}
        <div id="panel-pedidos" class="hidden">

            @php
                $pedidos = auth()->user()->pedidos()->with(['detalles.variante.producto', 'tipoEntrega'])->latest()->get();
            @endphp

            @if($pedidos->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="font-black text-gray-300 text-lg uppercase tracking-widest">Sin pedidos aún</p>
                    <a href="{{ route('home') }}" class="inline-block mt-4 bg-black text-white text-xs font-bold uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-gray-800 transition">
                        Explorar productos
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pedidos as $pedido)
                        @php
                            $colores = [
                                'Pendiente'          => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Confirmado'         => 'bg-blue-50 text-blue-700 border-blue-200',
                                'En camino'          => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'Listo para recoger' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'Entregado'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Anulado'            => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                            $color = $colores[$pedido->estado_pedido] ?? 'bg-gray-50 text-gray-600 border-gray-200';

                            $pasos = ['Pendiente', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'];
                            $indexActual = array_search($pedido->estado_pedido, $pasos);
                        @endphp

                        <div class="pedido-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                            <button onclick="toggleDetalle('pedido-{{ $pedido->id_pedido }}')"
                                class="w-full text-left px-6 py-5 flex items-center gap-4 hover:bg-gray-50/50 transition">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="font-black text-gray-900 text-sm">#{{ $pedido->numero_pedido }}</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                            {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $pedido->detalles->sum('cantidad') }} artículo(s) · {{ $pedido->tipoEntrega->nombre_tipo_entrega }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0 flex items-center gap-4">
                                    <span class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $color }}">
                                        {{ $pedido->estado_pedido }}
                                    </span>
                                    <p class="font-black text-gray-900 text-lg leading-none">S/ {{ number_format($pedido->total_pedido, 2) }}</p>
                                    <svg id="chevron-{{ $pedido->id_pedido }}" class="w-4 h-4 text-gray-300 transition-transform duration-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            <div id="pedido-{{ $pedido->id_pedido }}" class="detalle-body">
                                <div class="px-6 pb-6 space-y-5 border-t border-gray-50 pt-5">

                                    <div class="sm:hidden">
                                        <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $color }}">
                                            {{ $pedido->estado_pedido }}
                                        </span>
                                    </div>

                                    {{-- Linea de progreso --}}
                                    @if($pedido->estado_pedido !== 'Anulado')
                                    <div class="flex items-start px-2 pt-2">
                                        @foreach($pasos as $i => $paso)
                                            @php
                                                $stepClass = '';
                                                if ($indexActual !== false) {
                                                    if ($i < $indexActual) $stepClass = 'done';
                                                    elseif ($i === $indexActual) $stepClass = 'active';
                                                }
                                            @endphp
                                            <div class="step {{ $stepClass }}">
                                                <div class="step-dot">
                                                    @if($i < ($indexActual ?? -1))
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @else
                                                        {{ $i + 1 }}
                                                    @endif
                                                </div>
                                                <p class="text-center text-[8px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider leading-tight px-1">{{ $paso }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="bg-rose-50 border border-rose-100 rounded-xl px-4 py-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span class="text-xs font-bold text-rose-600 uppercase tracking-widest">Este pedido fue anulado</span>
                                    </div>
                                    @endif

                                    {{-- FECHAS --}}
                                    @if($pedido->fecha_entrega_estimada || $pedido->fecha_envio || $pedido->fecha_entrega_real)
                                    <div class="grid grid-cols-2 gap-3">
                                        @if($pedido->fecha_envio)
                                        <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Fecha de envío</p>
                                            <p class="font-black text-indigo-900 text-sm">{{ \Carbon\Carbon::parse($pedido->fecha_envio)->format('d/m/Y') }}</p>
                                        </div>
                                        @endif
                                        @if($pedido->fecha_entrega_estimada && $pedido->estado_pedido !== 'Entregado')
                                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                                            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">Entrega estimada</p>
                                            <p class="font-black text-amber-900 text-sm">{{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}</p>
                                        </div>
                                        @endif
                                        @if($pedido->fecha_entrega_real)
                                        <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                                            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Entregado el</p>
                                            <p class="font-black text-emerald-900 text-sm">{{ \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d/m/Y') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- PRODUCTOS --}}
                                    <div class="space-y-3">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Artículos</p>
                                        @foreach($pedido->detalles as $detalle)
                                        <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                            <img src="{{ asset('productos/' . $detalle->variante->producto->imagen) }}"
                                                class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-bold text-gray-900 text-sm truncate leading-tight">{{ $detalle->variante->producto->nombre_producto }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">
                                                    Talla {{ $detalle->variante->talla }} · {{ $detalle->variante->color }}
                                                </p>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <p class="text-[10px] text-gray-400 font-bold">x{{ $detalle->cantidad }}</p>
                                                <p class="font-black text-gray-900 text-sm">S/ {{ number_format($detalle->subtotal, 2) }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end">
                                        <div class="bg-black text-white rounded-xl px-5 py-3 flex items-center gap-3">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
                                            <span class="font-black text-xl tracking-tight">S/ {{ number_format($pedido->total_pedido, 2) }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function switchTab(tab) {
        ['datos', 'pedidos'].forEach(t => {
            document.getElementById('panel-' + t).classList.add('hidden');
            document.getElementById('tab-' + t).classList.remove('active');
        });
        document.getElementById('panel-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    function toggleDetalle(id) {
        const body = document.getElementById(id);
        const pedidoId = id.replace('pedido-', '');
        const chevron = document.getElementById('chevron-' + pedidoId);
        body.classList.toggle('open');
        chevron.style.transform = body.classList.contains('open') ? 'rotate(180deg)' : '';
    }
</script>

@endsection
