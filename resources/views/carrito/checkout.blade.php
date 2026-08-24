<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra - B-EDEN</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-stone-50 text-gray-900">

    @php
        $total = 0;

        foreach ($carrito->detalles as $detalle) {
            $variante = $detalle->variante ?? null;
            $producto = $variante?->producto;

            if (!$producto) {
                continue;
            }

            $precio = $producto->precio_oferta ?? $producto->precio;
            $total += $precio * $detalle->cantidad;
        }

        $usuario = auth()->user();
        $faltaDocumento = empty($usuario->numero_documento);
    @endphp

    <div x-data="checkoutData()" x-cloak>

        <header class="bg-gray-900 text-white">
            <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
                <a href="{{ url('/carrito') }}" class="text-sm text-gray-300 hover:text-white">
                    ← Volver al carrito
                </a>

                <span class="font-semibold tracking-widest">
                    B-EDEN
                </span>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-8">

            <div class="mb-8">
                <h1 class="text-3xl font-bold">Finalizar compra</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Completa tus datos, elige la entrega y confirma tu pedido.
                </p>
            </div>

            @if(session('error') || $errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @if(session('error'))
                        <p class="font-medium">{{ session('error') }}</p>
                    @endif

                    @if($errors->any())
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @if(session('aviso'))
                <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    {{ session('aviso') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">

                    {{-- DATOS PERSONALES --}}
                    <section class="bg-white rounded-2xl border border-gray-200 p-6">

                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-bold">
                                1
                            </span>

                            <div>
                                <h2 class="font-semibold">Datos personales</h2>
                                <p class="text-xs text-gray-500">
                                    Datos necesarios para emitir tu boleta.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Nombres
                                </label>

                                <input
                                    type="text"
                                    value="{{ $usuario->nombres }}"
                                    readonly
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Apellidos
                                </label>

                                <input
                                    type="text"
                                    value="{{ $usuario->apellidos }}"
                                    readonly
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    DNI
                                </label>

                                <input
                                    type="text"
                                    value="{{ $usuario->numero_documento }}"
                                    readonly
                                    class="w-full rounded-xl border px-4 py-3 text-sm
                                    {{ $faltaDocumento ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}"
                                >

                                @if($faltaDocumento)
                                    <p class="text-xs text-red-600 mt-1">
                                        Necesitas registrar tu DNI antes de continuar.
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Correo
                                </label>

                                <input
                                    type="text"
                                    value="{{ $usuario->correo }}"
                                    readonly
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm"
                                >
                            </div>

                        </div>

                        @if($faltaDocumento)
                            <div class="mt-5 pt-5 border-t border-gray-100">

                                <form method="POST" action="{{ route('usuario.actualizar') }}">
                                    @csrf
                                    @method('PUT')

                                    <label class="block text-sm font-medium mb-1">
                                        Registrar DNI
                                    </label>

                                    <div class="flex gap-3">
                                        <input
                                            type="text"
                                            name="numero_documento"
                                            maxlength="8"
                                            minlength="8"
                                            inputmode="numeric"
                                            required
                                            placeholder="8 dígitos"
                                            value="{{ old('numero_documento') }}"
                                            class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                                        >

                                        <button
                                            type="submit"
                                            class="rounded-xl bg-gray-900 text-white px-5 py-3 text-sm font-semibold hover:bg-gray-800"
                                        >
                                            Guardar DNI
                                        </button>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2">
                                        El sistema intentará obtener tu nombre mediante RENIEC.
                                    </p>
                                </form>

                            </div>
                        @endif

                    </section>


                    {{-- ENTREGA --}}
                    <section class="bg-white rounded-2xl border border-gray-200 p-6">

                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-bold">
                                2
                            </span>

                            <div>
                                <h2 class="font-semibold">Entrega</h2>
                                <p class="text-xs text-gray-500">
                                    Elige cómo recibirás tu pedido.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">

                            @foreach($tiposEntrega as $tipo)

                                <label
                                    class="block border rounded-xl p-4 cursor-pointer transition"
                                    :class="tipoEntrega == {{ $tipo->id_tipo_entrega }}
                                        ? 'border-gray-900 bg-gray-50'
                                        : 'border-gray-200 hover:border-gray-400'"
                                >

                                    <div class="flex items-center gap-3">

                                        <input
                                            type="radio"
                                            value="{{ $tipo->id_tipo_entrega }}"
                                            @change="seleccionarTipoEntrega(
                                                {{ $tipo->id_tipo_entrega }},
                                                '{{ $tipo->nombre_tipo_entrega }}'
                                            )"
                                            :checked="tipoEntrega == {{ $tipo->id_tipo_entrega }}"
                                        >

                                        <div>
                                            <p class="font-medium text-sm">
                                                {{ $tipo->nombre_tipo_entrega }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                @if($tipo->id_tipo_entrega == 2)
                                                    Envío mediante agencia.
                                                @else
                                                    Recoge tu pedido en tienda.
                                                @endif
                                            </p>
                                        </div>

                                    </div>

                                </label>

                            @endforeach

                        </div>


                        {{-- UBICACION --}}
                        <div
                            x-show="tipoEntrega == 2"
                            class="mt-5 pt-5 border-t border-gray-100 space-y-4"
                        >

                            <h3 class="text-sm font-semibold">
                                Ubicación de entrega
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                <select
                                    x-model="departamento"
                                    @change="cargarProvincias"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm"
                                >
                                    <option value="">Departamento</option>

                                    @foreach($departamentos as $dep)
                                        <option value="{{ $dep->id_departamento }}">
                                            {{ $dep->nombre_departamento }}
                                        </option>
                                    @endforeach
                                </select>

                                <select
                                    x-model="provincia"
                                    @change="cargarDistritos"
                                    :disabled="!provincias.length"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm disabled:bg-gray-100"
                                >
                                    <option value="">Provincia</option>

                                    <template x-for="prov in provincias" :key="prov.id_provincia">
                                        <option
                                            :value="prov.id_provincia"
                                            x-text="prov.nombre_provincia"
                                        ></option>
                                    </template>
                                </select>

                                <select
                                    x-model="distrito"
                                    @change="cargarAgencias"
                                    :disabled="!distritos.length"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm disabled:bg-gray-100"
                                >
                                    <option value="">Distrito</option>

                                    <template x-for="dist in distritos" :key="dist.id_distrito">
                                        <option
                                            :value="dist.id_distrito"
                                            x-text="dist.nombre_distrito"
                                        ></option>
                                    </template>
                                </select>

                            </div>


                            {{-- AGENCIAS --}}
                            <div x-show="cargandoAgencias" class="text-sm text-gray-500">
                                Buscando agencias...
                            </div>

                            <div
                                x-show="!cargandoAgencias && agencias.length"
                                class="space-y-3"
                            >

                                <h3 class="text-sm font-semibold">
                                    Agencia
                                </h3>

                                <template x-for="ag in agencias" :key="ag.id_agencia">

                                    <label
                                        class="block border rounded-xl p-4 cursor-pointer"
                                        :class="agenciaSeleccionada == ag.id_agencia
                                            ? 'border-gray-900 bg-gray-50'
                                            : 'border-gray-200 hover:border-gray-400'"
                                    >

                                        <div class="flex items-center gap-3">

                                            <input
                                                type="radio"
                                                :value="ag.id_agencia"
                                                @change="seleccionarAgencia(ag)"
                                            >

                                            <div class="flex-1">
                                                <p
                                                    class="text-sm font-semibold"
                                                    x-text="ag.nombre_agencia"
                                                ></p>

                                                <p
                                                    class="text-xs text-gray-500"
                                                    x-text="ag.direccion"
                                                ></p>
                                            </div>

                                            <p class="font-semibold text-sm">
                                                S/
                                                <span x-text="parseFloat(ag.costo_envio).toFixed(2)"></span>
                                            </p>

                                        </div>

                                    </label>

                                </template>

                            </div>

                            <div
                                x-show="!cargandoAgencias && distrito && !agencias.length"
                                class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"
                            >
                                No hay agencias disponibles para este distrito.
                            </div>

                        </div>

                    </section>


                    {{-- TERMINOS --}}
                    <section class="bg-white rounded-2xl border border-gray-200 p-6">

                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-bold">
                                3
                            </span>

                            <div>
                                <h2 class="font-semibold">Confirmación</h2>
                                <p class="text-xs text-gray-500">
                                    Revisa todo antes de continuar al pago.
                                </p>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer">

                            <input
                                type="checkbox"
                                x-model="aceptaTerminos"
                                class="mt-1 w-4 h-4"
                            >

                            <span class="text-sm text-gray-600">
                                Acepto los
                                <a
                                    href="{{ route('terminos') }}"
                                    target="_blank"
                                    class="font-semibold text-gray-900 underline"
                                >
                                    Términos y Condiciones
                                </a>
                                y la
                                <a
                                    href="{{ route('politica-privacidad') }}"
                                    target="_blank"
                                    class="font-semibold text-gray-900 underline"
                                >
                                    Política de Privacidad
                                </a>.
                            </span>

                        </label>

                        <div
                            x-show="errorPago"
                            x-text="errorPago"
                            class="mt-3 text-sm text-red-600 font-medium"
                        ></div>

                    </section>

                </div>


                {{-- RESUMEN --}}
                <aside class="lg:sticky lg:top-6 h-fit">

                    <div class="bg-white rounded-2xl border border-gray-200 p-6">

                        <h2 class="text-lg font-bold mb-5">
                            Resumen del pedido
                        </h2>

                        <div class="space-y-4">

                            @foreach($carrito->detalles as $detalle)

                                @php
                                    $variante = $detalle->variante ?? null;
                                    $producto = $variante?->producto;

                                    if (!$producto) continue;

                                    $precio = $producto->precio_oferta ?? $producto->precio;
                                    $subtotal = $precio * $detalle->cantidad;
                                @endphp

                                <div class="flex gap-3">

                                    @if($producto->imagen)

                                        <img
                                            src="{{ asset('productos/' . $producto->imagen) }}"
                                            class="w-16 h-16 rounded-xl object-cover"
                                            alt="{{ $producto->nombre_producto }}"
                                        >

                                    @else

                                        <div class="w-16 h-16 rounded-xl bg-gray-100"></div>

                                    @endif

                                    <div class="flex-1 min-w-0">

                                        <p class="text-sm font-semibold truncate">
                                            {{ $producto->nombre_producto }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $variante->color }} · Talla {{ $variante->talla }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Cantidad: {{ $detalle->cantidad }}
                                        </p>

                                    </div>

                                    <p class="text-sm font-semibold">
                                        S/ {{ number_format($subtotal, 2) }}
                                    </p>

                                </div>

                            @endforeach

                        </div>

                        <div class="border-t border-gray-200 mt-5 pt-5 space-y-3">

                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Subtotal</span>
                                <span>S/ {{ number_format($total, 2) }}</span>
                            </div>

                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Envío</span>

                                <span x-show="tipoEntrega != 2">
                                    Gratis
                                </span>

                                <span x-show="tipoEntrega == 2 && costoEnvio > 0">
                                    S/ <span x-text="parseFloat(costoEnvio).toFixed(2)"></span>
                                </span>

                                <span x-show="tipoEntrega == 2 && costoEnvio == 0">
                                    —
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                                <span class="font-bold">
                                    Total
                                </span>

                                <span class="text-2xl font-bold">
                                    S/ <span x-text="totalConEnvio()"></span>
                                </span>
                            </div>

                        </div>

                        <button
                            type="button"
                            @click="intentarPagar"
                            :disabled="!puedePagar() || procesando"
                            class="w-full mt-6 rounded-xl bg-gray-900 text-white py-4 font-semibold text-sm
                                   hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            <span x-show="!procesando">
                                Continuar al pago
                            </span>

                            <span x-show="procesando">
                                Procesando...
                            </span>
                        </button>

                        <p
                            x-show="!puedePagar()"
                            class="text-xs text-gray-400 text-center mt-3"
                        >
                            Completa los datos y la entrega para continuar.
                        </p>

                    </div>

                </aside>

            </div>

        </main>


        {{-- FORMULARIO REAL --}}
        <form
            id="form-pedido"
            method="POST"
            action="{{ route('checkout.confirmar') }}"
        >
            @csrf

            <input
                type="hidden"
                name="id_tipo_entrega"
                :value="tipoEntrega"
            >

            <input
                type="hidden"
                name="id_distrito"
                :value="tipoEntrega == 2 ? distrito : ''"
            >

            <input
                type="hidden"
                name="id_agencia"
                :value="tipoEntrega == 2 ? agenciaSeleccionada : ''"
            >

            <input
                type="hidden"
                name="terminos"
                :value="aceptaTerminos ? 1 : 0"
            >
        </form>

    </div>


    <script>
        function checkoutData() {
            return {
                tipoEntrega: null,
                tipoEntregaNombre: '',

                departamento: '',
                provincia: '',
                distrito: '',

                provincias: [],
                distritos: [],
                agencias: [],

                agenciaSeleccionada: null,
                agenciaNombre: '',
                costoEnvio: 0,

                aceptaTerminos: false,
                procesando: false,
                errorPago: '',

                totalProductos: {{ $total }},

                totalConEnvio() {
                    return (
                        parseFloat(this.totalProductos) +
                        parseFloat(this.costoEnvio || 0)
                    ).toFixed(2);
                },

                seleccionarTipoEntrega(id, nombre) {
                    this.tipoEntrega = id;
                    this.tipoEntregaNombre = nombre;

                    this.departamento = '';
                    this.provincia = '';
                    this.distrito = '';

                    this.provincias = [];
                    this.distritos = [];
                    this.agencias = [];

                    this.agenciaSeleccionada = null;
                    this.agenciaNombre = '';
                    this.costoEnvio = 0;
                },

                seleccionarAgencia(agencia) {
                    this.agenciaSeleccionada = agencia.id_agencia;
                    this.agenciaNombre = agencia.nombre_agencia;
                    this.costoEnvio = parseFloat(agencia.costo_envio);
                },

                cargarProvincias() {
                    this.provincia = '';
                    this.distrito = '';
                    this.provincias = [];
                    this.distritos = [];
                    this.agencias = [];
                    this.agenciaSeleccionada = null;
                    this.costoEnvio = 0;

                    if (!this.departamento) {
                        return;
                    }

                    fetch('/ubicacion/provincias/' + this.departamento)
                        .then(response => response.json())
                        .then(data => {
                            this.provincias = data;
                        });
                },

                cargarDistritos() {
                    this.distrito = '';
                    this.distritos = [];
                    this.agencias = [];
                    this.agenciaSeleccionada = null;
                    this.costoEnvio = 0;

                    if (!this.provincia) {
                        return;
                    }

                    fetch('/ubicacion/distritos/' + this.provincia)
                        .then(response => response.json())
                        .then(data => {
                            this.distritos = data;
                        });
                },

                cargarAgencias() {
                    this.agencias = [];
                    this.agenciaSeleccionada = null;
                    this.agenciaNombre = '';
                    this.costoEnvio = 0;

                    if (!this.distrito) {
                        return;
                    }

                    fetch('/ubicacion/agencias/' + this.distrito)
                        .then(response => response.json())
                        .then(data => {
                            this.agencias = data;
                        });
                },

                puedePagar() {
                    if (!this.tipoEntrega) {
                        return false;
                    }

                    if ({{ $faltaDocumento ? 'true' : 'false' }}) {
                        return false;
                    }

                    if (this.tipoEntrega == 2) {
                        if (!this.departamento) {
                            return false;
                        }

                        if (!this.provincia) {
                            return false;
                        }

                        if (!this.distrito) {
                            return false;
                        }

                        if (!this.agenciaSeleccionada) {
                            return false;
                        }
                    }

                    if (!this.aceptaTerminos) {
                        return false;
                    }

                    return true;
                },

                intentarPagar() {
                    this.errorPago = '';

                    if (!this.puedePagar()) {
                        this.errorPago = 'Completa todos los datos requeridos antes de continuar.';
                        return;
                    }

                    this.procesando = true;

                    document.getElementById('form-pedido').submit();
                }
            };
        }
    </script>

</body>

</html>