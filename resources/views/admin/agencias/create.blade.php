@extends('admin.layout')

@section('content')
<div
    x-data="agenciaForm()"
    class="bg-gray-100 min-h-screen -m-8 p-8 relative"
>
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4 translate-x-4"
        x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-6 right-6 z-50 w-full max-w-sm"
        style="display: none;"
    >
        <div
            class="bg-white rounded-2xl shadow-2xl border-l-4 p-5 flex items-start gap-3"
            :class="toast.type === 'success' ? 'border-black' : 'border-rose-500'"
        >
            <div
                class="p-1.5 rounded-full flex-shrink-0"
                :class="toast.type === 'success' ? 'bg-black' : 'bg-rose-500'"
            >
                <template x-if="toast.type === 'success'">
                    <x-heroicon-o-check class="w-4 h-4 text-white" />
                </template>

                <template x-if="toast.type === 'error'">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-white" />
                </template>
            </div>

            <div class="flex-1">
                <p
                    class="text-xs font-black uppercase tracking-wide"
                    :class="toast.type === 'success' ? 'text-black' : 'text-rose-600'"
                    x-text="toast.type === 'success' ? 'Listo' : 'Falta algo'"
                ></p>

                <p
                    class="text-sm font-semibold text-gray-700 mt-0.5"
                    x-text="toast.message"
                ></p>
            </div>

            <button
                type="button"
                @click="toast.show = false"
                class="text-gray-300 hover:text-black transition-colors"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
    </div>

    <div class="flex items-center gap-4 mb-10">
        <a
            href="{{ route('admin.agencias.index') }}"
            class="p-3 bg-white border-2 border-gray-200 rounded-2xl text-gray-600 hover:border-black hover:text-black transition-all shadow-sm"
        >
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>

        <div>
            <h1 class="text-4xl font-extrabold text-black tracking-tight">
                Nueva Agencia
            </h1>

            <p class="text-sm text-gray-500 font-medium mt-1">
                Registra un nuevo punto de despacho y configura su ubicación.
            </p>
        </div>
    </div>

    <form
        x-ref="agenciaForm"
        action="{{ route('admin.agencias.store') }}"
        method="POST"
        @submit="submitForm"
        class="grid grid-cols-1 lg:grid-cols-3 gap-8"
    >
        @csrf

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm space-y-6">

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-black rounded-lg text-white">
                        <x-heroicon-o-building-office-2 class="w-5 h-5" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-black">
                            Información de la Agencia
                        </h2>

                        <p class="text-xs text-gray-500 mt-0.5">
                            Datos principales del punto de despacho.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Nombre de la agencia
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="nombre_agencia"
                            value="{{ old('nombre_agencia') }}"
                            required
                            maxlength="100"
                            placeholder="Ej: Shalom - Lima"
                            class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all @error('nombre_agencia') border-rose-400 focus:border-rose-500 @else border-gray-200 focus:border-black @enderror"
                        >

                        @error('nombre_agencia')
                            <span class="text-[11px] font-bold text-rose-500 ml-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Dirección exacta
                            <span class="text-rose-500">*</span>
                        </label>

                        <textarea
                            name="direccion"
                            rows="4"
                            required
                            placeholder="Ingresa la dirección completa de la agencia"
                            class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-medium text-sm text-black outline-none transition-all resize-none @error('direccion') border-rose-400 focus:border-rose-500 @else border-gray-200 focus:border-black @enderror"
                        >{{ old('direccion') }}</textarea>

                        @error('direccion')
                            <span class="text-[11px] font-bold text-rose-500 ml-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm space-y-6">

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-black rounded-lg text-white">
                        <x-heroicon-o-map-pin class="w-5 h-5" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-black">
                            Ubicación
                        </h2>

                        <p class="text-xs text-gray-500 mt-0.5">
                            Selecciona la ubicación geográfica de la agencia.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Departamento
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            x-model="deptoId"
                            @change="fetchProvincias"
                            required
                            class="w-full px-5 py-4 bg-white border-2 border-gray-200 rounded-2xl font-bold text-sm text-black outline-none transition-all focus:border-black"
                        >
                            <option value="">Seleccionar</option>

                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">
                                    {{ $dep->nombre_departamento }}
                                </option>
                            @endforeach
                        </select>

                        <template x-if="loadingProvincias">
                            <span class="text-[10px] font-bold text-gray-400 ml-1">
                                Cargando provincias...
                            </span>
                        </template>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Provincia
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            x-model="provId"
                            @change="fetchDistritos"
                            :disabled="!deptoId || loadingProvincias"
                            required
                            class="w-full px-5 py-4 bg-white border-2 border-gray-200 rounded-2xl font-bold text-sm text-black outline-none transition-all focus:border-black disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            <option value="">Seleccionar</option>

                            <template x-for="p in provincias" :key="p.id_provincia">
                                <option
                                    :value="p.id_provincia"
                                    x-text="p.nombre_provincia"
                                ></option>
                            </template>
                        </select>

                        <template x-if="loadingDistritos">
                            <span class="text-[10px] font-bold text-gray-400 ml-1">
                                Cargando distritos...
                            </span>
                        </template>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Distrito
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            name="id_distrito"
                            x-model="distritoId"
                            :disabled="!provId || loadingDistritos"
                            required
                            class="w-full px-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed @error('id_distrito') border-rose-400 focus:border-rose-500 @else border-gray-200 focus:border-black @enderror"
                        >
                            <option value="">Seleccionar</option>

                            <template x-for="d in distritos" :key="d.id_distrito">
                                <option
                                    :value="d.id_distrito"
                                    x-text="d.nombre_distrito"
                                ></option>
                            </template>
                        </select>

                        @error('id_distrito')
                            <span class="text-[11px] font-bold text-rose-500 ml-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>

                <template x-if="ajaxError">
                    <div class="flex items-center gap-2 p-3 bg-rose-50 border-2 border-rose-200 rounded-xl text-rose-600">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 flex-shrink-0" />

                        <span
                            class="text-xs font-bold"
                            x-text="ajaxError"
                        ></span>
                    </div>
                </template>

            </div>

            <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm">

                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-black rounded-lg text-white">
                        <x-heroicon-o-truck class="w-5 h-5" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-black">
                            Configuración de Envío
                        </h2>

                        <p class="text-xs text-gray-500 mt-0.5">
                            Define el costo asociado a esta agencia.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-gray-500 ml-1">
                            Costo de envío (S/)
                            <span class="text-rose-500">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">
                                S/
                            </span>

                            <input
                                type="number"
                                name="costo_envio"
                                step="0.01"
                                min="0"
                                required
                                value="{{ old('costo_envio', '0.00') }}"
                                class="w-full pl-12 pr-5 py-4 bg-white border-2 rounded-2xl font-bold text-sm text-black outline-none transition-all @error('costo_envio') border-rose-400 focus:border-rose-500 @else border-gray-200 focus:border-black @enderror"
                            >
                        </div>

                        @error('costo_envio')
                            <span class="text-[11px] font-bold text-rose-500 ml-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <label class="w-full flex items-center justify-between gap-4 p-4 bg-gray-50 border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-black transition-all">

                            <div>
                                <p class="text-sm font-black text-black">
                                    Agencia activa
                                </p>

                                <p class="text-[10px] font-medium text-gray-500 mt-0.5">
                                    Disponible para realizar envíos
                                </p>
                            </div>

                            <div class="relative">
                                <input
                                    type="checkbox"
                                    name="estado"
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('estado', true) ? 'checked' : '' }}
                                >

                                <div class="w-12 h-7 bg-gray-300 rounded-full peer-checked:bg-black transition-all"></div>

                                <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full shadow-sm transition-all peer-checked:translate-x-5"></div>
                            </div>

                        </label>
                    </div>

                </div>
            </div>

        </div>

        <div class="space-y-8">

            <div class="bg-white p-8 rounded-[2rem] border-2 border-gray-200 shadow-sm">

                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-black rounded-lg text-white">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-black">
                            Resumen
                        </h2>

                        <p class="text-xs text-gray-500 mt-0.5">
                            Verifica los datos antes de guardar.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">

                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            Agencia
                        </p>

                        <p
                            class="text-sm font-bold text-black mt-1 break-words"
                            x-text="$refs.agenciaForm?.nombre_agencia?.value || 'Sin registrar'"
                        ></p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            Ubicación
                        </p>

                        <p class="text-sm font-bold text-black mt-1">
                            <span x-text="getSelectedText('depto') || 'Departamento'"></span>
                            <span class="text-gray-400">›</span>
                            <span x-text="getSelectedText('prov') || 'Provincia'"></span>
                            <span class="text-gray-400">›</span>
                            <span x-text="getSelectedText('distrito') || 'Distrito'"></span>
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            Costo de envío
                        </p>

                        <p class="text-lg font-black text-black mt-1">
                            S/ <span x-text="$refs.agenciaForm?.costo_envio?.value || '0.00'"></span>
                        </p>
                    </div>

                </div>
            </div>

            <div class="flex flex-col gap-4">

                <button
                    type="submit"
                    :disabled="submitting"
                    :class="submitting
                        ? 'bg-gray-300 cursor-not-allowed'
                        : 'bg-black hover:bg-gray-800'"
                    class="w-full py-5 text-white font-black rounded-2xl shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2"
                >
                    <template x-if="submitting">
                        <svg
                            class="animate-spin h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            ></path>
                        </svg>
                    </template>

                    <span x-text="submitting ? 'Guardando...' : 'Guardar Agencia'"></span>
                </button>

                <a
                    href="{{ route('admin.agencias.index') }}"
                    class="w-full py-5 bg-white text-gray-600 font-bold rounded-2xl text-center border-2 border-gray-200 hover:border-black hover:text-black transition-all"
                >
                    Cancelar
                </a>

            </div>

        </div>

    </form>
</div>

<script>
function agenciaForm() {
    return {
        deptoId: '',
        provId: '',
        distritoId: '{{ old('id_distrito') }}',

        provincias: [],
        distritos: [],

        loadingProvincias: false,
        loadingDistritos: false,
        submitting: false,

        ajaxError: '',

        toast: {
            show: false,
            type: 'error',
            message: ''
        },

        toastTimer: null,

        async fetchProvincias() {
            this.provId = '';
            this.distritoId = '';
            this.provincias = [];
            this.distritos = [];
            this.ajaxError = '';

            if (!this.deptoId) {
                return;
            }

            this.loadingProvincias = true;

            try {
                const url = "{{ route('admin.api.provincias', ':id') }}"
                    .replace(':id', this.deptoId);

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error();
                }

                this.provincias = await response.json();

            } catch (error) {
                this.ajaxError = 'No se pudieron cargar las provincias.';
                this.showToast('error', this.ajaxError);
            } finally {
                this.loadingProvincias = false;
            }
        },

        async fetchDistritos() {
            this.distritoId = '';
            this.distritos = [];
            this.ajaxError = '';

            if (!this.provId) {
                return;
            }

            this.loadingDistritos = true;

            try {
                const url = "{{ route('admin.api.distritos', ':id') }}"
                    .replace(':id', this.provId);

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error();
                }

                this.distritos = await response.json();

            } catch (error) {
                this.ajaxError = 'No se pudieron cargar los distritos.';
                this.showToast('error', this.ajaxError);
            } finally {
                this.loadingDistritos = false;
            }
        },

        getSelectedText(type) {
            if (type === 'depto') {
                const select = document.querySelector('[x-model="deptoId"]');
                return select?.options[select.selectedIndex]?.text || '';
            }

            if (type === 'prov') {
                const select = document.querySelector('[x-model="provId"]');
                return select?.options[select.selectedIndex]?.text || '';
            }

            if (type === 'distrito') {
                const select = document.querySelector('[x-model="distritoId"]');
                return select?.options[select.selectedIndex]?.text || '';
            }

            return '';
        },

        submitForm(event) {
            if (this.submitting) {
                event.preventDefault();
                return;
            }

            const form = this.$refs.agenciaForm;

            if (!form.checkValidity()) {
                event.preventDefault();

                form.reportValidity();

                this.showToast(
                    'error',
                    'Completa correctamente los campos obligatorios.'
                );

                return;
            }

            this.submitting = true;
        },

        showToast(type, message) {
            this.toast = {
                show: true,
                type: type,
                message: message
            };

            clearTimeout(this.toastTimer);

            this.toastTimer = setTimeout(() => {
                this.toast.show = false;
            }, 5000);
        }
    }
}
</script>
@endsection