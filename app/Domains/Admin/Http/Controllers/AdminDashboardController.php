<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Admin\Services\DashboardService;
use App\Domains\Admin\Services\ReportingService;
use App\Domains\Admin\Models\FinancialReport;
use App\Domains\Admin\Models\UserReport;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Listings\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $dashboardService;
    protected $reportingService;

    public function __construct(DashboardService $dashboardService, ReportingService $reportingService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        
        $this->dashboardService = $dashboardService;
        $this->reportingService = $reportingService;
    }

    public function index()
    {
        $overview = $this->dashboardService->getDashboardOverview();
        
        return view('admin.dashboard.index', $overview);
    }

    public function analytics()
    {
        $weeklyStats = $this->dashboardService->getWeeklyStats();
        $monthlyStats = $this->dashboardService->getMonthlyStats();
        
        $userMetrics = $this->dashboardService->getUserMetrics();
        $orderMetrics = $this->dashboardService->getOrderMetrics();
        $listingMetrics = $this->dashboardService->getListingMetrics();
        $revenueMetrics = $this->dashboardService->getRevenueMetrics();
        
        return view('admin.dashboard.analytics', [
            'weeklyStats' => $weeklyStats,
            'monthlyStats' => $monthlyStats,
            'users' => $userMetrics,
            'orders' => $orderMetrics,
            'listings' => $listingMetrics,
            'revenue' => $revenueMetrics,
        ]);
    }

    public function financialReports()
    {
        $reports = $this->reportingService->getFinancialReports();
        
        return view('admin.dashboard.financial-reports', compact('reports'));
    }

    public function generateFinancialReport(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $report = $this->reportingService->generateMonthlyFinancialReport($month, $year);
        
        return back()->with('success', 'Financial report generated successfully.');
    }

    public function userManagement()
    {
        $users = User::with('roles')
            ->paginate(20);
        
        $stats = [
            'total' => User::count(),
            'sellers' => User::role('seller')->count(),
            'buyers' => User::role('buyer')->count(),
            'active_today' => User::where('last_seen_at', '>=', Carbon::today())->count(),
        ];
        
        return view('admin.dashboard.users', compact('users', 'stats'));
    }

    public function userDetails($userId)
    {
        $user = User::with(['orders', 'roles'])->findOrFail($userId);
        
        $stats = [
            'total_orders_as_buyer' => Order::where('buyer_id', $userId)->count(),
            'total_orders_as_seller' => Order::where('seller_id', $userId)->count(),
            'total_revenue_generated' => Order::where('seller_id', $userId)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'total_spent' => Order::where('buyer_id', $userId)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'reviews_received' => $user->receivedReviews()->count() ?? 0,
            'average_rating' => $user->receivedReviews()->avg('rating') ?? 0,
        ];
        
        $reports = UserReport::where('reported_user_id', $userId)->get();
        
        return view('admin.dashboard.user-details', compact('user', 'stats', 'reports'));
    }

    public function disputeResolution()
    {
        $pendingReports = $this->reportingService->getPendingReports();
        $allReports = $this->reportingService->getUserReports(null, 50);
        
        $stats = [
            'pending' => UserReport::where('status', 'pending')->count(),
            'resolved' => UserReport::where('status', 'resolved')->count(),
            'dismissed' => UserReport::where('status', 'dismissed')->count(),
        ];
        
        $topReasons = $this->reportingService->getTopReportReasons();
        
        return view('admin.dashboard.dispute-resolution', [
            'pending_reports' => $pendingReports,
            'all_reports' => $allReports,
            'stats' => $stats,
            'top_reasons' => $topReasons,
        ]);
    }

    public function reportDetails($reportId)
    {
        $report = UserReport::with(['reportedUser', 'reporter'])->findOrFail($reportId);
        
        $userStats = $this->reportingService->getReportedUserStats($report->reported_user_id);
        $reasonOptions = UserReport::getReasonOptions();
        
        return view('admin.dashboard.report-details', compact('report', 'userStats', 'reasonOptions'));
    }

    public function resolveReport(Request $request, $reportId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $this->reportingService->resolveReport($reportId, $request->input('notes'));
        
        return back()->with('success', 'Report resolved successfully.');
    }

    public function dismissReport(Request $request, $reportId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $this->reportingService->dismissReport($reportId, $request->input('notes'));
        
        return back()->with('success', 'Report dismissed successfully.');
    }

    public function suspendUser(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'duration_days' => 'required|integer|min:1|max:365',
        ]);
        
        $user = User::findOrFail($userId);
        $user->suspended_until = Carbon::now()->addDays($request->input('duration_days'));
        $user->suspension_reason = $request->input('reason');
        $user->save();
        
        return back()->with('success', "User suspended for {$request->input('duration_days')} days.");
    }

    public function unsuspendUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->suspended_until = null;
        $user->suspension_reason = null;
        $user->save();
        
        return back()->with('success', 'User unsuspended successfully.');
    }

    public function listingManagement()
    {
        $listings = Service::with('creator')
            ->paginate(20);
        
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('status', 'active')->count(),
            'inactive' => Service::where('status', 'inactive')->count(),
            'average_price' => Service::avg('price'),
        ];
        
        return view('admin.dashboard.listings', compact('listings', 'stats'));
    }

    public function deactivateListing($listingId)
    {
        $listing = Service::findOrFail($listingId);
        $listing->status = 'inactive';
        $listing->save();
        
        return back()->with('success', 'Listing deactivated successfully.');
    }

    public function reactivateListing($listingId)
    {
        $listing = Service::findOrFail($listingId);
        $listing->status = 'active';
        $listing->save();
        
        return back()->with('success', 'Listing reactivated successfully.');
    }

    public function exportFinancialReport($reportId)
    {
        $report = FinancialReport::findOrFail($reportId);
        
        $csv = "Financial Report\n";
        $csv .= "Period: {$report->getPeriodLabel()}\n\n";
        $csv .= "Total GMV,{$report->getFormattedGMV()}\n";
        $csv .= "Platform Fees,{$report->getFormattedFees()}\n";
        $csv .= "Seller Earnings,{$report->getFormattedEarnings()}\n";
        $csv .= "Total Refunds,{$report->getFormattedRefunds()}\n";
        $csv .= "Net Revenue,{$report->getFormattedNetRevenue()}\n";
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"financial_report_{$reportId}.csv\"");
    }
}
