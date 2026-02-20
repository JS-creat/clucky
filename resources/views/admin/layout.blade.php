<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin | Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

<div
    x-data="{ open: true }"
    class="flex min-h-screen"
>
    <!-- SIDEBAR -->
    <aside
        :class="open ? 'w-64' : 'w-20'"
        class="bg-gray-900 text-gray-300 transition-all duration-300 flex flex-col shadow-lg"
    >
        <!-- HEADER -->
        <div class="flex items-center justify-between p-4 border-b border-gray-800">
            <span
                x-show="open"
                class="text-lg font-bold text-white"
            >
                C´LUCKY
            </span>

            <button
                @click="open = !open"
                class="text-gray-400 hover:text-white"
            >
                <!-- menu icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- NAV -->
        <nav class="flex-1 p-3 space-y-1">

            <!-- Dashboard -->
            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 p-3 rounded transition
                {{ request()->routeIs('admin.dashboard')
                    ? 'bg-black text-white'
                    : 'hover:bg-gray-800' }}"
            >
                <!-- icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2 4 4 8-8 4 4" />
                </svg>

                <span x-show="open">Dashboard</span>
            </a>

            <!-- Productos -->
            <a
                href="{{ route('admin.productos.index') }}"
                class="flex items-center gap-3 p-3 rounded transition
                {{ request()->routeIs('admin.productos.*')
                    ? 'bg-black text-white'
                    : 'hover:bg-gray-800' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M20 13V7a2 2 0 00-2-2h-4M4 7h16M4 7v6a2 2 0 002 2h12" />
                </svg>

                <span x-show="open">Productos</span>
            </a>

        </nav>

        <!-- LOGOUT -->
        <div class="p-3 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-3 p-3 rounded text-red-400 hover:bg-gray-800 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>

                    <span x-show="open">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-6 max-w-7xl mx-auto w-full">

        <!-- ALERTAS -->
        @if (session('error'))
            <div class="mb-6 p-4 rounded bg-red-100 border border-red-300 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 p-4 rounded bg-green-100 border border-green-300 text-green-800"
            >
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
