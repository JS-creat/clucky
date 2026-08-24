<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – B-EDEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                }
            }
        }
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }

        @layer components {
            .card {
                @apply bg-white rounded-2xl border border-gray-200 shadow-sm p-6;
            }
            .step-badge {
                @apply w-6 h-6 rounded-full bg-gray-900 text-white text-xs font-bold flex items-center justify-center shrink-0;
            }
            .field {
                @apply w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white transition-colors
                       focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900;
            }
            .field-error {
                @apply border-red-400 focus:border-red-500 focus:ring-red-500;
            }
            .option-row {
                @apply flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer transition-all;
            }
            .option-row-on {
                @apply border-gray-900 bg-gray-50 ring-1 ring-gray-900;
            }
            .option-row-off {
                @apply border-gray-200 hover:border-gray-400;
            }
            .radio-dot {
                @apply w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors;
            }
            .btn-primary {
                @apply w-full bg-gray-900 text-white rounded-2xl py-4 text-sm font-semibold tracking-wide
                       hover:bg-gray-800 transition-all disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed
                       focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900;
            }
            .btn-secondary {
                @apply text-sm font-medium text-gray-700 border border-gray-300 rounded-xl px-5 py-2
                       hover:border-gray-900 hover:bg-gray-50 transition-all;
            }
            .link-edit {
                @apply text-xs text-gray-500 hover:text-gray-900 flex items-center gap-1 transition-colors underline underline-offset-2;
            }
            .banner {
                @apply rounded-xl px-4 py-3 flex items-start gap-3 text-sm;
            }
            .banner-error {
                @apply banner bg-red-50 border border-red-200 text-red-700;
            }
            .banner-notice {
                @apply banner bg-gray-100 border border-gray-200 text-gray-700;
            }
            .pill {
                @apply text-xs font-semibold px-2 py-0.5 rounded-full;
            }
        }
    </style>
</head>

<body class="bg-stone-50 font-sans text-gray-900 antialiased">

    {{-- NAVBAR --}}
    <nav class="bg-gray-900 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="{{ url('/carrito') }}"
                class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al carrito
            </a>
            <span class="font-display text-white text-sm tracking-widest">B-EDEN</span>
        </div>
    </nav>

    {{-- CALCULAR TOTAL --}}
    @php
        $total = 0;
        if ($carrito && $carrito->detalles->count()) {
            foreach ($carrito->detalles as $detalle) {
                $variante = $detalle->variante ?? null;
                $producto = $variante?->producto;
                if (!$producto) continue;
                $precio = $producto->precio_oferta ?? $producto->precio;
                $total += $precio * $detalle->cantidad;
            }
        }
        $faltaDocumento = empty(auth()->user()->numero_documento);
    @endphp

    {{-- CONTENIDO PRINCIPAL --}}
    <div x-data="checkoutData()" x-cloak
        class="max-w-7xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- IZQUIERDA --}}
        <div class="lg:col-span-3 space-y-5">

            <div class="space-y-3">
                <div>
                    <h1 class="text-3xl font-display font-semibold">Finalizar pedido</h1>
                    <p class="text-sm text-gray-500 mt-1">Completa los 3 pasos para confirmar tu compra</p>
                </div>

                {{-- Errores del servidor: siempre lo primero que se ve, sin ambigüedad --}}
                @if(session('error') || $errors->any())
                    <div class="banner-error">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <div>
                            @if(session('error'))
                                <p class="font-medium">{{ session('error') }}</p>
                            @endif
                            @if($errors->any())
                                <ul class="list-disc list-inside mt-1 space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Aviso de RENIEC (si la última verificación de DNI falló) --}}
                @if(session('aviso'))
                    <div class="banner-notice">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>{{ session('aviso') }}</p>
                    </div>
                @endif

                {{-- Falta DNI: bloquea el avance, se explica en una sola línea --}}
                @if($faltaDocumento)
                    <div class="banner-notice">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p><span class="font-medium">Falta tu DNI.</span> Lo necesitamos para emitir tu boleta electrónica — complétalo en el paso 2.</p>
                    </div>
                @endif
            </div>

            {{-- PASO 1: TIPO DE ENTREGA --}}
            <div class="card">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold flex items-center gap-2">
                        <span class="step-badge">1</span>
                        Tipo de entrega
                    </h2>
                    <button x-show="!editandoEntrega" @click="editandoEntrega = true" class="link-edit">
                        Modificar
                    </button>
                </div>

                {{-- Vista resumen --}}
                <div x-show="!editandoEntrega">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-9 h-9 bg-gray-900 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" x-text="tipoEntregaNombre"></p>
                            <p x-show="tipoEntrega == 2 && distritoNombre"
                                class="text-xs text-gray-500 mt-0.5"
                                x-text="'Distrito: ' + distritoNombre"></p>
                            <p x-show="tipoEntrega == 2 && agenciaNombre"
                                class="text-xs text-gray-500 mt-0.5"
                                x-text="'Agencia: ' + agenciaNombre"></p>
                            <p x-show="tipoEntrega == 2 && costoEnvio > 0"
                                class="text-xs text-gray-900 font-semibold mt-0.5"
                                x-text="'Costo: S/ ' + parseFloat(costoEnvio).toFixed(2)"></p>
                            <p x-show="tipoEntrega != 2" class="text-xs text-gray-500 mt-0.5">Sin costo de envío</p>
                        </div>
                    </div>
                </div>

                {{-- Vista edición --}}
                <div x-show="editandoEntrega" class="space-y-3">

                    @foreach($tiposEntrega as $tipo)
                        <label class="option-row"
                            :class="tipoEntrega == {{ $tipo->id_tipo_entrega }} ? 'option-row-on' : 'option-row-off'">

                            <input type="radio" name="tipo_entrega" value="{{ $tipo->id_tipo_entrega }}" class="sr-only"
                                @click="seleccionarTipoEntrega({{ $tipo->id_tipo_entrega }}, '{{ $tipo->nombre_tipo_entrega }}')">

                            <div class="radio-dot"
                                :class="tipoEntrega == {{ $tipo->id_tipo_entrega }} ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
                                <div class="w-2 h-2 bg-white rounded-full"
                                    x-show="tipoEntrega == {{ $tipo->id_tipo_entrega }}"></div>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $tipo->nombre_tipo_entrega }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($tipo->id_tipo_entrega == 2)
                                        Calculado según agencia de envío
                                    @else
                                        Recoge en tienda · Sin costo adicional
                                    @endif
                                </p>
                            </div>

                            @if($tipo->id_tipo_entrega != 2)
                                <span class="pill bg-gray-100 text-gray-600">Gratis</span>
                            @endif
                        </label>
                    @endforeach

                    {{-- Selección de envío --}}
                    <div x-show="tipoEntrega == 2" class="space-y-3 pt-3 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selecciona tu ubicación</p>

                        <select x-model="departamento" @change="cargarProvincias" class="field">
                            <option value="">Departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">{{ $dep->nombre_departamento }}</option>
                            @endforeach
                        </select>

                        <select x-show="provincias.length" x-model="provincia" @change="cargarDistritos" class="field">
                            <option value="">Provincia</option>
                            <template x-for="prov in provincias" :key="prov.id_provincia">
                                <option :value="prov.id_provincia" x-text="prov.nombre_provincia"></option>
                            </template>
                        </select>

                        <select x-show="distritos.length"
                            @change="distrito = $event.target.value; cargarAgencias()"
                            class="field {{ $errors->has('id_distrito') ? 'field-error' : '' }}">
                            <option value="">Distrito</option>
                            <template x-for="dist in distritos" :key="dist.id_distrito">
                                <option :value="dist.id_distrito" x-text="dist.nombre_distrito"></option>
                            </template>
                        </select>
                        @error('id_distrito')
                            <p class="text-xs text-red-600 -mt-2">{{ $message }}</p>
                        @enderror

                        <div x-show="cargandoAgencias" class="flex items-center gap-2 text-sm text-gray-400 px-1">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Buscando agencias disponibles…
                        </div>

                        <div x-show="!cargandoAgencias && agencias.length > 0" class="space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Agencia de envío</p>
                            <template x-for="ag in agencias" :key="ag.id_agencia">
                                <label class="option-row items-start"
                                    :class="agenciaSeleccionada == ag.id_agencia ? 'option-row-on' : 'option-row-off'">

                                    <input type="radio" class="sr-only" @click="seleccionarAgencia(ag)">

                                    <div class="radio-dot mt-0.5"
                                        :class="agenciaSeleccionada == ag.id_agencia ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
                                        <div class="w-2 h-2 bg-white rounded-full"
                                            x-show="agenciaSeleccionada == ag.id_agencia"></div>
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-semibold" x-text="ag.nombre_agencia"></p>
                                        <p class="text-xs text-gray-400 mt-0.5" x-text="ag.direccion"></p>
                                    </div>

                                    <span class="text-sm font-bold text-gray-900 shrink-0">
                                        S/ <span x-text="parseFloat(ag.costo_envio).toFixed(2)"></span>
                                    </span>
                                </label>
                            </template>
                        </div>

                        @error('id_agencia')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div x-show="!cargandoAgencias && distrito && agencias.length === 0" class="banner-error">
                            No hay agencias de envío disponibles para este distrito. Selecciona otro distrito o contáctanos.
                        </div>

                        <div x-show="agenciaSeleccionada && costoEnvio > 0"
                            class="flex items-center justify-between bg-gray-100 border border-gray-200 rounded-xl px-4 py-3">
                            <span class="text-sm text-gray-700 font-medium">Costo de envío</span>
                            <span class="text-sm font-bold text-gray-900">
                                S/ <span x-text="parseFloat(costoEnvio).toFixed(2)"></span>
                            </span>
                        </div>
                    </div>

                    <button @click="guardarEntrega" class="btn-secondary">✓ Confirmar entrega</button>
                    <p x-show="errorEntrega" x-text="errorEntrega" class="text-xs text-red-600 font-medium"></p>

                </div>
            </div>

            {{-- PASO 2: DATOS PERSONALES --}}
            <div x-data="{ editandoDatos: {{ $faltaDocumento || $errors->has('numero_documento') ? 'true' : 'false' }} }" class="card">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold flex items-center gap-2">
                        <span class="step-badge">2</span>
                        Datos personales
                        @if($faltaDocumento)
                            <span class="pill bg-red-50 text-red-600">Falta DNI</span>
                        @endif
                    </h2>
                    <button x-show="!editandoDatos" @click="editandoDatos = true" class="link-edit">Modificar</button>
                </div>

                {{-- Vista resumen --}}
                <div x-show="!editandoDatos" class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-xs text-gray-400 mb-0.5">Nombre completo</p>
                        <p class="text-sm font-semibold">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-xs text-gray-400 mb-0.5">Documento</p>
                        <p class="text-sm font-semibold {{ $faltaDocumento ? 'text-red-600' : '' }}">
                            {{ auth()->user()->numero_documento ?? 'No registrado' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-xs text-gray-400 mb-0.5">Teléfono</p>
                        <p class="text-sm font-semibold">{{ auth()->user()->telefono ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <p class="text-xs text-gray-400 mb-0.5">Correo</p>
                        <p class="text-sm font-semibold truncate">{{ auth()->user()->correo }}</p>
                    </div>
                </div>

                {{-- Vista edición: SOLO el DNI es obligatorio y visible de entrada.
                     Nombres/Apellidos se autocompletan desde RENIEC al guardar el DNI,
                     por eso viven colapsados detrás de "Editar manualmente" —
                     así no confunden pidiendo algo que el sistema ya va a completar solo. --}}
                <div x-show="editandoDatos" x-data="{ editarNombreManual: false }">
                    <form method="POST" action="{{ route('usuario.actualizar') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-3">
                            <div>
                                <label class="text-xs font-medium text-gray-500 block mb-1.5">
                                    N° Documento (DNI) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="numero_documento" required inputmode="numeric" maxlength="8"
                                    value="{{ old('numero_documento', auth()->user()->numero_documento) }}"
                                    class="field {{ $errors->has('numero_documento') ? 'field-error' : '' }}"
                                    placeholder="8 dígitos">
                                @error('numero_documento')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-400 mt-1">
                                    Verificamos tu nombre automáticamente con RENIEC al guardar — no necesitas escribirlo.
                                </p>
                            </div>

                            <button type="button" @click="editarNombreManual = !editarNombreManual"
                                class="text-xs text-gray-500 underline underline-offset-2 hover:text-gray-900">
                                <span x-text="editarNombreManual ? 'Ocultar nombre manual' : '¿RENIEC no encontró tu nombre? Editarlo manualmente'"></span>
                            </button>

                            <div x-show="editarNombreManual" class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-500 block mb-1.5">Nombres</label>
                                    <input type="text" name="nombres" value="{{ old('nombres', auth()->user()->nombres) }}" class="field">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 block mb-1.5">Apellidos</label>
                                    <input type="text" name="apellidos" value="{{ old('apellidos', auth()->user()->apellidos) }}" class="field">
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-500 block mb-1.5">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}"
                                    class="field" placeholder="9XXXXXXXX">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-4">
                            <button type="submit" class="btn-secondary">Guardar cambios</button>
                            @unless($faltaDocumento)
                                <button type="button" @click="editandoDatos = false"
                                    class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                                    Cancelar
                                </button>
                            @endunless
                        </div>
                    </form>
                </div>
            </div>

            {{-- PASO 3: TÉRMINOS --}}
            <div class="card">
                <h2 class="font-semibold flex items-center gap-2 mb-5">
                    <span class="step-badge">3</span>
                    Términos y condiciones
                </h2>

                <div class="flex items-start gap-3">
                    <button type="button" @click="aceptaTerminos = !aceptaTerminos"
                        class="mt-0.5 w-5 h-5 rounded shrink-0 border-2 flex items-center justify-center transition-colors"
                        :class="aceptaTerminos ? 'bg-gray-900 border-gray-900' : 'bg-white border-gray-300'">
                        <svg x-show="aceptaTerminos" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                    <div>
                        <p class="text-sm text-gray-700">
                            He leído y acepto los
                            <a href="{{ route('terminos') }}" class="font-semibold underline underline-offset-2 hover:text-gray-900">
                                Términos y Condiciones
                            </a>
                            y la
                            <a href="{{ route('politica-privacidad') }}" class="font-semibold underline underline-offset-2 hover:text-gray-900">
                                Política de Privacidad
                            </a>
                        </p>
                        <p x-show="!aceptaTerminos && intentoPagar" class="text-xs text-red-600 font-medium mt-1">
                            ⚠ Debes aceptar los términos para continuar
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botón pagar mobile --}}
            <div class="lg:hidden">
                <button @click="intentarPagar" :disabled="!aceptaTerminos" class="btn-primary">
                    <span x-show="!procesando">Continuar al pago · S/ <span x-text="totalConEnvio()"></span></span>
                    <span x-show="procesando" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Procesando…
                    </span>
                </button>
                <p x-show="errorPago" x-text="errorPago" class="text-xs text-red-600 font-medium mt-2 text-center"></p>
            </div>

        </div>

        {{-- DERECHA --}}
        <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-24 h-fit">

            <div class="card">

                <h2 class="font-display font-semibold text-lg mb-1">Resumen del pedido</h2>
                <p class="text-xs text-gray-400 mb-5">
                    {{ $carrito->detalles->count() }} {{ $carrito->detalles->count() == 1 ? 'producto' : 'productos' }}
                </p>

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
                            <div class="relative shrink-0">
                                @if($producto->imagen)
                                    <img src="{{ asset('productos/' . $producto->imagen) }}"
                                        alt="{{ $producto->nombre_producto }}"
                                        class="w-14 h-14 rounded-xl object-cover border border-gray-100">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 border border-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-gray-900 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ $detalle->cantidad }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate">{{ $producto->nombre_producto }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $variante->color }} · Talla {{ $variante->talla }}</p>
                                @if($producto->precio_oferta)
                                    <span class="text-xs text-gray-500 font-medium">En oferta</span>
                                @endif
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold">S/ {{ number_format($subtotal, 2) }}</p>
                                <p class="text-xs text-gray-400">S/ {{ number_format($precio, 2) }} c/u</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 pt-4 mt-2 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>S/ {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Envío</span>
                        <span x-show="costoEnvio > 0" class="font-medium text-gray-900">
                            + S/ <span x-text="parseFloat(costoEnvio).toFixed(2)"></span>
                        </span>
                        <span x-show="costoEnvio == 0" class="text-gray-500 font-medium">
                            <span x-show="tipoEntrega != 2 || !agenciaSeleccionada">—</span>
                            <span x-show="tipoEntrega != 2">Gratis</span>
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 mt-3 flex justify-between items-end">
                    <span class="font-display font-semibold">Total a pagar</span>
                    <div class="text-right">
                        <p class="text-2xl font-display font-bold">S/ <span x-text="totalConEnvio()"></span></p>
                        <p class="text-xs text-gray-400">IGV incluido</p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:block">
                <button @click="intentarPagar" :disabled="!aceptaTerminos" class="btn-primary flex items-center justify-center gap-2">
                    <span x-show="!procesando" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Continuar al pago · S/ <span x-text="totalConEnvio()"></span>
                    </span>
                    <span x-show="procesando" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Procesando…
                    </span>
                </button>
                <p x-show="errorPago" x-text="errorPago" class="text-xs text-red-600 font-medium mt-2 text-center"></p>

                <div class="mt-3 text-center space-y-1.5">
                    <p class="text-xs text-gray-400">Serás redirigido a</p>
                    <span class="inline-flex items-center gap-1.5 bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg">
                        Mercado Pago
                    </span>
                    <p class="text-xs text-gray-400">Encriptación SSL · Datos protegidos</p>
                </div>
            </div>

        </div>

        {{-- Formulario oculto — sin costo_envio: el servidor lo recalcula --}}
        <form id="form-pedido" method="POST" action="{{ route('checkout.confirmar') }}">
            @csrf
            <input type="hidden" name="id_tipo_entrega" x-bind:value="tipoEntrega">
            <input type="hidden" name="id_distrito"     x-bind:value="distrito">
            <input type="hidden" name="id_agencia"      x-bind:value="agenciaSeleccionada">
        </form>

    </div>

    {{-- MODAL TÉRMINOS --}}
    <div x-show="modalTerminos" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="modalTerminos = false">

        <div class="bg-white rounded-2xl p-8 w-full max-w-lg max-h-[80vh] overflow-y-auto shadow-2xl"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-display font-semibold">Términos y Condiciones</h2>
                <button @click="modalTerminos = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 text-gray-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-5 text-sm text-gray-600 leading-relaxed">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">1. Compra y Disponibilidad</h3>
                    <p>Todos los productos están sujetos a disponibilidad de stock. Nos reservamos el derecho de cancelar un pedido si el producto no está disponible al momento de confirmar el pago.</p>
                </div>
                <div class="h-px bg-gray-200"></div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">2. Precios y Pagos</h3>
                    <p>Los precios incluyen IGV (18%). El pago se realiza de forma segura a través de Mercado Pago. C Lucky no almacena datos bancarios del cliente.</p>
                </div>
                <div class="h-px bg-gray-200"></div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">3. Política de Envíos</h3>
                    <p>Los costos de envío varían según la agencia de destino. Los tiempos de entrega son referenciales y pueden variar por factores externos.</p>
                </div>
                <div class="h-px bg-gray-200"></div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">4. Devoluciones y Cambios</h3>
                    <p>Aceptamos cambios dentro de los 30 días calendario desde la recepción, siempre que el artículo esté sin usar, con etiquetas y en su empaque original.</p>
                </div>
                <div class="h-px bg-gray-200"></div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">5. Privacidad de Datos</h3>
                    <p>Los datos personales serán utilizados exclusivamente para gestionar el pedido, en cumplimiento con la Ley N° 29733 de Protección de Datos Personales del Perú.</p>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button @click="aceptaTerminos = true; modalTerminos = false"
                    class="flex-1 bg-gray-900 text-white rounded-xl py-3 text-sm font-semibold hover:bg-gray-800 transition-colors">
                    ✓ Acepto los términos
                </button>
                <button @click="modalTerminos = false" class="btn-secondary">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- ALPINE — misma API pública que antes, sin cambios de nombres/rutas --}}
    <script>
        function checkoutData() {
            return {
                editandoEntrega: true,
                modalTerminos:   false,
                procesando:      false,
                intentoPagar:    false,
                aceptaTerminos:  false,
                errorEntrega:    '',
                errorPago:       '',

                tipoEntrega:       {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
                tipoEntregaNombre: '{{ $tiposEntrega->first()->nombre_tipo_entrega ?? "Recojo en tienda" }}',

                departamento:        '',
                provincia:           '',
                distrito:            '',
                distritoNombre:      '',
                provincias:          [],
                distritos:           [],
                agencias:            [],
                agenciaSeleccionada: null,
                agenciaNombre:       '',
                cargandoAgencias:    false,
                costoEnvio:          0, // Solo para mostrar; el servidor recalcula el real.

                totalProductos: {{ $total }},

                totalConEnvio() {
                    return (parseFloat(this.totalProductos) + parseFloat(this.costoEnvio || 0)).toFixed(2);
                },

                seleccionarTipoEntrega(id, nombre) {
                    this.tipoEntrega       = id;
                    this.tipoEntregaNombre = nombre;
                    if (id != 2) this.resetEnvio();
                },

                seleccionarAgencia(agencia) {
                    this.agenciaSeleccionada = agencia.id_agencia;
                    this.agenciaNombre       = agencia.nombre_agencia;
                    this.costoEnvio          = parseFloat(agencia.costo_envio);
                },

                resetEnvio({ keepDepartamento = true, keepProvincia = true, keepDistrito = true } = {}) {
                    if (!keepDepartamento) this.departamento = '';
                    if (!keepProvincia)    { this.provincia = ''; this.provincias = []; }
                    if (!keepDistrito)     { this.distrito = ''; this.distritoNombre = ''; this.distritos = []; }
                    this.agencias            = [];
                    this.agenciaSeleccionada = null;
                    this.agenciaNombre       = '';
                    this.costoEnvio          = 0;
                },

                guardarEntrega() {
                    this.errorEntrega = '';
                    if (this.tipoEntrega == 2 && !this.distrito) {
                        this.errorEntrega = 'Selecciona un distrito para el envío.';
                        return;
                    }
                    if (this.tipoEntrega == 2 && !this.agenciaSeleccionada) {
                        this.errorEntrega = 'Selecciona una agencia de envío.';
                        return;
                    }
                    this.editandoEntrega = false;
                },

                cargarProvincias() {
                    if (!this.departamento) return;
                    this.resetEnvio({ keepProvincia: false, keepDistrito: false });

                    fetch('/ubicacion/provincias/' + this.departamento)
                        .then(r => r.json())
                        .then(data => { this.provincias = data; });
                },

                cargarDistritos() {
                    if (!this.provincia) return;
                    this.resetEnvio({ keepDistrito: false });

                    fetch('/ubicacion/distritos/' + this.provincia)
                        .then(r => r.json())
                        .then(data => { this.distritos = data; });
                },

                cargarAgencias() {
                    if (!this.distrito) return;

                    const dist = this.distritos.find(d => String(d.id_distrito) === String(this.distrito));
                    this.distritoNombre      = dist ? dist.nombre_distrito : '';
                    this.agencias            = [];
                    this.agenciaSeleccionada = null;
                    this.agenciaNombre       = '';
                    this.costoEnvio          = 0;
                    this.cargandoAgencias    = true;

                    fetch('/ubicacion/agencias/' + this.distrito)
                        .then(r => r.json())
                        .then(data => { this.agencias = data; })
                        .finally(() => { this.cargandoAgencias = false; });
                },

                intentarPagar() {
                    this.intentoPagar = true;
                    this.errorPago    = '';

                    if (!this.aceptaTerminos) return;

                    if (this.tipoEntrega == 2 && !this.distrito) {
                        this.errorPago = 'Selecciona un distrito de envío.';
                        return;
                    }
                    if (this.tipoEntrega == 2 && !this.agenciaSeleccionada) {
                        this.errorPago = 'Selecciona una agencia de envío.';
                        return;
                    }

                    this.procesando = true;
                    document.getElementById('form-pedido').submit();
                },
            }
        }
    </script>

</body>
</html>