@extends('layouts.app')

@section('title', 'C\'Lucky - Tienda Online')

@section('categorias')
    {{-- BARRA DE CATEGORÍAS STICKY --}}
    <div class="sticky top-16 bg-white border-b z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="flex justify-center space-x-8 py-3 font-medium uppercase text-sm tracking-widest">
                <a href="{{ route('home') }}"
                    class="{{ !request()->has('categoria') && !request()->has('promocion') ? 'text-black font-bold' : 'text-gray-900' }} transition-colors">
                    Todo
                </a>

                <a href="{{ route('home', ['categoria' => 'Mujer']) }}"
                    class="{{ request('categoria') == 'Mujer' ? 'text-black font-bold' : 'text-gray-900' }} transition-colors">
                    Mujer
                </a>

                <a href="{{ route('home', ['categoria' => 'Hombre']) }}"
                    class="{{ request('categoria') == 'Hombre' ? 'text-black font-bold' : 'text-gray-900' }} transition-colors">
                    Hombre
                </a>

                <a href="{{ route('home', ['promocion' => 1]) }}"
                    class="{{ request()->has('promocion') ? 'text-black font-bold' : 'text-red-600 font-bold' }} transition-colors">
                    Promociones
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Carrusel -->
    <section class="relative w-full h-[500px] bg-gray-100 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner-home.jpg') }}" alt="Promoción Summer Sale"
                class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <div class="relative max-w-full mx-auto h-full px-4 sm:px-8 flex items-center">
            <div class="text-white">
                <h2 class="text-sm uppercase tracking-[0.3em] font-bold mb-2">Nueva Colección 2026</h2>
                <h1 class="text-5xl md:text-7xl font-black italic mb-6 leading-none">
                    SUMMER <br> SALE
                </h1>
                <p class="text-lg md:text-xl mb-8 max-w-md font-medium">
                    Aprovecha hasta un <span class="text-pink-400 font-bold">70% OFF</span> en prendas seleccionadas.
                </p>
                <a href="#"
                    class="inline-block bg-white text-black px-10 py-4 font-bold uppercase text-sm tracking-widest hover:bg-black hover:text-white transition-colors duration-300">
                    Comprar Ahora
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($productos as $item)
                <a href="{{ url('/producto/' . $item->id_producto) }}"
                    class="group cursor-pointer block bg-white border border-gray-200 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">

                    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden mb-4 rounded-lg">
                        @if($item->precio_oferta)
                            <span
                                class="absolute top-0 left-0 bg-red-600 text-white text-[10px] font-bold px-2 py-1 z-10 uppercase">
                                Oferta
                            </span>
                        @endif
                        <img src="{{ asset('productos/' . $item->imagen) }}" alt="{{ $item->nombre_producto }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $item->marca }}</p>
                        <h3 class="text-sm font-medium text-gray-800">{{ $item->nombre_producto }}</h3>
                        <div class="h-[1px] bg-gray-100 my-2"></div>

                        <div class="flex flex-col">
                            @if($item->precio_oferta)
                                <span class="text-sm font-black text-[#f50057]">
                                    S/ {{ number_format($item->precio_oferta, 2) }}
                                </span>
                                <span class="text-[11px] text-gray-400 line-through">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @else
                                <span class="text-sm font-bold text-black">
                                    S/ {{ number_format($item->precio, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection