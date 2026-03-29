@extends('admin.layout')

@section('content')
    <div x-data="{ createModal: false }" class="p-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Gestión de Agencias</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Administra los puntos de despacho y sus costos.</p>
            </div>

            <a href="{{ route('admin.agencias.create') }}"
                class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
                <x-heroicon-o-plus class="w-6 h-6" />
                Nueva Agencia
            </a>
        </div>

        <hr class="border-gray-100 mb-10">

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 text-emerald-600 rounded-2xl font-bold border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        {{-- GRID DE AGENCIAS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($agencias as $agencia)
                <div
                    class="group relative bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-2xl transition-all duration-500 {{ !$agencia->estado ? 'opacity-75 grayscale' : '' }}">

                    {{-- Badge Estado --}}
                    <div class="absolute top-8 right-8">
                        <span
                            class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-xs font-black uppercase tracking-wider {{ $agencia->estado ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $agencia->estado ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>

                    <div class="mb-8">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
                            <x-heroicon-o-map-pin class="w-8 h-8" />
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 leading-tight">{{ $agencia->nombre_agencia }}</h3>
                        <p class="text-gray-400 font-medium mt-1">{{ Str::limit($agencia->direccion, 40) }}</p>
                        <p class="text-sm text-gray-500 mt-3 font-medium">
                            {{ $agencia->distrito->nombre_distrito }}, {{ $agencia->distrito->provincia->nombre_provincia }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                        <div class="text-xl font-black text-gray-900">
                            S/ {{ number_format($agencia->costo_envio, 2) }}
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.agencias.edit', $agencia) }}"
                                class="text-gray-400 hover:text-indigo-600 transition">
                                <x-heroicon-o-pencil-square class="w-6 h-6" />
                            </a>

                            <form action="{{ route('admin.agencias.toggle', $agencia) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-gray-400 hover:text-rose-500 transition">
                                    <x-heroicon-o-power class="w-6 h-6" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold">No hay agencias registradas.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $agencias->links() }}
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
