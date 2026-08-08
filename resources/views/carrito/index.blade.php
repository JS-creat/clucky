@extends('layouts.app')

@section('title', 'Bolsa de Compras – B-EDEN')

@section('content')
    <div x-data="carritoData(@js($items), @js($total))" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 py-10 w-full">

        {{-- Mensaje de error (ej. stock no disponible) --}}
        <div x-show="mensajeError" x-transition x-cloak
            class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3" x-text="mensajeError">
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-display font-semibold">Tu carrito de compras</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span x-show="cantidadItems > 0" x-cloak>
                    <span x-text="cantidadItems"></span>
                    <span x-text="cantidadItems === 1 ? 'producto' : 'productos'"></span>
                    seleccionados
                </span>
                <span x-show="cantidadItems === 0" x-cloak>No tienes productos en tu carrito</span>
            </p>
        </div>

        <div x-show="cantidadItems > 0" x-cloak class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- IZQUIERDA: Lista de Productos --}}
            <div class="lg:col-span-3 space-y-3">

                <template x-for="(detalle, id) in items" :key="id">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex gap-4 group transition-all hover:shadow-md hover:border-gray-200"
                        :class="{ 'opacity-50 pointer-events-none': cargando[id] }">

                        {{-- Imagen --}}
                        <div class="relative shrink-0">
                            <img :src="'/productos/' + detalle.imagen" :alt="detalle.nombre"
                                class="w-20 h-24 sm:w-24 sm:h-28 object-cover rounded-xl border border-gray-100">
                            <span x-show="detalle.precio_oferta" x-cloak
                                class="absolute -top-1.5 -left-1.5 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                OFERTA
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 flex flex-col justify-between min-w-0">

                            <div>
                                <h3 class="font-semibold text-sm leading-snug truncate" x-text="detalle.nombre"></h3>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5">
                                        <span class="font-medium text-gray-700">Color:</span>
                                        <span x-text="detalle.color ?? '—'"></span>
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5">
                                        <span class="font-medium text-gray-700">Talla:</span>
                                        <span x-text="detalle.talla ?? '—'"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 flex-wrap gap-3">

                                {{-- Cantidad --}}
                                <div
                                    class="flex items-center gap-0.5 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden">
                                    <button type="button" @click="disminuir(id)" :disabled="cargando[id]"
                                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm disabled:opacity-50">
                                        −
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold" x-text="detalle.cantidad"></span>
                                    <button type="button" @click="aumentar(id)" :disabled="cargando[id]"
                                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-900 hover:text-white transition-all font-bold text-sm disabled:opacity-50">
                                        +
                                    </button>
                                </div>

                                {{-- Eliminar --}}
                                <button type="button" @click="eliminar(id)" :disabled="cargando[id]"
                                    class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors font-medium disabled:opacity-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>

                                {{-- Precio --}}
                                <div class="text-right ml-auto">
                                    <p class="text-xs text-gray-400">S/ <span x-text="detalle.precio.toFixed(2)"></span> c/u
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">
                                        S/ <span x-text="(detalle.precio * detalle.cantidad).toFixed(2)"></span>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </template>

                {{-- Seguir comprando --}}
                <div class="pt-2">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-700 transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Seguir comprando
                    </a>
                </div>

            </div>


            {{-- DERECHA: Resumen del Pedido --}}
            <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-24 h-fit">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                    <h2 class="font-display font-semibold text-lg mb-1">Resumen del pedido</h2>
                    <p class="text-xs text-gray-400 mb-5">
                        <span x-text="cantidadItems"></span>
                        <span x-text="cantidadItems === 1 ? 'producto' : 'productos'"></span>
                    </p>

                    <div class="space-y-2 border-b border-gray-100 pb-4">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span>S/ <span x-text="total.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Envío</span>
                            <span class="text-gray-400 italic text-xs">Se calcula en el checkout</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end pt-4">
                        <span class="font-display font-semibold">Total</span>
                        <div class="text-right">
                            <p class="text-2xl font-display font-bold">S/ <span x-text="total.toFixed(2)"></span></p>
                            <p class="text-xs text-gray-400">IGV incluido</p>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}"
                        class="mt-6 flex items-center justify-center gap-2 w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide hover:bg-gray-800 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Finalizar compra
                    </a>
                </div>

            </div>

        </div>

        {{-- Carrito Vacío --}}
        <div x-show="cantidadItems === 0" x-cloak
            class="bg-white rounded-2xl border border-gray-100 shadow-sm py-24 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-5 border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                    <circle cx="8" cy="21" r="1" />
                    <circle cx="19" cy="21" r="1" />
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                </svg>
            </div>
            <h2 class="font-display font-semibold text-xl mb-2">Tu carrito está vacío</h2>
            <p class="text-sm text-gray-400 mb-8 max-w-xs">Aún no has agregado ningún producto. Descubre nuestra colección y
                encuentra algo que te guste.</p>
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-2xl px-7 py-3 text-sm font-semibold hover:bg-gray-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a la tienda
            </a>
        </div>

    </div>

    <script>
        function carritoData(itemsIniciales, totalInicial) {
            return {
                items: itemsIniciales,
                total: totalInicial,
                cargando: {},       // { [id_variante]: true/false } — evita doble clic en un item específico
                mensajeError: null,

                get cantidadItems() {
                    return Object.keys(this.items).length;
                },

                async llamarApi(url, id, method = 'POST') {
                    this.cargando[id] = true;
                    this.mensajeError = null;

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                        if (!csrfMeta) {
                            // Esto avisa CLARAMENTE si falta el meta tag, en vez de
                            // fallar silenciosamente y mostrar un mensaje genérico.
                            console.error('[carrito] Falta <meta name="csrf-token"> en el <head> del layout.');
                            this.mensajeError = 'Error de configuración (falta meta CSRF). Revisa la consola.';
                            return;
                        }

                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': csrfMeta.content,
                                'Accept': 'application/json',
                            },
                        });

                        // Si el servidor no respondió JSON (ej. página de error HTML,
                        // redirect a login, 500 sin manejar), lo detectamos aquí
                        // en vez de que .json() explote sin explicación.
                        const contentType = res.headers.get('content-type') || '';

                        if (!contentType.includes('application/json')) {
                            const textoRespuesta = await res.text();
                            console.error(`[carrito] Respuesta no-JSON (status ${res.status}):`, textoRespuesta);
                            this.mensajeError = `Error inesperado del servidor (status ${res.status}). Revisa la consola.`;
                            return;
                        }

                        const data = await res.json();

                        if (!data.ok) {
                            this.mensajeError = data.mensaje ?? 'Ocurrió un error, intenta de nuevo.';
                            return;
                        }

                        // Reemplazamos el estado completo con lo que confirma el servidor.
                        // Así el navegador nunca "inventa" un número — siempre refleja
                        // lo que realmente quedó guardado en BD/sesión.
                        this.items = data.items;
                        this.total = data.total;

                    } catch (e) {
                        // Este catch ahora solo debería dispararse por errores de red reales
                        // (servidor caído, sin internet, CORS bloqueado, etc.)
                        console.error('[carrito] Error de red o inesperado:', e);
                        this.mensajeError = 'No se pudo conectar con el servidor. Revisa la consola para más detalle.';
                    } finally {
                        this.cargando[id] = false;
                    }
                },

                aumentar(id) {
                    this.llamarApi(`/carrito/aumentar/${id}`, id);
                },

                disminuir(id) {
                    this.llamarApi(`/carrito/disminuir/${id}`, id);
                },

                eliminar(id) {
                    this.llamarApi(`/carrito/eliminar/${id}`, id, 'DELETE');
                },
            }
        }
    </script>
@endsection
