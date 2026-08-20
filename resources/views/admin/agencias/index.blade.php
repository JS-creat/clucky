@extends('admin.layout')

@section('content')

<div class="space-y-8">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <x-heroicon-o-building-office class="w-6 h-6" />
                </div>

                <span class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">
                    Administración
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                Gestión de Agencias
            </h1>

            <p class="text-gray-500 mt-2 font-medium">
                Administra los puntos de despacho y sus costos de envío.
            </p>
        </div>

        <a href="{{ route('admin.agencias.create') }}"
           class="inline-flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700
                  text-white px-6 py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100
                  transition-all hover:-translate-y-0.5 active:scale-95">
            <x-heroicon-o-plus class="w-5 h-5" />
            Nueva Agencia
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm">

        <form method="GET"
              action="{{ route('admin.agencias.index') }}"
              class="flex flex-col lg:flex-row gap-4">

            <div class="relative flex-1">
                <x-heroicon-o-magnifying-glass
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar agencia, dirección o distrito..."
                    class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl
                           text-sm font-medium text-gray-700 outline-none
                           focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                           transition-all">
            </div>

            <select
                name="estado"
                class="lg:w-48 px-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl
                       text-sm font-bold text-gray-700 outline-none
                       focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">

                <option value="">Todos los estados</option>

                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>
                    Activas
                </option>

                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>
                    Inactivas
                </option>
            </select>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-4
                       bg-gray-900 hover:bg-black text-white rounded-2xl
                       font-bold transition-all">
                <x-heroicon-o-funnel class="w-5 h-5" />
                Filtrar
            </button>

            @if(request()->hasAny(['search', 'estado']))
                <a
                    href="{{ route('admin.agencias.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-4
                           bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl
                           font-bold transition-all">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    Limpiar
                </a>
            @endif

        </form>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm font-bold text-gray-900">
                {{ $agencias->total() }}
                {{ $agencias->total() === 1 ? 'agencia encontrada' : 'agencias encontradas' }}
            </p>

            @if(request('search'))
                <p class="text-xs text-gray-400 mt-1">
                    Resultados para:
                    <span class="font-bold text-gray-600">
                        "{{ request('search') }}"
                    </span>
                </p>
            @endif
        </div>

        <div class="text-xs font-semibold text-gray-400">
            Mostrando {{ $agencias->firstItem() ?? 0 }} -
            {{ $agencias->lastItem() ?? 0 }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        @forelse($agencias as $agencia)

            <div class="group bg-white border border-gray-100 rounded-3xl p-6
                        shadow-sm hover:shadow-xl hover:-translate-y-1
                        transition-all duration-300
                        {{ !$agencia->estado ? 'opacity-70' : '' }}">

                <div class="flex items-start justify-between gap-4">

                    <div class="w-12 h-12 rounded-2xl
                                bg-indigo-50 text-indigo-600
                                flex items-center justify-center flex-shrink-0">

                        <x-heroicon-o-building-office class="w-6 h-6" />

                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                 text-[11px] font-black uppercase tracking-wide
                                 {{ $agencia->estado
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : 'bg-gray-100 text-gray-500' }}">

                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $agencia->estado ? 'bg-emerald-500' : 'bg-gray-400' }}">
                        </span>

                        {{ $agencia->estado ? 'Activa' : 'Inactiva' }}

                    </span>

                </div>

                <div class="mt-6">

                    <h3 class="text-xl font-black text-gray-900 leading-tight">
                        {{ $agencia->nombre_agencia }}
                    </h3>

                    <div class="flex items-start gap-2 mt-3 text-gray-500">
                        <x-heroicon-o-map-pin class="w-4 h-4 mt-0.5 flex-shrink-0" />

                        <p class="text-sm font-medium leading-relaxed">
                            {{ $agencia->direccion }}
                        </p>
                    </div>

                    <div class="mt-4 p-4 rounded-2xl bg-gray-50">

                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400">
                            Ubicación
                        </p>

                        <p class="text-sm font-bold text-gray-700 mt-1">
                            {{ $agencia->distrito->nombre_distrito }}
                            <span class="text-gray-300 mx-1">/</span>
                            {{ $agencia->distrito->provincia->nombre_provincia }}
                        </p>

                    </div>

                </div>

                <div class="mt-6 pt-5 border-t border-gray-100
                            flex items-center justify-between gap-4">

                    <div>
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400">
                            Costo de envío
                        </p>

                        <p class="text-xl font-black text-gray-900 mt-1">
                            S/ {{ number_format($agencia->costo_envio, 2) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">

                        <a
                            href="{{ route('admin.agencias.edit', $agencia) }}"
                            title="Editar agencia"
                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-500
                                   hover:bg-indigo-50 hover:text-indigo-600
                                   flex items-center justify-center transition-all">

                            <x-heroicon-o-pencil-square class="w-5 h-5" />

                        </a>

                        <form
                            action="{{ route('admin.agencias.toggle', $agencia) }}"
                            method="POST">

                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                title="{{ $agencia->estado ? 'Desactivar' : 'Activar' }}"
                                class="w-10 h-10 rounded-xl bg-gray-50
                                       {{ $agencia->estado
                                            ? 'text-gray-500 hover:bg-rose-50 hover:text-rose-600'
                                            : 'text-gray-500 hover:bg-emerald-50 hover:text-emerald-600' }}
                                       flex items-center justify-center transition-all">

                                <x-heroicon-o-power class="w-5 h-5" />

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full bg-white border border-gray-100 rounded-3xl
                        py-20 px-6 text-center">

                <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100
                            flex items-center justify-center text-gray-400">

                    <x-heroicon-o-building-office class="w-8 h-8" />

                </div>

                <h3 class="mt-5 text-lg font-black text-gray-800">
                    No encontramos agencias
                </h3>

                <p class="text-sm text-gray-400 mt-2">
                    Prueba modificando los filtros de búsqueda.
                </p>

            </div>

        @endforelse

    </div>

    @if($agencias->hasPages())

        <div class="bg-white border border-gray-100 rounded-3xl p-4 shadow-sm">
            {{ $agencias->withQueryString()->links() }}
        </div>

    @endif

</div>

@endsection