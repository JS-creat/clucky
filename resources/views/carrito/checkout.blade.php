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

    <div x-data="{ ...ubicacion(), editando:false, tipoEntrega: 1, totalProductos: {{ $total }} }"
        class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">


        {{-- IZQUIERDA --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ENTREGA --}}
            <div x-data="{ ...ubicacion(), editando:false, tipoEntrega: 1 }"
                class="bg-white border rounded-xl p-6 shadow-sm">

                <!-- VISTA RESUMIDA -->
                <div x-show="!editando">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-semibold">Entrega</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Tipo seleccionado:
                                <span class="font-medium">
                                    {{ $tiposEntrega->first()->nombre_tipo_entrega ?? '' }}
                                </span>
                            </p>
                        </div>

                        <button @click="editando = true" class="text-sm text-blue-600 hover:underline">
                            Modificar ✎
                        </button>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <div x-show="editando" x-transition class="mt-4 space-y-4">

                    @foreach($tiposEntrega as $tipo)
                        <label class="flex items-center space-x-3">
                            <input type="radio" name="id_tipo_entrega" value="{{ $tipo->id_tipo_entrega }}"
                                @click="tipoEntrega = {{ $tipo->id_tipo_entrega }}">
                            <span>{{ $tipo->nombre_tipo_entrega }}</span>
                        </label>
                    @endforeach

                    <!-- DIRECCIÓN SOLO SI ES ENVÍO (ejemplo id 2) -->
                    <div x-show="tipoEntrega == 2" x-transition class="border-t pt-4 space-y-3">

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
        </div>

        {{-- DERECHA RESUMEN --}}
        <div class="bg-white border rounded-xl p-6 shadow-sm h-fit">

            <h2 class="text-lg font-semibold mb-4">Resumen del pedido</h2>

            @php $total = 0; @endphp

            @if($carrito && $carrito->detalles->count())

                @foreach($carrito->detalles as $detalle)

                    @php
                        $precio = $detalle->producto->precio_oferta ?? $detalle->producto->precio;
                        $subtotal = $precio * $detalle->cantidad;
                        $total += $subtotal;
                    @endphp

                    <div class="flex justify-between text-sm mb-2">
                        <span>{{ $detalle->producto->nombre_producto }} x{{ $detalle->cantidad }}</span>
                        <span>S/ {{ number_format($subtotal, 2) }}</span>
                    </div>

                @endforeach

                <hr class="my-4">

                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>S/ {{ number_format($total, 2) }}</span>
                </div>

            @else
                <p class="text-sm text-gray-500">Tu carrito está vacío</p>
            @endif

        </div>

    </div>

@endsection


<script>
    function ubicacion() {
        return {
            departamento: '',
            provincia: '',
            distrito: '',
            provincias: [],
            distritos: [],
            costoEnvio: 0,

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

            mostrarCosto() {
                let seleccionado = this.distritos.find(d => d.id_distrito == this.distrito);
                this.costoEnvio = seleccionado ? seleccionado.costo_envio : 0;
            }
        }
    }
</script>
