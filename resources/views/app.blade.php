<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beyours') }}</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if(app()->environment('local'))
        <!-- Desarrollo: Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Producción: Assets compilados -->
        <link rel="stylesheet" href="{{ asset('build/assets/app-BgAgxFXk.css') }}">
        <script src="{{ asset('build/assets/app-DRPAYixA.js') }}" defer></script>
    @endif
</head>
<body>
    <div id="app"></div>
</body>
</html>