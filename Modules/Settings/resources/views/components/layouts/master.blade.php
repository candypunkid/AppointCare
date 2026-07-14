<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Settings Module - {{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="{{ $description ?? '' }}">
        <meta name="keywords" content="{{ $keywords ?? '' }}">
        <meta name="author" content="{{ $author ?? '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        {{-- Vite CSS --}}
        {{-- {{ module_vite('build-settings', 'resources/assets/sass/app.scss') }} --}}
    </head>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <body>
        {{ $slot }}

        {{-- Vite JS --}}
        {{-- {{ module_vite('build-settings', 'resources/assets/js/app.js') }} --}}
    </body>
</html>
