<x-layouts.app title="Survey: {{ $move->reference_code }}">
    <div x-data="{
        showCatalogModal: false,
        showCustomItemModal: false,
        showAddRoomModal: false,
        showEditItemModal: false,
        showPhotoModal: false,
        activeItem: {},
        selectedRoomId: {{ $selectedRoomId }},
        totalItems: {{ $move->total_items_count }},
        totalVolume: '{{ number_format((float) $move->total_volume_cuft, 1) }}',
        totalWeight: '{{ number_format((float) $move->total_weight_lbs, 0) }}'
    }"
    @open-add-room-modal.window="showAddRoomModal = true"
    @open-edit-item-modal.window="activeItem = $event.detail.item; showEditItemModal = true"
    @open-photo-upload-modal.window="activeItem = $event.detail.item; showPhotoModal = true"
    @totals-updated.window="
        totalItems = $event.detail.total_items_count;
        totalVolume = parseFloat($event.detail.total_volume_cuft).toFixed(1);
        totalWeight = parseInt($event.detail.total_weight_lbs);
    "
    class="flex flex-col min-h-[calc(100vh-3.5rem)] lg:min-h-screen bg-slate-100">

        <!-- STICKY MOVE SUMMARY BAR -->
        <div class="bg-slate-900 text-white px-4 py-2.5 shadow-md sticky top-14 lg:top-0 z-40 border-b border-slate-800 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <a href="{{ route('moves.show', $move) }}" class="p-1 text-slate-400 hover:text-white rounded" title="Back to Move Overview">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-orange-400">{{ $move->reference_code }}</span>
                        <x-status-badge :status="$move->status" />
                    </div>
                    <p class="text-xs font-semibold text-slate-200 truncate max-w-[200px] sm:max-w-xs">{{ $move->customer->name }}</p>
                </div>
            </div>

            <!-- Live Totals Counter (Thumb-readable) -->
            <div class="flex items-center gap-3 font-mono text-xs sm:text-sm">
                <div class="text-right">
                    <span class="text-slate-400 text-[10px] uppercase block leading-none">Items</span>
                    <span class="font-bold text-white" x-text="totalItems">{{ $move->total_items_count }}</span>
                </div>
                <div class="h-6 w-px bg-slate-700"></div>
                <div class="text-right">
                    <span class="text-slate-400 text-[10px] uppercase block leading-none">Volume</span>
                    <span class="font-bold text-white"><span x-text="totalVolume">{{ number_format((float) $move->total_volume_cuft, 1) }}</span> <span class="text-[10px] text-slate-400">cuft</span></span>
                </div>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <div class="text-right hidden sm:block">
                    <span class="text-slate-400 text-[10px] uppercase block leading-none">Weight</span>
                    <span class="font-bold text-white"><span x-text="totalWeight">{{ number_format((float) $move->total_weight_lbs, 0) }}</span> <span class="text-[10px] text-slate-400">lbs</span></span>
                </div>
            </div>
        </div>

        <!-- HORIZONTAL ROOM TABS BAR -->
        <x-room-tabs :move="$move" :selectedRoomId="$selectedRoomId" />

        <!-- ACTIVE ROOM INVENTORY WORKSPACE -->
        @php
            $currentRoom = $move->rooms->firstWhere('id', $selectedRoomId) ?? $move->rooms->first();
            $roomItems = $currentRoom ? $currentRoom->items : collect();
        @endphp

        <div class="flex-1 p-3 sm:p-5 lg:p-6 max-w-7xl mx-auto w-full pb-28 lg:pb-8">

            @if($currentRoom)
                <!-- Active Room Title & Actions -->
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">
                                {{ $currentRoom->name }}
                            </h2>
                            <span class="text-xs text-slate-500 font-medium">({{ $currentRoom->floor_level }})</span>
                        </div>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">
                            {{ $currentRoom->room_items_count }} item(s) &bull; {{ number_format((float) $currentRoom->room_volume_cuft, 1) }} cu ft
                        </p>
                    </div>

                    <!-- Room Settings / Delete -->
                    <div class="flex items-center gap-2">
                        @if($move->rooms->count() > 1)
                        <form action="{{ route('moves.rooms.destroy', ['move' => $move, 'room' => $currentRoom]) }}" method="POST"
                              onsubmit="return confirm('Delete this room and its items?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="confirm_delete_items" value="1">
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-white text-xs font-semibold" title="Delete Room">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Items Grid (1-col on mobile, 2-col tablet, 3-col desktop) -->
                @if($roomItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                        @foreach($roomItems as $item)
                            <x-inventory-item-card :item="$item" :move="$move" :canEdit="$move->status->canEditInventory()" />
                        @endforeach
                    </div>
                @else
                    <!-- Empty Room State -->
                    <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-8 sm:p-12 text-center my-6 shadow-2xs">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Room is empty</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                            Start logging items in <span class="font-semibold text-slate-700">{{ $currentRoom->name }}</span> using 1-tap presets from the catalog or add a custom item.
                        </p>
                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            <button @click="showCatalogModal = true" type="button"
                                    class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xs transition-colors min-h-11 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Quick Add from Catalog</span>
                            </button>
                            <button @click="showCustomItemModal = true" type="button"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition-colors min-h-11">
                                + Custom Item
                            </button>
                        </div>
                    </div>
                @endif

            @else
                <div class="p-8 text-center text-slate-500">No rooms available. Please add a room first.</div>
            @endif

        </div>

        <!-- FLOATING BOTTOM ACTION BAR (Sticky Thumb Zone) -->
        <div class="fixed bottom-16 lg:bottom-4 inset-x-0 z-30 px-3 sm:px-6 pointer-events-none">
            <div class="max-w-xl mx-auto w-full bg-slate-900/95 backdrop-blur-md rounded-2xl p-2 text-white shadow-2xl border border-slate-700 pointer-events-auto flex items-center justify-between gap-2">
                <button @click="showCatalogModal = true" type="button"
                        class="flex-1 min-h-11 px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-xs active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Catalog</span>
                </button>

                <button @click="showCustomItemModal = true" type="button"
                        class="min-h-11 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs sm:text-sm transition-colors">
                    + Custom
                </button>

                <a href="{{ route('moves.sign', ['move' => $move, 'stage' => 'origin']) }}"
                   class="min-h-11 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Sign-Off</span>
                </a>
            </div>
        </div>

        <!-- 1. CATALOG QUICK-ADD BOTTOM SHEET / MODAL -->
        <x-catalog-quick-add :move="$move" :catalogItems="$catalogItems" :catalogCategories="$catalogCategories" :selectedRoomId="$selectedRoomId" />

        <!-- 2. ADD CUSTOM ITEM MODAL -->
        <div x-show="showCustomItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200" @click.outside="showCustomItemModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Add Custom Inventory Item</h3>
                    <button @click="showCustomItemModal = false" class="p-1 text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('moves.items.store', $move) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="move_room_id" value="{{ $selectedRoomId }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Item Description / Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Antique Oak Grandfather Clock"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Category</label>
                            <select name="item_category" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm">
                                <option value="furniture">Furniture</option>
                                <option value="appliance">Appliance</option>
                                <option value="electronics">Electronics</option>
                                <option value="box_carton">Box / Carton</option>
                                <option value="fragile_art">Fragile / Art</option>
                                <option value="outdoor">Outdoor / Lawn</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="500" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit Volume (cu ft)</label>
                            <input type="number" step="0.1" name="unit_volume_cuft" value="5.0" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit Weight (lbs)</label>
                            <input type="number" step="0.5" name="unit_weight_lbs" value="25.0" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Packing Method</label>
                            <select name="packing_type" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm">
                                <option value="unpacked_furniture">Blanket Wrapped</option>
                                <option value="company_packed">Company Packed</option>
                                <option value="pbo">Packed by Owner (PBO)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Condition</label>
                            <select name="origin_condition" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm">
                                <option value="good">Good / Normal</option>
                                <option value="fair_worn">Fair / Worn</option>
                                <option value="pre_existing_damage">Pre-Existing Damage</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Condition Notes</label>
                        <textarea name="condition_notes" rows="2" placeholder="Describe any pre-existing scratches, chips, or dents..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                    </div>

                    <!-- Handling Checkboxes -->
                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="is_fragile" value="0">
                            <input type="checkbox" name="is_fragile" value="1" class="rounded border-slate-300 text-blue-600">
                            <span>Fragile</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="is_high_value" value="0">
                            <input type="checkbox" name="is_high_value" value="1" class="rounded border-slate-300 text-blue-600">
                            <span>High Value</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="requires_disassembly" value="0">
                            <input type="checkbox" name="requires_disassembly" value="1" class="rounded border-slate-300 text-blue-600">
                            <span>Disassembly</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showCustomItemModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs">
                            Add to Room
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. EDIT ITEM & CONDITION MODAL -->
        <div x-show="showEditItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200" @click.outside="showEditItemModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900" x-text="activeItem.name">Edit Item</h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="activeItem.tag_number"></p>
                    </div>
                    <button @click="showEditItemModal = false" class="p-1 text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/moves/' . $move->id . '/items') }}/' + activeItem.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="move_room_id" :value="activeItem.move_room_id">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Item Name</label>
                        <input type="text" name="name" x-model="activeItem.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Category</label>
                            <select name="item_category" x-model="activeItem.item_category" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                                <option value="furniture">Furniture</option>
                                <option value="appliance">Appliance</option>
                                <option value="electronics">Electronics</option>
                                <option value="box_carton">Box / Carton</option>
                                <option value="fragile_art">Fragile / Art</option>
                                <option value="outdoor">Outdoor</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Quantity</label>
                            <input type="number" name="quantity" x-model="activeItem.quantity" min="1" max="500" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit Volume (cu ft)</label>
                            <input type="number" step="0.1" name="unit_volume_cuft" x-model="activeItem.unit_volume_cuft" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Unit Weight (lbs)</label>
                            <input type="number" step="0.5" name="unit_weight_lbs" x-model="activeItem.unit_weight_lbs" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Packing Method</label>
                            <select name="packing_type" x-model="activeItem.packing_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                                <option value="unpacked_furniture">Blanket Wrapped</option>
                                <option value="company_packed">Company Packed</option>
                                <option value="pbo">Packed by Owner (PBO)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Condition</label>
                            <select name="origin_condition" x-model="activeItem.origin_condition" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                                <option value="good">Good / Normal</option>
                                <option value="fair_worn">Fair / Worn</option>
                                <option value="pre_existing_damage">Pre-Existing Damage</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pre-Existing Condition Notes</label>
                        <textarea name="condition_notes" x-model="activeItem.condition_notes" rows="2" placeholder="e.g. Scratched left leg, dent on front panel..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="is_fragile" value="0">
                            <input type="checkbox" name="is_fragile" value="1" :checked="activeItem.is_fragile" class="rounded border-slate-300 text-blue-600">
                            <span>Fragile</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="is_high_value" value="0">
                            <input type="checkbox" name="is_high_value" value="1" :checked="activeItem.is_high_value" class="rounded border-slate-300 text-blue-600">
                            <span>High Value</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="hidden" name="requires_disassembly" value="0">
                            <input type="checkbox" name="requires_disassembly" value="1" :checked="activeItem.requires_disassembly" class="rounded border-slate-300 text-blue-600">
                            <span>Disassembly</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <button type="button" @click="if(confirm('Delete this item?')) {
                            const f = document.createElement('form');
                            f.method = 'POST';
                            f.action = '{{ url('/moves/' . $move->id . '/items') }}/' + activeItem.id;
                            f.innerHTML = '<input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'><input type=\'hidden\' name=\'_method\' value=\'DELETE\'>';
                            document.body.appendChild(f);
                            f.submit();
                        }" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                            Delete Item
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="showEditItemModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4. PHOTO UPLOAD MODAL -->
        <div x-show="showPhotoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200" @click.outside="showPhotoModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Upload Condition Photo</h3>
                        <p class="text-xs text-slate-500" x-text="activeItem.name"></p>
                    </div>
                    <button @click="showPhotoModal = false" class="p-1 text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/moves/' . $move->id . '/items') }}/' + activeItem.id + '/photos'" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Select Photo (Max 5MB)</label>
                        <!-- Mobile camera capture attribute environment -->
                        <input type="file" name="photos[]" accept="image/*" capture="environment" required
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Caption / Damage Location (Optional)</label>
                        <input type="text" name="caption" placeholder="e.g. Scuff on rear panel" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showPhotoModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold">
                            Upload Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 5. ADD ROOM MODAL -->
        <div x-show="showAddRoomModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-200" @click.outside="showAddRoomModal = false">
                <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">Add Room to Move</h3>

                <form action="{{ route('moves.rooms.store', $move) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Room Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Attic / Sunroom / Den"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Floor Level</label>
                        <select name="floor_level" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                            <option value="Main Floor">Main Floor</option>
                            <option value="2nd Floor">2nd Floor</option>
                            <option value="Basement">Basement</option>
                            <option value="Attic">Attic</option>
                            <option value="Garage">Garage</option>
                            <option value="Outdoor / Yard">Outdoor / Yard</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showAddRoomModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold">
                            Add Room
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
