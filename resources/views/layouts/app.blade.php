<!DOCTYPE html>
<html lang="es" class="min-h-screen">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'B-EDEN Premium Clothing')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
</head>

<body class="bg-white text-gray-900 min-h-screen">
    <div class="flex flex-col min-h-screen">

        {{-- Navbar sticky --}}
        <div class="sticky top-0 z-50">
            <x-navbar />
            @yield('categorias')
        </div>


        <main class="flex-1 flex flex-col">
            @yield('content')
        </main>

        <x-footer />

    </div>
</body>

</html>
