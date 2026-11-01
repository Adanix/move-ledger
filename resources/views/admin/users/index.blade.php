<x-layouts.app title="Staff Accounts Management">
    <div x-data="{
        createModalOpen: false,
        editModalOpen: false,
        editUser: {
            id: null,
            name: '',
            email: '',
            phone: '',
            role: 'surveyor',
            is_active: true
        },
        openEdit(user) {
            this.editUser = { ...user };
            this.editModalOpen = true;
        }
    }" class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

        <!-- Top Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Staff Accounts</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manage system access, assign roles (Administrator, Coordinator, Surveyor), and deactivate staff accounts.
                </p>
            </div>

            <button @click="createModalOpen = true"
                    type="button"
                    class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors flex items-center justify-center gap-1.5 min-h-11 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Add Staff Member</span>
            </button>
        </div>

        <!-- Filter & Search Controls -->
        <div class="space-y-3">
            <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                @if($selectedRole)
                    <input type="hidden" name="role" value="{{ $selectedRole }}">
                @endif
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search staff by name, email, or phone..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold min-h-11 cursor-pointer">
                        Search
                    </button>
                    @if($search || $selectedRole)
                        <a href="{{ route('users.index') }}" class="px-3 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold min-h-11 flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Role Pills Filter -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
                <a href="{{ route('users.index', array_filter(['search' => $search])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ empty($selectedRole) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    All Roles
                </a>
                @foreach($roles as $role)
                    <a href="{{ route('users.index', array_filter(['role' => $role->value, 'search' => $search])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $selectedRole === $role->value ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                        {{ $role->label() }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Staff Member</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4">System Role</th>
                            <th class="py-3 px-4 text-center">Assigned Moves</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono">
                                    {{ $user->phone ?: '—' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $user->role->badgeClass() }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                        {{ $user->assigned_moves_count }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="openEdit({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                phone: '{{ addslashes($user->phone ?? '') }}',
                                                role: '{{ $user->role->value }}',
                                                is_active: {{ $user->is_active ? 'true' : 'false' }}
                                            })"
                                            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No staff members found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE STAFF MODAL -->
        <div x-show="createModalOpen"
             x-cloak
             @click.self="createModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Add New Staff Member</h2>
                    <button @click="createModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Full Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. Sarah Jenkins"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Email Address <span class="text-rose-600">*</span>
                            </label>
                            <input type="email" name="email" required placeholder="sarah@moveledger.test"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" placeholder="(555) 012-3456"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            System Role <span class="text-rose-600">*</span>
                        </label>
                        <select name="role" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Password <span class="text-rose-600">*</span>
                            </label>
                            <input type="password" name="password" required placeholder="Min 8 characters"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Confirm Password <span class="text-rose-600">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required placeholder="Repeat password"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600">
                            <span class="text-xs font-medium text-slate-700">Account is Active (Can log in)</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button @click="createModalOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold">
                            Create Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT STAFF MODAL -->
        <div x-show="editModalOpen"
             x-cloak
             @click.self="editModalOpen = false"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Edit Staff Account</h2>
                    <button @click="editModalOpen = false" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/users/' + editUser.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Full Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" x-model="editUser.name" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Email Address <span class="text-rose-600">*</span>
                            </label>
                            <input type="email" name="email" x-model="editUser.email" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" x-model="editUser.phone" placeholder="(555) 012-3456"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            System Role <span class="text-rose-600">*</span>
                        </label>
                        <select name="role" x-model="editUser.role" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600">
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600 block">Change Password (leave blank to keep current)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">New Password</label>
                                <input type="password" name="password" placeholder="New password"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation" placeholder="Repeat new password"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editUser.is_active" class="rounded border-slate-300 text-emerald-600">
                            <span class="text-xs font-medium text-slate-700">Account is Active (Can log in)</span>
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
