@extends('admin.layout')

@section('content')

<div
    class="max-w-5xl mx-auto"
    x-data="agenciaEditor({
        departamento: @js(old('id_departamento', $departamentoActual?->id_departamento)),
        provincia: @js(old('id_provincia', $provinciaActual?->id_provincia)),
        distrito: @js(old('id_distrito', $agencia->id_distrito)),
        provinciasIniciales: @js($provincias),
        distritosIniciales: @js($distritos),
        provinciasUrl: @js(route('admin.api.provincias', ['id' => '__ID__'])),
        distritosUrl: @js(route('admin.api.distritos', ['id' => '__ID__']))
    })"
>

    <div class="mb-8">
        <a
            href="{{ route('admin.agencias.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600
                   hover:text-indigo-800 transition-colors"
        >
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Volver al listado
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
            Editar Agencia
        </h1>

        <p class="text-gray-500 mt-2 text-base sm:text-lg font-medium">
            Actualiza la información de la agencia y su ubicación.
        </p>
    </div>

    <form
        action="{{ route('admin.agencias.update', $agencia) }}"
        method="POST"
        class="bg-white p-6 sm:p-8 lg:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm"
    >

        @csrf
        @method('PUT')

        <div class="space-y-8">

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                    Nombre de la agencia
                </label>

                <input
                    type="text"
                    name="nombre_agencia"
                    value="{{ old('nombre_agencia', $agencia->nombre_agencia) }}"
                    maxlength="100"
                    class="w-full px-6 py-5 bg-gray-50 border-2
                           border-transparent rounded-2xl
                           focus:bg-white focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-100
                           outline-none text-base sm:text-lg font-bold
                           transition-all
                           @error('nombre_agencia') border-rose-400 @enderror"
                    placeholder="Ej: Shalom - Huancayo"
                >

                @error('nombre_agencia')
                    <p class="mt-2 ml-1 text-sm font-semibold text-rose-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                    Dirección exacta
                </label>

                <textarea
                    name="direccion"
                    rows="3"
                    class="w-full px-6 py-5 bg-gray-50 border-2
                           border-transparent rounded-2xl
                           focus:bg-white focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-100
                           outline-none text-base sm:text-lg font-bold
                           transition-all resize-none
                           @error('direccion') border-rose-400 @enderror"
                    placeholder="Ej: Av. Ferrocarril 123"
                >{{ old('direccion', $agencia->direccion) }}</textarea>

                @error('direccion')
                    <p class="mt-2 ml-1 text-sm font-semibold text-rose-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>

                <div class="mb-5">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                        Ubicación
                    </h2>

                    <p class="text-sm text-gray-400 mt-1">
                        Selecciona el departamento, provincia y distrito.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                            Departamento
                        </label>

                        <select
                            x-model="departamento"
                            @change="cargarProvincias()"
                            class="w-full px-5 py-4 bg-gray-50 border-2
                                   border-transparent rounded-2xl
                                   focus:bg-white focus:border-indigo-500
                                   focus:ring-4 focus:ring-indigo-100
                                   outline-none font-bold text-gray-700
                                   transition-all"
                        >

                            <option value="">
                                Seleccionar...
                            </option>

                            @foreach($departamentos as $departamento)

                                <option value="{{ $departamento->id_departamento }}">
                                    {{ $departamento->nombre_departamento }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                            Provincia
                        </label>

                        <select
                            x-model="provincia"
                            @change="cargarDistritos()"
                            :disabled="!departamento || cargandoProvincias"
                            class="w-full px-5 py-4 bg-gray-50 border-2
                                   border-transparent rounded-2xl
                                   focus:bg-white focus:border-indigo-500
                                   focus:ring-4 focus:ring-indigo-100
                                   outline-none font-bold text-gray-700
                                   transition-all
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                        >

                            <option value="">
                                Seleccionar...
                            </option>

                            <template x-for="item in provincias" :key="item.id_provincia">

                                <option
                                    :value="String(item.id_provincia)"
                                    x-text="item.nombre_provincia"
                                ></option>

                            </template>

                        </select>

                        <p
                            x-show="cargandoProvincias"
                            class="text-xs text-indigo-500 font-semibold mt-2 ml-1"
                        >
                            Cargando provincias...
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                            Distrito
                        </label>

                        <select
                            name="id_distrito"
                            x-model="distrito"
                            :disabled="!provincia || cargandoDistritos"
                            class="w-full px-5 py-4 bg-gray-50 border-2
                                   border-transparent rounded-2xl
                                   focus:bg-white focus:border-indigo-500
                                   focus:ring-4 focus:ring-indigo-100
                                   outline-none font-bold text-gray-700
                                   transition-all
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   @error('id_distrito') border-rose-400 @enderror"
                        >

                            <option value="">
                                Seleccionar...
                            </option>

                            <template x-for="item in distritos" :key="item.id_distrito">

                                <option
                                    :value="String(item.id_distrito)"
                                    x-text="item.nombre_distrito"
                                ></option>

                            </template>

                        </select>

                        <p
                            x-show="cargandoDistritos"
                            class="text-xs text-indigo-500 font-semibold mt-2 ml-1"
                        >
                            Cargando distritos...
                        </p>

                        @error('id_distrito')
                            <p class="mt-2 ml-1 text-sm font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                        Costo de envío (S/)
                    </label>

                    <input
                        type="number"
                        name="costo_envio"
                        step="0.01"
                        min="0"
                        value="{{ old('costo_envio', number_format($agencia->costo_envio, 2, '.', '')) }}"
                        class="w-full px-6 py-5 bg-gray-50 border-2
                               border-transparent rounded-2xl
                               focus:bg-white focus:border-indigo-500
                               focus:ring-4 focus:ring-indigo-100
                               outline-none text-base sm:text-lg font-bold
                               transition-all
                               @error('costo_envio') border-rose-400 @enderror"
                        placeholder="0.00"
                    >

                    @error('costo_envio')
                        <p class="mt-2 ml-1 text-sm font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="w-full px-6 py-5 bg-gray-50 border-2
                               border-transparent rounded-2xl
                               focus:bg-white focus:border-indigo-500
                               focus:ring-4 focus:ring-indigo-100
                               outline-none text-base sm:text-lg font-bold
                               transition-all"
                    >

                        <option
                            value="1"
                            {{ old('estado', $agencia->estado) == 1 ? 'selected' : '' }}
                        >
                            Activa
                        </option>

                        <option
                            value="0"
                            {{ old('estado', $agencia->estado) == 0 ? 'selected' : '' }}
                        >
                            Inactiva
                        </option>

                    </select>
                </div>

            </div>

            @if($errors->any())

                <div class="p-5 rounded-2xl bg-rose-50 border border-rose-100">

                    <div class="flex gap-3">

                        <x-heroicon-o-exclamation-circle
                            class="w-5 h-5 text-rose-500 flex-shrink-0"
                        />

                        <div>

                            <p class="text-sm font-black text-rose-700">
                                Revisa los datos ingresados.
                            </p>

                            <ul class="mt-2 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm text-rose-600 font-medium">
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>

                        </div>

                    </div>

                </div>

            @endif

        </div>

        <div class="mt-10 pt-8 border-t border-gray-100 flex flex-col sm:flex-row gap-4">

            <a
                href="{{ route('admin.agencias.index') }}"
                class="sm:w-40 inline-flex items-center justify-center
                       px-7 py-4 bg-gray-100 hover:bg-gray-200
                       text-gray-600 rounded-2xl font-bold
                       transition-all"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="flex-1 inline-flex items-center justify-center gap-3
                       bg-indigo-600 hover:bg-indigo-700
                       text-white px-7 py-4 rounded-2xl font-black
                       shadow-lg shadow-indigo-100
                       transition-all hover:-translate-y-0.5
                       active:scale-95"
            >
                <x-heroicon-o-check class="w-5 h-5" />
                Guardar cambios
            </button>

        </div>

    </form>

</div>

<script>
function agenciaEditor(config) {
    return {
        departamento: String(config.departamento || ''),
        provincia: String(config.provincia || ''),
        distrito: String(config.distrito || ''),

        provincias: config.provinciasIniciales || [],
        distritos: config.distritosIniciales || [],

        provinciasUrl: config.provinciasUrl,
        distritosUrl: config.distritosUrl,

        cargandoProvincias: false,
        cargandoDistritos: false,

        async cargarProvincias() {

            this.provincia = '';
            this.distrito = '';
            this.provincias = [];
            this.distritos = [];

            if (!this.departamento) {
                return;
            }

            this.cargandoProvincias = true;

            try {

                const url = this.provinciasUrl.replace(
                    '__ID__',
                    this.departamento
                );

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar provincias');
                }

                this.provincias = await response.json();

            } catch (error) {

                console.error(error);

            } finally {

                this.cargandoProvincias = false;

            }
        },

        async cargarDistritos() {

            this.distrito = '';
            this.distritos = [];

            if (!this.provincia) {
                return;
            }

            this.cargandoDistritos = true;

            try {

                const url = this.distritosUrl.replace(
                    '__ID__',
                    this.provincia
                );

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar distritos');
                }

                this.distritos = await response.json();

            } catch (error) {

                console.error(error);

            } finally {

                this.cargandoDistritos = false;

            }
        }
    }
}
</script>

@endsection