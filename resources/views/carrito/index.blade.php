<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolsa de Compras – C Lucky</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        gold: '#C9A84C',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-stone-50 font-sans text-gray-900 antialiased">


{{-- ══════════════════════════════
     NAVBAR  (sin cambios)
══════════════════════════════ --}}
<nav class="border-b sticky top-0 bg-white z-50">
    <div class="max-w-full mx-auto px-4 sm:px-8">
        <div class="flex justify-between h-20 items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                        class="h-14 w-auto transition-transform hover:scale-105">
                </a>
            </div>
        </div>
    </div>
</nav>


{{-- ══════════════════════════════
     CONTENIDO
══════════════════════════════ --}}
<div x-data="carritoData()" x-cloak
     class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-display font-semibold">Tu bolsa de compras</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if(count($items) > 0)
                {{ count($items) }} {{ count($items) == 1 ? 'producto' : 'productos' }} seleccionados
            @else
                No tienes productos en tu bolsa
            @endif
        </p>
    </div>

    @if(count($items) > 0)

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- ─────────────────────────
             IZQUIERDA  (3 columnas)
        ───────────────────────── --}}
        <div class="lg:col-span-3 space-y-3">

            @foreach($items as $id => $detalles)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex gap-4 group transition-all hover:shadow-md hover:border-gray-200">

                {{-- Imagen --}}
                <div class="relative shrink-0">
                    <img src="{{ asset('productos/' . $detalles['imagen']) }}"
                         alt="{{ $detalles['nombre'] }}"
                         class="w-20 h-24 sm:w-24 sm:h-28 object-cover rounded-xl border border-gray-100">
                    @if(isset($detalles['precio_oferta']))
                    <span class="absolute -top-1.5 -left-1.5 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        OFERTA
                    </span>
                    @endif
                </div>

                {{-- Info + controles --}}
                <div class="flex-1 flex flex-col justify-between min-w-0">

                    <div>
                        <h3 class="font-semibold text-sm leading-snug truncate">{{ $detalles['nombre'] }}</h3>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5">
                                <span class="font-medium text-gray-700">Color:</span>
                                {{ $detalles['color'] ?? '—' }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5">
                                <span class="font-medium text-gray-700">Talla:</span>
                                {{ $detalles['talla'] ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-3 flex-wrap gap-3">

                        {{-- Cantidad --}}
                        <div class="flex items-center gap-0.5 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden">
                            <a href="{{ route('carrito.disminuir', $id) }}"
                               class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm">−</a>
                            <span class="w-8 text-center text-sm font-semibold">{{ $detalles['cantidad'] }}</span>
                            <a href="{{ route('carrito.aumentar', $id) }}"
                               class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm">+</a>
                        </div>

                        {{-- Eliminar --}}
                        <a href="{{ route('carrito.eliminar', $id) }}"
                           class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </a>

                        {{-- Precio --}}
                        <div class="text-right ml-auto">
                            <p class="text-xs text-gray-400">S/ {{ number_format($detalles['precio'], 2) }} c/u</p>
                            <p class="text-sm font-bold text-gray-900">
                                S/ {{ number_format($detalles['precio'] * $detalles['cantidad'], 2) }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

            {{-- Seguir comprando --}}
            <div class="pt-2">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-700 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Seguir comprando
                </a>
            </div>

        </div>


        {{-- ─────────────────────────
             DERECHA  (2 columnas)
        ───────────────────────── --}}
        <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-24 h-fit">

            {{-- Resumen --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                <h2 class="font-display font-semibold text-lg mb-1">Resumen del pedido</h2>
                <p class="text-xs text-gray-400 mb-5">
                    {{ count($items) }} {{ count($items) == 1 ? 'producto' : 'productos' }}
                </p>

                {{-- Líneas de total --}}
                <div class="space-y-2 border-b border-gray-100 pb-4">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>S/ {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Envío</span>
                        <span class="text-gray-400 italic text-xs">Se calcula en el checkout</span>
                    </div>
                </div>

                <div class="flex justify-between items-end pt-4">
                    <span class="font-display font-semibold">Total</span>
                    <div class="text-right">
                        <p class="text-2xl font-display font-bold">S/ {{ number_format($total, 2) }}</p>
                        <p class="text-xs text-gray-400">IGV incluido</p>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('carrito.checkout') }}"
                   class="mt-6 flex items-center justify-center gap-2 w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide hover:bg-gray-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Finalizar compra
                </a>

                {{-- Pago seguro --}}
                <div class="mt-4 text-center">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Pago 100% seguro
                    </span>
                </div>
            </div>

            {{-- Garantías --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <div class="w-9 h-9 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-gray-600">Pago seguro</p>
                    </div>
                    <div>
                        <div class="w-9 h-9 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-gray-600">Devoluciones</p>
                    </div>
                    <div>
                        <div class="w-9 h-9 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-gray-600">Soporte 24/7</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    @else

    {{-- ══ CARRITO VACÍO ══ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-24 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-5 border border-gray-100">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <h2 class="font-display font-semibold text-xl mb-2">Tu bolsa está vacía</h2>
        <p class="text-sm text-gray-400 mb-8 max-w-xs">Aún no has agregado ningún producto. Descubre nuestra colección y encuentra algo que te guste.</p>
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-2xl px-7 py-3 text-sm font-semibold hover:bg-gray-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a la tienda
        </a>
    </div>

    @endif

</div>

<script>
function carritoData() {
    return {}
}
</script>

</body>
</html>
