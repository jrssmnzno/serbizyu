<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Weekly Stats -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">This Week's Performance</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-600 text-sm">Weekly Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $weekly_stats->sum('total_users') ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Active: {{ $weekly_stats->sum('active_users') ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Weekly Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $weekly_stats->sum('total_orders') ?? 0 }}</p>
                            <p class="text-xs text-green-600 mt-1">
                                Completed: {{ $weekly_stats->sum('completed_orders') ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Weekly Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">
                                ${{ number_format($weekly_stats->sum('total_revenue') ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-purple-600 mt-1">
                                Fees: ${{ number_format($weekly_stats->sum('platform_fees_collected') ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Stats -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month's Performance</h3>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-600 text-sm">Monthly Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $monthly_stats->sum('total_users') ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                New: {{ $monthly_stats->sum('new_users') ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Monthly Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $monthly_stats->sum('total_orders') ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Listings: {{ $monthly_stats->sum('total_listings') ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Monthly Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">
                                ${{ number_format($monthly_stats->sum('total_revenue') ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                AOV: ${{ number_format($monthly_stats->avg('average_order_value') ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Order Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Breakdown</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pending</span>
                            <span class="font-bold text-gray-900">{{ $order_metrics['pending'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">In Progress</span>
                            <span class="font-bold text-gray-900">{{ $order_metrics['in_progress'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Completed</span>
                            <span class="font-bold text-green-600">{{ $order_metrics['completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Cancelled</span>
                            <span class="font-bold text-red-600">{{ $order_metrics['cancelled'] ?? 0 }}</span>
                        </div>
                        <hr class="my-3" />
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Paid Orders</span>
                            <span class="font-bold text-gray-900">{{ $order_metrics['paid'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Unpaid Orders</span>
                            <span class="font-bold text-gray-900">{{ $order_metrics['unpaid'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- User Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Demographics</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Users</span>
                            <span class="font-bold text-gray-900">{{ $user_metrics['total_users'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Users</span>
                            <span class="font-bold text-green-600">{{ $user_metrics['active_users'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Sellers</span>
                            <span class="font-bold text-gray-900">{{ $user_metrics['sellers'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Buyers</span>
                            <span class="font-bold text-gray-900">{{ $user_metrics['buyers'] ?? 0 }}</span>
                        </div>
                        <hr class="my-3" />
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Suspended Users</span>
                            <span class="font-bold text-red-600">{{ $user_metrics['suspended'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Listing Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Listing Analytics</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Listings</span>
                            <span class="font-bold text-gray-900">{{ $listing_metrics['total_listings'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Listings</span>
                            <span class="font-bold text-green-600">{{ $listing_metrics['active_listings'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Inactive Listings</span>
                            <span class="font-bold text-gray-900">{{ $listing_metrics['inactive_listings'] ?? 0 }}</span>
                        </div>
                        <hr class="my-3" />
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Avg Price</span>
                            <span class="font-bold text-gray-900">
                                ${{ number_format($listing_metrics['avg_price'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Revenue Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Analytics</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total GMV</span>
                            <span class="font-bold text-purple-600">
                                ${{ number_format($revenue_metrics['total_gmv'] ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Platform Fees (5%)</span>
                            <span class="font-bold text-green-600">
                                ${{ number_format($revenue_metrics['total_platform_fees'] ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Seller Earnings</span>
                            <span class="font-bold text-blue-600">
                                ${{ number_format($revenue_metrics['total_seller_earnings'] ?? 0, 2) }}
                            </span>
                        </div>
                        <hr class="my-3" />
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Avg Order Value</span>
                            <span class="font-bold text-gray-900">
                                ${{ number_format($revenue_metrics['average_order_value'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
