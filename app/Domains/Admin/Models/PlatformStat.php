<?php

namespace App\Domains\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformStat extends Model
{
    protected $table = 'platform_stats';
    protected $fillable = [
        'date',
        'total_users',
        'active_users',
        'new_users',
        'total_listings',
        'active_listings',
        'total_orders',
        'completed_orders',
        'total_revenue',
        'platform_fees_collected',
        'average_order_value',
    ];

    protected $casts = [
        'date' => 'date',
        'total_users' => 'integer',
        'active_users' => 'integer',
        'new_users' => 'integer',
        'total_listings' => 'integer',
        'active_listings' => 'integer',
        'total_orders' => 'integer',
        'completed_orders' => 'integer',
        'total_revenue' => 'decimal:2',
        'platform_fees_collected' => 'decimal:2',
        'average_order_value' => 'decimal:2',
    ];

    public function getFormattedRevenue()
    {
        return '$' . number_format($this->total_revenue, 2);
    }

    public function getFormattedFees()
    {
        return '$' . number_format($this->platform_fees_collected, 2);
    }

    public function getFormattedAOV()
    {
        return '$' . number_format($this->average_order_value, 2);
    }

    public function getCompletionRate()
    {
        if ($this->total_orders == 0) return 0;
        return round(($this->completed_orders / $this->total_orders) * 100, 2);
    }

    public function getActiveUserPercentage()
    {
        if ($this->total_users == 0) return 0;
        return round(($this->active_users / $this->total_users) * 100, 2);
    }

    public function getListingUtilizationRate()
    {
        if ($this->total_listings == 0) return 0;
        return round(($this->active_listings / $this->total_listings) * 100, 2);
    }
}
