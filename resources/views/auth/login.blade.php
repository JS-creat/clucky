<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo -->
        <div>
            <x-input-label for="correo" value="Correo" />
            <x-text-input id="correo" class="block mt-1 w-full"
                type="email"
                name="correo"
                required autofocus />

            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="contrasena" value="Contraseña" />

            <x-text-input id="contrasena" class="block mt-1 w-full"
                type="password"
                name="contrasena"
                required />

            <x-input-error :messages="$errors->get('contrasena')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                Iniciar sesión
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
