<x-layouts.share :title="'Unlock Inventory Manifest — MoveLedger'">
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-900">Protected Manifest</h1>
                <p class="text-xs text-slate-500 font-mono mt-1">Move: {{ $move->reference_code }}</p>
                <p class="text-xs text-slate-600 mt-2">
                    Enter the access PIN provided by your relocation coordinator to view the inventory manifest and sign off.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('share.pin.verify', $share->token) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="pin" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 text-center mb-2">
                        Enter Security PIN
                    </label>
                    <input type="tel"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           name="pin"
                           id="pin"
                           maxlength="6"
                           required
                           autofocus
                           autocomplete="one-time-code"
                           placeholder="••••"
                           class="w-full h-14 text-center font-mono text-2xl tracking-[0.5em] rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-colors" />
                </div>

                <button type="submit"
                        class="w-full h-12 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-sm transition-colors flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <span>Unlock Manifest</span>
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    If you do not have a PIN code, please contact your Move Coordinator directly.
                </p>
            </div>
        </div>
    </div>
</x-layouts.share>
