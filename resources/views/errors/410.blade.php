<x-layouts.guest title="410 — Link Expired or Revoked">
    <div class="text-center space-y-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-amber-600 uppercase tracking-widest block">Access Expired</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">Manifest Link No Longer Active</h2>
            <p class="text-xs text-slate-600 mt-2">
                {{ $exception->getMessage() ?: 'This customer inventory manifest link has expired or has been revoked by the move coordinator for security.' }}
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
            <p class="text-[11px] text-slate-400">
                Please contact your Move Coordinator to request a fresh secure manifest link.
            </p>
            <a href="/" class="mt-2 text-xs font-semibold text-blue-600 hover:underline">
                Go to MoveLedger Portal
            </a>
        </div>
    </div>
</x-layouts.guest>
