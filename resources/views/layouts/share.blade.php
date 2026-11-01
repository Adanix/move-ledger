<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Move Manifest' }} — MoveLedger Customer Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex flex-col overflow-x-hidden">

    <!-- CUSTOMER HEADER (no-print on paper) -->
    <header class="no-print bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m0 0l3 3v6m-3-9V4a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1h2m10-7h3m-3 0v7m-7 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-white block leading-tight">MoveLedger</span>
                    <span class="text-[10px] text-slate-400 font-mono tracking-wider uppercase">Customer Portal</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span class="hidden sm:inline">Print / Save PDF</span>
                    <span class="sm:hidden">Print</span>
                </button>
            </div>
        </div>
    </header>

    <!-- PRINT HEADER (Only visible when printing / saving PDF) -->
    <div class="print-only hidden p-4 border-b-2 border-slate-900 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">MoveLedger &mdash; Bill of Lading &amp; Inventory Manifest</h1>
                <p class="text-xs text-slate-600 mt-1">Official Relocation Inventory Documentation &bull; Timestamp: {{ now()->toFormattedDateString() }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono font-bold text-sm text-slate-900">VERIFIED MANIFEST</span>
            </div>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if(session('success'))
    <div class="no-print bg-emerald-600 text-white px-4 py-3 text-sm flex items-center justify-between shrink-0">
        <div class="max-w-5xl mx-auto w-full flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="no-print bg-rose-600 text-white px-4 py-3 text-sm flex items-center justify-between shrink-0">
        <div class="max-w-5xl mx-auto w-full flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ $errors->first() }}</span>
        </div>
    </div>
    @endif

    <!-- MAIN CUSTOMER SLOT -->
    <main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 lg:p-8">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="no-print py-8 border-t border-slate-200 text-center text-xs text-slate-500 font-mono">
        &copy; {{ date('Y') }} MoveLedger Inc. All rights reserved. Secure cryptographic token access.
    </footer>

</body>
</html>
