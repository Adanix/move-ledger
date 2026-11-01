@props(['move', 'selectedRoomId'])

<div class="w-full bg-white border-b border-slate-200 px-3 py-2.5 shadow-xs sticky top-14 lg:top-0 z-30 flex items-center justify-between gap-2 overflow-hidden">
    <!-- Horizontal Touch-scrollable Container -->
    <div class="flex-1 flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-1 px-1">
        @foreach($move->rooms as $room)
            @php
                $isActive = $room->id === (int) $selectedRoomId;
            @endphp
            <a href="{{ route('moves.survey', ['move' => $move, 'room_id' => $room->id]) }}"
               class="shrink-0 flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all min-h-10 {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <span>{{ $room->name }}</span>
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-md text-[11px] font-mono {{ $isActive ? 'bg-slate-800 text-slate-200' : 'bg-slate-200 text-slate-700' }}">
                    {{ $room->room_items_count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Add Room Trigger Button -->
    <button @click="$dispatch('open-add-room-modal')" type="button"
            class="shrink-0 min-h-10 min-w-10 px-3 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs sm:text-sm font-semibold flex items-center gap-1.5 transition-colors"
            title="Add Room">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="hidden sm:inline">Room</span>
    </button>
</div>
