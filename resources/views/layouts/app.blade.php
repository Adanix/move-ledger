<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'MoveLedger' }} — Relocation Inventory</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex flex-col md:flex-row overflow-x-hidden" x-data="{ mobileMenuOpen: false }">

    <!-- DESKTOP SIDEBAR (Visible lg:block >= 1024px) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 lg:border-r lg:border-slate-200 bg-slate-900 text-white z-30">
        <!-- Brand Header -->
        <div class="h-16 flex items-center px-6 gap-3 border-b border-slate-800">
            <div class="w-9 h-9 rounded-lg bg-orange-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                <!-- Box Truck Icon SVG -->
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <span class="font-bold text-lg tracking-tight text-white block leading-none">MoveLedger</span>
                <span class="text-[11px] text-slate-400 font-mono tracking-wide">FIELD OPS v1.0</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('moves.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('moves.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Move Jobs</span>
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Customers</span>
            </a>
            @endif

            <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('catalog.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Standard Catalog</span>
            </a>

            @if(auth()->user()->isAdmin())
            <div class="pt-4 mt-4 border-t border-slate-800">
                <span class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin</span>
            </div>

            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Staff Accounts</span>
            </a>
            @endif

            <div class="pt-4 mt-4 border-t border-slate-800">
                <span class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Reference &amp; Demo</span>
            </div>

            <a href="{{ route('public.docs') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Feature Guide</span>
            </a>

            @if(config('app.demo_mode', true))
            <a href="{{ route('demo.login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium text-orange-400 hover:bg-slate-800 hover:text-orange-300 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span>Switch Persona</span>
            </a>
            @endif
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/60">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1 pr-3">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block mt-0.5 text-[10px] font-medium px-2 py-0.5 rounded-full {{ auth()->user()->role->badgeClass() }}">
                        {{ auth()->user()->role->label() }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MOBILE TOP BAR (< 1024px) -->
    <header class="lg:hidden h-14 bg-slate-900 text-white flex items-center justify-between px-4 sticky top-0 z-40 border-b border-slate-800 shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-md bg-orange-600 flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="font-bold text-base tracking-tight text-white">MoveLedger</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-300 font-medium px-2 py-1 rounded bg-slate-800">
                {{ auth()->user()->role->label() }}
            </span>
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="min-h-10 min-w-10 flex items-center justify-center text-slate-300 hover:text-white rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <!-- MOBILE SLIDE-OVER DRAWER -->
    <div x-show="mobileMenuOpen" x-cloak class="relative z-50 lg:hidden" aria-modal="true">
        <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" @click="mobileMenuOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-slate-900 p-6 flex flex-col justify-between text-white shadow-2xl">
            <div>
                <div class="flex items-center justify-between pb-6 border-b border-slate-800">
                    <span class="font-bold text-lg text-white">Menu Navigation</span>
                    <button @click="mobileMenuOpen = false" class="p-2 text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="mt-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('moves.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium {{ request()->routeIs('moves.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Move Jobs
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Customers
                    </a>
                    @endif
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium {{ request()->routeIs('catalog.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Standard Catalog
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        Staff Accounts
                    </a>
                    @endif
                </nav>
            </div>

            <div class="pt-6 border-t border-slate-800">
                <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 font-mono mb-4">{{ auth()->user()->email }}</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white transition-colors text-sm font-semibold">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto pb-20 lg:pb-0">

        <!-- FLASH NOTIFICATIONS -->
        @if(session('success'))
        <div class="bg-emerald-600 text-white px-4 py-3 text-sm flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="p-1 hover:bg-emerald-700 rounded">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-rose-600 text-white px-4 py-3 text-sm flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-medium">{{ $errors->first() }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="p-1 hover:bg-rose-700 rounded">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        <!-- PAGE CONTENT SLOT -->
        <main class="flex-1 w-full min-w-0">
            {{ $slot }}
        </main>
    </div>

    <!-- MOBILE PERSISTENT BOTTOM NAVIGATION BAR (< 1024px) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 h-16 bg-white border-t border-slate-200 z-40 flex items-center justify-around px-2 shadow-lg">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[11px] font-medium mt-1">Home</span>
        </a>

        <a href="{{ route('moves.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 {{ request()->routeIs('moves.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[11px] font-medium mt-1">Moves</span>
        </a>

        <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 {{ request()->routeIs('catalog.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span class="text-[11px] font-medium mt-1">Catalog</span>
        </a>

        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
        <a href="{{ route('customers.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 {{ request()->routeIs('customers.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[11px] font-medium mt-1">Clients</span>
        </a>
        @endif
    </nav>

</body>
</html>
