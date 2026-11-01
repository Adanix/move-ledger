<x-layouts.guest title="403 — Access Forbidden">
    <div class="text-center space-y-4">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-rose-600 uppercase tracking-widest block">Error 403</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">Access Restricted</h2>
            <p class="text-xs text-slate-600 mt-2">
                {{ $exception->getMessage() ?: 'You do not have administrative permission to access this resource.' }}
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-center">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                Return to Dashboard
            </a>
        </div>
    </div>
</x-layouts.guest>
