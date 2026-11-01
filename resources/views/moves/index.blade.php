<x-layouts.app title="Move Jobs">
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Header Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                    Relocation Move Jobs
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Manage, catalog, and monitor customer inventories from survey to delivery.
                </p>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
            <a href="{{ route('moves.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold shadow-xs transition-colors min-h-11">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Move Job</span>
            </a>
            @endif
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <form action="{{ route('moves.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="relative sm:col-span-2">
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search reference, customer, or address..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ ($filters['status'] ?? '') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit / Reset Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-colors">
                        Filter
                    </button>
                    @if(array_filter($filters))
                    <a href="{{ route('moves.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- MOVES LIST: Responsive Hybrid (Cards on Mobile < 1024px, Table on Desktop >= 1024px) -->

        <!-- Mobile Card Feed (< 1024px) -->
        <div class="lg:hidden space-y-3">
            @forelse($moves as $move)
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-col justify-between gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs font-bold text-slate-900 px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                    {{ $move->reference_code }}
                                </span>
                                <x-status-badge :status="$move->status" />
                            </div>
                            <h2 class="font-bold text-base text-slate-900 mt-1.5 leading-snug">
                                {{ $move->customer->name }}
                            </h2>
                        </div>
                        <span class="text-xs font-mono font-semibold text-slate-500 whitespace-nowrap">
                            {{ $move->scheduled_move_date->format('M d, Y') }}
                        </span>
                    </div>

                    <div class="space-y-1 text-xs text-slate-600 border-t border-slate-100 pt-2">
                        <p class="truncate"><span class="font-semibold text-slate-800">Origin:</span> {{ $move->origin_address }}</p>
                        <p class="truncate"><span class="font-semibold text-slate-800">Dest:</span> {{ $move->destination_address }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                        <span class="font-mono text-slate-700 font-medium">
                            {{ $move->total_items_count }} items &bull; {{ number_format((float) $move->total_volume_cuft, 1) }} cu ft
                        </span>

                        <div class="flex items-center gap-2">
                            @if($move->status->canEditInventory())
                                <a href="{{ route('moves.survey', $move) }}"
                                   class="px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs">
                                    Survey
                                </a>
                            @elseif($move->status->value === 'in_transit' || $move->status->value === 'origin_signed')
                                <a href="{{ route('moves.delivery', $move) }}"
                                   class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs">
                                    Delivery
                                </a>
                            @endif
                            <a href="{{ route('moves.show', $move) }}"
                               class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="font-semibold text-base text-slate-800">No move jobs found</p>
                    <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or schedule a new move.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (>= 1024px) -->
        <div class="hidden lg:block bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                        <th class="py-3.5 px-4">Ref Code</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Move Date</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Volume / Items</th>
                        <th class="py-3.5 px-4">Surveyor</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($moves as $move)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('moves.show', $move) }}" class="text-blue-600 hover:underline">
                                    {{ $move->reference_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-semibold text-slate-900 leading-snug">{{ $move->customer->name }}</p>
                                <p class="text-xs text-slate-500 font-mono">{{ $move->customer->phone }}</p>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-700 whitespace-nowrap">
                                {{ $move->scheduled_move_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <x-status-badge :status="$move->status" />
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-700 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ number_format((float) $move->total_volume_cuft, 1) }}</span> cu ft
                                <span class="text-slate-400">({{ $move->total_items_count }} items)</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $move->assignedSurveyor?->name ?? 'Unassigned' }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($move->status->canEditInventory())
                                        <a href="{{ route('moves.survey', $move) }}" class="px-3 py-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors">
                                            Survey
                                        </a>
                                    @elseif($move->status->value === 'in_transit' || $move->status->value === 'origin_signed')
                                        <a href="{{ route('moves.delivery', $move) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                            Delivery
                                        </a>
                                    @endif
                                    <a href="{{ route('moves.show', $move) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                No move jobs match your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $moves->links() }}
        </div>

    </div>
</x-layouts.app>
