<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $overview = $this->dashboardService->getDashboardOverview();
        $chartData = $this->dashboardService->getChartData();
        
        return view('admin.dashboard', [
            'users' => $overview['user_metrics'],
            'orders' => $overview['order_metrics'],
            'listings' => $overview['listing_metrics'],
            'revenue' => $overview['revenue_metrics'],
            'today_stats' => $overview['today_stats'],
            'top_performers' => $overview['top_performers'],
            'recent_orders' => $overview['recent_orders'],
            'chartData' => $chartData,
        ]);
    }
}
