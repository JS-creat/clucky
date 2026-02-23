<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'C\'Lucky - Tienda Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Tailwind ya está incluido en app.css, no necesitas el CDN --}}
</head>

<body class="bg-white text-gray-900 h-full">
    <div class="flex flex-col h-full">
        <!--Navbar-->
        <x-navbar />
        
        <!-- CONTENT - Aquí irá el contenido específico de cada página -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <x-footer />
    </div>
</body>

</html>
