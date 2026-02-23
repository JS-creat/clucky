{{-- resources/views/components/navbar.blade.php --}}
<nav class="border-b sticky top-0 bg-white z-50">
    <div class="max-w-full mx-auto px-4 sm:px-8">
        <div class="flex justify-between h-20 items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo C'Lucky"
                        class="h-14 w-auto transition-transform hover:scale-105">
                </a>
            </div>

            <div class="flex items-center space-x-5">
                <button class="hover:scale-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                
                @auth
                    <a href="{{ route('perfil.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="hover:scale-110 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                @endguest

                <a href="{{ route('carrito.index') }}" class="hover:scale-110 transition relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 11-8 0m-4 8h16l-1.244 6.323A2 2 0 0114.805 23H9.195a2 2 0 01-1.951-1.677L6 11z" />
                    </svg>
                    <span
                        class="absolute -top-1 -right-1 bg-[#f50057] text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                        {{ session('carrito') ? count(session('carrito')) : 0 }}
                    </span>
                </a>
            </div>

            @auth
                @if(auth()->user()->id_rol == 1)
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-sm font-bold text-gray-900 hover:underline">
                        Panel Admin
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>