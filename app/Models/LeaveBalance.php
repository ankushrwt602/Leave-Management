<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'pending_days',
        'carried_forward_days',
        'adjustment_days',
        'notes',
    ];

    protected $casts = [
        'allocated_days' => 'decimal:1',
        'used_days' => 'decimal:1',
        'pending_days' => 'decimal:1',
        'carried_forward_days' => 'decimal:1',
        'adjustment_days' => 'decimal:1',
        'year' => 'integer',
    ];

    /**
     * Get the user that owns the leave balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type for this balance.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Scope to get balances for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get balances for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the current available balance (allocated - used - pending).
     */
    public function getAvailableBalance(): float
    {
        return $this->allocated_days + $this->adjustment_days - $this->used_days - $this->pending_days;
    }

    /**
     * Get the remaining balance for the year.
     */
    public function getRemainingBalance(): float
    {
        return max(0, $this->getAvailableBalance());
    }

    /**
     * Check if user has enough balance for the requested days.
     */
    public function hasEnoughBalance(float $requestedDays): bool
    {
        return $this->getRemainingBalance() >= $requestedDays;
    }

    /**
     * Use days from the balance.
     */
    public function useDays(float $days): void
    {
        $this->increment('used_days', $days);
    }

    /**
     * Add pending days (when a request is submitted).
     */
    public function addPendingDays(float $days): void
    {
        $this->increment('pending_days', $days);
    }

    /**
     * Remove pending days (when a request is cancelled or rejected).
     */
    public function removePendingDays(float $days): void
    {
        $this->decrement('pending_days', $days);
    }

    /**
     * Approve pending days (move from pending to used).
     */
    public function approvePendingDays(float $days): void
    {
        $this->increment('used_days', $days);
        $this->decrement('pending_days', $days);
    }

    /**
     * Carry forward days to next year.
     */
    public function carryForwardToNextYear(float $days): void
    {
        $this->increment('carried_forward_days', $days);
    }

    /**
     * Get or create balance for a specific user, leave type, and year.
     */
    public static function getOrCreateForUserAndYear(User $user, LeaveType $leaveType, int $year): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ],
            [
                'allocated_days' => 0, // Start with 0, admins can allocate specific amounts
            ]
        );
    }
}