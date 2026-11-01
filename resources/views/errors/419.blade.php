<x-layouts.guest title="419 — Page Expired">
    <div class="text-center space-y-4">
        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 mx-auto flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-orange-600 uppercase tracking-widest block">Error 419</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">Session Expired</h2>
            <p class="text-xs text-slate-600 mt-2">
                Your security token has expired due to inactivity. Please refresh the page and try submitting again.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-center">
            <button onclick="window.location.reload()" type="button" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors cursor-pointer">
                Refresh Page
            </button>
        </div>
    </div>
</x-layouts.guest>
