<x-layouts.app title="Standard Item Catalog">
    <div x-data="{
        createModalOpen: false,
        editModalOpen: false,
        editItem: {
            id: null,
            name: '',
            category: 'living_room',
            item_type: 'furniture',
            default_volume_cuft: '10.00',
            default_weight_lbs: '50.00',
            is_fragile_default: false,
            requires_disassembly_default: false,
            is_active: true
        },
        openEdit(item) {
            this.editItem = { ...item };
            this.editModalOpen = true;
        }
    }" class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Header & Search Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Standard Item Catalog</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Industry standard volume (cu ft) and weight (lbs) benchmarks for 1-tap inventory capture.
                </p>
            </div>

            @can('create', App\Models\CatalogItem::class)
            <button @click="createModalOpen = true"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors flex items-center justify-center gap-1.5 min-h-11 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Catalog Item</span>
            </button>
            @endcan
        </div>

        <!-- Filter & Search Controls -->
        <div class="space-y-3">
            <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search catalog items by name..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold min-h-11 cursor-pointer">
                        Search
                    </button>
                    @if($search || $selectedCategory)
                        <a href="{{ route('catalog.index') }}" class="px-3 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold min-h-11 flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Category Pills Filter -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
                <a href="{{ route('catalog.index', array_filter(['search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ empty($selectedCategory) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    All Categories
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('catalog.index', array_filter(['category' => $category->value, 'search' => $search])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $selectedCategory === $category->value ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                        {{ $category->label() }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Catalog Items Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4 text-right">Volume</th>
                            <th class="py-3 px-4 text-right">Weight</th>
                            <th class="py-3 px-4 text-center">Attributes</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $item->name }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $item->category->label() }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $item->item_type->label() }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-slate-800 whitespace-nowrap">
                                    {{ number_format((float) $item->default_volume_cuft, 2) }} <span class="text-[10px] text-slate-400">cu ft</span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-slate-800 whitespace-nowrap">
                                    {{ number_format((float) $item->default_weight_lbs, 1) }} <span class="text-[10px] text-slate-400">lbs</span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        @if($item->is_fragile_default)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800" title="Fragile Item">Fragile</span>
                                        @endif
                                        @if($item->requires_disassembly_default)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800" title="Requires Disassembly">Disassembly</span>
                                        @endif
                                        @if(!$item->is_fragile_default && !$item->requires_disassembly_default)
                                            <span class="text-slate-300 text-[10px]">Standard</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        @can('update', $item)
                                        <button type="button"
                                                @click="openEdit({
                                                    id: {{ $item->id }},
                                                    name: '{{ addslashes($item->name) }}',
                                                    category: '{{ $item->category->value }}',
                                                    item_type: '{{ $item->item_type->value }}',
                                                    default_volume_cuft: '{{ $item->default_volume_cuft }}',
                                                    default_weight_lbs: '{{ $item->default_weight_lbs }}',
                                                    is_fragile_default: {{ $item->is_fragile_default ? 'true' : 'false' }},
                                                    requires_disassembly_default: {{ $item->requires_disassembly_default ? 'true' : 'false' }},
                                                    is_active: {{ $item->is_active ? 'true' : 'false' }}
                                                })"
                                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs transition-colors cursor-pointer">
                                            Edit
                                        </button>
                                        @endcan

                                        @can('delete', $item)
                                        <form action="{{ route('catalog.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete catalog item \'{{ addslashes($item->name) }}\'?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-xs transition-colors cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    No catalog items found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE CATALOG ITEM MODAL -->
        <div x-show="createModalOpen"
             x-cloak
             @click.self="createModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Add New Standard Catalog Item</h2>
                    <button @click="createModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('catalog.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Item Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. Coffee Table - Marble Top"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Room Category <span class="text-rose-600">*</span>
                            </label>
                            <select name="category" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Item Type <span class="text-rose-600">*</span>
                            </label>
                            <select name="item_type" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm">
                                @foreach($itemTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Default Volume (cu ft) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.01" min="0.10" max="999.99" name="default_volume_cuft" value="10.00" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Default Weight (lbs) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.1" min="0.5" max="9999.9" name="default_weight_lbs" value="50.0" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_fragile_default" value="1" class="rounded border-slate-300 text-orange-600">
                            <span class="font-medium text-slate-700">Flag as Fragile by default</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requires_disassembly_default" value="1" class="rounded border-slate-300 text-orange-600">
                            <span class="font-medium text-slate-700">Requires Disassembly by default</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600">
                            <span class="font-medium text-slate-700">Active in survey quick-add picker</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button @click="createModalOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold">
                            Create Item
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT CATALOG ITEM MODAL -->
        <div x-show="editModalOpen"
             x-cloak
             @click.self="editModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Edit Catalog Item</h2>
                    <button @click="editModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/catalog/' + editItem.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Item Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" x-model="editItem.name" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Room Category <span class="text-rose-600">*</span>
                            </label>
                            <select name="category" x-model="editItem.category" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Item Type <span class="text-rose-600">*</span>
                            </label>
                            <select name="item_type" x-model="editItem.item_type" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm">
                                @foreach($itemTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Default Volume (cu ft) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.01" min="0.10" max="999.99" name="default_volume_cuft" x-model="editItem.default_volume_cuft" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Default Weight (lbs) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.1" min="0.5" max="9999.9" name="default_weight_lbs" x-model="editItem.default_weight_lbs" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_fragile_default" value="1" x-model="editItem.is_fragile_default" class="rounded border-slate-300 text-orange-600">
                            <span class="font-medium text-slate-700">Flag as Fragile by default</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requires_disassembly_default" value="1" x-model="editItem.requires_disassembly_default" class="rounded border-slate-300 text-orange-600">
                            <span class="font-medium text-slate-700">Requires Disassembly by default</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editItem.is_active" class="rounded border-slate-300 text-emerald-600">
                            <span class="font-medium text-slate-700">Active in survey quick-add picker</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button @click="editModalOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
