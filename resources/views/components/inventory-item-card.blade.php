@props(['item', 'move', 'canEdit' => true])

<div x-data="{
    qty: {{ $item->quantity }},
    submitting: false,
    updateQuantity(action) {
        if (this.submitting) return;
        this.submitting = true;

        fetch('{{ route('moves.items.quantity', ['move' => $move, 'item' => $item]) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ action: action })
        })
        .then(res => res.json())
        .then(data => {
            this.submitting = false;
            if (data.confirm_delete_required) {
                if (confirm(data.message)) {
                    // Send second request with confirm_delete = true
                    fetch('{{ route('moves.items.quantity', ['move' => $move, 'item' => $item]) }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ action: action, confirm_delete: true })
                    }).then(() => window.location.reload());
                }
            } else if (data.success) {
                this.qty = data.quantity;
                if (data.deleted) {
                    $el.closest('.inventory-item-card').remove();
                }
                // Dispatch event to update sticky header totals
                window.dispatchEvent(new CustomEvent('totals-updated', { detail: data }));
            }
        })
        .catch(() => {
            this.submitting = false;
            alert('Failed to update quantity. Please check connection.');
        });
    }
}"
class="inventory-item-card bg-white rounded-xl border border-slate-200 p-4 shadow-xs transition-shadow hover:shadow-md flex flex-col justify-between gap-3">

    <!-- Top Row: Tag, Name & Edit Button -->
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                    {{ $item->tag_number }}
                </span>
                <span class="text-xs text-slate-500 font-medium">
                    {{ $item->item_category->label() }}
                </span>
                @if($item->packing_type)
                    <span class="text-[11px] px-2 py-0.5 rounded-full {{ $item->packing_type->badgeClass() }}">
                        {{ $item->packing_type->label() }}
                    </span>
                @endif
            </div>

            <h3 class="font-semibold text-base text-slate-900 leading-snug break-words">
                {{ $item->name }}
            </h3>

            <!-- Volume and Weight summary -->
            <p class="text-xs text-slate-500 font-mono mt-1">
                {{ number_format((float) $item->unit_volume_cuft, 2) }} cu ft &bull; {{ number_format((float) $item->unit_weight_lbs, 1) }} lbs / unit
            </p>
        </div>

        @if($canEdit)
        <button @click="$dispatch('open-edit-item-modal', { item: {{ json_encode($item) }} })"
                type="button"
                class="p-2 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-50 transition-colors min-h-10 min-w-10 flex items-center justify-center shrink-0"
                title="Edit item condition & handling">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
        </button>
        @endif
    </div>

    <!-- Handling & Condition Flags -->
    <div class="flex flex-wrap items-center gap-1.5">
        @if($item->is_fragile)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Fragile
            </span>
        @endif

        @if($item->is_high_value)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                High Value
            </span>
        @endif

        @if($item->requires_disassembly)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-300">
                Disassembly
            </span>
        @endif

        @if($item->origin_condition->value === 'pre_existing_damage')
            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                Damage Logged
            </span>
        @endif
    </div>

    <!-- Pre-existing Damage Notes -->
    @if($item->condition_notes)
        <div class="p-2.5 rounded-lg bg-amber-50/70 border border-amber-200 text-xs text-amber-900 leading-relaxed break-words">
            <span class="font-bold">Condition:</span> {{ $item->condition_notes }}
        </div>
    @endif

    <!-- Photo Thumbnails Strip -->
    @if($item->photos->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            @foreach($item->photos as $photo)
                <a href="{{ $photo->url() }}" target="_blank" class="block w-14 h-14 rounded-lg overflow-hidden border border-slate-200 shrink-0 bg-slate-100 shadow-2xs hover:opacity-85">
                    <img src="{{ $photo->url() }}" alt="Condition photo" class="w-full h-full object-cover">
                </a>
            @endforeach
        </div>
    @endif

    <!-- Bottom Row: Stepper and Action Row -->
    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-3">
        @if($canEdit)
        <!-- Photo Upload Trigger -->
        <button @click="$dispatch('open-photo-upload-modal', { item: {{ json_encode($item) }} })"
                type="button"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Photo ({{ $item->photos->count() }}/5)</span>
        </button>

        <!-- Rapid Quantity Stepper (min 48px touch targets) -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
            <button @click="updateQuantity('decrement')"
                    type="button"
                    :disabled="submitting"
                    class="w-10 h-10 flex items-center justify-center rounded-lg bg-white text-slate-800 hover:bg-slate-200 active:scale-95 shadow-2xs font-bold text-lg transition-transform"
                    title="Decrease quantity">
                &minus;
            </button>
            <span class="w-9 text-center font-mono font-bold text-base text-slate-900 select-none" x-text="qty">
                {{ $item->quantity }}
            </span>
            <button @click="updateQuantity('increment')"
                    type="button"
                    :disabled="submitting"
                    class="w-10 h-10 flex items-center justify-center rounded-lg bg-white text-slate-800 hover:bg-slate-200 active:scale-95 shadow-2xs font-bold text-lg transition-transform"
                    title="Increase quantity">
                +
            </button>
        </div>
        @else
        <div class="text-right w-full">
            <span class="font-mono font-bold text-sm text-slate-900">Qty: {{ $item->quantity }}</span>
        </div>
        @endif
    </div>

</div>
