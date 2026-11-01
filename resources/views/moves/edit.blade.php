<x-layouts.app title="Edit Move Job">
    <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto w-full space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('moves.show', $move) }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Move Details
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                    Edit Job: <span class="font-mono text-blue-600">{{ $move->reference_code }}</span>
                </h1>
            </div>
        </div>

        <form action="{{ route('moves.update', $move) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Customer & Move Date -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
                    1. Customer &amp; Schedule
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Customer</label>
                        <select name="customer_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $move->customer_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Property Type</label>
                        <select name="property_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @foreach($propertyTypes as $pt)
                                <option value="{{ $pt->value }}" {{ old('property_type', $move->property_type->value) === $pt->value ? 'selected' : '' }}>
                                    {{ $pt->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Move Date</label>
                        <input type="date" name="scheduled_move_date" value="{{ old('scheduled_move_date', $move->scheduled_move_date->toDateString()) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Assigned Surveyor</label>
                    <select name="assigned_surveyor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        @foreach($surveyors as $s)
                            <option value="{{ $s->id }}" {{ old('assigned_surveyor_id', $move->assigned_surveyor_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->role->label() }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Addresses -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
                    2. Addresses
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Origin Address</label>
                            <textarea name="origin_address" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ old('origin_address', $move->origin_address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Origin Floor / Access</label>
                            <input type="text" name="origin_floor_access" value="{{ old('origin_floor_access', $move->origin_floor_access) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Destination Address</label>
                            <textarea name="destination_address" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ old('destination_address', $move->destination_address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Destination Floor / Access</label>
                            <input type="text" name="destination_floor_access" value="{{ old('destination_floor_access', $move->destination_floor_access) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Move Instructions</label>
                    <textarea name="notes" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">{{ old('notes', $move->notes) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('moves.show', $move) }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm min-h-12 flex items-center">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md min-h-12 flex items-center gap-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
