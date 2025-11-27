<?php

namespace App\Domains\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserReport extends Model
{
    protected $table = 'user_reports';
    protected $fillable = [
        'reported_user_id',
        'reporter_id',
        'reason',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isDismissed()
    {
        return $this->status === 'dismissed';
    }

    public function resolve($notes = null)
    {
        $this->status = 'resolved';
        $this->admin_notes = $notes;
        $this->resolved_at = now();
        $this->save();

        return $this;
    }

    public function dismiss($notes = null)
    {
        $this->status = 'dismissed';
        $this->admin_notes = $notes;
        $this->resolved_at = now();
        $this->save();

        return $this;
    }

    public static function getReasonOptions()
    {
        return [
            'inappropriate_content' => 'Inappropriate Content',
            'scam_fraud' => 'Scam/Fraud',
            'harassment' => 'Harassment',
            'fake_profile' => 'Fake Profile',
            'payment_issue' => 'Payment Issue',
            'quality_issue' => 'Quality Issue',
            'other' => 'Other',
        ];
    }

    public static function getReasonLabel($reason)
    {
        return self::getReasonOptions()[$reason] ?? $reason;
    }
}
