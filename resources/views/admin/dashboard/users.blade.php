<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $user_stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Sellers</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $user_stats['sellers'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Buyers</p>
                    <p class="text-3xl font-bold text-green-600">{{ $user_stats['buyers'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Active Today</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $user_stats['active_today'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search by name or email..." 
                        value="{{ request('search') }}" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Search
                    </button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Joined</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Orders</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $user->firstname }} {{ $user->lastname }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($user->hasRole('seller'))
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded font-semibold">Seller</span>
                                    @endif
                                    @if($user->hasRole('buyer'))
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold">Buyer</span>
                                    @endif
                                    @if($user->hasRole('admin'))
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded font-semibold">Admin</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($user->suspended_until && $user->suspended_until > now())
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded font-semibold">
                                            Suspended
                                        </span>
                                    @elseif($user->email_verified_at)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded font-semibold">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->orders_as_buyer_count + $user->orders_as_seller_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 mr-2">View</a>
                                    @if($user->suspended_until && $user->suspended_until > now())
                                        <form action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">Unsuspend</button>
                                        </form>
                                    @else
                                        <button onclick="openSuspendModal({{ $user->id }})" class="text-red-600 hover:text-red-800">Suspend</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No users found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Suspend User Account</h3>
            <form id="suspendForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea name="reason" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter reason for suspension..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (days)</label>
                    <input type="number" name="duration_days" min="1" max="365" value="7" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Suspend User
                    </button>
                    <button type="button" onclick="closeSuspendModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSuspendModal(userId) {
            const form = document.getElementById('suspendForm');
            form.action = `/admin/users/${userId}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
