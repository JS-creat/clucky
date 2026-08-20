@extends('admin.layout')

@section('content')

<div class="space-y-8 px-2 sm:px-4 lg:px-0">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">

        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-11 h-11 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <x-heroicon-o-shopping-cart class="w-6 h-6" />
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    Pedidos
                </h1>
            </div>

            <p class="text-gray-500 text-sm sm:text-base lg:text-lg font-medium">
                Gestiona y supervisa el flujo de ventas de tu tienda.
            </p>

            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black uppercase tracking-wider">
                <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                {{ $pedidos->total() }} registros totales
            </div>
        </div>

    </div>

    <div class="h-px bg-gray-100"></div>

    <form
        method="GET"
        action="{{ route('admin.pedidos.index') }}"
        class="flex flex-col sm:flex-row gap-3"
    >

        <div class="relative flex-1">

            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
            </div>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Buscar por pedido, cliente, correo o estado..."
                class="w-full pl-12 pr-4 py-3.5 rounded-2xl
                       border border-gray-200 bg-white
                       text-sm font-medium text-gray-700
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2 focus:ring-indigo-400
                       focus:border-transparent
                       shadow-sm transition"
            >

        </div>

        <div class="flex gap-2">

            <button
                type="submit"
                class="flex-1 sm:flex-none px-6 py-3.5
                       bg-indigo-600 hover:bg-indigo-700
                       text-white text-sm font-bold
                       rounded-2xl shadow-sm
                       transition-colors"
            >
                Buscar
            </button>

            @if(request('search'))

                <a
                    href="{{ route('admin.pedidos.index') }}"
                    class="px-5 py-3.5 bg-gray-100
                           hover:bg-gray-200 text-gray-600
                           text-sm font-bold rounded-2xl
                           transition-colors"
                >
                    Limpiar
                </a>

            @endif

        </div>

    </form>

    @php
        $colores = [
            'Pendiente' => 'bg-amber-50 text-amber-600 border-amber-100',
            'Confirmado' => 'bg-teal-50 text-teal-600 border-teal-100',
            'En camino' => 'bg-sky-50 text-sky-600 border-sky-100',
            'Listo para recoger' => 'bg-purple-50 text-purple-600 border-purple-100',
            'Entregado' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'Anulado' => 'bg-rose-50 text-rose-600 border-rose-100',
        ];
    @endphp

    <div class="hidden md:block bg-white rounded-[2rem] lg:rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] border-collapse">

                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">

                        <th class="px-5 lg:px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Pedido
                        </th>

                        <th class="px-5 lg:px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Cliente
                        </th>

                        <th class="px-5 lg:px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Fecha
                        </th>

                        <th class="px-5 lg:px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Total
                        </th>

                        <th class="px-5 lg:px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Estado
                        </th>

                        <th class="px-5 lg:px-6 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            Acción
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">

                    @forelse($pedidos as $pedido)

                        @php
                            $color = $colores[$pedido->estado_pedido]
                                ?? 'bg-gray-50 text-gray-500 border-gray-100';
                        @endphp

                        <tr class="group hover:bg-indigo-50/30 transition-colors duration-300">

                            <td class="px-5 lg:px-6 py-6">
                                <span class="text-base lg:text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    #{{ $pedido->numero_pedido }}
                                </span>
                            </td>

                            <td class="px-5 lg:px-6 py-6 max-w-[250px]">

                                @if($pedido->usuario)

                                    <div class="truncate">
                                        <span class="text-sm lg:text-base font-bold text-gray-800">
                                            {{ $pedido->usuario->nombres }}
                                            {{ $pedido->usuario->apellidos }}
                                        </span>
                                    </div>

                                    <div class="truncate text-xs text-gray-400 font-medium mt-1">
                                        {{ $pedido->usuario->correo }}
                                    </div>

                                @else

                                    <span class="text-sm text-rose-600 italic font-medium">
                                        Usuario eliminado
                                    </span>

                                @endif

                            </td>

                            <td class="px-5 lg:px-6 py-6">

                                <div class="flex items-center gap-2 text-sm font-bold text-gray-600">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-indigo-300 flex-shrink-0" />

                                    {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}
                                </div>

                                <div class="flex items-center gap-2 text-xs text-gray-400 mt-1">
                                    <x-heroicon-o-clock class="w-4 h-4 text-gray-300 flex-shrink-0" />

                                    {{ \Carbon\Carbon::parse($pedido->created_at)->format('H:i') }}
                                </div>

                            </td>

                            <td class="px-5 lg:px-6 py-6 text-center">

                                <span class="inline-block text-base font-black text-gray-900 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100 whitespace-nowrap">
                                    S/ {{ number_format($pedido->total_pedido, 2) }}
                                </span>

                            </td>

                            <td class="px-5 lg:px-6 py-6 text-center">

                                <span class="inline-flex px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider border {{ $color }} whitespace-nowrap">
                                    {{ $pedido->estado_pedido }}
                                </span>

                            </td>

                            <td class="px-5 lg:px-6 py-6 text-right">

                                <a
                                    href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
                                    class="inline-flex w-11 h-11
                                           items-center justify-center
                                           rounded-2xl bg-white
                                           border border-gray-100
                                           text-gray-400 shadow-sm
                                           hover:bg-indigo-600
                                           hover:text-white
                                           hover:shadow-lg
                                           hover:shadow-indigo-200
                                           transition-all duration-300"
                                >
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="py-28 text-center">

                                <div class="w-20 h-20 bg-gray-50 rounded-[1.75rem] flex items-center justify-center mx-auto mb-5">
                                    <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-gray-200" />
                                </div>

                                <h3 class="text-lg font-black text-gray-400">
                                    {{ request('search')
                                        ? 'Sin resultados para "' . request('search') . '"'
                                        : 'No hay pedidos registrados'
                                    }}
                                </h3>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="flex flex-col gap-4 md:hidden">

        @forelse($pedidos as $pedido)

            @php
                $color = $colores[$pedido->estado_pedido]
                    ?? 'bg-gray-50 text-gray-500 border-gray-100';
            @endphp

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">

                <div class="flex items-start justify-between gap-3 mb-4">

                    <div>
                        <span class="text-xl font-black text-gray-900">
                            #{{ $pedido->numero_pedido }}
                        </span>

                        <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-1">
                            <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                            {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border {{ $color }}">
                        {{ $pedido->estado_pedido }}
                    </span>

                </div>

                <div class="border-t border-gray-50 pt-4 mb-4">

                    @if($pedido->usuario)

                        <p class="text-sm font-bold text-gray-800">
                            {{ $pedido->usuario->nombres }}
                            {{ $pedido->usuario->apellidos }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1 truncate">
                            {{ $pedido->usuario->correo }}
                        </p>

                    @else

                        <p class="text-sm font-medium text-rose-500 italic">
                            Usuario eliminado
                        </p>

                    @endif

                </div>

                <div class="flex items-center justify-between gap-3">

                    <span class="text-lg font-black text-gray-900 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                        S/ {{ number_format($pedido->total_pedido, 2) }}
                    </span>

                    <a
                        href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
                        class="inline-flex items-center justify-center gap-2
                               px-4 py-3 rounded-2xl
                               bg-indigo-50 text-indigo-600
                               text-sm font-black
                               hover:bg-indigo-600
                               hover:text-white
                               transition-colors"
                    >
                        <x-heroicon-o-eye class="w-4 h-4" />
                        Ver
                    </a>

                </div>

            </div>

        @empty

            <div class="py-20 text-center bg-white rounded-3xl border border-gray-100">

                <x-heroicon-o-clipboard-document-list
                    class="w-12 h-12 text-gray-200 mx-auto mb-4"
                />

                <h3 class="text-lg font-black text-gray-400">
                    {{ request('search')
                        ? 'Sin resultados para "' . request('search') . '"'
                        : 'No hay pedidos registrados'
                    }}
                </h3>

            </div>

        @endforelse

    </div>

    <div class="pt-2 pb-4">
        {{ $pedidos->links() }}
    </div>

</div>

@endsection