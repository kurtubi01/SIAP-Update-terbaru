<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    margin: 0;
                    font-family: Figtree, Arial, sans-serif;
                    color: #111827;
                }
                .min-h-screen { min-height: 100vh; }
                .flex { display: flex; }
                .flex-col { flex-direction: column; }
                .items-center { align-items: center; }
                .pt-6 { padding-top: 1.5rem; }
                .mt-6 { margin-top: 1.5rem; }
                .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
                .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                .w-full { width: 100%; }
                .w-20 { width: 5rem; }
                .h-20 { height: 5rem; }
                .bg-gray-100 { background: #f3f4f6; }
                .bg-white { background: #ffffff; }
                .text-gray-500 { color: #6b7280; }
                .shadow-md { box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); }
                .overflow-hidden { overflow: hidden; }
                @media (min-width: 640px) {
                    .sm\:justify-center { justify-content: center; }
                    .sm\:pt-0 { padding-top: 0; }
                    .sm\:max-w-md { max-width: 28rem; }
                    .sm\:rounded-lg { border-radius: 0.5rem; }
                }
            </style>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
