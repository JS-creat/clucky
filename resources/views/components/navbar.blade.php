{{-- resources/views/components/navbar.blade.php --}}
<nav class="border-b sticky top-0 bg-white z-50"
    x-data="{
        searchOpen: false,
        query: '{{ addslashes(request('buscar', '')) }}',
        submit() {
            const q = this.query.trim();
            if (q.length > 0) {
                window.location.href = '{{ route('home') }}?buscar=' + encodeURIComponent(q);
            }
        },
        clear() {
            this.query = '';
            window.location.href = '{{ route('home') }}';
        }
    }"
>
    <div class="max-w-full mx-auto px-4 sm:px-8">
        <div class="flex justify-between h-16 items-center gap-4">

            {{-- LOGO --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky" class="h-14 w-auto">
                </a>
            </div>

            {{-- BUSCADOR DESKTOP --}}
            <div class="hidden sm:flex flex-1 max-w-lg items-center">
                <div class="relative w-full">
                    <input
                        x-ref="searchInput"
                        x-model="query"
                        @keydown.enter="submit()"
                        type="text"
                        placeholder="Buscar productos, marcas..."
                        class="w-full pl-5 pr-20 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:border-gray-800 focus:bg-white transition-all duration-200"
                    >
                    {{-- Botón limpiar --}}
                    <button
                        x-show="query.length > 0"
                        x-cloak
                        @click="clear()"
                        type="button"
                        class="absolute right-11 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition p-1"
                        title="Limpiar"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    {{-- Botón buscar --}}
                    <button
                        @click="submit()"
                        type="button"
                        class="absolute right-1 top-1/2 -translate-y-1/2 bg-gray-900 hover:bg-black text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors duration-200"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ICONOS DERECHA --}}
            <div class="flex items-center space-x-4">

                {{-- LUPA MÓVIL --}}
                <button @click="searchOpen = !searchOpen" class="sm:hidden hover:scale-110 transition" aria-label="Buscar">
                    <svg x-show="!searchOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <svg x-show="searchOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- USUARIO --}}
                @auth
                    <a href="{{ route('perfil.index') }}" class="hover:scale-110 transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="hover:scale-110 transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @endguest

                {{-- CARRITO --}}
                <a href="{{ route('carrito.index') }}" class="hover:scale-110 transition relative">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 bg-[#f50057] text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                        {{ session('carrito') ? count(session('carrito')) : 0 }}
                    </span>
                </a>

                {{-- PANEL ADMIN --}}
                @auth
                    @if(auth()->user()->id_rol == 1)
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:block text-xs font-bold text-gray-900 border border-gray-900 px-3 py-1.5 rounded-full hover:bg-gray-900 hover:text-white transition-all duration-200">
                            Admin
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- BUSCADOR MÓVIL --}}
    <div
        x-show="searchOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="sm:hidden border-t border-gray-100 px-4 py-3 bg-white shadow-md"
    >
        <div class="relative">
            <input
                x-model="query"
                @keydown.enter="submit()"
                type="text"
                placeholder="Buscar productos, marcas..."
                class="w-full pl-4 pr-20 py-2.5 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:border-gray-800 focus:bg-white transition-all"
            >
            <button
                x-show="query.length > 0"
                x-cloak
                @click="clear()"
                type="button"
                class="absolute right-11 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition p-1"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <button
                @click="submit()"
                type="button"
                class="absolute right-1 top-1/2 -translate-y-1/2 bg-gray-900 hover:bg-black text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>

</nav>