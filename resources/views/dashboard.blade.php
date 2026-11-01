<x-layouts.app title="Operations & Field Dashboard">
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Header & Role Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                    Operations Dashboard
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Welcome back, <span class="font-semibold text-slate-700">{{ $user->name }}</span> &bull;
                    <span class="font-mono text-xs uppercase">{{ $user->role->label() }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                @if($user->isAdmin() || $user->isCoordinator())
                <a href="{{ route('moves.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs sm:text-sm font-bold shadow-xs transition-colors min-h-11">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Schedule Move</span>
                </a>
                @endif
                <a href="{{ route('moves.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs sm:text-sm font-semibold shadow-2xs transition-colors min-h-11">
                    <span>All Moves</span>
                </a>
            </div>
        </div>

        <!-- KPI Metric Cards (2x2 on mobile, 4-col on desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Moves</span>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-mono text-slate-900">{{ $kpis['total'] }}</p>
                <span class="mt-1 text-[11px] text-slate-400">All registered relocation jobs</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">In Survey</span>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-mono text-blue-700">{{ $kpis['in_survey'] }}</p>
                <span class="mt-1 text-[11px] text-slate-400">Active cataloging on-site</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-600">Pending Sign-Off</span>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-mono text-amber-700">{{ $kpis['pending_signoff'] }}</p>
                <span class="mt-1 text-[11px] text-slate-400">Awaiting customer approval</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Delivered</span>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-mono text-emerald-700">{{ $kpis['delivered'] }}</p>
                <span class="mt-1 text-[11px] text-slate-400">Completed relocations</span>
            </div>
        </div>

        <!-- Split View: Today's Moves & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Today's Field Moves (7 cols on desktop) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h2 class="font-bold text-base text-slate-900">Today's Moves Schedule</h2>
                    </div>
                    <span class="text-xs font-mono font-medium text-slate-500">{{ now()->toFormattedDateString() }}</span>
                </div>

                <div class="p-4 sm:p-5 flex-1 divide-y divide-slate-100">
                    @forelse($todayMoves as $tMove)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $tMove->reference_code }}</span>
                                    <x-status-badge :status="$tMove->status" />
                                </div>
                                <p class="font-semibold text-sm text-slate-900 mt-1 truncate">{{ $tMove->customer->name }}</p>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $tMove->origin_address }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($tMove->status->canEditInventory())
                                    <a href="{{ route('moves.survey', $tMove) }}"
                                       class="px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors">
                                        Open Survey
                                    </a>
                                @elseif($tMove->status->value === 'in_transit' || $tMove->status->value === 'origin_signed')
                                    <a href="{{ route('moves.delivery', $tMove) }}"
                                       class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                        Deliver
                                    </a>
                                @endif
                                <a href="{{ route('moves.show', $tMove) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-sm font-medium">No moves scheduled for today.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Moves Feed (5 cols on desktop) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-base text-slate-900">Recent Move Activity</h2>
                    <a href="{{ route('moves.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">View All</a>
                </div>

                <div class="p-4 sm:p-5 flex-1 divide-y divide-slate-100">
                    @forelse($recentMoves as $rMove)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-semibold text-slate-800">{{ $rMove->reference_code }}</span>
                                    <span class="text-[10px] text-slate-400">&bull; {{ $rMove->scheduled_move_date->format('M d') }}</span>
                                </div>
                                <p class="text-sm font-medium text-slate-900 truncate mt-0.5">{{ $rMove->customer->name }}</p>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <x-status-badge :status="$rMove->status" />
                                <a href="{{ route('moves.show', $rMove) }}" class="p-1.5 text-slate-400 hover:text-slate-800 rounded">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <p class="text-sm">No recent moves found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>
