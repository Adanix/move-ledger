<x-layouts.guest title="500 — Server Error">
    <div class="text-center space-y-4">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-rose-600 uppercase tracking-widest block">Error 500</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">Internal Application Error</h2>
            <p class="text-xs text-slate-600 mt-2">
                An unexpected condition was encountered on the server. The operations team has been notified.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-center">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                Return to Dashboard
            </a>
        </div>
    </div>
</x-layouts.guest>
