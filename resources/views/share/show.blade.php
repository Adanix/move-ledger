<x-layouts.share :title="'Inventory Manifest: ' . $move->reference_code">
    <div x-data="{ lightboxUrl: null, lightboxTitle: '' }" class="space-y-6">

        <!-- Top Header & Status Banner -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Move Reference</span>
                    <h1 class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 tracking-tight">
                        {{ $move->reference_code }}
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <x-status-badge :status="$move->status" />
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">&bull; {{ $move->property_type->label() }}</span>
                </div>
            </div>

            <!-- Route & Customer Meta Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 text-xs sm:text-sm">
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-1 shrink-0"></span>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Origin Loading</span>
                            <p class="font-medium text-slate-800">{{ $move->origin_address }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mt-1 shrink-0"></span>
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Destination Delivery</span>
                            <p class="font-medium text-slate-800">{{ $move->destination_address }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 md:border-l md:border-slate-100 md:pl-4">
                    <p><span class="text-slate-400">Shipper Name:</span> <strong class="text-slate-800">{{ $move->customer->name }}</strong></p>
                    <p><span class="text-slate-400">Scheduled Date:</span> <strong class="text-slate-800">{{ $move->scheduled_move_date?->toFormattedDateString() ?? 'Pending' }}</strong></p>
                    @if($move->special_instructions)
                        <p class="text-slate-600 bg-amber-50 border border-amber-100 p-2 rounded-lg text-xs mt-2">
                            <span class="font-bold text-amber-900">Notes:</span> {{ $move->special_instructions }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric Summary Strip (5 metrics) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Items</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $move->total_items_count }}</p>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Boxes (Est.)</span>
                <p class="text-2xl font-bold font-mono text-blue-600 mt-1">{{ $move->total_boxes_count }}</p>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Volume</span>
                <p class="text-xl sm:text-2xl font-bold font-mono text-slate-900 mt-1">{{ number_format((float) $move->total_volume_cuft, 1) }} <span class="text-xs font-sans text-slate-500 font-normal">cu ft</span></p>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Est. Weight</span>
                <p class="text-xl sm:text-2xl font-bold font-mono text-slate-900 mt-1">{{ number_format((float) $move->total_weight_lbs, 0) }} <span class="text-xs font-sans text-slate-500 font-normal">lbs</span></p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Declared Value</span>
                <p class="text-xl sm:text-2xl font-bold font-mono text-emerald-600 mt-1">${{ number_format((float) $move->total_declared_value, 2) }}</p>
            </div>
        </div>

        <!-- Room-by-Room Inventory Section -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Room-by-Room Inventory Manifest</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Itemized listing of household goods with condition tags</p>
                </div>
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $move->rooms->count() }} {{ Str::plural('Room', $move->rooms->count()) }}
                </span>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($move->rooms as $room)
                    <div class="p-4 sm:p-5 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded bg-orange-500 shrink-0"></span>
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $room->name }}</h3>
                                <span class="text-xs text-slate-400">({{ $room->items_count }} items)</span>
                            </div>
                            <div class="text-xs font-mono text-slate-500 space-x-3">
                                <span>{{ number_format((float) $room->total_volume_cuft, 1) }} cu ft</span>
                                <span>&bull;</span>
                                <span>{{ $room->total_boxes_count }} boxes</span>
                            </div>
                        </div>

                        @if($room->items->count() === 0)
                            <p class="text-xs text-slate-400 italic py-2">No items inventoried in this room yet.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                            <th class="py-2 pr-3">Tag</th>
                                            <th class="py-2 pr-4">Item &amp; Notes</th>
                                            <th class="py-2 pr-3 text-center">Qty</th>
                                            <th class="py-2 pr-3">Packing</th>
                                            <th class="py-2 pr-3">Origin Condition</th>
                                            <th class="py-2 pr-3 text-right">Value</th>
                                            <th class="py-2 text-right">Photos</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($room->items as $item)
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="py-2.5 pr-3 font-mono font-bold text-slate-700 whitespace-nowrap">
                                                    #{{ str_pad((string) $item->tag_number, 3, '0', STR_PAD_LEFT) }}
                                                </td>
                                                <td class="py-2.5 pr-4">
                                                    <span class="font-semibold text-slate-900 block">{{ $item->name }}</span>
                                                    @if($item->condition_notes)
                                                        <span class="text-[11px] text-amber-700 font-medium block mt-0.5">
                                                            Condition: {{ $item->condition_notes }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 pr-3 text-center font-mono font-semibold text-slate-800">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="py-2.5 pr-3 text-slate-600 whitespace-nowrap">
                                                    {{ $item->packing_type->label() }}
                                                </td>
                                                <td class="py-2.5 pr-3 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->origin_condition->badgeClass() }}">
                                                        {{ $item->origin_condition->label() }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 pr-3 text-right font-mono text-slate-700 whitespace-nowrap">
                                                    @if((float)$item->declared_value > 0)
                                                        ${{ number_format((float) $item->declared_value, 2) }}
                                                    @else
                                                        <span class="text-slate-300">&mdash;</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 text-right whitespace-nowrap">
                                                    @if($item->photos->count() > 0)
                                                        <div class="flex items-center justify-end gap-1">
                                                            @foreach($item->photos as $photo)
                                                                <button type="button"
                                                                        @click="lightboxUrl = '/storage/{{ $photo->photo_path }}'; lightboxTitle = '#{{ str_pad((string) $item->tag_number, 3, '0', STR_PAD_LEFT) }} - {{ addslashes($item->name) }}'"
                                                                        class="w-8 h-8 rounded-md overflow-hidden border border-slate-200 hover:border-blue-500 transition-colors focus:ring-2 focus:ring-blue-500">
                                                                    <img src="/storage/{{ $photo->photo_path }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-slate-300 text-[11px]">&mdash;</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs">
                        No rooms configured for this move.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Lightbox Modal (Click to View Photo) -->
        <div x-show="lightboxUrl"
             x-cloak
             @click.self="lightboxUrl = null"
             class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl">
                <div class="p-3 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-800" x-text="lightboxTitle"></span>
                    <button @click="lightboxUrl = null" type="button" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 bg-slate-900 flex items-center justify-center max-h-[75vh]">
                    <img :src="lightboxUrl" alt="Item Photo" class="max-w-full max-h-[70vh] object-contain rounded-lg">
                </div>
            </div>
        </div>

        <!-- Cryptographic Sign-Off Section -->
        <div class="space-y-6">

            <!-- STAGE 1: Origin Loading Sign-Off -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Stage 1: Origin Loading Manifest Sign-Off</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Verification of pre-existing condition and load manifest</p>
                    </div>
                    @if($move->validOriginSignature)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Verified &amp; Locked
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                            Pending Origin Sign-Off
                        </span>
                    @endif
                </div>

                @if($move->validOriginSignature)
                    <!-- Signature Proof Card -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="space-y-2 text-xs">
                            <p><span class="text-slate-500">Signer Name:</span> <strong class="text-slate-800">{{ $move->validOriginSignature->signer_name }}</strong></p>
                            <p><span class="text-slate-500">Authority Role:</span> <span class="capitalize font-semibold text-slate-700">{{ str_replace('_', ' ', $move->validOriginSignature->signer_role) }}</span></p>
                            <p><span class="text-slate-500">Timestamp:</span> <span class="font-mono text-slate-700">{{ $move->validOriginSignature->signed_at->toDayDateTimeString() }}</span></p>
                            <div class="pt-2">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 block tracking-wider">Cryptographic Hash (SHA-256):</span>
                                <code class="text-[11px] font-mono text-slate-600 bg-white p-1.5 rounded border border-slate-200 block break-all mt-1 select-all">
                                    {{ $move->validOriginSignature->manifest_snapshot_hash }}
                                </code>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider mb-2">Captured Signature</span>
                            <img src="/storage/{{ $move->validOriginSignature->signature_image_path }}"
                                 alt="Origin Signature"
                                 class="max-h-24 max-w-full object-contain">
                        </div>
                    </div>
                @elseif($share->allow_customer_signoff && $move->status->canEditInventory())
                    <!-- Customer Signature Capture Form -->
                    <form action="{{ route('share.sign.store', ['token' => $share->token, 'stage' => 'origin']) }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 leading-relaxed">
                            <span class="font-bold">Important:</span> Please review the inventory items and conditions listed above. Once you draw your signature and submit, this manifest will be cryptographically locked for transit.
                        </div>

                        <x-signature-pad stage="origin" :customerName="$move->customer->name" />

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-colors flex items-center justify-center gap-2 cursor-pointer min-h-12">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Sign &amp; Lock Origin Manifest</span>
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-xs text-slate-500 italic">
                        Origin signature is managed directly by the Move Surveyor or will be enabled when the survey is complete.
                    </p>
                @endif
            </div>

            <!-- STAGE 2: Destination Delivery Sign-Off -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Stage 2: Destination Delivery Receipt Sign-Off</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Verification of shipment delivery and condition upon receipt</p>
                    </div>
                    @if($move->validDestinationSignature)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Delivery Verified
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            Pending Delivery
                        </span>
                    @endif
                </div>

                @if($move->validDestinationSignature)
                    <!-- Signature Proof Card -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="space-y-2 text-xs">
                            <p><span class="text-slate-500">Signer Name:</span> <strong class="text-slate-800">{{ $move->validDestinationSignature->signer_name }}</strong></p>
                            <p><span class="text-slate-500">Authority Role:</span> <span class="capitalize font-semibold text-slate-700">{{ str_replace('_', ' ', $move->validDestinationSignature->signer_role) }}</span></p>
                            <p><span class="text-slate-500">Timestamp:</span> <span class="font-mono text-slate-700">{{ $move->validDestinationSignature->signed_at->toDayDateTimeString() }}</span></p>
                            <div class="pt-2">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 block tracking-wider">Cryptographic Hash (SHA-256):</span>
                                <code class="text-[11px] font-mono text-slate-600 bg-white p-1.5 rounded border border-slate-200 block break-all mt-1 select-all">
                                    {{ $move->validDestinationSignature->manifest_snapshot_hash }}
                                </code>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl border border-slate-200">
                            <span class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider mb-2">Captured Signature</span>
                            <img src="/storage/{{ $move->validDestinationSignature->signature_image_path }}"
                                 alt="Destination Signature"
                                 class="max-h-24 max-w-full object-contain">
                        </div>
                    </div>
                @elseif($share->allow_customer_signoff && in_array($move->status->value, ['in_transit', 'origin_signed', 'delivery_completed'], true))
                    <!-- Customer Delivery Signature Capture Form -->
                    <form action="{{ route('share.sign.store', ['token' => $share->token, 'stage' => 'destination']) }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                            <span class="font-bold">Delivery Acknowledgment:</span> By signing below, you confirm receipt of your shipment at the destination address.
                        </div>

                        <x-signature-pad stage="destination" :customerName="$move->customer->name" />

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition-colors flex items-center justify-center gap-2 cursor-pointer min-h-12">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Sign Delivery Receipt</span>
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-xs text-slate-500 italic">
                        Delivery sign-off will become available when your shipment arrives at the destination.
                    </p>
                @endif
            </div>

        </div>

    </div>
</x-layouts.share>
