@extends('admin.layout')

@section('content')

<div
    class="space-y-6"
    x-data="agenciaEditor({
        selectedDep: '{{ old('id_departamento', $departamentoActual?->id_departamento) }}',
        selectedProv: '{{ old('id_provincia', $provinciaActual?->id_provincia) }}',
        selectedDist: '{{ old('id_distrito', $agencia->id_distrito) }}',
        provinciasIniciales: @js($provincias),
        distritosIniciales: @js($distritos)
    })"
>

    <div>
        <a
            href="{{ route('admin.agencias.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-gray-500
                   hover:text-indigo-600 transition-colors">

            <x-heroicon-o-arrow-left class="w-4 h-4" />

            Volver a agencias
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">

        <div class="p-6 sm:p-8 lg:p-10">

            <div class="flex flex-col sm:flex-row sm:items-center gap-5 mb-10">

                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600
                            flex items-center justify-center flex-shrink-0">

                    <x-heroicon-o-pencil-square class="w-7 h-7" />

                </div>

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-indigo-600">
                        Administración de agencias
                    </p>

                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight mt-1">
                        Editar Agencia
                    </h1>

                    <p class="text-gray-500 font-medium mt-1">
                        Actualiza la información de
                        <span class="font-bold text-gray-700">
                            {{ $agencia->nombre_agencia }}
                        </span>
                    </p>
                </div>

            </div>

            <form
                action="{{ route('admin.agencias.update', $agencia) }}"
                method="POST"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div class="space-y-6">

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                Nombre de la agencia
                            </label>

                            <input
                                type="text"
                                name="nombre_agencia"
                                value="{{ old('nombre_agencia', $agencia->nombre_agencia) }}"
                                class="w-full px-5 py-4 bg-gray-50 border rounded-2xl
                                       border-gray-100 text-sm font-semibold text-gray-800
                                       outline-none transition-all
                                       focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                       @error('nombre_agencia') border-rose-400 @enderror"
                                placeholder="Ej. Shalom - Huancayo"
                            >

                            @error('nombre_agencia')
                                <p class="mt-2 text-xs font-bold text-rose-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                Dirección exacta
                            </label>

                            <textarea
                                name="direccion"
                                rows="4"
                                class="w-full px-5 py-4 bg-gray-50 border rounded-2xl
                                       border-gray-100 text-sm font-semibold text-gray-800
                                       outline-none resize-none transition-all
                                       focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                       @error('direccion') border-rose-400 @enderror"
                                placeholder="Ingrese la dirección de la agencia"
                            >{{ old('direccion', $agencia->direccion) }}</textarea>

                            @error('direccion')
                                <p class="mt-2 text-xs font-bold text-rose-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Costo de envío
                                </label>

                                <div class="relative">

                                    <span class="absolute left-5 top-1/2 -translate-y-1/2
                                                 text-sm font-black text-gray-400">
                                        S/
                                    </span>

                                    <input
                                        type="number"
                                        name="costo_envio"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('costo_envio', $agencia->costo_envio) }}"
                                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border rounded-2xl
                                               border-gray-100 text-sm font-bold text-gray-800
                                               outline-none transition-all
                                               focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                               @error('costo_envio') border-rose-400 @enderror"
                                    >

                                </div>

                                @error('costo_envio')
                                    <p class="mt-2 text-xs font-bold text-rose-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Estado
                                </label>

                                <select
                                    name="estado"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100
                                           rounded-2xl text-sm font-bold text-gray-700
                                           outline-none transition-all
                                           focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                >

                                    <option value="1" {{ old('estado', $agencia->estado) == 1 ? 'selected' : '' }}>
                                        Activa
                                    </option>

                                    <option value="0" {{ old('estado', $agencia->estado) == 0 ? 'selected' : '' }}>
                                        Inactiva
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 sm:p-7">

                        <div class="flex items-center gap-3 mb-6">

                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600
                                        flex items-center justify-center">

                                <x-heroicon-o-map-pin class="w-5 h-5" />

                            </div>

                            <div>
                                <h2 class="font-black text-gray-900">
                                    Ubicación
                                </h2>

                                <p class="text-xs text-gray-400 font-medium">
                                    Selecciona la ubicación de la agencia
                                </p>
                            </div>

                        </div>

                        <div class="space-y-5">

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Departamento
                                </label>

                                <select
                                    x-model="selectedDep"
                                    @change="fetchProvincias()"
                                    class="w-full px-5 py-4 bg-white border border-gray-100
                                           rounded-2xl text-sm font-bold text-gray-700
                                           outline-none focus:border-indigo-500
                                           focus:ring-4 focus:ring-indigo-100"
                                >

                                    <option value="">
                                        Seleccionar departamento
                                    </option>

                                    @foreach($departamentos as $dep)

                                        <option value="{{ $dep->id_departamento }}">
                                            {{ $dep->nombre_departamento }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Provincia
                                </label>

                                <div class="relative">

                                    <select
                                        x-model="selectedProv"
                                        @change="fetchDistritos()"
                                        :disabled="!selectedDep || loadingProv"
                                        class="w-full px-5 py-4 bg-white border border-gray-100
                                               rounded-2xl text-sm font-bold text-gray-700
                                               outline-none transition-all
                                               focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                               disabled:bg-gray-100 disabled:text-gray-400"
                                    >

                                        <option value="">
                                            Seleccionar provincia
                                        </option>

                                        <template x-for="prov in provincias" :key="prov.id_provincia">

                                            <option
                                                :value="prov.id_provincia"
                                                x-text="prov.nombre_provincia">
                                            </option>

                                        </template>

                                    </select>

                                    <div
                                        x-show="loadingProv"
                                        class="absolute right-4 top-1/2 -translate-y-1/2"
                                    >
                                        <svg class="w-5 h-5 animate-spin text-indigo-600"
                                             viewBox="0 0 24 24"
                                             fill="none">
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="9"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                class="opacity-25">
                                            </circle>
                                            <path
                                                d="M21 12a9 9 0 0 1-9 9"
                                                stroke="currentColor"
                                                stroke-width="3">
                                            </path>
                                        </svg>
                                    </div>

                                </div>

                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Distrito
                                </label>

                                <select
                                    name="id_distrito"
                                    x-model="selectedDist"
                                    :disabled="!selectedProv || loadingDist"
                                    class="w-full px-5 py-4 bg-white border border-gray-100
                                           rounded-2xl text-sm font-bold text-gray-700
                                           outline-none transition-all
                                           focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                           disabled:bg-gray-100 disabled:text-gray-400"
                                >

                                    <option value="">
                                        Seleccionar distrito
                                    </option>

                                    <template x-for="dist in distritos" :key="dist.id_distrito">

                                        <option
                                            :value="dist.id_distrito"
                                            x-text="dist.nombre_distrito">
                                        </option>

                                    </template>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>

                @if($errors->any())

                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5">

                        <div class="flex gap-3">

                            <x-heroicon-o-exclamation-circle
                                class="w-5 h-5 text-rose-500 flex-shrink-0"
                            />

                            <div>

                                <p class="text-sm font-black text-rose-700">
                                    Revisa los datos ingresados
                                </p>

                                <ul class="mt-2 space-y-1 text-xs font-medium text-rose-600">

                                    @foreach($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    </div>

                @endif

                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">

                    <a
                        href="{{ route('admin.agencias.index') }}"
                        class="sm:w-40 inline-flex items-center justify-center
                               px-6 py-4 rounded-2xl bg-gray-100 hover:bg-gray-200
                               text-gray-600 font-bold transition-all"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2
                               px-6 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700
                               text-white font-black shadow-lg shadow-indigo-100
                               transition-all hover:-translate-y-0.5 active:scale-[0.99]"
                    >

                        <x-heroicon-o-check class="w-5 h-5" />

                        Guardar cambios

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

        provincias: config.provinciasIniciales || [],
        distritos: config.distritosIniciales || [],

        loadingProv: false,
        loadingDist: false,

        async fetchProvincias() {
            this.selectedProv = '';
            this.selectedDist = '';
            this.provincias = [];
            this.distritos = [];

            if (!this.selectedDep) {
                return;
            }

            this.loadingProv = true;

            try {
                const response = await fetch(
                    `/admin/agencias/provincias/${this.selectedDep}`
                );

                if (!response.ok) {
                    throw new Error('No se pudieron cargar las provincias.');
                }

                this.provincias = await response.json();

            } catch (error) {
                console.error(error);
            } finally {
                this.loadingProv = false;
            }
        },

        async fetchDistritos() {
            this.selectedDist = '';
            this.distritos = [];

            if (!this.selectedProv) {
                return;
            }

            this.loadingDist = true;

            try {
                const response = await fetch(
                    `/admin/agencias/distritos/${this.selectedProv}`
                );

                if (!response.ok) {
                    throw new Error('No se pudieron cargar los distritos.');
                }

                this.distritos = await response.json();

            } catch (error) {
                console.error(error);
            } finally {
                this.loadingDist = false;
            }
        }
    }
}
</script>

@endsection