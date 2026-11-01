<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'MoveLedger' }} — Relocation Inventory &amp; Bill of Lading Platform</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex flex-col min-h-screen selection:bg-orange-600 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- PUBLIC HEADER -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">

            <!-- Brand Logo -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-lg p-1">
                <div class="w-10 h-10 rounded-xl bg-orange-600 flex items-center justify-center text-white shrink-0 shadow-sm group-hover:bg-orange-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg sm:text-xl tracking-tight text-slate-900 block leading-tight">MoveLedger</span>
                    <span class="text-[10px] text-slate-500 font-mono tracking-wider uppercase block">Relocation Ops</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="{{ route('public.home') }}" class="hover:text-slate-900 transition-colors {{ request()->routeIs('public.home') ? 'text-orange-600 font-bold' : '' }}">
                    Overview
                </a>
                <a href="{{ route('public.docs') }}" class="hover:text-slate-900 transition-colors {{ request()->routeIs('public.docs') ? 'text-orange-600 font-bold' : '' }}">
                    Feature Guide
                </a>
                <a href="{{ route('demo.login') }}" class="hover:text-slate-900 transition-colors {{ request()->routeIs('demo.login') ? 'text-orange-600 font-bold' : '' }}">
                    Demo Personas
                </a>
            </nav>

            <!-- Desktop Action Buttons -->
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 hover:border-slate-400 text-slate-700 text-xs sm:text-sm font-semibold transition-colors">
                    Staff Login
                </a>
                <a href="{{ route('demo.login') }}" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs sm:text-sm font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                    <span>Explore Demo</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center gap-2 md:hidden">
                <a href="{{ route('demo.login') }}" class="px-3 py-1.5 rounded-lg bg-orange-600 text-white text-xs font-bold sm:hidden">
                    Demo
                </a>
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg min-h-11 min-w-11 flex items-center justify-center"
                        aria-label="Toggle Navigation Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen"
             x-cloak
             @click.away="mobileMenuOpen = false"
             class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2 shadow-lg">
            <a href="{{ route('public.home') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Overview
            </a>
            <a href="{{ route('public.docs') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Feature Guide
            </a>
            <a href="{{ route('demo.login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-orange-600 bg-orange-50">
                Demo Personas (1-Click Login)
            </a>
            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="text-center px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                    Staff Credential Login
                </a>
                <a href="{{ route('demo.login') }}" class="text-center px-4 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold">
                    Explore Demo Platform
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN PAGE CONTENT -->
    <main class="flex-1 w-full">
        {{ $slot }}
    </main>

    <!-- PUBLIC FOOTER -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                <!-- Col 1: Brand & Description -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-600 flex items-center justify-center text-white shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-white tracking-tight">MoveLedger</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-400 max-w-md leading-relaxed">
                        Mobile-first relocation inventory field platform. Eliminates clipboards, automates cube/weight benchmarks, enforces pre-existing damage photo documentation, and cryptographically locks Bills of Lading with touch signatures.
                    </p>
                    <div class="text-xs text-slate-400 pt-2">
                        <span>Direct Inquiry / Customization: </span>
                        <a href="mailto:nixresponding@gmail.com" class="text-orange-400 hover:text-orange-300 font-mono font-medium underline">
                            nixresponding@gmail.com
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Navigation</h3>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400">
                        <li>
                            <a href="{{ route('public.home') }}" class="hover:text-white transition-colors">Overview</a>
                        </li>
                        <li>
                            <a href="{{ route('public.docs') }}" class="hover:text-white transition-colors">Feature Documentation</a>
                        </li>
                        <li>
                            <a href="{{ route('demo.login') }}" class="hover:text-white transition-colors">Demo Personas</a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff Login</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Capabilities -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Capabilities</h3>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-400 font-mono">
                        <li>1-Tap Room Survey</li>
                        <li>Cube &amp; Weight Benchmarks</li>
                        <li>Damage Photo Capture</li>
                        <li>SHA-256 Manifest Lock</li>
                        <li>PIN Customer Portal</li>
                        <li>Destination Check-Off</li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Strip -->
            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-mono">
                <p>&copy; {{ date('Y') }} MoveLedger Relocation Suite. Single-Tenant Architecture.</p>
                <p>Public Contact: <a href="mailto:nixresponding@gmail.com" class="text-slate-400 hover:underline">nixresponding@gmail.com</a></p>
            </div>
        </div>
    </footer>

</body>
</html>
