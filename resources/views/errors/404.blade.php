<x-layouts.guest title="404 — Page Not Found">
    <div class="text-center space-y-4">
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-slate-500 uppercase tracking-widest block">Error 404</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">Record or Page Not Found</h2>
            <p class="text-xs text-slate-600 mt-2">
                {{ $exception->getMessage() ?: 'The move job, customer, or page you requested could not be located.' }}
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-center">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                Return to Dashboard
            </a>
        </div>
    </div>
</x-layouts.guest>
