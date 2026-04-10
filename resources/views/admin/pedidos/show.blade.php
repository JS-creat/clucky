@extends('admin.layout')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- VOLVER Y TÍTULO --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.pedidos.index') }}"
                    class="group bg-white p-3 rounded-2xl border border-gray-100 shadow-sm hover:bg-gray-50 transition-all">
                    <x-heroicon-o-arrow-left class="w-6 h-6 text-gray-400 group-hover:text-indigo-600" />
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pedido #{{ $pedido->numero_pedido }}</h1>
                    <p class="text-gray-500 font-medium">Registro:
                        {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div>
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
                @endphp
                <span class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest border {{ $color }} shadow-sm">
                    {{ $pedido->estado_pedido }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8">

            {{-- COLUMNA IZQUIERDA --}}
            <div class="col-span-12 lg:col-span-8 space-y-6">

                {{-- TABLA DE PRODUCTOS --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                        <x-heroicon-o-shopping-bag class="w-5 h-5 text-gray-400" />
                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Artículos Solicitados</h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($pedido->detalles as $detalle)
                            <div class="p-8 flex items-center gap-6 group hover:bg-gray-50/30 transition-all">
                                <img src="{{ asset('productos/' . $detalle->variante->producto->imagen) }}"
                                    class="w-20 h-20 rounded-2xl object-cover shadow-sm">
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 text-lg leading-tight">
                                        {{ $detalle->variante->producto->nombre_producto }}</h4>
                                    <div class="flex gap-4 mt-2">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span> Talla: {{ $detalle->variante->talla }}
                                        </span>
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Color: {{ $detalle->variante->color }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 font-bold mb-1">{{ $detalle->cantidad }} unidad(es)</p>
                                    <p class="text-xl font-black text-gray-900">S/ {{ number_format($detalle->subtotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FECHAS --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                        <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Historial de Fechas</h2>
                    </div>
                    <div class="p-8">
                        <div class="relative">
                            <div class="absolute left-5 top-2 bottom-2 w-0.5 bg-gray-100"></div>
                            <div class="space-y-6">

                                <div class="flex items-start gap-5">
                                    <div class="w-10 h-10 rounded-2xl bg-gray-900 flex items-center justify-center flex-shrink-0 z-10 shadow-sm">
                                        <x-heroicon-s-document-text class="w-4 h-4 text-white" />
                                    </div>
                                    <div class="pt-1">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido Registrado</p>
                                        <p class="font-black text-gray-900 text-lg leading-tight">{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($pedido->created_at)->format('H:i') }} hrs</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-5">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 z-10 shadow-sm {{ $pedido->fecha_envio ? 'bg-indigo-600' : 'bg-gray-100' }}">
                                        <x-heroicon-s-truck class="w-4 h-4 {{ $pedido->fecha_envio ? 'text-white' : 'text-gray-300' }}" />
                                    </div>
                                    <div class="pt-1">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha de Envío</p>
                                        @if($pedido->fecha_envio)
                                            <p class="font-black text-gray-900 text-lg leading-tight">{{ \Carbon\Carbon::parse($pedido->fecha_envio)->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($pedido->fecha_envio)->format('H:i') }} hrs</p>
                                        @else
                                            <p class="font-bold text-gray-300 text-sm">Pendiente</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start gap-5">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 z-10 shadow-sm {{ $pedido->fecha_entrega_estimada ? 'bg-amber-400' : 'bg-gray-100' }}">
                                        <x-heroicon-s-calendar-days class="w-4 h-4 {{ $pedido->fecha_entrega_estimada ? 'text-white' : 'text-gray-300' }}" />
                                    </div>
                                    <div class="pt-1">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Entrega Estimada</p>
                                        @if($pedido->fecha_entrega_estimada)
                                            <p class="font-black text-gray-900 text-lg leading-tight">{{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}</p>
                                            <p class="text-xs text-amber-500 font-black uppercase tracking-wider">Aproximado</p>
                                        @else
                                            <p class="font-bold text-gray-300 text-sm">Sin definir</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start gap-5">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 z-10 shadow-sm {{ $pedido->fecha_entrega_real ? 'bg-emerald-500' : 'bg-gray-100' }}">
                                        <x-heroicon-s-check-badge class="w-4 h-4 {{ $pedido->fecha_entrega_real ? 'text-white' : 'text-gray-300' }}" />
                                    </div>
                                    <div class="pt-1">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Entrega Confirmada</p>
                                        @if($pedido->fecha_entrega_real)
                                            <p class="font-black text-gray-900 text-lg leading-tight">{{ \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('H:i') }} hrs</p>
                                        @else
                                            <p class="font-bold text-gray-300 text-sm">Pendiente</p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- METODO DE ENTREGA --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-gray-900 rounded-2xl flex items-center justify-center text-white">
                            <x-heroicon-o-map-pin class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 uppercase">Logística de Entrega</h3>
                            <p class="text-indigo-600 font-bold text-xs tracking-widest">{{ $pedido->tipoEntrega->nombre_tipo_entrega }}</p>
                        </div>
                    </div>

                    @if($pedido->id_tipo_entrega == 2)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-gray-50 p-6 rounded-[2rem] border border-gray-100">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Punto de Destino</label>
                                    <p class="font-bold text-gray-800 text-lg">{{ $pedido->nombre_agencia ?? 'Recojo en Tienda' }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Distrito / Ubicación</label>
                                    <p class="font-bold text-gray-800">{{ $pedido->distrito?->nombre_distrito ?? 'JR. Francisco Bolognesi N° 908' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4 md:border-l md:border-gray-200 md:pl-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Referencia de Dirección</label>
                                    <p class="font-bold text-gray-800">{{ $pedido->direccion_agencia }}</p>
                                    <p class="text-xs text-gray-400 font-bold mt-1 uppercase">
                                        {{ $pedido->distrito?->provincia?->nombre_provincia }} /
                                        {{ $pedido->distrito?->provincia?->departamento?->nombre_departamento }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-indigo-50 border border-indigo-100 p-8 rounded-[2rem] flex items-center gap-6">
                            <x-heroicon-o-building-storefront class="w-10 h-10 text-indigo-600" />
                            <div>
                                <p class="text-xl font-black text-indigo-900 leading-tight">Retiro en Tienda</p>
                                <p class="text-indigo-600 text-sm font-medium mt-1 uppercase tracking-wider">El cliente gestionará el recojo en el local físico.</p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="col-span-12 lg:col-span-4 space-y-6">

                {{-- ACTUALIZAR ESTADO --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4">
                    <x-heroicon-s-cog-6-tooth class="w-12 h-12 text-gray-50" />
                </div>

                <h3 class="text-lg font-black mb-6 uppercase tracking-tight relative z-10 text-gray-900">Actualizar Estado</h3>

                @if(in_array($pedido->estado_pedido, ['Entregado', 'Anulado']))
                    {{-- ESTADO FINAL no se puede cambiar --}}
                    @php
                    $esFinal = $pedido->estado_pedido === 'Entregado';
                    @endphp
                    <div class="rounded-2xl p-5 flex items-center gap-4 {{ $esFinal ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }}">
                        @if($esFinal)
                            <x-heroicon-s-check-badge class="w-8 h-8 text-emerald-500 flex-shrink-0" />
                            <div>
                                <p class="font-black text-emerald-800 text-sm uppercase tracking-widest">Pedido completado</p>
                                <p class="text-emerald-600 text-xs font-medium mt-0.5">Este pedido ya fue entregado y no puede modificarse.</p>
                            </div>
                        @else
                            <x-heroicon-s-x-circle class="w-8 h-8 text-rose-400 flex-shrink-0" />
                            <div>
                                <p class="font-black text-rose-800 text-sm uppercase tracking-widest">Pedido anulado</p>
                                <p class="text-rose-500 text-xs font-medium mt-0.5">Este pedido fue anulado y no puede modificarse.</p>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- FORMULARIO  --}}
                    <form action="{{ route('admin.pedidos.update', $pedido->id_pedido) }}" method="POST" class="space-y-4 relative z-10">
                        @csrf @method('PUT')

                        <select name="estado_pedido" id="estado_pedido"
                            class="w-full bg-gray-50 border-gray-200 rounded-xl py-4 px-5 font-bold text-gray-700 focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer text-sm">
                            <option value="Pendiente"          {{ $pedido->estado_pedido == 'Pendiente'          ? 'selected' : '' }}>Pendiente</option>
                            <option value="Confirmado"         {{ $pedido->estado_pedido == 'Confirmado'         ? 'selected' : '' }}>Confirmado</option>
                            <option value="En camino"          {{ $pedido->estado_pedido == 'En camino'          ? 'selected' : '' }}>En camino</option>
                            <option value="Listo para recoger" {{ $pedido->estado_pedido == 'Listo para recoger' ? 'selected' : '' }}>Listo para recoger</option>
                            <option value="Entregado"          {{ $pedido->estado_pedido == 'Entregado'          ? 'selected' : '' }}>Entregado</option>
                            <option value="Anulado"            {{ $pedido->estado_pedido == 'Anulado'            ? 'selected' : '' }}>Anulado</option>
                        </select>

                        <div id="campo_fecha_estimada"
                            class="{{ in_array($pedido->estado_pedido, ['En camino', 'Listo para recoger']) ? '' : 'hidden' }} space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">
                                Fecha Estimada de Entrega
                            </label>
                            <input type="date" name="fecha_entrega_estimada"
                                value="{{ $pedido->fecha_entrega_estimada ? \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('Y-m-d') : '' }}"
                                class="w-full bg-gray-50 border-gray-200 rounded-xl py-3 px-4 font-bold text-gray-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <button type="submit"
                            class="w-full bg-gray-900 hover:bg-black text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-[0.98] text-xs uppercase tracking-widest">
                            Actualizar Registro
                        </button>
                    </form>
                @endif
            </div>

                {{-- CLIENTE --}}
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-gray-900 mb-6 uppercase text-sm tracking-widest">Información del Cliente</h3>
                    <div class="space-y-5">
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-black uppercase">
                                {{ substr($pedido->usuario->nombres, 0, 1) }}{{ substr($pedido->usuario->apellidos, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-black text-gray-900 truncate leading-tight">{{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}</p>
                                <p class="text-[11px] text-gray-400 font-bold uppercase truncate mt-1">{{ $pedido->usuario->correo }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Teléfono</span>
                                <span class="text-sm font-black text-gray-700">{{ $pedido->usuario->telefono ?? 'N/A' }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">DNI / RUC</span>
                                <span class="text-sm font-black text-gray-700">{{ $pedido->usuario->numero_documento ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOTAL --}}
                <div class="bg-indigo-700 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 relative">
                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-indigo-200 mb-2">Liquidación Total</h3>
                    <div class="flex items-end justify-between">
                        <span class="text-4xl font-black tracking-tighter italic">S/ {{ number_format($pedido->total_pedido, 2) }}</span>
                        <x-heroicon-s-check-badge class="w-10 h-10 text-indigo-400/50" />
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const select = document.getElementById('estado_pedido');
        const campoFecha = document.getElementById('campo_fecha_estimada');
        const estadosConFecha = ['En camino', 'Listo para recoger'];

        select.addEventListener('change', function () {
            estadosConFecha.includes(this.value)
                ? campoFecha.classList.remove('hidden')
                : campoFecha.classList.add('hidden');
        });
    </script>
@endsection
