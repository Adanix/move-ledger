<x-layouts.app title="Delivery Check-Off: {{ $move->reference_code }}">
    <div x-data="{
        showExceptionModal: false,
        activeItem: {},
        exceptionStatus: 'damaged_in_transit',
        openException(item, status) {
            this.activeItem = item;
            this.exceptionStatus = status;
            this.showExceptionModal = true;
        }
    }" class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('moves.show', $move) }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Move Details
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold font-mono tracking-tight text-slate-900">
                        {{ $move->reference_code }}
                    </h1>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Destination Unloading
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $move->destination_address }} &bull; Customer: <span class="font-semibold text-slate-700">{{ $move->customer->name }}</span>
                </p>
            </div>

            <!-- Destination Signature CTA -->
            <div>
                @if($stats['is_all_accounted_for'])
                    <a href="{{ route('moves.sign', ['move' => $move, 'stage' => 'destination']) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition-colors min-h-12 animate-pulse">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Capture Destination Signature</span>
                    </a>
                @else
                    <button disabled type="button"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-200 text-slate-400 font-bold text-sm cursor-not-allowed min-h-12">
                        <span>Check Off All Items to Sign</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Progress Counter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between text-xs sm:text-sm font-semibold">
                <span class="text-slate-700">Unloading &amp; Verification Progress</span>
                <span class="font-mono text-slate-900">{{ $stats['delivered'] + $stats['exceptions'] }} / {{ $stats['total'] }} items accounted for</span>
            </div>

            @php
                $pct = $stats['total'] > 0 ? round((($stats['delivered'] + $stats['exceptions']) / $stats['total']) * 100) : 0;
            @endphp
            <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-emerald-600 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
            </div>

            <div class="grid grid-cols-4 gap-2 pt-2 text-center text-xs font-mono">
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase block">Total</span>
                    <span class="font-bold text-slate-900 text-base">{{ $stats['total'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="text-[10px] text-emerald-700 uppercase block">Delivered</span>
                    <span class="font-bold text-emerald-800 text-base">{{ $stats['delivered'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-amber-50 border border-amber-100">
                    <span class="text-[10px] text-amber-700 uppercase block">Exceptions</span>
                    <span class="font-bold text-amber-800 text-base">{{ $stats['exceptions'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-slate-100 border border-slate-200">
                    <span class="text-[10px] text-slate-600 uppercase block">Pending</span>
                    <span class="font-bold text-slate-800 text-base">{{ $stats['pending'] }}</span>
                </div>
            </div>
        </div>

        <!-- Room-by-Room Delivery List -->
        <div class="space-y-6">
            @foreach($move->rooms as $room)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <!-- Room Header with Batch Button -->
                    <div class="p-4 sm:p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">{{ $room->name }}</h3>
                            <span class="text-xs text-slate-500 font-mono">{{ $room->items->count() }} items in room</span>
                        </div>

                        <!-- Batch Mark Room Delivered -->
                        @if($room->items->where('delivery_status.value', 'pending')->count() > 0)
                        <form action="{{ route('moves.delivery.mark-room', $move) }}" method="POST"
                              onsubmit="return confirm('Mark all pending items in {{ $room->name }} as Delivered?');">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold shadow-2xs transition-colors">
                                Mark All Delivered
                            </button>
                        </form>
                        @endif
                    </div>

                    <!-- Items in Room -->
                    <div class="divide-y divide-slate-100">
                        @foreach($room->items as $item)
                            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-slate-800 px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                            {{ $item->tag_number }}
                                        </span>
                                        <span class="font-semibold text-sm text-slate-900">{{ $item->name }}</span>
                                        <span class="text-xs font-mono text-slate-500">(&times;{{ $item->quantity }})</span>
                                        <x-status-badge :status="$item->delivery_status" />
                                    </div>

                                    @if($item->delivery_exception_notes)
                                        <div class="mt-2 p-2 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-800">
                                            <span class="font-bold">Exception:</span> {{ $item->delivery_exception_notes }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($item->delivery_status->value === 'pending')
                                        <form action="{{ route('moves.items.delivery', ['move' => $move, 'item' => $item]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delivery_status" value="delivered">
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors min-h-10">
                                                Mark Delivered
                                            </button>
                                        </form>

                                        <button @click="openException({{ json_encode($item) }}, 'damaged_in_transit')"
                                                type="button" class="px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold min-h-10">
                                            Damage
                                        </button>

                                        <button @click="openException({{ json_encode($item) }}, 'missing')"
                                                type="button" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold min-h-10">
                                            Missing
                                        </button>
                                    @else
                                        <!-- Revert to pending button if needed -->
                                        <form action="{{ route('moves.items.delivery', ['move' => $move, 'item' => $item]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delivery_status" value="pending">
                                            <button type="submit" class="p-2 text-slate-400 hover:text-slate-700 text-xs font-semibold" title="Reset to pending">
                                                Reset
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Exception Modal -->
        <div x-show="showExceptionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.outside="showExceptionModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Log Delivery Exception</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="activeItem.name"></p>
                    </div>
                    <button @click="showExceptionModal = false" class="p-1 text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/moves/' . $move->id . '/items') }}/' + activeItem.id + '/delivery'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Exception Type</label>
                        <select name="delivery_status" x-model="exceptionStatus" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                            <option value="damaged_in_transit">Damaged in Transit</option>
                            <option value="missing">Missing from Truck</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                            Exception Notes <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="delivery_exception_notes" rows="3" required
                                  placeholder="Describe exact damage, location, or missing item status..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showExceptionModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                            Confirm Exception
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
