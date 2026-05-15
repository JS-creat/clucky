@extends('layouts.app')

@section('title', 'B-EDEN - Premium Clothing')

@section('categorias')
    {{-- BARRA DE CATEGORÍAS STICKY --}}
    <div class="sticky top-16 bg-white border-b z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="flex justify-center space-x-4 sm:space-x-8 py-3 font-medium uppercase text-xs sm:text-sm tracking-widest overflow-x-auto">
                <a href="{{ route('home') }}"
                    class="{{ !request()->has('categoria') && !request()->has('promocion') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200 whitespace-nowrap">
                    Todo
                </a>
                <a href="{{ route('home', ['categoria' => 'Mujer']) }}"
                    class="{{ request('categoria') == 'Mujer' ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200 whitespace-nowrap">
                    Mujer
                </a>
                <a href="{{ route('home', ['categoria' => 'Hombre']) }}"
                    class="{{ request('categoria') == 'Hombre' ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-500 hover:text-black' }} transition-colors duration-200 whitespace-nowrap">
                    Hombre
                </a>
                <a href="{{ route('home', ['promocion' => 1]) }}"
                    class="{{ request()->has('promocion') ? 'text-red-600 font-bold border-b-2 border-red-600 pb-1' : 'text-red-600 font-bold hover:text-red-700' }} transition-colors duration-200 whitespace-nowrap">
                    Promociones
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')

{{-- ===== AVISO DE ENTORNO DE DESARROLLO ===== --}}
<div class="bg-yellow-100 border-b border-yellow-200 py-3 px-4 sm:px-8 shadow-inner">
    <div class="max-w-7xl mx-auto flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 17c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs sm:text-sm text-yellow-800 font-bold leading-tight">
                MODO DE PRUEBA / SITIO EN DESARROLLO
            </p>
            <p class="text-[11px] sm:text-xs text-yellow-700 leading-normal mt-1">
                Este sitio es un proyecto académico/formativo. Las imágenes y productos son referenciales.
                Ninguna compra tiene validez y no debes ingresar datos reales ni de pago.
            </p>
        </div>
    </div>
</div>

{{-- ===== CARRUSEL REDISEÑADO ===== --}}
@if(isset($banners) && $banners->count() > 0)
<section class="w-full bg-gray-50 py-3 sm:py-6">
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
        class="relative max-w-7xl mx-auto px-3 sm:px-8"
    >
        {{-- CONTENEDOR DEL SLIDE --}}
        {{-- Móvil: 4/3 para tener altura visible | sm+: 21/7 proporción cine wide --}}
        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden shadow-lg bg-white [aspect-ratio:4/3] sm:[aspect-ratio:21/7]"
             style="min-height: 220px;"
        >

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
                <img
                    src="{{ asset('banners/' . $banner->imagen) }}"
                    alt="{{ $banner->titulo }}"
                    class="w-full h-full object-cover object-center"
                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                >
                @if($banner->titulo || $banner->texto_boton)
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent"></div>

                <div
                    class="absolute inset-0 flex items-center px-5 sm:px-14"
                    x-show="current === {{ $index }}"
                    x-transition:enter="transition transform duration-700 delay-300 ease-out"
                    x-transition:enter-start="opacity-0 translate-y-5"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <div class="text-white max-w-[60%] sm:max-w-xs lg:max-w-sm">
                        @if($banner->etiqueta)
                        <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-[0.25em] sm:tracking-[0.35em] text-white/65 mb-1 sm:mb-1.5">
                            {{ $banner->etiqueta }}
                        </p>
                        @endif

                        @if($banner->titulo)
                        <h2 class="text-xl sm:text-4xl font-black italic leading-none mb-2 drop-shadow">
                            {{ $banner->titulo }}
                        </h2>
                        @endif

                        @if($banner->descripcion)
                        <p class="hidden sm:block text-[11px] sm:text-sm text-white/70 mb-4 leading-relaxed">
                            {{ $banner->descripcion }}
                        </p>
                        @endif

                        @if($banner->texto_boton && $banner->url_boton)
                        <a href="{{ $banner->url_boton }}"
                            class="inline-flex items-center gap-1.5 bg-white text-black px-4 sm:px-7 py-1.5 sm:py-2.5 font-bold uppercase text-[9px] sm:text-xs tracking-widest hover:bg-black hover:text-white transition-all duration-300 group shadow-md rounded-full">
                            {{ $banner->texto_boton }}
                            <svg class="w-2.5 h-2.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endforeach

            {{-- FLECHAS --}}
            @if($banners->count() > 1)
            <button @click="prev()" aria-label="Anterior"
                class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center bg-white/90 text-gray-800 hover:bg-white hover:scale-110 transition-all duration-200 rounded-full shadow-md">
                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next()" aria-label="Siguiente"
                class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center bg-white/90 text-gray-800 hover:bg-white hover:scale-110 transition-all duration-200 rounded-full shadow-md">
                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>

        @if($banners->count() > 1)
        <div class="flex items-center justify-center gap-2 mt-3 sm:mt-4">
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
{{-- Banner por defecto si no hay banners en BD --}}
<section class="w-full bg-gray-50 py-3 sm:py-6">
    <div class="max-w-6xl mx-auto px-3 sm:px-8">
        <div class="relative rounded-xl sm:rounded-2xl overflow-hidden shadow-lg [aspect-ratio:4/3] sm:[aspect-ratio:21/8]"
             style="min-height: 200px;">
            <img src="{{ asset('images/banner-home.jpg') }}" alt="Banner" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent flex items-center px-6 sm:px-10">
                <div class="text-white">
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest text-white/65 mb-1">Nueva Colección 2026</p>
                    <h2 class="text-2xl sm:text-5xl font-black italic leading-none mb-3 sm:mb-4">SUMMER<br>SALE</h2>
                    <a href="{{ route('home', ['promocion' => 1]) }}"
                        class="inline-flex items-center gap-1.5 bg-white text-black px-4 sm:px-7 py-1.5 sm:py-2.5 font-bold uppercase text-[9px] sm:text-xs tracking-widest hover:bg-black hover:text-white transition-all duration-300 rounded-full shadow-md">
                        Comprar Ahora
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif


{{-- PRODUCTOS --}}
<div class="max-w-7xl mx-auto px-3 sm:px-8 py-6 sm:py-10">

    <div class="flex items-center justify-between mb-5 sm:mb-7">
        <div>
            <h2 class="text-xs sm:text-sm font-black uppercase tracking-[0.2em] text-gray-900">
                @if(request('categoria')) {{ request('categoria') }}
                @elseif(request('promocion')) Promociones
                @else Todos los Productos
                @endif
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $productos->count() }} artículos</p>
        </div>
    </div>

    @if($productos->isEmpty())
        <div class="text-center py-20 sm:py-24">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
            </svg>
            <p class="text-sm font-medium text-gray-400">No hay productos disponibles</p>
        </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
        @foreach($productos as $item)
            <a href="{{ url('/producto/' . $item->id_producto) }}"
                class="group cursor-pointer block bg-white border border-gray-100 rounded-xl sm:rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                <div class="relative aspect-[3/4] bg-gray-50 overflow-hidden">
                    @if($item->precio_oferta)
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-[8px] sm:text-[9px] font-black px-2 py-0.5 sm:px-2.5 sm:py-1 z-10 uppercase tracking-wider rounded-full shadow-sm">
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

                <div class="p-2.5 sm:p-3.5 space-y-0.5 sm:space-y-1">
                    <p class="text-[8px] sm:text-[9px] text-gray-400 uppercase font-bold tracking-widest">{{ $item->marca }}</p>
                    <h3 class="text-[12px] sm:text-[13px] font-semibold text-gray-800 leading-snug line-clamp-2">{{ $item->nombre_producto }}</h3>
                    <div class="h-px bg-gray-100 my-1.5 sm:my-2"></div>
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                        @if($item->precio_oferta)
                            <span class="text-xs sm:text-sm font-black text-red-500">S/ {{ number_format($item->precio_oferta, 2) }}</span>
                            <span class="text-[10px] sm:text-[11px] text-gray-400 line-through">S/ {{ number_format($item->precio, 2) }}</span>
                            <span class="ml-auto text-[8px] sm:text-[9px] font-bold text-red-500 bg-red-50 px-1 sm:px-1.5 py-0.5 rounded">
                                -{{ round((1 - $item->precio_oferta / $item->precio) * 100) }}%
                            </span>
                        @else
                            <span class="text-xs sm:text-sm font-bold text-gray-900">S/ {{ number_format($item->precio, 2) }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @endif

</div>

@endsection
