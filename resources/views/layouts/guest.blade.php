<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'MoveLedger' }} — Staff Access</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <div class="mx-auto w-12 h-12 rounded-xl bg-orange-600 flex items-center justify-center text-white shadow-md">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">MoveLedger</h1>
        <p class="mt-1 text-sm text-slate-600">Mobile-First Relocation Inventory Field Platform</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
            {{ $slot }}
        </div>
        <p class="mt-6 text-center text-xs text-slate-500 font-mono">
            MoveLedger Relocation Suite &bull; Single-Tenant Ops
        </p>
    </div>
</body>
</html>
