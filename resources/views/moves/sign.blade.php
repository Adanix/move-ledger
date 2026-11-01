<x-layouts.app title="Sign Manifest: {{ $move->reference_code }}">
    <div class="p-4 sm:p-6 lg:p-8 max-w-2xl mx-auto w-full space-y-6">

        <div>
            <a href="{{ route('moves.show', $move) }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Move Details
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                @if($stage === 'origin')
                    Origin Loading Sign-Off &amp; Acknowledgment
                @else
                    Destination Delivery Receipt &amp; Acknowledgment
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Move Reference: <span class="font-mono font-bold text-slate-800">{{ $move->reference_code }}</span> &bull;
                Customer: <span class="font-semibold text-slate-800">{{ $move->customer->name }}</span>
            </p>
        </div>

        <!-- Manifest Recap Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Inventory Summary Recap</h2>
            <div class="grid grid-cols-3 gap-2 text-center text-xs font-mono">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase block">Items</span>
                    <span class="font-bold text-slate-900 text-lg">{{ $move->total_items_count }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase block">Volume</span>
                    <span class="font-bold text-slate-900 text-lg">{{ number_format((float) $move->total_volume_cuft, 1) }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase block">Weight</span>
                    <span class="font-bold text-slate-900 text-lg">{{ number_format((float) $move->total_weight_lbs, 0) }}</span>
                </div>
            </div>

            @if($stage === 'origin')
                <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 leading-relaxed">
                    <span class="font-bold">Notice:</span> By signing below, the customer or authorized representative verifies that all items listed in this manifest have been inventoried and all pre-existing condition notes are agreed upon before vehicle transport.
                </div>
            @else
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                    <span class="font-bold">Notice:</span> By signing below, the recipient acknowledges receipt of the shipment at the destination address.
                </div>
            @endif
        </div>

        <!-- Signature Form -->
        <form action="{{ route('moves.sign.store', ['move' => $move, 'stage' => $stage]) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-6">
            @csrf

            <x-signature-pad :stage="$stage" :customerName="$move->customer->name" />

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('moves.show', $move) }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-xs sm:text-sm min-h-12 flex items-center">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md active:scale-[0.99] transition-all min-h-12 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Submit &amp; Lock Manifest</span>
                </button>
            </div>
        </form>

    </div>
</x-layouts.app>
