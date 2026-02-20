<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50">

<nav class="border-b sticky top-0 bg-white z-50">
    <div class="max-w-full mx-auto px-4 sm:px-8">
        <div class="flex justify-between h-20 items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}"
                         class="h-14 w-auto transition-transform hover:scale-105">
                </a>
            </div>
        </div>
    </div>
</nav>

@php $total = 0; @endphp

@if($carrito && $carrito->detalles->count())
    @foreach($carrito->detalles as $detalle)
        @php
            $variante = $detalle->variante ?? null;
            $producto = $variante?->producto;
            if (!$producto) continue;

            $precio = $producto->precio_oferta ?? $producto->precio;
            $subtotal = $precio * $detalle->cantidad;
            $total += $subtotal;
        @endphp
    @endforeach
@endif


<div x-data="checkoutData()"
    class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{--  IZQUIERDA  --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- ENTREGA --}}
        <div class="bg-white border rounded-xl p-6 shadow-sm">

            <div x-show="!editando">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-semibold">Entrega</h2>

                        <p class="text-sm text-gray-600 mt-1">
                            Tipo seleccionado:
                            <span class="font-medium" x-text="tipoEntregaNombre"></span>
                        </p>

                        <p class="text-sm text-gray-600 mt-1"
                           x-show="tipoEntrega == 2 && distrito">
                            Distrito seleccionado:
                            <span x-text="distrito"></span>
                        </p>
                    </div>

                    <button @click="editando = true"
                            class="text-sm text-blue-600 hover:underline">
                        Modificar
                    </button>
                </div>
            </div>

            <div x-show="editando" x-transition class="mt-4 space-y-4">

                @foreach($tiposEntrega as $tipo)
                    <label class="flex items-center space-x-3">
                        <input type="radio"
                               value="{{ $tipo->id_tipo_entrega }}"
                               @click="
                               tipoEntrega={{ $tipo->id_tipo_entrega }};
                               tipoEntregaNombre='{{ $tipo->nombre_tipo_entrega }}';
                               calcularEnvioPorTipo();
                               ">
                        <span>{{ $tipo->nombre_tipo_entrega }}</span>
                    </label>
                @endforeach

                <div x-show="tipoEntrega == 2" class="border-t pt-4 space-y-3">

                    <select x-model="departamento"
                            @change="cargarProvincias"
                            class="w-full border rounded-lg p-3">
                        <option value="">Seleccionar Departamento</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}">
                                {{ $dep->nombre_departamento }}
                            </option>
                        @endforeach
                    </select>

                    <select x-model="provincia"
                            @change="cargarDistritos"
                            x-show="provincias.length"
                            class="w-full border rounded-lg p-3">
                        <option value="">Seleccionar Provincia</option>
                        <template x-for="prov in provincias">
                            <option :value="prov.id_provincia"
                                    x-text="prov.nombre_provincia"></option>
                        </template>
                    </select>

                    <select x-model="distrito"
                            @change="mostrarCosto"
                            x-show="distritos.length"
                            class="w-full border rounded-lg p-3">
                        <option value="">Seleccionar Distrito</option>
                        <template x-for="dist in distritos">
                            <option :value="dist.id_distrito"
                                    x-text="dist.nombre_distrito"></option>
                        </template>
                    </select>

                    <div x-show="costoEnvio > 0"
                         class="text-sm text-gray-700">
                        Costo envío:
                        <span class="font-semibold">
                            S/ <span x-text="costoEnvio"></span>
                        </span>
                    </div>
                </div>

                <button @click="editando = false"
                        class="text-sm text-gray-600 underline">
                    Guardar
                </button>

            </div>
        </div>

        {{-- DATOS PERSONALES --}}
        <div x-data="{ editandoDatos:false }"
             class="bg-white border rounded-xl p-6 shadow-sm">

            <div x-show="!editandoDatos" class="flex justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Datos personales</h2>
                    <p class="text-sm mt-1">
                        {{ auth()->user()->nombres }}
                        {{ auth()->user()->apellidos }}
                    </p>
                    <p class="text-sm">
                        Documento:
                        {{ auth()->user()->numero_documento ?? 'No registrado' }}
                    </p>
                    <p class="text-sm">
                        Teléfono:
                        {{ auth()->user()->telefono ?? 'No registrado' }}
                    </p>
                    <p class="text-sm">
                        {{ auth()->user()->correo }}
                    </p>
                </div>

                <button @click="editandoDatos=true"
                        class="text-blue-600 text-sm hover:underline">
                    Modificar ✎
                </button>
            </div>

            <div x-show="editandoDatos" x-transition>
                <form method="POST" action="{{ route('usuario.actualizar') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 mt-4">
                        <input type="text" name="nombres"
                               value="{{ auth()->user()->nombres }}"
                               class="w-full border rounded-lg p-3">

                        <input type="text" name="apellidos"
                               value="{{ auth()->user()->apellidos }}"
                               class="w-full border rounded-lg p-3">

                        <input type="text" name="numero_documento"
                               value="{{ auth()->user()->numero_documento }}"
                               class="w-full border rounded-lg p-3">

                        <input type="text" name="telefono"
                               value="{{ auth()->user()->telefono }}"
                               class="w-full border rounded-lg p-3">
                    </div>

                    <div class="mt-5 flex gap-3">
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                            Guardar cambios
                        </button>

                        <button type="button"
                                @click="editandoDatos=false"
                                class="text-gray-600 underline">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>


    {{-- DERECHA --}}
    <div class="bg-white border rounded-xl p-6 shadow-sm h-fit">

        <h2 class="text-lg font-semibold mb-4">
            Resumen del pedido
        </h2>

        @foreach($carrito->detalles as $detalle)
            @php
                $variante = $detalle->variante ?? null;
                $producto = $variante?->producto;
                if (!$producto) continue;

                $precio = $producto->precio_oferta ?? $producto->precio;
                $subtotal = $precio * $detalle->cantidad;
            @endphp

            <div class="flex justify-between text-sm mb-2">
                <span>
                    {{ $producto->nombre_producto }}
                    ({{ $variante->color }} - {{ $variante->talla }})
                    x{{ $detalle->cantidad }}
                </span>
                <span>S/ {{ number_format($subtotal, 2) }}</span>
            </div>
        @endforeach

        <hr class="my-4">

        <div class="flex justify-between text-sm">
            <span>Envío</span>
            <span>
                S/
                <span x-text="parseFloat(costoEnvio || 0).toFixed(2)"></span>
            </span>
        </div>

        <div class="flex justify-between font-semibold text-lg mt-2">
            <span>Total</span>
            <span>
                S/
                <span x-text="(parseFloat(totalProductos) + parseFloat(costoEnvio || 0)).toFixed(2)"></span>
            </span>
        </div>

    </div>

</div>

<script>
function checkoutData() {
    return {
        editando:false,
        tipoEntrega: {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
        tipoEntregaNombre: '{{ $tiposEntrega->first()->nombre_tipo_entrega ?? '' }}',
        departamento:'',
        provincia:'',
        distrito:'',
        provincias:[],
        distritos:[],
        costoEnvio:0,
        totalProductos: {{ $total }},

        calcularEnvioPorTipo(){
            if(this.tipoEntrega != 2){
                this.costoEnvio = 0
            }
        },

        cargarProvincias(){
            if(!this.departamento) return;

            fetch('/provincias/' + this.departamento)
                .then(res => res.json())
                .then(data => {
                    this.provincias = data
                    this.provincia = ''
                    //console.log("Distritos:", data)
                    this.distritos = []
                    this.distrito = ''
                    this.costoEnvio = 0
                })
        },

        cargarDistritos(){
            if(!this.provincia) return;

            fetch('/distritos/' + this.provincia)
                .then(res => res.json())
                .then(data => {
                    this.distritos = data
                    this.distrito = ''
                    this.costoEnvio = 0
                })
        },

        mostrarCosto(){
            const distritoObj = this.distritos.find(d => d.id_distrito == this.distrito)
            if(distritoObj){
                this.costoEnvio = parseFloat(distritoObj.costo_envio)
            }
        }
    }
}
</script>

</body>
</html>
