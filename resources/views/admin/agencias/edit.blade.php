@extends('admin.layout')

@section('content')
<div class="p-6 max-w-5xl mx-auto"
     x-data="agenciaEditor({
        selectedDep: '{{ $departamentoActual->id_departamento }}',
        selectedProv: '{{ $provinciaActual->id_provincia }}',
        selectedDist: '{{ $agencia->id_distrito }}',
        provinciasIniciales: {{ json_encode($provincias) }},
        distritosIniciales: {{ json_encode($distritos) }}
     })">

    <div class="mb-8">
        <a href="{{ route('admin.agencias.index') }}" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:text-indigo-800 transition-colors">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Volver al listado
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-10">
            <div class="mb-10">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Editar Agencia</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium italic">"{{ $agencia->nombre_agencia }}"</p>
            </div>

            <form action="{{ route('admin.agencias.update', $agencia) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Columna Izquierda: Datos --}}
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-gray-700 uppercase ml-1">Nombre</label>
                            <input type="text" name="nombre_agencia" value="{{ old('nombre_agencia', $agencia->nombre_agencia) }}"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all font-medium">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-black text-gray-700 uppercase ml-1">Costo Envío</label>
                            <input type="number" step="0.01" name="costo_envio" value="{{ old('costo_envio', $agencia->costo_envio) }}"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all font-medium">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-black text-gray-700 uppercase ml-1">Estado</label>
                            <select name="estado" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all font-medium">
                                <option value="1" {{ $agencia->estado ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ !$agencia->estado ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>
                    </div>

                    {{-- Columna Derecha: Ubicación --}}
                    <div class="space-y-6 bg-indigo-50/30 p-6 rounded-[2rem]">
                        <div class="space-y-2">
                            <label class="text-sm font-black text-indigo-900 uppercase ml-1">Departamento</label>
                            <select x-model="selectedDep" @change="fetchProvincias()"
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-100 font-medium">
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->id_departamento }}">{{ $dep->nombre_departamento }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-black text-indigo-900 uppercase ml-1">Provincia</label>
                            <select x-model="selectedProv" @change="fetchDistritos()" :disabled="loadingProv"
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-100 font-medium disabled:opacity-50">
                                <template x-for="prov in provincias" :key="prov.id_provincia">
                                    <option :value="prov.id_provincia" x-text="prov.nombre_provincia" :selected="prov.id_provincia == selectedProv"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-black text-indigo-900 uppercase ml-1">Distrito Final</label>
                            <select name="id_distrito" x-model="selectedDist" :disabled="loadingDist"
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-100 font-medium disabled:opacity-50">
                                <template x-for="dist in distritos" :key="dist.id_distrito">
                                    <option :value="dist.id_distrito" x-text="dist.nombre_distrito" :selected="dist.id_distrito == selectedDist"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-black text-gray-700 uppercase ml-1">Dirección Exacta</label>
                    <textarea name="direccion" rows="2" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all font-medium" required>{{ old('direccion', $agencia->direccion) }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-5 rounded-2xl font-black shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95 text-lg">
                        Confirmar y Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function agenciaEditor(config) {
        return {
            selectedDep: config.selectedDep,
            selectedProv: config.selectedProv,
            selectedDist: config.selectedDist,
            provincias: config.provinciasIniciales,
            distritos: config.distritosIniciales,
            loadingProv: false,
            loadingDist: false,

            async fetchProvincias() {
                this.loadingProv = true;
                try {
                    const response = await fetch(`/admin/agencias/provincias/${this.selectedDep}`);
                    this.provincias = await response.json();
                    if (this.provincias.length > 0) {
                        this.selectedProv = this.provincias[0].id_provincia;
                        this.fetchDistritos();
                    }
                } finally {
                    this.loadingProv = false;
                }
            },

            async fetchDistritos() {
                this.loadingDist = true;
                try {
                    const response = await fetch(`/admin/agencias/distritos/${this.selectedProv}`);
                    this.distritos = await response.json();
                    if (this.distritos.length > 0) {
                        this.selectedDist = this.distritos[0].id_distrito;
                    }
                } finally {
                    this.loadingDist = false;
                }
            }
        }
    }
</script>
@endsection
