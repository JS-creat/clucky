<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Admin | Panel</title>

    {{-- Fonts & Styles --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
</head>

<body class="bg-gray-50 min-h-screen text-gray-900 overflow-x-hidden">

    <div x-data="{ open: localStorage.getItem('sidebarOpen') === null ? true : localStorage.getItem('sidebarOpen') === 'true' }"
        class="flex min-h-screen relative">

        {{-- SIDEBAR --}}
        <aside :class="open ? 'w-64' : 'w-24'"
            class="bg-black text-gray-400 transition-all duration-300 flex flex-col shadow-2xl z-40">

            {{-- Header Sidebar --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-800/50">
                <span x-show="open" x-transition.opacity class="text-xl font-black text-white tracking-tighter">
                    B-EDEN
                </span>
                <button @click="open = !open; localStorage.setItem('sidebarOpen', open);"
                    class="p-2 rounded-xl bg-gray-800 text-white hover:bg-black transition-colors">
                    <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" />
                </button>
            </div>

            {{-- Navegación --}}
            <nav class="flex-1 p-4 space-y-2 mt-4">
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'icon' => 'o-chart-bar', 'label' => 'Dashboard'],
                        ['route' => 'admin.productos.index', 'icon' => 'o-shopping-bag', 'label' => 'Productos'],
                        ['route' => 'admin.categorias.index', 'icon' => 'o-tag', 'label' => 'Categorías y Género'],
                        ['route' => 'admin.pedidos.index', 'icon' => 'o-clipboard-document-list', 'label' => 'Pedidos'],
                        ['route' => 'admin.agencias.index', 'icon' => 'o-building-office', 'label' => 'Agencias'],
                        ['route' => 'admin.reportes.index', 'icon' => 'o-document-chart-bar', 'label' => 'Reportes'],
                    ];

                    $otrosRoutes = ['admin.banners.index', 'admin.cupones.index', 'admin.movimientos.index'];
                    $otrosActive = request()->routeIs($otrosRoutes);
                @endphp

                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                        class="flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all group {{ request()->routeIs($link['route']) ? 'bg-white/10 text-white shadow-lg' : 'hover:bg-white/10 hover:text-white' }}">
                        <x-dynamic-component :component="'heroicon-' . $link['icon']"
                            class="w-6 h-6 transition-transform group-hover:scale-110 flex-shrink-0" />
                        <span x-show="open" x-transition.opacity>{{ $link['label'] }}</span>
                    </a>
                @endforeach

                {{-- Dropdown Otros --}}
                <div x-data="{ otrosOpen: {{ $otrosActive ? 'true' : 'false' }} }">
                    <button @click="otrosOpen = !otrosOpen" 
                        class="w-full flex items-center gap-4 p-4 rounded-2xl font-semibold transition-all group {{ $otrosActive ? 'bg-white/10 text-white' : 'hover:bg-white/10 hover:text-white' }}">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6 transition-transform group-hover:scale-110 flex-shrink-0" />
                        <span x-show="open" x-transition.opacity class="flex-1 text-left">Otros</span>
                        <x-heroicon-o-chevron-down x-show="open" :class="otrosOpen ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform duration-200" />
                    </button>

                    {{-- Enlaces desplegados (Sidebar Abierto) --}}
                    <div x-show="otrosOpen && open" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-1 ml-4 pl-4 border-l border-gray-700/60 space-y-1">

                        <a href="{{ route('admin.banners.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all group {{ request()->routeIs('admin.banners.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-photo class="w-5 h-5 transition-transform group-hover:scale-110 flex-shrink-0" />
                            <span>Banners</span>
                        </a>

                        <a href="{{ route('admin.cupones.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all group {{ request()->routeIs('admin.cupones.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-ticket class="w-5 h-5 transition-transform group-hover:scale-110 flex-shrink-0" />
                            <span>Cupones</span>
                        </a>

                        <a href="{{ route('admin.movimientos.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all group {{ request()->routeIs('admin.movimientos.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 transition-transform group-hover:scale-110 flex-shrink-0" />
                            <span>Movimientos</span>
                        </a>
                    </div>

                    {{-- Íconos desplegados (Sidebar Colapsado) --}}
                    <div x-show="otrosOpen && !open" class="mt-1 ml-1 space-y-1">
                        <a href="{{ route('admin.banners.index') }}"
                            class="flex items-center justify-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.banners.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-photo class="w-5 h-5" />
                        </a>
                        <a href="{{ route('admin.cupones.index') }}"
                            class="flex items-center justify-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.cupones.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-ticket class="w-5 h-5" />
                        </a>
                        <a href="{{ route('admin.movimientos.index') }}"
                            class="flex items-center justify-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.movimientos.index') ? 'bg-white/10 text-white' : 'text-gray-500 hover:bg-white/10 hover:text-white' }}">
                            <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Footer Sidebar (Cerrar Sesión) --}}
            <div class="p-4 border-t border-gray-800/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-4 p-4 rounded-2xl font-semibold text-rose-400 hover:bg-rose-500/10 transition-all group text-left">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 group-hover:-translate-x-1 transition-transform flex-shrink-0" />
                        <span x-show="open" x-transition.opacity>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- TOAST NOTIFICATIONS --}}
        <div class="fixed top-5 right-5 z-[100] w-[calc(100%-2.5rem)] sm:w-auto pointer-events-none">
            
            {{-- Toast Éxito --}}
            @if(session('success'))
                <div x-data="{ show: true }" 
                    x-show="show" 
                    x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-5" 
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0" 
                    x-transition:leave-end="opacity-0 translate-x-5"
                    class="pointer-events-auto w-full sm:w-[380px] bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-gray-200/60 overflow-hidden">

                    <div class="flex items-start gap-4 p-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <x-heroicon-s-check class="w-5 h-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-gray-900">Operación completada</p>
                            <p class="text-xs font-medium text-gray-500 mt-1 leading-relaxed">
                                {{ session('success') }}
                            </p>
                        </div>

                        <button @click="show = false" class="text-gray-300 hover:text-gray-600 transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="h-1 bg-emerald-500/10">
                        <div class="h-full bg-emerald-500" 
                            x-init="$el.style.width = '100%'; setTimeout(() => $el.style.width = '0%', 5000)"
                            style="transition: width 5s linear"></div>
                    </div>
                </div>
            @endif

            {{-- Toast Error --}}
            @if(session('error'))
                <div x-data="{ show: true }" 
                    x-show="show" 
                    x-init="setTimeout(() => show = false, 6000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-5" 
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0" 
                    x-transition:leave-end="opacity-0 translate-x-5"
                    class="pointer-events-auto w-full sm:w-[380px] bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-gray-200/60 overflow-hidden">

                    <div class="flex items-start gap-4 p-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <x-heroicon-s-exclamation-triangle class="w-5 h-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-gray-900">No se pudo completar</p>
                            <p class="text-xs font-medium text-gray-500 mt-1 leading-relaxed">
                                {{ session('error') }}
                            </p>
                        </div>

                        <button @click="show = false" class="text-gray-300 hover:text-gray-600 transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="h-1 bg-rose-500/10">
                        <div class="h-full bg-rose-500" 
                            x-init="$el.style.width = '100%'; setTimeout(() => $el.style.width = '0%', 6000)"
                            style="transition: width 6s linear"></div>
                    </div>
                </div>
            @endif

        </div>

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="flex-1 p-8 lg:p-12 overflow-y-auto max-h-screen">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

    </div>

</body>

</html>