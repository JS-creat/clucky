@extends('admin.layout')

@section('content')
<div class="max-w-3xl mx-auto py-10" x-data="agenciaForm()">

    {{-- HEADER --}}
    <div class="mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Nueva Agencia</h1>
        <p class="text-gray-500 mt-2 text-lg font-medium">Completa los detalles para registrar un nuevo punto de despacho.</p>
    </div>

    <form action="{{ route('admin.agencias.store') }}" method="POST" class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
        @csrf

        <div class="space-y-8">
            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nombre de la agencia</label>
                <input type="text" name="nombre_agencia" value="{{ old('nombre_agencia') }}"
                    class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-lg font-bold transition-all @error('nombre_agencia') border-rose-500 @enderror"
                    placeholder="Ej: Shalom - Lima">
            </div>

            {{-- Dirección --}}
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Dirección exacta</label>
                <textarea name="direccion" rows="2"
                    class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-lg font-bold transition-all">{{ old('direccion') }}</textarea>
            </div>

            {{-- SELECTS ENCADENADOS CON ALPINE --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Departamento</label>
                    <select x-model="deptoId" @change="fetchProvincias()" class="w-full px-5 py-4 bg-gray-50 rounded-2xl font-bold text-gray-700 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Seleccionar...</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}">{{ $dep->nombre_departamento }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Provincia</label>
                    <select x-model="provId" @change="fetchDistritos()" :disabled="!deptoId" class="w-full px-5 py-4 bg-gray-50 rounded-2xl font-bold text-gray-700 outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50">
                        <option value="">--</option>
                        <template x-for="p in provincias" :key="p.id_provincia">
                            <option :value="p.id_provincia" x-text="p.nombre_provincia"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Distrito</label>
                    <select name="id_distrito" :disabled="!provId" class="w-full px-5 py-4 bg-gray-50 rounded-2xl font-bold text-gray-700 outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50">
                        <option value="">--</option>
                        <template x-for="d in distritos" :key="d.id_distrito">
                            <option :value="d.id_distrito" x-text="d.nombre_distrito"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Costo y Estado --}}
            <div class="flex items-center gap-8">
                <div class="flex-1">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Costo de envío (S/)</label>
                    <input type="number" name="costo_envio" step="0.01" value="{{ old('costo_envio', '0.00') }}"
                        class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-indigo-500 outline-none text-lg font-bold">
                </div>

                <div class="pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="estado" value="1" class="w-6 h-6 rounded-lg accent-indigo-600" checked>
                        <span class="font-black text-gray-700">Agencia Activa</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-10 flex gap-4">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-2xl font-black text-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">Guardar Agencia</button>
            <a href="{{ route('admin.agencias.index') }}" class="px-8 py-5 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</a>
        </div>
    </form>
</div>

<script>
    function agenciaForm() {
        return {
            deptoId: '',
            provId: '',
            provincias: [],
            distritos: [],
            async fetchProvincias() {
                this.provId = ''; this.distritos = [];
                if(!this.deptoId) return;
                const res = await fetch("{{ route('admin.api.provincias', ':id') }}".replace(':id', this.deptoId));
                this.provincias = await res.json();
            },
            async fetchDistritos() {
                if(!this.provId) return;
                const res = await fetch("{{ route('admin.api.distritos', ':id') }}".replace(':id', this.provId));
                this.distritos = await res.json();
            }
        }
    }
</script>
@endsection
