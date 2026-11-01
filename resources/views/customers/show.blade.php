<x-layouts.app title="Customer: {{ $customer->name }}">
    <div x-data="{ editModalOpen: false }" class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Breadcrumb & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Customers
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $customer->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Customer since {{ $customer->created_at->format('M Y') }}</p>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $customer)
                <button @click="editModalOpen = true"
                        type="button"
                        class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs sm:text-sm shadow-xs transition-colors min-h-11 cursor-pointer">
                    Edit Profile
                </button>
                @endcan

                <a href="{{ route('moves.create', ['customer_id' => $customer->id]) }}"
                   class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5 min-h-11">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Move Job</span>
                </a>
            </div>
        </div>

        <!-- Customer Profile Information Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Contact Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-xs">Primary Phone</span>
                    <a href="tel:{{ $customer->phone }}" class="font-mono font-semibold text-blue-600 hover:underline mt-0.5 block">
                        {{ $customer->phone }}
                    </a>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Secondary Phone</span>
                    <p class="font-mono text-slate-700 mt-0.5">
                        {{ $customer->secondary_phone ?: '—' }}
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-slate-400 block text-xs">Email Address</span>
                    <a href="mailto:{{ $customer->email }}" class="font-semibold text-blue-600 hover:underline mt-0.5 block break-all">
                        {{ $customer->email }}
                    </a>
                </div>
            </div>

            @if($customer->notes)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Customer Notes</span>
                    <p class="text-xs sm:text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        {{ $customer->notes }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Move History Section -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Relocation Move Jobs</h2>
                    <p class="text-xs text-slate-500 mt-0.5">All historical and active relocation jobs for this client</p>
                </div>
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $customer->moves->count() }} {{ Str::plural('Job', $customer->moves->count()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Move Code</th>
                            <th class="py-3 px-4">Route &amp; Scheduled Date</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Items / Volume</th>
                            <th class="py-3 px-4">Surveyor</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customer->moves as $move)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('moves.show', $move) }}" class="text-blue-600 hover:underline">
                                        {{ $move->reference_code }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 max-w-xs">
                                    <div class="truncate text-slate-800 font-medium">
                                        {{ $move->origin_city ?: 'Origin' }} &rarr; {{ $move->destination_city ?: 'Destination' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $move->scheduled_move_date?->toFormattedDateString() ?? 'Pending' }} &bull; {{ $move->property_type->label() }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <x-status-badge :status="$move->status" />
                                </td>
                                <td class="py-3 px-4 text-right font-mono whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $move->total_items_count }} items</div>
                                    <div class="text-[11px] text-slate-400">{{ number_format((float) $move->total_volume_cuft, 1) }} cu ft</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $move->assignedSurveyor?->name ?? 'Unassigned' }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('moves.show', $move) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No move jobs registered for this customer yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT CUSTOMER MODAL -->
        @can('update', $customer)
        <div x-show="editModalOpen"
             x-cloak
             @click.self="editModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Edit Customer Profile</h2>
                    <button @click="editModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Full Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Primary Phone <span class="text-rose-600">*</span>
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Secondary Phone
                            </label>
                            <input type="tel" name="secondary_phone" value="{{ old('secondary_phone', $customer->secondary_phone) }}"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Email Address <span class="text-rose-600">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Customer Notes
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">{{ old('notes', $customer->notes) }}</textarea>
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
        @endcan

    </div>
</x-layouts.app>
