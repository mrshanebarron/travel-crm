<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Safari CRM - Tapestry of Africa</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100">
            <div class="mb-6">
                <a href="/">
                    <img src="{{ asset('images/tapestry_logo.jpg') }}" alt="Tapestry of Africa" class="h-24 w-auto rounded-lg shadow-md">
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl overflow-hidden rounded-xl border border-slate-200">
                {{ $slot }}
            </div>

            <p class="mt-6 text-sm text-slate-500">Safari CRM by Tapestry of Africa Tours & Safaris</p>
        </div>
    </body>
</html>
