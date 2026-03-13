@extends('layouts.app')

@section('title', 'C\'Lucky - Tienda Online')

@section('categorias')
    {{-- BARRA DE CATEGORÍAS STICKY --}}
    <div class="sticky top-16 bg-white border-b z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="flex justify-center space-x-8 py-3 font-medium uppercase text-sm tracking-widest">
                <a href="{{ route('home') }}"
                    class="{{ !request()->has('categoria') && !request()->has('promocion') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200">
                    Todo
                </a>
                <a href="{{ route('home', ['categoria' => 'Mujer']) }}"
                    class="{{ request('categoria') == 'Mujer' ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200">
                    Mujer
                </a>
                <a href="{{ route('home', ['categoria' => 'Hombre']) }}"
                    class="{{ request('categoria') == 'Hombre' ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200">
                    Hombre
                </a>
                <a href="{{ route('home', ['promocion' => 1]) }}"
                    class="{{ request()->has('promocion') ? 'text-red-600 font-bold border-b-2 border-red-600 pb-1' : 'text-red-600 font-bold hover:text-red-700' }} transition-colors duration-200">
                    Promociones
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')

{{-- ===== CARRUSEL REDISEÑADO ===== --}}
@if(isset($banners) && $banners->count() > 0)
<section class="w-full bg-gray-50 py-4 sm:py-6">
    <div
        x-data="{
            current: 0,
            total: {{ $banners->count() }},
            autoplay: null,
            init() { this.startAutoplay(); },
            startAutoplay() { this.autoplay = setInterval(() => this.next(), 5500); },
            stopAutoplay() { clearInterval(this.autoplay); },
            next() { this.current = (this.current + 1) % this.total; },
            prev() { this.current = (this.current - 1 + this.total) % this.total; },
            goTo(i) { this.current = i; this.stopAutoplay(); this.startAutoplay(); }
        }"
        @mouseenter="stopAutoplay()"
        @mouseleave="startAutoplay()"
        class="relative max-w-7xl mx-auto px-4 sm:px-8"
    >
        {{-- CONTENEDOR DEL SLIDE--}}
        <div class="relative rounded-2xl overflow-hidden shadow-xl bg-white" style="aspect-ratio: 21/7; min-height: 240px;">

            @foreach($banners as $index => $banner)
            <div
                x-show="current === {{ $index }}"
                x-transition:enter="transition-opacity duration-700 ease-in-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-500 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0"
            >
                {{-- Imagen completa --}}
                <img
                    src="{{ asset('banners/' . $banner->imagen) }}"
                    alt="{{ $banner->titulo }}"
                    class="w-full h-full object-cover object-center"
                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                >
                @if($banner->titulo || $banner->texto_boton)
                <div class="absolute inset-0 bg-gradient-to-r from-black/55 via-black/15 to-transparent"></div>

                {{-- Texto animado --}}
                <div
                    class="absolute inset-0 flex items-center px-8 sm:px-14"
                    x-show="current === {{ $index }}"
                    x-transition:enter="transition transform duration-700 delay-300 ease-out"
                    x-transition:enter-start="opacity-0 translate-y-5"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <div class="text-white max-w-xs sm:max-w-sm">
                        @if($banner->etiqueta)
                        <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.35em] text-white/65 mb-1.5">
                            {{ $banner->etiqueta }}
                        </p>
                        @endif

                        @if($banner->titulo)
                        <h2 class="text-2xl sm:text-4xl font-black italic leading-none mb-2 drop-shadow">
                            {{ $banner->titulo }}
                        </h2>
                        @endif

                        @if($banner->descripcion)
                        <p class="text-[11px] sm:text-sm text-white/70 mb-4 leading-relaxed hidden sm:block">
                            {{ $banner->descripcion }}
                        </p>
                        @endif

                        @if($banner->texto_boton && $banner->url_boton)
                        <a href="{{ $banner->url_boton }}"
                            class="inline-flex items-center gap-2 bg-white text-black px-5 sm:px-7 py-2 sm:py-2.5 font-bold uppercase text-[10px] sm:text-xs tracking-widest hover:bg-black hover:text-white transition-all duration-300 group shadow-md rounded-full">
                            {{ $banner->texto_boton }}
                            <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endforeach

            {{-- FLECHAS--}}
            @if($banners->count() > 1)
            <button @click="prev()" aria-label="Anterior"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 flex items-center justify-center bg-white/90 text-gray-800 hover:bg-white hover:scale-110 transition-all duration-200 rounded-full shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next()" aria-label="Siguiente"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 flex items-center justify-center bg-white/90 text-gray-800 hover:bg-white hover:scale-110 transition-all duration-200 rounded-full shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>

        @if($banners->count() > 1)
        <div class="flex items-center justify-center gap-2 mt-4">
            @foreach($banners as $index => $banner)
            <button
                @click="goTo({{ $index }})"
                :class="current === {{ $index }} ? 'w-6 bg-gray-800' : 'w-2 bg-gray-300 hover:bg-gray-500'"
                class="h-2 rounded-full transition-all duration-300"
                aria-label="Slide {{ $index + 1 }}"
            ></button>
            @endforeach
        </div>
        @endif

    </div>
</section>

@else
<section class="w-full bg-gray-50 py-4 sm:py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-8">
        <div class="relative rounded-2xl overflow-hidden shadow-xl" style="aspect-ratio: 21/8; min-height: 200px;">
            <img src="{{ asset('images/banner-home.jpg') }}" alt="Banner" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/55 via-black/15 to-transparent flex items-center px-10">
                <div class="text-white">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-white/65 mb-1">Nueva Colección 2026</p>
                    <h2 class="text-3xl sm:text-5xl font-black italic leading-none mb-4">SUMMER<br>SALE</h2>
                    <a href="{{ route('home', ['promocion' => 1]) }}"
                        class="inline-flex items-center gap-2 bg-white text-black px-7 py-2.5 font-bold uppercase text-xs tracking-widest hover:bg-black hover:text-white transition-all duration-300 rounded-full shadow-md">
                        Comprar Ahora
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif


{{-- PRODUCTOS --}}
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">

    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-900">
                @if(request('categoria')) {{ request('categoria') }}
                @elseif(request('promocion')) Promociones
                @else Todos los Productos
                @endif
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $productos->count() }} artículos</p>
        </div>
    </div>

    @if($productos->isEmpty())
        <div class="text-center py-24">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
            </svg>
            <p class="text-sm font-medium text-gray-400">No hay productos disponibles</p>
        </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($productos as $item)
            <a href="{{ url('/producto/' . $item->id_producto) }}"
                class="group cursor-pointer block bg-white border border-gray-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                <div class="relative aspect-[3/4] bg-gray-50 overflow-hidden">
                    @if($item->precio_oferta)
                        <span class="absolute top-2.5 left-2.5 bg-red-500 text-white text-[9px] font-black px-2.5 py-1 z-10 uppercase tracking-wider rounded-full shadow-sm">
                            OFERTA
                        </span>
                    @endif
                    <img
                        src="{{ asset('productos/' . $item->imagen) }}"
                        alt="{{ $item->nombre_producto }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
                </div>

                <div class="p-3.5 space-y-1">
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">{{ $item->marca }}</p>
                    <h3 class="text-[13px] font-semibold text-gray-800 leading-snug line-clamp-2">{{ $item->nombre_producto }}</h3>
                    <div class="h-px bg-gray-100 my-2"></div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($item->precio_oferta)
                            <span class="text-sm font-black text-red-500">S/ {{ number_format($item->precio_oferta, 2) }}</span>
                            <span class="text-[11px] text-gray-400 line-through">S/ {{ number_format($item->precio, 2) }}</span>
                            <span class="ml-auto text-[9px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">
                                -{{ round((1 - $item->precio_oferta / $item->precio) * 100) }}%
                            </span>
                        @else
                            <span class="text-sm font-bold text-gray-900">S/ {{ number_format($item->precio, 2) }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @endif

</div>

@endsection
