<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin | Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white p-4 space-y-4">
        <h2 class="text-xl font-bold">ADMIN</h2>

        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block hover:text-yellow-400">Dashboard</a>
            <a href="{{ route('admin.productos.index') }}" class="block hover:text-yellow-400">Productos</a>
        </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-6 max-w-7xl mx-auto w-full">
        @yield('content')
    </main>

</body>
</html>
