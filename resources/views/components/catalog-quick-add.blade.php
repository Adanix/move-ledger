@props(['move', 'catalogItems', 'catalogCategories', 'selectedRoomId'])

<div x-data="catalogSearch()"
     x-show="showCatalogModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop -->
    <div x-show="showCatalogModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
         @click="showCatalogModal = false"></div>

    <!-- Bottom Sheet on Mobile / Centered Modal on Desktop -->
    <div class="fixed inset-x-0 bottom-0 sm:inset-y-0 sm:flex sm:items-center sm:justify-center p-0 sm:p-4">
        <div x-show="showCatalogModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="translate-y-0 sm:scale-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95"
             class="w-full sm:max-w-2xl bg-white rounded-t-3xl sm:rounded-2xl max-h-[85vh] sm:max-h-[80vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200">

            <!-- Sheet Header -->
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center text-white shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">Quick Add from Catalog</h2>
                        <p class="text-xs text-slate-500">Tap any item to add it to the active room</p>
                    </div>
                </div>
                <button @click="showCatalogModal = false" type="button" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search & Category Filters -->
            <div class="p-4 border-b border-slate-200 bg-white space-y-3 shrink-0">
                <div class="relative">
                    <input x-model="search" type="search" placeholder="Search catalog items (e.g. Sofa, Bed, Box)..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Category Filters (Horizontal Scrollable) -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
                    <button @click="selectedCategory = 'all'" type="button"
                            :class="selectedCategory === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">
                        All Items
                    </button>
                    @foreach($catalogCategories as $cat)
                        <button @click="selectedCategory = '{{ $cat->value }}'" type="button"
                                :class="selectedCategory === '{{ $cat->value }}' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">
                            {{ $cat->label() }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Items Feed -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @foreach($catalogItems as $cItem)
                    <div x-show="matches('{{ $cItem->category->value }}', '{{ addslashes($cItem->name) }}')"
                         class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
                        <div class="min-w-0 flex-1 pr-3">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-sm text-slate-900 leading-snug">{{ $cItem->name }}</span>
                                @if($cItem->is_fragile_default)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">Fragile</span>
                                @endif
                                @if($cItem->requires_disassembly_default)
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">Disassembly</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ number_format((float) $cItem->default_volume_cuft, 2) }} cu ft &bull; {{ number_format((float) $cItem->default_weight_lbs, 1) }} lbs
                            </p>
                        </div>

                        <!-- 1-Tap Quick Add Form -->
                        <form action="{{ route('moves.items.store', $move) }}" method="POST">
                            @csrf
                            <input type="hidden" name="move_room_id" value="{{ $selectedRoomId }}">
                            <input type="hidden" name="catalog_item_id" value="{{ $cItem->id }}">
                            <input type="hidden" name="name" value="{{ $cItem->name }}">
                            <input type="hidden" name="item_category" value="{{ $cItem->item_type->value }}">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="unit_volume_cuft" value="{{ $cItem->default_volume_cuft }}">
                            <input type="hidden" name="unit_weight_lbs" value="{{ $cItem->default_weight_lbs }}">
                            <input type="hidden" name="packing_type" value="unpacked_furniture">
                            <input type="hidden" name="is_fragile" value="{{ $cItem->is_fragile_default ? 1 : 0 }}">
                            <input type="hidden" name="is_high_value" value="0">
                            <input type="hidden" name="requires_disassembly" value="{{ $cItem->requires_disassembly_default ? 1 : 0 }}">
                            <input type="hidden" name="origin_condition" value="good">

                            <button type="submit"
                                    class="min-h-10 min-w-10 px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold inline-flex items-center gap-1 shadow-xs active:scale-95 transition-transform shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Add</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
