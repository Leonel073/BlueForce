<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SICOR-EPAB') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
    <!-- Fondo Azul Naval -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-blue-900">
        
<!-- Cabecera / Logo -->
        <div class="text-center">
            <!-- Logo Oficial de la EPAB con tamaño forzado -->
            <img src="https://epab.edu.bo/wp-content/uploads/2024/11/WhatsApp-Image-2024-10-23-at-4.08.41-PM.jpeg" 
                 alt="Logo Escuela de Posgrado de la Armada Boliviana" 
                 style="width: 130px; height: 130px; object-fit: contain;"
                 class="mx-auto mb-4 bg-white rounded-full p-1 shadow-lg">

            <h1 class="text-3xl font-bold text-white tracking-widest mt-2">SICOR-EPAB</h1>
            <p class="text-blue-200 mt-2 text-sm uppercase font-semibold">Escuela de Posgrado de la Armada Boliviana</p>
        </div>

        <!-- Tarjeta Blanca del Formulario -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg border-t-4 border-blue-500">
            {{ $slot }}
        </div>
        
        <div class="mt-8 text-blue-300 text-xs">
            &copy; {{ date('Y') }} Armada Boliviana - Todos los derechos reservados.
        </div>
    </div>
</body>
</html>