@extends('admin.layout')

@section('content')
<div x-data="{ createModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-indigo-500 mb-2">Promociones</p>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Cupones</h1>
            <p class="text-gray-500 mt-2 text-lg font-medium">Administra los cupones de descuento.</p>
        </div>
        <button @click="createModal = true"
            class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
            <x-heroicon-o-plus class="w-6 h-6" />
            Nuevo Cupón
        </button>
    </div>

    <hr class="border-gray-100 mb-10">

    {{-- ESTADÍSTICAS RÁPIDAS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
        @php
            $total    = $cupones->count();
            $activos  = $cupones->where('estado_cupon', 1)->count();
            $vencidos = $cupones->filter(fn($c) => $c->fecha_vencimiento < now()->toDateString())->count();
            $inactivos = $cupones->where('estado_cupon', 0)->count();
        @endphp

        <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total</p>
            <p class="text-4xl font-black text-gray-900">{{ $total }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 rounded-[2rem] p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-emerald-600 mb-1">Activos</p>
            <p class="text-4xl font-black text-emerald-700">{{ $activos }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-[2rem] p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-amber-600 mb-1">Vencidos</p>
            <p class="text-4xl font-black text-amber-700">{{ $vencidos }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-100 rounded-[2rem] p-6 shadow-sm">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Inactivos</p>
            <p class="text-4xl font-black text-gray-500">{{ $inactivos }}</p>
        </div>
    </div>

    {{-- TABLA DE CUPONES --}}
    <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-sm overflow-hidden">

        {{-- Encabezado tabla --}}
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-lg font-black text-gray-800">Lista de Cupones</h2>
            <span class="text-sm font-bold text-gray-400">{{ $cupones->count() }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="text-left px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Código</th>
                        <th class="text-left px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Descuento</th>
                        <th class="text-left px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Compra Mínima</th>
                        <th class="text-left px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Vencimiento</th>
                        <th class="text-left px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Estado</th>
                        <th class="text-right px-8 py-4 text-xs font-black uppercase tracking-widest text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($cupones as $cupon)
                        @php
                            $vencido = $cupon->fecha_vencimiento < now()->toDateString();
                        @endphp
                        <tr x-data="{ confirmModal: false, editModal: false }"
                            class="hover:bg-gray-50/60 transition-colors group">

                            {{-- Código --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-ticket class="w-5 h-5 text-indigo-600" />
                                    </div>
                                    <span class="font-black text-gray-900 tracking-widest text-sm font-mono bg-gray-100 px-3 py-1.5 rounded-lg">
                                        {{ $cupon->codigo_cupon }}
                                    </span>
                                </div>
                            </td>

                            {{-- Descuento --}}
                            <td class="px-6 py-5">
                                <span class="text-2xl font-black text-indigo-600">
                                    S/ {{ number_format($cupon->monto_cupon, 2) }}
                                </span>
                            </td>

                            {{-- Compra mínima --}}
                            <td class="px-6 py-5">
                                <span class="font-bold text-gray-700">
                                    S/ {{ number_format($cupon->monto_compra_minima, 2) }}
                                </span>
                            </td>

                            {{-- Vencimiento --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    @if($vencido)
                                        <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-500 flex-shrink-0" />
                                    @else
                                        <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                    @endif
                                    <span class="font-semibold {{ $vencido ? 'text-amber-600' : 'text-gray-600' }}">
                                        {{ \Carbon\Carbon::parse($cupon->fecha_vencimiento)->format('d M Y') }}
                                    </span>
                                </div>
                                @if($vencido)
                                    <span class="text-xs font-bold text-amber-500 mt-0.5 block">Vencido</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider
                                    {{ $cupon->estado_cupon ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cupon->estado_cupon ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                    {{ $cupon->estado_cupon ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <button @click="editModal = true"
                                        class="p-2.5 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
                                        title="Editar">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                    <form action="{{ route('admin.cupones.toggle', $cupon->id_cupon) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="p-2.5 rounded-xl transition-all {{ $cupon->estado_cupon ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $cupon->estado_cupon ? 'Desactivar' : 'Activar' }}">
                                            @if($cupon->estado_cupon)
                                                <x-heroicon-o-pause-circle class="w-5 h-5" />
                                            @else
                                                <x-heroicon-o-play-circle class="w-5 h-5" />
                                            @endif
                                        </button>
                                    </form>
                                    <button @click="confirmModal = true"
                                        class="p-2.5 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                        title="Eliminar">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>

                            {{-- Modal Confirmar Eliminar --}}
                            <template x-if="confirmModal">
                                <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                                    <div @click="confirmModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                                    <div class="relative bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center">
                                        <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <x-heroicon-o-trash class="w-10 h-10" />
                                        </div>
                                        <h3 class="text-2xl font-black text-gray-900 mb-2">¿Eliminar cupón?</h3>
                                        <p class="text-gray-500 font-medium mb-8">
                                            Estás a punto de eliminar el cupón
                                            <span class="font-black text-gray-800 font-mono">{{ $cupon->codigo_cupon }}</span>.
                                        </p>
                                        <form action="{{ route('admin.cupones.destroy', $cupon->id_cupon) }}" method="POST" class="flex gap-3">
                                            @csrf @method('DELETE')
                                            <button type="button" @click="confirmModal = false"
                                                class="flex-1 py-3 bg-gray-100 text-gray-500 font-bold rounded-xl">Cancelar</button>
                                            <button type="submit"
                                                class="flex-1 py-3 bg-rose-500 text-white font-bold rounded-xl shadow-lg">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </template>

                            {{-- Modal Editar Cupón --}}
                            <template x-if="editModal">
                                <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                                    <div @click="editModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md"></div>
                                    <div class="relative bg-white rounded-[2.5rem] p-10 max-w-lg w-full shadow-2xl">

                                        <div class="flex justify-between items-center mb-8">
                                            <h2 class="text-3xl font-black text-gray-900">Editar Cupón</h2>
                                            <button @click="editModal = false"
                                                class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition">
                                                <x-heroicon-o-x-mark class="w-6 h-6" />
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.cupones.update', $cupon->id_cupon) }}" method="POST" class="space-y-5">
                                            @csrf @method('PUT')

                                            <div>
                                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Código</label>
                                                <input type="text" name="codigo_cupon" value="{{ $cupon->codigo_cupon }}" required
                                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-mono font-bold text-lg uppercase tracking-widest transition-all">
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descuento (S/)</label>
                                                    <input type="number" name="monto_cupon" value="{{ $cupon->monto_cupon }}" step="0.01" min="0" required
                                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold text-lg transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mínimo (S/)</label>
                                                    <input type="number" name="monto_compra_minima" value="{{ $cupon->monto_compra_minima }}" step="0.01" min="0" required
                                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold text-lg transition-all">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Fecha de Vencimiento</label>
                                                <input type="date" name="fecha_vencimiento" value="{{ $cupon->fecha_vencimiento }}" required
                                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all">
                                            </div>

                                            <div class="flex gap-4 pt-2">
                                                <button type="button" @click="editModal = false"
                                                    class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl">Cancelar</button>
                                                <button type="submit"
                                                    class="flex-[2] py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                        <x-heroicon-o-ticket class="w-10 h-10 text-gray-300" />
                                    </div>
                                    <p class="text-gray-400 font-bold text-lg">No hay cupones registrados.</p>
                                    <p class="text-gray-300 font-medium mt-1">Crea tu primer cupón de descuento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== MODAL CREAR CUPÓN ===================== --}}
    <template x-if="createModal">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div @click="createModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xl"></div>
            <div class="relative bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl">

                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900">Nuevo Cupón</h2>
                        <p class="text-gray-400 mt-1 font-medium italic">Crea un código de descuento.</p>
                    </div>
                    <button @click="createModal = false"
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition shadow-sm">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <form action="{{ route('admin.cupones.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Código *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-5 flex items-center text-gray-300">
                                <x-heroicon-o-ticket class="w-6 h-6" />
                            </span>
                            <input type="text" name="codigo_cupon" required autofocus
                                placeholder="Ej: VERANO2025"
                                class="w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-mono font-black text-lg uppercase tracking-widest transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descuento (S/) *</label>
                            <input type="number" name="monto_cupon" step="0.01" min="0" required
                                placeholder="0.00"
                                class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-black text-xl text-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mínimo (S/) *</label>
                            <input type="number" name="monto_compra_minima" step="0.01" min="0" required
                                placeholder="0.00"
                                class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold text-xl transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Fecha de Vencimiento *</label>
                        <input type="date" name="fecha_vencimiento" required
                            min="{{ now()->toDateString() }}"
                            class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none font-bold transition-all">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="createModal = false"
                            class="flex-1 px-4 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                        <button type="submit"
                            class="flex-[2] px-4 py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg transition-all active:scale-95">Crear Cupón</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection