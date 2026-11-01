<x-layouts.app title="Schedule New Move">
    <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto w-full space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('moves.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Moves
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Schedule New Relocation Job</h1>
            </div>
        </div>

        <form action="{{ route('moves.store') }}" method="POST"
              x-data="{ isNewCustomer: false }"
              class="space-y-6">
            @csrf

            <!-- Card 1: Customer Details -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">1. Customer Information</h2>
                    <button type="button" @click="isNewCustomer = !isNewCustomer"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        <span x-text="isNewCustomer ? 'Select Existing Customer' : '+ Add New Customer'"></span>
                    </button>
                </div>

                <!-- Existing Customer Select -->
                <div x-show="!isNewCustomer">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Select Existing Customer <span class="text-rose-600">*</span>
                    </label>
                    <select name="customer_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">-- Choose registered customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->phone }}) &mdash; {{ $c->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Inline New Customer Fields -->
                <div x-show="isNewCustomer" x-cloak class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Customer / Company Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               placeholder="e.g. John Doe"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        @error('customer_name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Email Address <span class="text-rose-600">*</span>
                            </label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                   placeholder="customer@example.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @error('customer_email')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Primary Phone Number <span class="text-rose-600">*</span>
                            </label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                                   placeholder="+1 (555) 000-0000"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @error('customer_phone')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Property Type & Staff Assignment -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
                    2. Relocation Specifications
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Property Type Template <span class="text-rose-600">*</span>
                        </label>
                        <select name="property_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @foreach($propertyTypes as $pt)
                                <option value="{{ $pt->value }}" {{ old('property_type', 'house_3br') === $pt->value ? 'selected' : '' }}>
                                    {{ $pt->label() }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-slate-400">Auto-seeds standard rooms upon creation.</p>
                        @error('property_type')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Scheduled Move Date <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" name="scheduled_move_date" value="{{ old('scheduled_move_date', now()->addDays(2)->toDateString()) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 font-mono">
                        @error('scheduled_move_date')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Assigned Field Surveyor <span class="text-rose-600">*</span>
                        </label>
                        <select name="assigned_surveyor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            @foreach($surveyors as $s)
                                <option value="{{ $s->id }}" {{ old('assigned_surveyor_id', auth()->id()) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ $s->role->label() }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_surveyor_id')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card 3: Origin and Destination Addresses -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
                    3. Addresses &amp; Access Notes
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Origin -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Origin (Pickup) Address <span class="text-rose-600">*</span>
                            </label>
                            <textarea name="origin_address" rows="3" required placeholder="Full street address, city, state, zip..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">{{ old('origin_address') }}</textarea>
                            @error('origin_address')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Origin Floor &amp; Access Details
                            </label>
                            <input type="text" name="origin_floor_access" value="{{ old('origin_floor_access') }}"
                                   placeholder="e.g. 2nd Floor, stairs only, long carry 100ft"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Destination -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Destination (Delivery) Address <span class="text-rose-600">*</span>
                            </label>
                            <textarea name="destination_address" rows="3" required placeholder="Full delivery address, city, state, zip..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">{{ old('destination_address') }}</textarea>
                            @error('destination_address')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Destination Floor &amp; Access Details
                            </label>
                            <input type="text" name="destination_floor_access" value="{{ old('destination_floor_access') }}"
                                   placeholder="e.g. Ground floor single family home, wide driveway"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        General Instructions / Move Notes
                    </label>
                    <textarea name="notes" rows="2" placeholder="Special handling requirements, parking permits, gating codes..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('moves.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm transition-colors min-h-12 flex items-center">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md active:scale-[0.99] transition-transform min-h-12 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Create Job &amp; Seed Rooms</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
