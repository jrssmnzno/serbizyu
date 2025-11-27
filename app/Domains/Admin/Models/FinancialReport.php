<?php

namespace App\Domains\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Orders\Models\Order;

class FinancialReport extends Model
{
    protected $table = 'financial_reports';
    protected $fillable = [
        'report_period_start',
        'report_period_end',
        'total_gmv',
        'total_platform_fees',
        'total_seller_earnings',
        'total_refunds',
        'net_platform_revenue',
        'status',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'total_gmv' => 'decimal:2',
        'total_platform_fees' => 'decimal:2',
        'total_seller_earnings' => 'decimal:2',
        'total_refunds' => 'decimal:2',
        'net_platform_revenue' => 'decimal:2',
    ];

    public function getFormattedGMV()
    {
        return '$' . number_format($this->total_gmv, 2);
    }

    public function getFormattedFees()
    {
        return '$' . number_format($this->total_platform_fees, 2);
    }

    public function getFormattedEarnings()
    {
        return '$' . number_format($this->total_seller_earnings, 2);
    }

    public function getFormattedRefunds()
    {
        return '$' . number_format($this->total_refunds, 2);
    }

    public function getFormattedNetRevenue()
    {
        return '$' . number_format($this->net_platform_revenue, 2);
    }

    public function getFeePercentage()
    {
        if ($this->total_gmv == 0) return 0;
        return round(($this->total_platform_fees / $this->total_gmv) * 100, 2);
    }

    public function getPeriodLabel()
    {
        return $this->report_period_start->format('M d, Y') . ' - ' . $this->report_period_end->format('M d, Y');
    }

    public function isComplete()
    {
        return $this->status === 'completed';
    }

    public static function generateReport($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->where('payment_status', 'paid')->get();
        
        $totalGMV = $orders->sum('total_amount');
        $totalPlatformFees = $orders->sum('platform_fee') ?? 0;
        $totalSellerEarnings = $orders->sum(function ($order) {
            return $order->total_amount - ($order->platform_fee ?? 0);
        });
        $totalRefunds = $orders->where('payment_status', 'refunded')->sum('total_amount');
        
        return self::create([
            'report_period_start' => $startDate,
            'report_period_end' => $endDate,
            'total_gmv' => $totalGMV,
            'total_platform_fees' => $totalPlatformFees,
            'total_seller_earnings' => $totalSellerEarnings,
            'total_refunds' => $totalRefunds,
            'net_platform_revenue' => $totalPlatformFees - $totalRefunds,
            'status' => 'completed',
        ]);
    }
}
