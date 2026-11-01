<x-layouts.app title="Move: {{ $move->reference_code }}">
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Breadcrumb & Status Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('moves.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Moves
                </a>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold font-mono tracking-tight text-slate-900">
                        {{ $move->reference_code }}
                    </h1>
                    <x-status-badge :status="$move->status" />
                    <span class="text-xs text-slate-500 font-medium">&bull; {{ $move->property_type->label() }}</span>
                </div>
            </div>

            <!-- Context Actions -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($move->status->canEditInventory())
                    <a href="{{ route('moves.survey', $move) }}"
                       class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5 min-h-11">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Open Survey Workspace</span>
                    </a>

                    <a href="{{ route('moves.sign', ['move' => $move, 'stage' => 'origin']) }}"
                       class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5 min-h-11">
                        <span>Sign Origin Manifest</span>
                    </a>
                @elseif($move->status->value === 'origin_signed')
                    <form action="{{ route('moves.status.update', $move) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_transit">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors min-h-11">
                            Start Loading &amp; Transit
                        </button>
                    </form>

                    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                    <button onclick="document.getElementById('unlock-modal').classList.remove('hidden')"
                            type="button" class="px-3 py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold text-xs transition-colors min-h-11">
                        Unlock for Revision
                    </button>
                    @endif
                @elseif($move->status->value === 'in_transit')
                    <a href="{{ route('moves.delivery', $move) }}"
                       class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-1.5 min-h-11">
                        <span>Delivery Check-Off</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                <a href="{{ route('moves.edit', $move) }}"
                   class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs sm:text-sm font-semibold transition-colors min-h-11 flex items-center">
                    Edit Job
                </a>
                @endif
            </div>
        </div>

        <!-- Metric Summary Strip (4-col) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Items</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $move->total_items_count }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Box Count</span>
                <p class="text-2xl font-bold font-mono text-blue-600 mt-1">{{ $move->total_boxes_count }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Volume</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ number_format((float) $move->total_volume_cuft, 1) }} <span class="text-xs font-sans text-slate-500 font-normal">cu ft</span>
                </p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Weight</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1">
                    {{ number_format((float) $move->total_weight_lbs, 0) }} <span class="text-xs font-sans text-slate-500 font-normal">lbs</span>
                </p>
            </div>
        </div>

        <!-- 2-Column Split: Details & Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Main Content (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Customer & Address Information -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
                        Customer &amp; Location Overview
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Customer</span>
                            <p class="font-bold text-slate-900 mt-0.5">{{ $move->customer->name }}</p>
                            <p class="text-xs font-mono text-slate-600">{{ $move->customer->phone }} &bull; {{ $move->customer->email }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Assigned Surveyor</span>
                            <p class="font-bold text-slate-900 mt-0.5">{{ $move->assignedSurveyor?->name ?? 'Unassigned' }}</p>
                            <p class="text-xs text-slate-500 font-mono">Move Date: {{ $move->scheduled_move_date->format('l, F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-sm">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Origin (Pickup)</span>
                            <p class="text-slate-900 font-medium leading-relaxed">{{ $move->origin_address }}</p>
                            @if($move->origin_floor_access)
                                <p class="text-xs text-slate-500 mt-1"><span class="font-semibold">Access:</span> {{ $move->origin_floor_access }}</p>
                            @endif
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Destination (Delivery)</span>
                            <p class="text-slate-900 font-medium leading-relaxed">{{ $move->destination_address }}</p>
                            @if($move->destination_floor_access)
                                <p class="text-xs text-slate-500 mt-1"><span class="font-semibold">Access:</span> {{ $move->destination_floor_access }}</p>
                            @endif
                        </div>
                    </div>

                    @if($move->notes)
                        <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900">
                            <span class="font-bold">Instructions:</span> {{ $move->notes }}
                        </div>
                    @endif
                </div>

                <!-- Room Breakdown Summary Table -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-base text-slate-900">Room Breakdown ({{ $move->rooms->count() }} rooms)</h2>
                        <a href="{{ route('moves.survey', $move) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                            Open Cataloging View &rarr;
                        </a>
                    </div>

                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                                <th class="py-3 px-4">Room Name</th>
                                <th class="py-3 px-4">Floor Level</th>
                                <th class="py-3 px-4 text-center">Items</th>
                                <th class="py-3 px-4 text-right">Volume</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($move->rooms as $r)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-slate-900">
                                        <a href="{{ route('moves.survey', ['move' => $move, 'room_id' => $r->id]) }}" class="text-blue-600 hover:underline">
                                            {{ $r->name }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-600">{{ $r->floor_level }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-900">{{ $r->room_items_count }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-700">{{ number_format((float) $r->room_volume_cuft, 1) }} cu ft</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">No rooms configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Digital Signatures Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100">
                        Digital Signatures &amp; Legal Audit
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Origin Signature -->
                        <div class="p-4 rounded-xl border {{ $move->validOriginSignature ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-xs uppercase text-slate-700">Origin Loading Sign-Off</span>
                                @if($move->validOriginSignature)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Verified</span>
                                @else
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Pending</span>
                                @endif
                            </div>

                            @if($move->validOriginSignature)
                                <div class="w-full h-20 bg-white rounded-lg border border-slate-200 overflow-hidden my-2 p-1 flex items-center justify-center">
                                    <img src="{{ $move->validOriginSignature->imageUrl() }}" alt="Origin signature" class="max-h-full max-w-full object-contain">
                                </div>
                                <p class="text-xs text-slate-700 font-semibold truncate">{{ $move->validOriginSignature->signer_name }} ({{ $move->validOriginSignature->signer_role }})</p>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $move->validOriginSignature->signed_at->toDayDateTimeString() }}</p>
                                <p class="text-[9px] text-slate-400 font-mono truncate mt-1">Hash: {{ substr($move->validOriginSignature->manifest_snapshot_hash, 0, 16) }}...</p>
                            @else
                                <p class="text-xs text-slate-500 my-4">Inventory has not been signed at origin yet.</p>
                                @if($move->status->canEditInventory())
                                    <a href="{{ route('moves.sign', ['move' => $move, 'stage' => 'origin']) }}" class="inline-block px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-xs">
                                        Sign Origin On-Device
                                    </a>
                                @endif
                            @endif
                        </div>

                        <!-- Destination Signature -->
                        <div class="p-4 rounded-xl border {{ $move->validDestinationSignature ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-xs uppercase text-slate-700">Destination Delivery Sign-Off</span>
                                @if($move->validDestinationSignature)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Verified</span>
                                @else
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Pending</span>
                                @endif
                            </div>

                            @if($move->validDestinationSignature)
                                <div class="w-full h-20 bg-white rounded-lg border border-slate-200 overflow-hidden my-2 p-1 flex items-center justify-center">
                                    <img src="{{ $move->validDestinationSignature->imageUrl() }}" alt="Destination signature" class="max-h-full max-w-full object-contain">
                                </div>
                                <p class="text-xs text-slate-700 font-semibold truncate">{{ $move->validDestinationSignature->signer_name }} ({{ $move->validDestinationSignature->signer_role }})</p>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $move->validDestinationSignature->signed_at->toDayDateTimeString() }}</p>
                            @else
                                <p class="text-xs text-slate-500 my-4">Delivery receipt not yet signed.</p>
                                @if($move->status->value === 'in_transit')
                                    <a href="{{ route('moves.sign', ['move' => $move, 'stage' => 'destination']) }}" class="inline-block px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs">
                                        Sign Delivery Receipt
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar (4 cols): Share Link, QR & Audit -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Customer Share Link & QR Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>Customer Share Link</span>
                        @if($move->activeShare)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                        @endif
                    </h2>

                    @if($move->activeShare)
                        @php
                            $shareUrl = route('share.show', ['token' => $move->activeShare->token]);
                        @endphp
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Share URL</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" readonly value="{{ $shareUrl }}" id="share-link-input"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-mono bg-slate-50 text-slate-700">
                                    <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}'); alert('Link copied to clipboard!');"
                                            class="px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shrink-0">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            @if($move->activeShare->has_pin)
                                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-center justify-between">
                                    <span class="font-semibold">Security PIN Protected</span>
                                    <span class="font-mono font-bold text-sm">Protected</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-xs text-slate-500 font-mono pt-2 border-t border-slate-100">
                                <span>Expires: {{ $move->activeShare->expires_at->format('M d, Y') }}</span>
                                <span>Views: {{ $move->activeShare->access_count }}</span>
                            </div>

                            <!-- Revoke Form -->
                            <form action="{{ route('moves.shares.destroy', ['move' => $move, 'share' => $move->activeShare]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Revoke this customer link immediately?')"
                                        class="w-full py-2 px-3 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-semibold transition-colors">
                                    Revoke Share Link
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Generate New Share Form -->
                        <form action="{{ route('moves.shares.store', $move) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Optional 4-Digit Access PIN</label>
                                <input type="text" name="pin" maxlength="6" placeholder="e.g. 4829"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono">
                                <p class="text-[11px] text-slate-400 mt-1">Leave blank for direct link access without PIN.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Link Expiration</label>
                                <select name="expires_days" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                                    <option value="7">7 Days</option>
                                    <option value="14" selected>14 Days (Default)</option>
                                    <option value="30">30 Days</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition-colors">
                                Generate Customer Link
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Audit Trail Timeline -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                    <h2 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100">Audit Trail</h2>

                    <div class="flow-root max-h-80 overflow-y-auto pr-1">
                        <ul class="-mb-8">
                            @forelse($move->auditLogs as $log)
                                <li>
                                    <div class="relative pb-6">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="h-2 w-2 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-slate-800">{{ $log->description }}</p>
                                                <div class="mt-0.5 text-[10px] text-slate-400 font-mono">
                                                    <span>{{ $log->actor_name }}</span> &bull;
                                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-xs text-slate-400 py-4 text-center">No audit logs recorded yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Unlock Revision Modal -->
    <div id="unlock-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">Unlock Manifest for Revision</h3>
            <p class="text-xs text-rose-600 font-semibold mt-1">
                Warning: Unlocking will invalidate the active origin signature and require the customer to re-sign the manifest.
            </p>

            <form action="{{ route('moves.status.update', $move) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="unlock_for_revision">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Reason for Revision <span class="text-rose-600">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="e.g. Customer requested adding 5 additional boxes from attic"
                              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('unlock-modal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">
                        Confirm &amp; Unlock
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
