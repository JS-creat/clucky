<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Control - ADMINISTRADOR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-red-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-red-900">
                    {{ __("Bienvenido, Jefe. Aquí puedes gestionar todo el sistema.") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
