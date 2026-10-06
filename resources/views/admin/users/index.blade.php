<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ __('User Registration Approvals & Management') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Review, approve, or manage registered user accounts in LunarC system.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200 rounded-r-xl shadow-sm">
                    <ul class="list-disc list-inside text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pending Approvals Card -->
                <div class="p-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Pending Approvals</span>
                        <span class="text-3xl font-black text-amber-600 dark:text-amber-300">{{ $pendingCount }}</span>
                    </div>
                    <div class="p-3 bg-amber-500 text-white rounded-xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Approved Active Users Card -->
                <div class="p-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Approved Accounts</span>
                        <span class="text-3xl font-black text-emerald-600 dark:text-emerald-300">{{ $approvedCount }}</span>
                    </div>
                    <div class="p-3 bg-emerald-600 text-white rounded-xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Total Registered Users Card -->
                <div class="p-6 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider block">Total Registered</span>
                        <span class="text-3xl font-black text-indigo-600 dark:text-indigo-300">{{ $users->total() }}</span>
                    </div>
                    <div class="p-3 bg-indigo-600 text-white rounded-xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Name / Email</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Approval Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500">
                            <option value="">All Users</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval Only ({{ $pendingCount }})</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved Users Only</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow transition">
                            Apply Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users List Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">User Details</th>
                                <th class="px-6 py-4">Registered Date</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Approval Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($users as $u)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">(You)</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $u->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium">
                                        {{ $u->created_at->format('M d, Y h:i A') }}
                                        <div class="text-[10px] text-gray-400">({{ $u->created_at->diffForHumans() }})</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($u->is_admin)
                                            <span class="px-2.5 py-1 text-xs font-extrabold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 rounded-full border border-purple-300 dark:border-purple-700">
                                                👑 Admin
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($u->is_approved)
                                            <span class="px-3 py-1 text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 rounded-full border border-emerald-300 dark:border-emerald-700">
                                                ✓ Approved
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-200 rounded-full border border-amber-300 dark:border-amber-700 animate-pulse">
                                                ⏳ Pending Approval
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if(!$u->is_approved)
                                            <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow transition">
                                                    ✓ Approve User
                                                </button>
                                            </form>
                                        @endif

                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-admin', $u->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 hover:bg-indigo-100 font-bold text-xs rounded-xl transition">
                                                    {{ $u->is_admin ? 'Demote User' : 'Make Admin' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline-block" data-confirm="Are you sure you want to reject and delete user account {{ $u->name }}?" data-title="Reject / Delete User Account" data-type="red" data-btn="Reject User">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 hover:bg-rose-100 font-bold text-xs rounded-xl transition">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        No user registrations found matching your query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
