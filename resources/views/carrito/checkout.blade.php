<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
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


@extends('layouts.app')

@section('content')

    @php $total = 0; @endphp

    @if($carrito && $carrito->detalles->count())
        @foreach($carrito->detalles as $detalle)
            @php
                $precio = $detalle->producto->precio_oferta ?? $detalle->producto->precio;
                $subtotal = $precio * $detalle->cantidad;
                $total += $subtotal;
            @endphp
        @endforeach
    @endif

    <div x-data="{
                                                ...ubicacion(),
                                                editando:false,
                                                tipoEntrega: {{ $tiposEntrega->first()->id_tipo_entrega ?? 1 }},
                                                tipoEntregaNombre: '{{ $tiposEntrega->first()->nombre_tipo_entrega ?? '' }}',
                                                totalProductos: {{ $total }}
                                                }"
        class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">


        {{-- IZQUIERDA --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ENTREGA --}}
            <div class="bg-white border rounded-xl p-6 shadow-sm">

                <!-- VISTA RESUMIDA -->
                <div x-show="!editando">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-semibold">Entrega</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Tipo seleccionado:
                                <span class="font-medium">
                                    <span x-text="tipoEntregaNombre"></span>

                                </span>
                            </p>
                        </div>

                        <button @click="editando = true" class="text-sm text-blue-600 hover:underline">
                            Modificar ✎
                        </button>
                    </div>
                </div>

                <!-- AGENCIA -->
                <div class="mt-3" x-show="tipoEntrega == 2 && distrito" x-transition>

                    <label class="block text-sm font-medium mb-1">
                        Agencia de envío
                    </label>

                    <select x-model="agencia" class="w-full border rounded p-2">

                        <option value="">
                            Seleccione agencia
                        </option>

                        <template x-for="ag in agencias" :key="ag.id_agencia">

                            <option :value="ag.id_agencia" x-text="ag.nombre_agencia + ' - ' + ag.direccion">
                            </option>

                        </template>

                    </select>

                </div>



                <!-- FORMULARIO -->
                <div x-show="editando" x-transition class="mt-4 space-y-4">

                    @foreach($tiposEntrega as $tipo)
                        <label class="flex items-center space-x-3">

                            <input type="radio" name="id_tipo_entrega" value="{{ $tipo->id_tipo_entrega }}" @click="
                                                            tipoEntrega = {{ $tipo->id_tipo_entrega }};
                                                            tipoEntregaNombre = '{{ $tipo->nombre_tipo_entrega }}';
                                                            calcularEnvioPorTipo();
                                                        ">

                            <span>{{ $tipo->nombre_tipo_entrega }}</span>

                        </label>
                    @endforeach


                        <!-- DIRECCIÓN SOLO SI ES ENVÍO (ejemplo id 2) -->
                        <div x-show=" tipoEntrega==2" x-transition class="border-t pt-4 space-y-3">

                            <div x-show="tipoEntrega == 2" x-transition class="border-t pt-4 space-y-3">

                                <!-- Departamento -->
                                <select x-model="departamento" @change="cargarProvincias" class="w-full border rounded-lg p-3">
                                    <option value="">Seleccionar Departamento</option>
                                    @foreach($departamentos as $dep)
                                        <option value="{{ $dep->id_departamento }}">
                                            {{ $dep->nombre_departamento }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Provincia -->
                                <select x-model="provincia" @change="cargarDistritos" class="w-full border rounded-lg p-3"
                                    x-show="provincias.length">
                                    <option value="">Seleccionar Provincia</option>
                                    <template x-for="prov in provincias" :key="prov.id_provincia">
                                        <option :value="prov.id_provincia" x-text="prov.nombre_provincia"></option>
                                    </template>
                                </select>

                                <!-- Distrito -->
                                <select x-model="distrito" @change="mostrarCosto" class="w-full border rounded-lg p-3"
                                    x-show="distritos.length">
                                    <option value="">Seleccionar Distrito</option>
                                    <template x-for="dist in distritos" :key="dist.id_distrito">
                                        <option :value="dist.id_distrito" x-text="dist.nombre_distrito"></option>
                                    </template>
                                </select>

                                <!-- Mostrar costo -->
                                <div x-show="costoEnvio > 0" class="text-sm text-gray-700">
                                    Costo de envío:
                                    <span class="font-semibold">
                                        S/ <span x-text="costoEnvio"></span>
                                    </span>
                                </div>

                            </div>


                        </div>

                        <button @click="editando = false" class="mt-2 text-sm text-gray-600 underline">
                            Guardar
                        </button>

                    </div>

                </div>
                {{-- DATOS PERSONALES --}}
                <div x-data="{ editandoDatos:false }" class="bg-white border rounded-xl p-6 shadow-sm">

                    <!-- VISTA RESUMIDA -->
                    <div x-show="!editandoDatos">

                        <div class="flex justify-between">

                            <div>

                                <h2 class="text-lg font-semibold">Datos personales</h2>

                                <p class="text-sm text-gray-700 mt-1">

                                    {{ auth()->user()->nombres }}
                                    {{ auth()->user()->apellidos }}

                                </p>

                                <p class="text-sm text-gray-700">
                                    Documento:
                                    {{ auth()->user()->numero_documento ?? 'No registrado' }}
                                </p>

                                <p class="text-sm text-gray-700">
                                    Teléfono:
                                    {{ auth()->user()->telefono ?? 'No registrado' }}
                                </p>

                                <p class="text-sm text-gray-700">
                                    {{ auth()->user()->correo }}
                                </p>

                            </div>

                            <button @click="editandoDatos=true" class="text-blue-600 text-sm hover:underline">

                                Modificar ✎

                            </button>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <div x-show="editandoDatos" x-transition>

                        <form method="POST" action="{{ route('usuario.actualizar') }}">

                            @csrf
                            @method('PUT')

                            <div class="space-y-4 mt-4">

                                <!-- Nombres -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombres
                                    </label>

                                    <input type="text" name="nombres" value="{{ auth()->user()->nombres }}"
                                        placeholder="Ingrese sus nombres"
                                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>


                                <!-- Apellidos -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Apellidos
                                    </label>

                                    <input type="text" name="apellidos" value="{{ auth()->user()->apellidos }}"
                                        placeholder="Ingrese sus apellidos"
                                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>

                                <!-- Tipo documento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo de documento
                                </label>

                                    <select name="id_tipo_documento"
                                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500">

                                    <option value="">Seleccionar</option>

                                    @foreach($tiposDocumento as $tipo)

                                        <option
                                            value="{{ $tipo->id_tipo_documento }}"

                                            {{ auth()->user()->id_tipo_documento == $tipo->id_tipo_documento ? 'selected' : '' }}

                                        >
                                            {{ $tipo->nombre_tipo_documento }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>



                                <!-- DNI -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        DNI / Documento
                                    </label>

                                    <input type="text" name="numero_documento" value="{{ auth()->user()->numero_documento }}"
                                        placeholder="Ej: 12345678"
                                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>


                                <!-- Teléfono -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Teléfono
                                    </label>

                                    <input type="text" name="telefono" value="{{ auth()->user()->telefono }}"
                                        placeholder="Ej: 987654321"
                                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>

                            </div>


                            <!-- BOTONES -->
                            <div class="mt-5 flex gap-3">

                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">

                                    Guardar cambios

                                </button>


                                <button type="button" @click="editandoDatos=false" class="text-gray-600 underline">

                                    Cancelar

                                </button>

                            </div>

                        </form>

                    </div>


                </div>

            </div>

            {{-- DERECHA RESUMEN --}}
            <div class="bg-white border rounded-xl p-6 shadow-sm h-fit">

                <h2 class="text-lg font-semibold mb-4">Resumen del pedido</h2>

                @foreach($carrito->detalles as $detalle)

                    @php
        $precio = $detalle->producto->precio_oferta ?? $detalle->producto->precio;
        $subtotal = $precio * $detalle->cantidad;
                    @endphp

                    <div class="flex justify-between text-sm mb-2">
                        <span>{{ $detalle->producto->nombre_producto }} x{{ $detalle->cantidad }}</span>
                        <span>S/ {{ number_format($subtotal, 2) }}</span>
                    </div>

                @endforeach

                <hr class="my-4">

                <!-- ENVIO -->
                <div class="flex justify-between text-sm">
                    <span>Envío</span>

                    <span>

                        S/

                        <span x-text="parseFloat(costoEnvio || 0).toFixed(2)"></span>

                    </span>

                </div>

                <!-- TOTAL -->
                <div class="flex justify-between font-semibold text-lg mt-2">

                    <span>Total</span>

                    <span>

                        S/

                        <span x-text="(parseFloat(totalProductos) + parseFloat(costoEnvio || 0)).toFixed(2)"></span>

                    </span>

                </div>

            </div>


        </div>

@endsection

</body>
</html>

<script>
    function ubicacion() {
        return {
            departamento: '',
            provincia: '',
            distrito: '',
            provincias: [],
            distritos: [],
            costoEnvio: 0,
            agencias: [],
            agencia: '',

            async cargarProvincias() {
                this.provincias = [];
                this.provincia = '';
                this.distritos = [];
                this.distrito = '';

                let res = await fetch(`/ubicacion/provincias/${this.departamento}`);
                this.provincias = await res.json();
            },

            async cargarDistritos() {
                this.distritos = [];
                this.distrito = '';

                let res = await fetch(`/ubicacion/distritos/${this.provincia}`);
                this.distritos = await res.json();
            },

            async cargarAgencias() {

                this.agencias = [];
                this.agencia = '';

                let res = await fetch(`/agencias/${this.distrito}`);
                this.agencias = await res.json();

            },


            mostrarCosto() {

                let seleccionado = this.distritos.find(d => d.id_distrito == this.distrito);

                this.costoEnvio = seleccionado ? seleccionado.costo_envio : 0;

                this.cargarAgencias();

            },

            calcularEnvioPorTipo() {

                if (this.tipoEntrega == 1) {

                    this.costoEnvio = 0;

                }

            }


        }
    }
</script>
