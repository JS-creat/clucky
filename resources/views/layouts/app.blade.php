<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'B-EDEN Premium Clothing')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
</head>

<body class="bg-white text-gray-900 h-full">
    <div class="flex flex-col h-full">
        <div class="sticky top-0 z-50">
            <!--Navbar-->
            <x-navbar />

            @yield('categorias')
        </main>

        <main class="flex-1">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <x-footer />
    </div>
</body>

</html>
