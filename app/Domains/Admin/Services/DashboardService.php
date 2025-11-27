<?php

namespace App\Domains\Admin\Services;

use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Listings\Models\Service;
use App\Domains\Admin\Models\PlatformStat;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get today's platform statistics
     */
    public function getTodayStats()
    {
        return PlatformStat::where('date', today())->first() ?? $this->generateDailyStats(today());
    }

    /**
     * Get this week's platform statistics
     */
    public function getWeeklyStats()
    {
        return PlatformStat::whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->get();
    }

    /**
     * Get this month's platform statistics
     */
    public function getMonthlyStats()
    {
        return PlatformStat::whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->get();
    }

    /**
     * Generate daily statistics for a given date
     */
    public function generateDailyStats($date)
    {
        $date = Carbon::parse($date)->startOfDay();

        $stat = PlatformStat::firstOrCreate(['date' => $date->format('Y-m-d')], [
            'total_users' => User::count(),
            'active_users' => User::where('last_seen_at', '>=', $date)->count(),
            'new_users' => User::whereDate('created_at', $date)->count(),
            'total_listings' => Service::count(),
            'active_listings' => Service::where('status', 'active')->count(),
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'platform_fees_collected' => Order::where('payment_status', 'paid')->sum('total_amount') * 0.05,
            'average_order_value' => Order::where('payment_status', 'paid')->avg('total_amount') ?? 0,
        ]);

        return $stat;
    }

    /**
     * Get top performing sellers
     */
    public function getTopPerformers($limit = 5)
    {
        return User::withCount(['ordersAsSeller' => fn($q) => $q->where('status', 'completed')])
            ->orderBy('orders_as_seller_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders($limit = 5)
    {
        return Order::with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get order metrics
     */
    public function getOrderMetrics()
    {
        return [
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),
            'unpaid' => Order::where('payment_status', '!=', 'paid')->count(),
        ];
    }

    /**
     * Get user metrics
     */
    public function getUserMetrics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
            'sellers' => User::role('seller')->count(),
            'buyers' => User::role('buyer')->count(),
            'suspended' => User::whereNotNull('suspended_until')->where('suspended_until', '>', now())->count(),
        ];
    }

    /**
     * Get listing metrics
     */
    public function getListingMetrics()
    {
        return [
            'total_listings' => Service::count(),
            'active_listings' => Service::where('status', 'active')->count(),
            'inactive_listings' => Service::where('status', 'inactive')->count(),
            'avg_price' => Service::avg('price') ?? 0,
        ];
    }

    /**
     * Get revenue metrics
     */
    public function getRevenueMetrics()
    {
        $paidOrders = Order::where('payment_status', 'paid');
        $totalGMV = $paidOrders->sum('total_amount');
        $platformFees = $totalGMV * 0.05;

        return [
            'total_gmv' => $totalGMV,
            'total_platform_fees' => $platformFees,
            'total_seller_earnings' => $totalGMV - $platformFees,
            'average_order_value' => $paidOrders->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Get complete dashboard overview
     */
    public function getDashboardOverview()
    {
        return [
            'today_stats' => $this->getTodayStats(),
            'user_metrics' => $this->getUserMetrics(),
            'order_metrics' => $this->getOrderMetrics(),
            'listing_metrics' => $this->getListingMetrics(),
            'revenue_metrics' => $this->getRevenueMetrics(),
            'top_performers' => $this->getTopPerformers(),
            'recent_orders' => $this->getRecentOrders(),
        ];
    }

    /**
     * Get data for charts
     */
    public function getChartData(): array
    {
        $days = 30;
        $labels = [];
        $usersData = [];
        $ordersData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $usersData[] = User::whereDate('created_at', $date)->count();
            $ordersData[] = Order::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $usersData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'New Orders',
                    'data' => $ordersData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
