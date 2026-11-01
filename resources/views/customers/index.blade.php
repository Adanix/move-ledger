<x-layouts.app title="Customer Directory">
    <div x-data="{ createModalOpen: false }" class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Header & Search Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Customer Directory</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manage shippers, view contact profiles, and trace historical relocation manifests.
                </p>
            </div>

            @can('create', App\Models\Customer::class)
            <button @click="createModalOpen = true"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors flex items-center justify-center gap-1.5 min-h-11 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Add Customer</span>
            </button>
            @endcan
        </div>

        <!-- Search Bar -->
        <form action="{{ route('customers.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search customers by name, email, or phone..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold min-h-11 cursor-pointer">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('customers.index') }}" class="px-3 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold min-h-11 flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Customers Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Customer Name</th>
                            <th class="py-3 px-4">Contact Info</th>
                            <th class="py-3 px-4 text-center">Moves</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <a href="{{ route('customers.show', $customer) }}" class="font-bold text-slate-900 hover:text-blue-600 block text-sm">
                                        {{ $customer->name }}
                                    </a>
                                    @if($customer->notes)
                                        <span class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $customer->notes }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 space-y-1">
                                    <div class="flex items-center gap-1.5 text-slate-700 font-mono">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <a href="tel:{{ $customer->phone }}" class="hover:underline">{{ $customer->phone }}</a>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-800">
                                        {{ $customer->moves_count }} {{ Str::plural('job', $customer->moves_count) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('customers.show', $customer) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                            Profile
                                        </a>
                                        <a href="{{ route('moves.create', ['customer_id' => $customer->id]) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition-colors">
                                            New Move
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    No customers found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE CUSTOMER MODAL -->
        <div x-show="createModalOpen"
             x-cloak
             @click.self="createModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Add New Customer</h2>
                    <button @click="createModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Full Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. Eleanor Vance"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Primary Phone <span class="text-rose-600">*</span>
                            </label>
                            <input type="tel" name="phone" required placeholder="(555) 234-5678"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Secondary Phone
                            </label>
                            <input type="tel" name="secondary_phone" placeholder="Optional"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Email Address <span class="text-rose-600">*</span>
                        </label>
                        <input type="email" name="email" required placeholder="eleanor.vance@example.com"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Customer Notes
                        </label>
                        <textarea name="notes" rows="2" placeholder="Account preferences, gate codes, etc."
                                  class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button @click="createModalOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold">
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
