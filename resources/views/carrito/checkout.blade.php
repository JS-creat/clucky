<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – C Lucky</title>
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
     NAVBAR
══════════════════════════════ --}}
<nav class="bg-gray-900 sticky top-0 z-50 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">

        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.jpg') }}" class="h-9 w-auto brightness-0 invert opacity-90 hover:opacity-100 transition-opacity">
        </a>

        <div class="flex items-center gap-4">
            {{-- Badge pago seguro --}}
            <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Pago seguro
            </span>

            <a href="{{ url('/carrito') }}" class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al carrito
            </a>
        </div>

    </div>
</nav>


{{-- ══════════════════════════════
     STEPS
══════════════════════════════ --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-center max-w-xs mx-auto">

            {{-- Step 1 - Activo --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 rounded-full bg-gray-900 text-white text-xs font-bold flex items-center justify-center">1</div>
                <span class="text-xs font-semibold text-gray-800">Entrega</span>
            </div>

            <div class="flex-1 h-px bg-gray-200 mx-3"></div>

            {{-- Step 2 --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 rounded-full border border-gray-200 text-gray-400 text-xs font-bold flex items-center justify-center">2</div>
                <span class="text-xs text-gray-400">Revisión</span>
            </div>

            <div class="flex-1 h-px bg-gray-200 mx-3"></div>

            {{-- Step 3 --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 rounded-full border border-gray-200 text-gray-400 text-xs font-bold flex items-center justify-center">3</div>
                <span class="text-xs text-gray-400">Pago</span>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════
     CALCULAR TOTAL (PHP)
══════════════════════════════ --}}
@php
    $total = 0;
    if ($carrito && $carrito->detalles->count()) {
        foreach ($carrito->detalles as $detalle) {
            $variante = $detalle->variante ?? null;
            $producto = $variante?->producto;
            if (!$producto) continue;
            $precio   = $producto->precio_oferta ?? $producto->precio;
            $total   += $precio * $detalle->cantidad;
        }
    }
@endphp


{{-- ══════════════════════════════
     CONTENIDO PRINCIPAL
══════════════════════════════ --}}
<div x-data="checkoutData()" x-cloak
     class="max-w-7xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 lg:grid-cols-5 gap-8">


    {{-- ─────────────────────────
         IZQUIERDA  (3 columnas)
    ───────────────────────── --}}
    <div class="lg:col-span-3 space-y-5">

        <div>
            <h1 class="text-3xl font-display font-semibold">Finalizar pedido</h1>
            <p class="text-sm text-gray-500 mt-1">Completa los datos para confirmar tu compra</p>
        </div>


        {{-- ┌─────────────────────────┐
             │  1. TIPO DE ENTREGA     │
             └─────────────────────────┘ --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            {{-- Cabecera --}}
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-semibold flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-xs font-bold flex items-center justify-center">1</span>
                    Tipo de entrega
                </h2>
                <button x-show="!editandoEntrega"
                        @click="editandoEntrega = true"
                        class="text-xs text-gray-400 hover:text-gray-700 flex items-center gap-1 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Modificar
                </button>
            </div>

            {{-- Vista resumen --}}
            <div x-show="!editandoEntrega">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 bg-gray-900 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" x-text="tipoEntregaNombre"></p>
                        <p x-show="tipoEntrega == 2 && distritoNombre"
                           class="text-xs text-gray-500 mt-0.5"
                           x-text="'Envío a: ' + distritoNombre"></p>
                        <p x-show="tipoEntrega == 2 && costoEnvio > 0"
                           class="text-xs text-amber-600 font-semibold mt-0.5"
                           x-text="'Costo: S/ ' + parseFloat(costoEnvio).toFixed(2)"></p>
                        <p x-show="tipoEntrega != 2"
                           class="text-xs text-emerald-600 font-medium mt-0.5">Sin costo de envío</p>
                    </div>
                </div>
            </div>

            {{-- Vista edición --}}
            <div x-show="editandoEntrega" class="space-y-3">

                @foreach($tiposEntrega as $tipo)
                <label class="flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer transition-all"
                       :class="tipoEntrega == {{ $tipo->id_tipo_entrega }}
                               ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900'
                               : 'border-gray-200 hover:border-gray-400'">

                    <input type="radio" name="tipo_entrega" value="{{ $tipo->id_tipo_entrega }}" class="sr-only"
                           @click="tipoEntrega = {{ $tipo->id_tipo_entrega }};
                                  tipoEntregaNombre = '{{ $tipo->nombre_tipo_entrega }}';
                                  calcularEnvioPorTipo()">

                    {{-- Radio visual --}}
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                         :class="tipoEntrega == {{ $tipo->id_tipo_entrega }} ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
                        <div class="w-2 h-2 bg-white rounded-full" x-show="tipoEntrega == {{ $tipo->id_tipo_entrega }}"></div>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $tipo->nombre_tipo_entrega }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if($tipo->id_tipo_entrega == 2)
                                Calculado según distrito
                            @else
                                Recoge en tienda · Sin costo adicional
                            @endif
                        </p>
                    </div>

                    @if($tipo->id_tipo_entrega != 2)
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Gratis</span>
                    @endif
                </label>
                @endforeach

                {{-- Selección geográfica (solo envío a domicilio) --}}
                <div x-show="tipoEntrega == 2" class="space-y-3 pt-3 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selecciona tu ubicación</p>

                    <select x-model="departamento" @change="cargarProvincias"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-gray-900 transition-colors">
                        <option value="">Departamento</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}">{{ $dep->nombre_departamento }}</option>
                        @endforeach
                    </select>

                    <select x-show="provincias.length" x-model="provincia" @change="cargarDistritos"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-gray-900 transition-colors">
                        <option value="">Provincia</option>
                        <template x-for="prov in provincias" :key="prov.id_provincia">
                            <option :value="prov.id_provincia" x-text="prov.nombre_provincia"></option>
                        </template>
                    </select>

                    <select x-show="distritos.length" x-model="distrito" @change="mostrarCosto"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-gray-900 transition-colors">
                        <option value="">Distrito</option>
                        <template x-for="dist in distritos" :key="dist.id_distrito">
                            <option :value="dist.id_distrito" x-text="dist.nombre_distrito"></option>
                        </template>
                    </select>

                    <div x-show="costoEnvio > 0"
                         class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                        <span class="text-sm text-amber-800 font-medium">Costo de envío</span>
                        <span class="text-sm font-bold text-amber-900">S/ <span x-text="parseFloat(costoEnvio).toFixed(2)"></span></span>
                    </div>
                </div>

                <button @click="guardarEntrega"
                        class="text-sm font-medium text-gray-700 border border-gray-200 rounded-xl px-5 py-2 hover:border-gray-900 hover:bg-gray-50 transition-all">
                    ✓ Confirmar entrega
                </button>

            </div>
        </div>


        {{-- ┌─────────────────────────┐
             │  2. DATOS PERSONALES    │
             └─────────────────────────┘ --}}
        <div x-data="{ editandoDatos: false }" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <div class="flex items-center justify-between mb-5">
                <h2 class="font-semibold flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-xs font-bold flex items-center justify-center">2</span>
                    Datos personales
                </h2>
                <button x-show="!editandoDatos"
                        @click="editandoDatos = true"
                        class="text-xs text-gray-400 hover:text-gray-700 flex items-center gap-1 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Modificar
                </button>
            </div>

            {{-- Vista resumen --}}
            <div x-show="!editandoDatos" class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Nombre completo</p>
                    <p class="text-sm font-semibold">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Documento</p>
                    <p class="text-sm font-semibold">{{ auth()->user()->numero_documento ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Teléfono</p>
                    <p class="text-sm font-semibold">{{ auth()->user()->telefono ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Correo</p>
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->correo }}</p>
                </div>
            </div>

            {{-- Vista edición --}}
            <div x-show="editandoDatos">
                <form method="POST" action="{{ route('usuario.actualizar') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1.5">Nombres</label>
                            <input type="text" name="nombres" value="{{ auth()->user()->nombres }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gray-900 transition-colors">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1.5">Apellidos</label>
                            <input type="text" name="apellidos" value="{{ auth()->user()->apellidos }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gray-900 transition-colors">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1.5">N° Documento</label>
                            <input type="text" name="numero_documento" value="{{ auth()->user()->numero_documento }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gray-900 transition-colors"
                                   placeholder="DNI / RUC">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1.5">Teléfono</label>
                            <input type="text" name="telefono" value="{{ auth()->user()->telefono }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gray-900 transition-colors"
                                   placeholder="9XXXXXXXX">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit"
                                class="text-sm font-medium border border-gray-200 rounded-xl px-5 py-2 hover:border-gray-900 hover:bg-gray-50 transition-all">
                            Guardar cambios
                        </button>
                        <button type="button" @click="editandoDatos = false"
                                class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ┌─────────────────────────┐
             │  3. TÉRMINOS            │
             └─────────────────────────┘ --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h2 class="font-semibold flex items-center gap-2 mb-5">
                <span class="w-6 h-6 rounded-full bg-gray-900 text-white text-xs font-bold flex items-center justify-center">3</span>
                Términos y condiciones
            </h2>

            {{-- Extracto --}}
            <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-xs text-gray-500 leading-relaxed max-h-24 overflow-y-auto mb-4">
                Al completar esta compra, aceptas nuestros <strong class="text-gray-700">Términos y Condiciones</strong>
                de venta, incluyendo la política de devoluciones (30 días desde recepción), política de privacidad
                y condiciones de envío. Los precios incluyen IGV. Las compras están sujetas a disponibilidad de stock.
            </div>

            {{-- Checkbox --}}
            <div class="flex items-start gap-3">
                <button type="button"
                        @click="aceptaTerminos = !aceptaTerminos"
                        class="mt-0.5 w-5 h-5 rounded shrink-0 border-2 flex items-center justify-center transition-colors"
                        :class="aceptaTerminos ? 'bg-gray-900 border-gray-900' : 'bg-white border-gray-300'">
                    <svg x-show="aceptaTerminos" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>

                <div>
                    <p class="text-sm text-gray-700">
                        He leído y acepto los
                        <button type="button" @click="modalTerminos = true"
                                class="font-semibold underline underline-offset-2 hover:text-amber-600 transition-colors">
                            Términos y Condiciones
                        </button>
                        y la
                        <button type="button" @click="modalTerminos = true"
                                class="font-semibold underline underline-offset-2 hover:text-amber-600 transition-colors">
                            Política de Privacidad
                        </button>
                    </p>
                    <p x-show="!aceptaTerminos && intentoPagar"
                       class="text-xs text-red-500 font-medium mt-1">
                        ⚠ Debes aceptar los términos para continuar
                    </p>
                </div>
            </div>
        </div>

        {{-- Botón pagar (solo mobile) --}}
        <div class="lg:hidden">
            <button @click="intentarPagar" :disabled="!aceptaTerminos"
                    class="w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide
                           hover:bg-gray-800 transition-all disabled:bg-gray-200 disabled:cursor-not-allowed">
                <span x-show="!procesando">
                    Continuar al pago · S/ <span x-text="(parseFloat(totalProductos) + parseFloat(costoEnvio || 0)).toFixed(2)"></span>
                </span>
                <span x-show="procesando" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Procesando…
                </span>
            </button>
        </div>

    </div>


    {{-- ─────────────────────────
         DERECHA  (2 columnas)
    ───────────────────────── --}}
    <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-24 h-fit">

        {{-- ┌─────────────────────────┐
             │  RESUMEN DEL PEDIDO     │
             └─────────────────────────┘ --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h2 class="font-display font-semibold text-lg mb-1">Resumen del pedido</h2>
            <p class="text-xs text-gray-400 mb-5">
                {{ $carrito->detalles->count() }} {{ $carrito->detalles->count() == 1 ? 'producto' : 'productos' }}
            </p>

            {{-- Productos --}}
            <div class="divide-y divide-gray-100">
                @foreach($carrito->detalles as $detalle)
                    @php
                        $variante = $detalle->variante ?? null;
                        $producto = $variante?->producto;
                        if (!$producto) continue;
                        $precio   = $producto->precio_oferta ?? $producto->precio;
                        $subtotal = $precio * $detalle->cantidad;
                    @endphp

                    <div class="flex items-center gap-3 py-3">

                        {{-- Thumbnail --}}
                        <div class="relative shrink-0">
                            @if($producto->imagen)
                                <img src="{{ asset('productos/' . $producto->imagen) }}"
                                     alt="{{ $producto->nombre_producto }}"
                                     class="w-14 h-14 rounded-xl object-cover border border-gray-100">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-gray-100 border border-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-900 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $detalle->cantidad }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $producto->nombre_producto }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $variante->color }} · Talla {{ $variante->talla }}</p>
                            @if($producto->precio_oferta)
                                <span class="text-xs text-emerald-600 font-medium">En oferta</span>
                            @endif
                        </div>

                        {{-- Precio --}}
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold">S/ {{ number_format($subtotal, 2) }}</p>
                            <p class="text-xs text-gray-400">S/ {{ number_format($precio, 2) }} c/u</p>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Totales --}}
            <div class="border-t border-gray-100 pt-4 mt-2 space-y-2">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($total, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Envío</span>
                    <span x-show="costoEnvio > 0">S/ <span x-text="parseFloat(costoEnvio).toFixed(2)"></span></span>
                    <span x-show="costoEnvio == 0" class="text-emerald-600 font-medium">Gratis</span>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mt-3 flex justify-between items-end">
                <span class="font-display font-semibold">Total a pagar</span>
                <div class="text-right">
                    <p class="text-2xl font-display font-bold">
                        S/ <span x-text="(parseFloat(totalProductos) + parseFloat(costoEnvio || 0)).toFixed(2)"></span>
                    </p>
                    <p class="text-xs text-gray-400">IGV incluido</p>
                </div>
            </div>
        </div>


        {{-- Botón pagar (desktop) --}}
        <div class="hidden lg:block">
            <button @click="intentarPagar" :disabled="!aceptaTerminos"
                    class="w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide
                           hover:bg-gray-800 transition-all disabled:bg-gray-200 disabled:cursor-not-allowed
                           flex items-center justify-center gap-2">
                <span x-show="!procesando" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Continuar al pago
                </span>
                <span x-show="procesando" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Procesando…
                </span>
            </button>

            {{-- Mercado Pago badge --}}
            <div class="mt-3 text-center space-y-1.5">
                <p class="text-xs text-gray-400">Serás redirigido a</p>
                <span class="inline-flex items-center gap-1.5 bg-[#009EE3] text-white text-xs font-bold px-3 py-1.5 rounded-lg">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 4c4.411 0 8 3.589 8 8s-3.589 8-8 8-8-3.589-8-8 3.589-8 8-8z"/>
                    </svg>
                    Mercado Pago
                </span>
                <p class="text-xs text-gray-400">Encriptación SSL · Datos protegidos</p>
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


{{-- ══════════════════════════════
     MODAL: TÉRMINOS Y CONDICIONES
══════════════════════════════ --}}
<div x-show="modalTerminos"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     @click.self="modalTerminos = false">

    <div class="bg-white rounded-2xl p-8 w-full max-w-lg max-h-[80vh] overflow-y-auto shadow-2xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-display font-semibold">Términos y Condiciones</h2>
            <button @click="modalTerminos = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Contenido --}}
        <div class="space-y-5 text-sm text-gray-600 leading-relaxed">
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">1. Compra y Disponibilidad</h3>
                <p>Todos los productos están sujetos a disponibilidad de stock. Nos reservamos el derecho de cancelar un pedido si el producto no está disponible al momento de confirmar el pago.</p>
            </div>
            <div class="h-px bg-gradient-to-r from-transparent via-amber-300/50 to-transparent"></div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">2. Precios y Pagos</h3>
                <p>Los precios incluyen IGV (18%). El pago se realiza de forma segura a través de Mercado Pago. C Lucky no almacena datos bancarios del cliente.</p>
            </div>
            <div class="h-px bg-gradient-to-r from-transparent via-amber-300/50 to-transparent"></div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">3. Política de Envíos</h3>
                <p>Los costos de envío varían según el distrito de destino. Los tiempos de entrega son referenciales y pueden variar por factores externos.</p>
            </div>
            <div class="h-px bg-gradient-to-r from-transparent via-amber-300/50 to-transparent"></div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">4. Devoluciones y Cambios</h3>
                <p>Aceptamos devoluciones dentro de los 30 días calendario desde la recepción, siempre que el artículo esté sin usar, con etiquetas y en su empaque original.</p>
            </div>
            <div class="h-px bg-gradient-to-r from-transparent via-amber-300/50 to-transparent"></div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">5. Privacidad de Datos</h3>
                <p>Los datos personales serán utilizados exclusivamente para gestionar el pedido, en cumplimiento con la Ley N° 29733 de Protección de Datos Personales del Perú.</p>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex gap-3 mt-8">
            <button @click="aceptaTerminos = true; modalTerminos = false"
                    class="flex-1 bg-gray-900 text-white rounded-xl py-3 text-sm font-semibold hover:bg-gray-800 transition-colors">
                ✓ Acepto los términos
            </button>
            <button @click="modalTerminos = false"
                    class="px-5 py-3 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
        </div>

    </div>
</div>


{{-- ══════════════════════════════
     ALPINE: LÓGICA PRINCIPAL
══════════════════════════════ --}}
<script>
function checkoutData() {
    return {
        // Estado entrega
        editandoEntrega:    false,
        tipoEntrega:        {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
        tipoEntregaNombre:  '{{ $tiposEntrega->first()->nombre_tipo_entrega ?? "Recojo en tienda" }}',
        departamento:       '',
        provincia:          '',
        distrito:           '',
        distritoNombre:     '',
        provincias:         [],
        distritos:          [],
        costoEnvio:         0,

        // Totales
        totalProductos:     {{ $total }},

        // UI flags
        aceptaTerminos:     false,
        intentoPagar:       false,
        procesando:         false,
        modalTerminos:      false,

        // ── Métodos ──────────────────────────────────────────

        guardarEntrega() {
            if (this.tipoEntrega == 2 && !this.distrito) {
                alert('Selecciona un distrito para el envío.');
                return;
            }
            this.editandoEntrega = false;
        },

        calcularEnvioPorTipo() {
            if (this.tipoEntrega != 2) {
                this.costoEnvio    = 0;
                this.distritoNombre = '';
            }
        },

        cargarProvincias() {
            if (!this.departamento) return;
            this.provincias    = [];
            this.distritos     = [];
            this.provincia     = '';
            this.distrito      = '';
            this.costoEnvio    = 0;

            fetch('/provincias/' + this.departamento)
                .then(r => r.json())
                .then(data => { this.provincias = data; });
        },

        cargarDistritos() {
            if (!this.provincia) return;
            this.distritos  = [];
            this.distrito   = '';
            this.costoEnvio = 0;

            fetch('/distritos/' + this.provincia)
                .then(r => r.json())
                .then(data => { this.distritos = data; });
        },

        mostrarCosto() {
            const dist = this.distritos.find(d => d.id_distrito == this.distrito);
            if (dist) {
                this.costoEnvio     = parseFloat(dist.costo_envio);
                this.distritoNombre = dist.nombre_distrito;
            }
        },

        intentarPagar() {
            this.intentoPagar = true;

            if (!this.aceptaTerminos) return;

            if (this.tipoEntrega == 2 && !this.distrito) {
                alert('Completa la información de entrega.');
                return;
            }

            // ── Mercado Pago (pendiente de implementar) ──────
            // this.procesarPago();
            this.procesando = true;
            setTimeout(() => {
                this.procesando = false;
                alert('Checkout listo. Aquí se integrará Mercado Pago.');
            }, 1500);
        },

        // async procesarPago() {
        //     const res = await fetch('/checkout/crear-preferencia', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //         },
        //         body: JSON.stringify({
        //             tipo_entrega: this.tipoEntrega,
        //             id_distrito:  this.distrito,
        //             costo_envio:  this.costoEnvio,
        //         })
        //     });
        //     const { init_point } = await res.json();
        //     window.location.href = init_point;
        // },
    }
}
</script>

</body>
</html>
