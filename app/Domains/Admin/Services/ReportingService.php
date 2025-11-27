<?php

namespace App\Domains\Admin\Services;

use App\Domains\Admin\Models\FinancialReport;
use App\Domains\Admin\Models\UserReport;
use App\Models\User;
use App\Domains\Orders\Models\Order;
use Carbon\Carbon;

class ReportingService
{
    public function generateMonthlyFinancialReport($month = null, $year = null)
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
        
        return FinancialReport::generateReport($startDate, $endDate);
    }

    public function generateWeeklyFinancialReport($weekStartDate = null)
    {
        $weekStartDate = $weekStartDate ?? Carbon::now()->startOfWeek();
        $weekEndDate = $weekStartDate->copy()->endOfWeek();
        
        return FinancialReport::generateReport($weekStartDate, $weekEndDate);
    }

    public function getFinancialReports($limit = 12)
    {
        return FinancialReport::latest('report_period_start')
            ->limit($limit)
            ->get();
    }

    public function getUserReports($status = null, $limit = 20)
    {
        $query = UserReport::with(['reportedUser', 'reporter']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->latest()->limit($limit)->get();
    }

    public function getPendingReports()
    {
        return UserReport::where('status', 'pending')
            ->with(['reportedUser', 'reporter'])
            ->latest()
            ->get();
    }

    public function getReportedUserStats($userId)
    {
        return [
            'total_reports' => UserReport::where('reported_user_id', $userId)->count(),
            'pending_reports' => UserReport::where('reported_user_id', $userId)->where('status', 'pending')->count(),
            'resolved_reports' => UserReport::where('reported_user_id', $userId)->where('status', 'resolved')->count(),
            'dismissed_reports' => UserReport::where('reported_user_id', $userId)->where('status', 'dismissed')->count(),
        ];
    }

    public function getReportReasonDistribution()
    {
        $reports = UserReport::select('reason')->get();
        
        $distribution = [];
        foreach ($reports as $report) {
            $reason = $report->reason;
            $distribution[$reason] = ($distribution[$reason] ?? 0) + 1;
        }
        
        return $distribution;
    }

    public function createUserReport($reportedUserId, $reporterId, $reason, $description)
    {
        return UserReport::create([
            'reported_user_id' => $reportedUserId,
            'reporter_id' => $reporterId,
            'reason' => $reason,
            'description' => $description,
            'status' => 'pending',
        ]);
    }

    public function resolveReport($reportId, $notes = null)
    {
        $report = UserReport::find($reportId);
        
        if ($report) {
            $report->resolve($notes);
        }
        
        return $report;
    }

    public function dismissReport($reportId, $notes = null)
    {
        $report = UserReport::find($reportId);
        
        if ($report) {
            $report->dismiss($notes);
        }
        
        return $report;
    }

    public function getSuspiciousUsers($threshold = 3)
    {
        return User::whereHas('reportedByUsers', function ($query) use ($threshold) {
            $query->where('status', 'pending');
        })
            ->withCount('reportedByUsers')
            ->having('reported_by_users_count', '>=', $threshold)
            ->get();
    }

    public function getRevenueByDateRange($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->get();
        
        return [
            'total_gmv' => $orders->sum('total_amount'),
            'total_fees' => $orders->sum(function ($order) {
                return ($order->platform_fee_percentage / 100) * $order->total_amount;
            }),
            'order_count' => $orders->count(),
        ];
    }

    public function getTopReportReasons()
    {
        $distribution = $this->getReportReasonDistribution();
        arsort($distribution);
        
        return array_slice($distribution, 0, 5, true);
    }

    public function getReportTrends($days = 30)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days);
        
        $reports = UserReport::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();
        
        return $reports;
    }
}
