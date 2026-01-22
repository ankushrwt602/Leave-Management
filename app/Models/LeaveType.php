<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'default_days_per_year',
        'max_consecutive_days',
        'requires_approval',
        'is_active',
        'description',
        'carry_forward_rules',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'default_days_per_year' => 'integer',
        'max_consecutive_days' => 'integer',
        'carry_forward_rules' => 'array',
    ];

    /**
     * Get all leave requests for this leave type.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get all leave balances for this leave type.
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Scope to get only active leave types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this leave type requires approval.
     */
    public function requiresApproval(): bool
    {
        return $this->requires_approval;
    }

    /**
     * Get the maximum consecutive days allowed for this leave type.
     */
    public function getMaxConsecutiveDays(): ?int
    {
        return $this->max_consecutive_days;
    }
}