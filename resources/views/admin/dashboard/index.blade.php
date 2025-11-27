<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600">
                Last updated: {{ now()->format('M d, Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-600 text-sm">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $users['total_users'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $users['active_today'] ?? 0 }} active today</p>
                            </div>
                            <div class="text-4xl text-blue-500">👥</div>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-600 text-sm">Total Orders</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $orders['total_orders'] ?? 0 }}</p>
                                <p class="text-xs text-green-600 mt-2">{{ $orders['completed'] ?? 0 }} completed</p>
                            </div>
                            <div class="text-4xl text-green-500">📦</div>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-600 text-sm">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900">
                                    ${{ number_format($revenue['total_gmv'] ?? 0, 2) }}
                                </p>
                                <p class="text-xs text-purple-600 mt-2">
                                    Fees: ${{ number_format($revenue['total_platform_fees'] ?? 0, 2) }}
                                </p>
                            </div>
                            <div class="text-4xl text-purple-500">💰</div>
                        </div>
                    </div>
                </div>

                <!-- Listings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-600 text-sm">Active Listings</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $listings['active_listings'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-2">of {{ $listings['total_listings'] ?? 0 }} total</p>
                            </div>
                            <div class="text-4xl text-orange-500">📋</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Analytics Card -->
                <a href="{{ route('admin.analytics') }}" class="hover:shadow-lg transition">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">📊 Analytics</h3>
                        <p class="text-gray-600 text-sm">View detailed platform analytics and performance metrics</p>
                        <div class="mt-4 text-blue-600 font-medium">View Analytics →</div>
                    </div>
                </a>

                <!-- Financial Reports Card -->
                <a href="{{ route('admin.financial-reports') }}" class="hover:shadow-lg transition">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">💹 Financial Reports</h3>
                        <p class="text-gray-600 text-sm">Generate and view monthly/weekly financial reports</p>
                        <div class="mt-4 text-green-600 font-medium">View Reports →</div>
                    </div>
                </a>

                <!-- User Management Card -->
                <a href="{{ route('admin.users') }}" class="hover:shadow-lg transition">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">👨‍💼 User Management</h3>
                        <p class="text-gray-600 text-sm">Manage users, view details, and handle suspensions</p>
                        <div class="mt-4 text-blue-600 font-medium">Manage Users →</div>
                    </div>
                </a>

                <!-- Dispute Resolution Card -->
                <a href="{{ route('admin.disputes') }}" class="hover:shadow-lg transition">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">⚖️ Dispute Resolution</h3>
                        <p class="text-gray-600 text-sm">Review and resolve user reports and disputes</p>
                        <div class="mt-4 text-red-600 font-medium">View Disputes →</div>
                    </div>
                </a>
            </div>

            <!-- Top Performers & Recent Orders -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Performers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🌟 Top Performers</h3>
                        <div class="space-y-3">
                            @forelse($top_performers as $performer)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $performer->firstname }} {{ $performer->lastname }}</p>
                                        <p class="text-xs text-gray-500">{{ $performer->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">{{ $performer->orders_as_seller_count }}</p>
                                        <p class="text-xs text-gray-500">sales</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No performers yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Recent Orders</h3>
                        <div class="space-y-3">
                            @forelse($recent_orders as $order)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-medium text-gray-900">Order #{{ $order->id }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->buyer->firstname }} → {{ $order->seller->firstname }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                        <span class="inline-block px-2 py-1 text-xs rounded {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No orders yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
