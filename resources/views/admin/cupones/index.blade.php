@extends('admin.layout')

@section('content')
<div x-data="{ createModal: false }">

    {{-- ========== HEADER ========== --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Promociones</p>
            <h1 class="text-3xl font-light text-gray-900 tracking-tight">Cupones</h1>
            <p class="text-gray-500 mt-1 text-sm">Gestiona los cupones exclusivos para la app móvil.</p>
        </div>
        <button @click="createModal = true"
            class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Cupón
        </button>
    </div>

    <hr class="border-gray-200 mb-8">

    {{-- ========== NOTIFICACIONES FLASH ========== --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-6 bg-white border border-gray-200 rounded-xl px-5 py-3.5 flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-700 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mb-6 bg-gray-900 border border-gray-900 rounded-xl px-5 py-3.5 flex items-center gap-3">
            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-white text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ========== ESTADÍSTICAS RÁPIDAS ========== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 transition-all duration-300">
            <div class="flex items-center gap-2.5 mb-1.5">
                <div class="w-7 h-7 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Total</p>
            </div>
            <p class="text-3xl font-light text-gray-900">{{ $estadisticas['total'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 transition-all duration-300">
            <div class="flex items-center gap-2.5 mb-1.5">
                <div class="w-7 h-7 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Activos</p>
            </div>
            <p class="text-3xl font-light text-gray-900">{{ $estadisticas['activos'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 transition-all duration-300">
            <div class="flex items-center gap-2.5 mb-1.5">
                <div class="w-7 h-7 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Vencidos</p>
            </div>
            <p class="text-3xl font-light text-gray-900">{{ $estadisticas['vencidos'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 transition-all duration-300">
            <div class="flex items-center gap-2.5 mb-1.5">
                <div class="w-7 h-7 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Inactivos</p>
            </div>
            <p class="text-3xl font-light text-gray-900">{{ $estadisticas['inactivos'] }}</p>
        </div>
    </div>

    {{-- ========== TABLA DE CUPONES ========== --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        {{-- Encabezado tabla --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Lista de Cupones</h2>
            </div>
            <span class="text-[11px] font-medium text-gray-500 bg-gray-50 border border-gray-100 px-3 py-1 rounded-md">{{ $cupones->total() }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/60">
                        <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Código</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Descuento</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Mínimo</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Vencimiento</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Usos</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Asignación</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Estado</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cupones as $cupon)
                        @php
                            $vencido = $cupon->vencido;
                            $limiteGlobal = $cupon->uso_maximo_global ?? '∞';
                            $usosActuales = $cupon->usos_actuales;
                            $porcentajeUso = $cupon->uso_maximo_global > 0
                                ? round(($usosActuales / $cupon->uso_maximo_global) * 100)
                                : 0;
                            $esPrivado = $cupon->usuarios_asignados_count > 0;
                        @endphp
                        <tr x-data="{ confirmModal: false, editModal: false }"
                            class="hover:bg-gray-50/60 transition-colors group">

                            {{-- Código --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900 tracking-widest text-xs font-mono bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-md block">
                                            {{ $cupon->codigo_cupon }}
                                        </span>
                                        @if($cupon->descripcion)
                                            <span class="text-xs text-gray-400 mt-1 block max-w-[150px] truncate">{{ $cupon->descripcion }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Descuento --}}
                            <td class="px-4 py-4">
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ $cupon->descuento_formateado }}
                                </span>
                                <span class="text-xs text-gray-400 block mt-0.5">
                                    {{ $cupon->tipo_descuento === 'porcentaje' ? 'Porcentaje' : 'Monto fijo' }}
                                </span>
                            </td>

                            {{-- Compra mínima --}}
                            <td class="px-4 py-4">
                                <span class="font-medium text-gray-700 text-sm">
                                    S/ {{ number_format($cupon->monto_compra_minima, 2) }}
                                </span>
                            </td>

                            {{-- Vencimiento --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    @if($vencido)
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @endif
                                    <span class="font-medium text-sm {{ $vencido ? 'text-gray-500' : 'text-gray-700' }}">
                                        {{ $cupon->fecha_vencimiento->format('d M Y') }}
                                    </span>
                                </div>
                                @if($vencido)
                                    <span class="text-[11px] font-medium text-gray-400 mt-0.5 block">Vencido</span>
                                @else
                                    <span class="text-[11px] text-gray-400 mt-0.5 block">{{ $cupon->fecha_vencimiento->diffForHumans() }}</span>
                                @endif
                            </td>

                            {{-- Usos --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-gray-700 text-sm">{{ $usosActuales }}</span>
                                    <span class="text-gray-300 text-sm">/</span>
                                    <span class="text-sm text-gray-400">{{ $limiteGlobal }}</span>
                                </div>
                                @if($cupon->uso_maximo_global)
                                    <div class="w-20 h-1 bg-gray-100 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full rounded-full bg-gray-900 transition-all" style="width: {{ min($porcentajeUso, 100) }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $porcentajeUso }}% usado</span>
                                @else
                                    <span class="text-[10px] text-gray-400 mt-0.5 block">Ilimitado</span>
                                @endif
                            </td>

                            {{-- Asignación --}}
                            <td class="px-4 py-4">
                                @if($esPrivado)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-[11px] font-medium bg-gray-900 text-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Privado ({{ $cupon->usuarios_asignados_count }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-[11px] font-medium bg-gray-50 border border-gray-100 text-gray-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Público
                                    </span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-[11px] font-medium border border-gray-200/50 backdrop-blur-md
                                    {{ $cupon->estado_cupon && !$vencido ? 'bg-white/90 text-gray-900' : 'bg-gray-900/80 text-white' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cupon->estado_cupon && !$vencido ? 'bg-black' : 'bg-white' }}"></span>
                                    {{ $cupon->estado_cupon && !$vencido ? 'Activo' : ($vencido ? 'Vencido' : 'Inactivo') }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                    <button @click="editModal = true"
                                        class="p-2 rounded-lg text-gray-400 hover:text-black hover:bg-gray-50 transition-all"
                                        title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.cupones.toggle', $cupon->id_cupon) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="p-2 rounded-lg text-gray-400 hover:text-black hover:bg-gray-50 transition-all"
                                            title="{{ $cupon->estado_cupon ? 'Desactivar' : 'Activar' }}">
                                            @if($cupon->estado_cupon)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <button @click="confirmModal = true"
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-gray-50 transition-all"
                                        title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>

                            {{-- ========== MODAL CONFIRMAR ELIMINAR ========== --}}
                            <template x-if="confirmModal">
                                <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                                    <div @click="confirmModal = false" class="absolute inset-0 bg-gray-900/20 backdrop-blur-xs"></div>
                                    <div class="relative bg-white border border-gray-200 rounded-xl p-6 max-w-sm w-full shadow-xl text-center mx-4">
                                        <div class="w-10 h-10 bg-gray-50 border border-gray-200 text-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">¿Eliminar cupón?</h3>
                                        <p class="text-gray-500 font-normal mb-5 text-xs">
                                            Estás a punto de eliminar el cupón
                                            <span class="font-semibold text-gray-800 font-mono">{{ $cupon->codigo_cupon }}</span>. Esta acción no se puede deshacer.
                                        </p>
                                        <form action="{{ route('admin.cupones.destroy', $cupon->id_cupon) }}" method="POST" class="flex gap-2">
                                            @csrf @method('DELETE')
                                            <button type="button" @click="confirmModal = false"
                                                class="flex-1 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-200 rounded-lg text-xs transition-all">Cancelar</button>
                                            <button type="submit"
                                                class="flex-1 py-2 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </template>

                            {{-- ========== MODAL EDITAR CUPÓN ========== --}}
                            <template x-if="editModal">
                                <div class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center p-0 sm:p-4">
                                    <div @click="editModal = false" class="absolute inset-0 bg-gray-900/30 backdrop-blur-xs"></div>
                                    <div class="relative bg-white border border-gray-200 rounded-t-xl sm:rounded-xl p-6 sm:p-8 w-full sm:max-w-xl shadow-2xl max-h-[92vh] overflow-y-auto">

                                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                                            <div>
                                                <h2 class="text-lg font-semibold text-gray-900">Editar Cupón</h2>
                                                <p class="text-gray-400 text-xs mt-0.5 font-mono">{{ $cupon->codigo_cupon }}</p>
                                            </div>
                                            <button @click="editModal = false"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-black hover:bg-gray-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.cupones.update', $cupon->id_cupon) }}" method="POST" class="space-y-4"
                                            x-data="{ tipoAsignacion: '{{ $esPrivado ? 'usuarios' : 'todos' }}' }">
                                            @csrf @method('PUT')

                                            {{-- Código --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Código</label>
                                                <input type="text" name="codigo_cupon" value="{{ old('codigo_cupon', $cupon->codigo_cupon) }}" required
                                                    class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('codigo_cupon') ? 'border-red-300' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm font-mono uppercase tracking-widest transition-colors">
                                                @error('codigo_cupon')<p class="text-red-500 text-[11px] font-medium mt-1">{{ $message }}</p>@enderror
                                            </div>

                                            {{-- Descripción --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Descripción</label>
                                                <input type="text" name="descripcion" value="{{ old('descripcion', $cupon->descripcion) }}"
                                                    placeholder="Ej: Cupón de verano 2025"
                                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                            </div>

                                            {{-- Tipo y Valor --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Tipo</label>
                                                    <select name="tipo_descuento" required
                                                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                        <option value="monto_fijo" {{ $cupon->tipo_descuento === 'monto_fijo' ? 'selected' : '' }}>Monto fijo (S/)</option>
                                                        <option value="porcentaje" {{ $cupon->tipo_descuento === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Valor</label>
                                                    <input type="number" name="valor_descuento" value="{{ old('valor_descuento', $cupon->valor_descuento) }}" step="0.01" min="0.01" required
                                                        class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('valor_descuento') ? 'border-red-300' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                    @error('valor_descuento')<p class="text-red-500 text-[11px] font-medium mt-1">{{ $message }}</p>@enderror
                                                </div>
                                            </div>

                                            {{-- Mínimo y Vencimiento --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Mínimo (S/)</label>
                                                    <input type="number" name="monto_compra_minima" value="{{ old('monto_compra_minima', $cupon->monto_compra_minima) }}" step="0.01" min="0" required
                                                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Vencimiento</label>
                                                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $cupon->fecha_vencimiento->format('Y-m-d')) }}" required
                                                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                </div>
                                            </div>

                                            {{-- Límites de uso --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Usos máx. global</label>
                                                    <input type="number" name="uso_maximo_global" value="{{ old('uso_maximo_global', $cupon->uso_maximo_global) }}" min="1" placeholder="∞"
                                                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                    <span class="text-[11px] text-gray-400 mt-1 block">Dejar vacío = ilimitado</span>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Usos máx. por usuario</label>
                                                    <input type="number" name="uso_maximo_por_usuario" value="{{ old('uso_maximo_por_usuario', $cupon->uso_maximo_por_usuario) }}" min="1" placeholder="∞"
                                                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                                    <span class="text-[11px] text-gray-400 mt-1 block">Dejar vacío = ilimitado</span>
                                                </div>
                                            </div>

                                            {{-- Tipo de asignación --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Asignar a *</label>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="tipo_asignacion" value="todos" x-model="tipoAsignacion" class="peer sr-only"
                                                            {{ !$esPrivado ? 'checked' : '' }}>
                                                        <div class="text-center py-2.5 px-2 rounded-lg border border-gray-200 peer-checked:border-black peer-checked:bg-gray-900 peer-checked:text-white transition-all">
                                                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            <span class="text-[11px] font-medium">Todos los usuarios</span>
                                                        </div>
                                                    </label>
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="tipo_asignacion" value="usuarios" x-model="tipoAsignacion" class="peer sr-only"
                                                            {{ $esPrivado ? 'checked' : '' }}>
                                                        <div class="text-center py-2.5 px-2 rounded-lg border border-gray-200 peer-checked:border-black peer-checked:bg-gray-900 peer-checked:text-white transition-all">
                                                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                            <span class="text-[11px] font-medium">Usuarios específicos</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Selector de usuarios (solo si es privado) --}}
                                            <div x-show="tipoAsignacion === 'usuarios'" x-transition class="space-y-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Seleccionar usuarios</label>
                                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 max-h-48 overflow-y-auto space-y-1">
                                                    @forelse($usuarios as $usuario)
                                                        @php
                                                            $asignado = $cupon->usuariosAsignados->contains('id_usuario', $usuario->id_usuario);
                                                        @endphp
                                                        <label class="flex items-center gap-3 cursor-pointer hover:bg-white p-2 rounded-lg transition">
                                                            <input type="checkbox" name="usuarios_asignados[]" value="{{ $usuario->id_usuario }}"
                                                                {{ $asignado ? 'checked' : '' }}
                                                                class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black transition">
                                                            <div class="flex-1 min-w-0">
                                                                <span class="font-medium text-gray-700 text-sm block truncate">{{ $usuario->nombres }}</span>
                                                                <span class="text-[11px] text-gray-400">{{ $usuario->correoS }}</span>
                                                            </div>
                                                        </label>
                                                    @empty
                                                        <p class="text-gray-400 text-xs text-center py-4">No hay usuarios registrados.</p>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="flex gap-2 pt-4 border-t border-gray-100">
                                                <button type="button" @click="editModal = false"
                                                    class="flex-1 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-200 text-xs transition-all">Cancelar</button>
                                                <button type="submit"
                                                    class="flex-[2] py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    </div>
                                    <p class="text-gray-900 font-medium text-sm">No hay cupones registrados</p>
                                    <p class="text-gray-400 text-xs mt-0.5">Crea tu primer cupón de descuento para la app.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($cupones->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $cupones->links() }}
            </div>
        @endif
    </div>

    {{-- ========== MODAL CREAR CUPÓN ========== --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/30 backdrop-blur-xs"></div>
            <div class="relative bg-white border border-gray-200 rounded-t-xl sm:rounded-xl p-6 sm:p-8 w-full sm:max-w-xl shadow-2xl max-h-[92vh] overflow-y-auto">

                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Nuevo Cupón</h2>
                        <p class="text-gray-400 text-xs mt-0.5">Crea un código de descuento exclusivo para la app.</p>
                    </div>
                    <button @click="createModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-black hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.cupones.store') }}" method="POST" class="space-y-4"
                    x-data="{ tipoAsignacion: 'todos' }">
                    @csrf

                    {{-- Código --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Código *</label>
                        <input type="text" name="codigo_cupon" required autofocus
                            value="{{ old('codigo_cupon') }}"
                            placeholder="Ej: VERANO2025"
                            class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('codigo_cupon') ? 'border-red-300' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm font-mono uppercase tracking-widest transition-colors">
                        @error('codigo_cupon')<p class="text-red-500 text-[11px] font-medium mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                            placeholder="Ej: Cupón de verano 2025"
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                    </div>

                    {{-- Tipo y Valor --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Tipo *</label>
                            <select name="tipo_descuento" required
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                                <option value="monto_fijo" {{ old('tipo_descuento') === 'monto_fijo' ? 'selected' : '' }}>Monto fijo (S/)</option>
                                <option value="porcentaje" {{ old('tipo_descuento') === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Valor *</label>
                            <input type="number" name="valor_descuento" step="0.01" min="0.01" required
                                value="{{ old('valor_descuento') }}"
                                placeholder="0.00"
                                class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('valor_descuento') ? 'border-red-300' : 'border-gray-200' }} rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            @error('valor_descuento')<p class="text-red-500 text-[11px] font-medium mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Mínimo y Vencimiento --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Mínimo (S/) *</label>
                            <input type="number" name="monto_compra_minima" step="0.01" min="0" required
                                value="{{ old('monto_compra_minima') }}"
                                placeholder="0.00"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento" required
                                value="{{ old('fecha_vencimiento') }}"
                                min="{{ now()->toDateString() }}"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                        </div>
                    </div>

                    {{-- Límites de uso --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Usos máx. global</label>
                            <input type="number" name="uso_maximo_global" min="1" placeholder="∞"
                                value="{{ old('uso_maximo_global') }}"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            <span class="text-[11px] text-gray-400 mt-1 block">Dejar vacío = ilimitado</span>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Usos máx. por usuario</label>
                            <input type="number" name="uso_maximo_por_usuario" min="1" placeholder="∞"
                                value="{{ old('uso_maximo_por_usuario') }}"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-black text-xs sm:text-sm transition-colors">
                            <span class="text-[11px] text-gray-400 mt-1 block">Dejar vacío = ilimitado</span>
                        </div>
                    </div>

                    {{-- Tipo de asignación --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Asignar a *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_asignacion" value="todos" x-model="tipoAsignacion" class="peer sr-only" checked>
                                <div class="text-center py-2.5 px-2 rounded-lg border border-gray-200 peer-checked:border-black peer-checked:bg-gray-900 peer-checked:text-white transition-all">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span class="text-[11px] font-medium">Todos los usuarios</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_asignacion" value="usuarios" x-model="tipoAsignacion" class="peer sr-only">
                                <div class="text-center py-2.5 px-2 rounded-lg border border-gray-200 peer-checked:border-black peer-checked:bg-gray-900 peer-checked:text-white transition-all">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-[11px] font-medium">Usuarios específicos</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Selector de usuarios --}}
                    <div x-show="tipoAsignacion === 'usuarios'" x-transition class="space-y-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Seleccionar usuarios</label>
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 max-h-48 overflow-y-auto space-y-1">
                            @forelse($usuarios as $usuario)
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-white p-2 rounded-lg transition">
                                    <input type="checkbox" name="usuarios_asignados[]" value="{{ $usuario->id_usuario }}"
                                        {{ in_array($usuario->id_usuario, old('usuarios_asignados', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black transition">
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-700 text-sm block truncate">{{ $usuario->nombres }}</span>
                                        <span class="text-[11px] text-gray-400">{{ $usuario->correo }}</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-gray-400 text-xs text-center py-4">No hay usuarios registrados.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="createModal = false"
                            class="flex-1 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-200 text-xs transition-all">Cancelar</button>
                        <button type="submit"
                            class="flex-[2] py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg text-xs transition-all">Crear Cupón</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection