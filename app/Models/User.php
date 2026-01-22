<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all leave requests for this user.
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get all leave balances for this user.
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Get approved leave requests for this user.
     */
    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class)->where('status', 'approved');
    }

    /**
     * Get pending leave requests for this user.
     */
    public function pendingLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class)->where('status', 'pending');
    }

    /**
     * Get leave requests that this user can approve (if they are a manager/admin).
     */
    public function approvableLeaveRequests()
    {
        // This would depend on your business logic for who can approve what
        // For now, we'll assume admins can approve all requests
        if ($this->isAdmin()) {
            return LeaveRequest::where('status', 'pending');
        }

        // Add more complex logic here based on department, role, etc.
        return LeaveRequest::where('status', 'pending')
            ->where('user_id', '!=', $this->id); // Can't approve own requests
    }

    /**
     * Get leave balance for a specific leave type and year.
     */
    public function getLeaveBalance(LeaveType $leaveType, int $year = null): ?LeaveBalance
    {
        $year = $year ?? now()->year;

        return $this->leaveBalances()
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $year)
            ->first();
    }

    /**
     * Get or create leave balance for a specific leave type and year.
     */
    public function getOrCreateLeaveBalance(LeaveType $leaveType, int $year = null): LeaveBalance
    {
        $year = $year ?? now()->year;

        return LeaveBalance::getOrCreateForUserAndYear($this, $leaveType, $year);
    }

    /**
     * Check if user has enough leave balance for requested days.
     */
    public function hasEnoughLeaveBalance(LeaveType $leaveType, float $requestedDays, int $year = null): bool
    {
        $balance = $this->getLeaveBalance($leaveType, $year);

        return $balance && $balance->hasEnoughBalance($requestedDays);
    }

    /**
     * Get remaining leave balance for a specific leave type.
     */
    public function getRemainingLeaveBalance(LeaveType $leaveType, int $year = null): float
    {
        $balance = $this->getLeaveBalance($leaveType, $year);

        return $balance ? $balance->getRemainingBalance() : 0;
    }

    /**
     * Check if user is an admin (you can customize this logic).
     */
    public function isAdmin(): bool
    {
        // Check for common admin email patterns
        $adminEmailPatterns = [
            'admin@example.com',
            'test@example.com',
            'admin@',
            'administrator@',
            'superuser@'
        ];

        $userEmail = strtolower($this->email);

        // Check if email matches any admin pattern
        foreach ($adminEmailPatterns as $pattern) {
            if (str_starts_with($userEmail, strtolower($pattern))) {
                return true;
            }
        }

        // Check if email contains admin keywords
        $adminKeywords = ['admin', 'administrator', 'superuser', 'manager'];
        foreach ($adminKeywords as $keyword) {
            if (str_contains($userEmail, $keyword)) {
                return true;
            }
        }

        // Check if user ID is 1 (first user) - useful for development
        if ($this->id === 1) {
            return true;
        }

        // Environment-based admin configuration
        $envAdminEmails = explode(',', env('ADMIN_EMAILS', ''));
        if (!empty($envAdminEmails)) {
            foreach ($envAdminEmails as $adminEmail) {
                $adminEmail = trim(strtolower($adminEmail));
                if (!empty($adminEmail) && $userEmail === $adminEmail) {
                    return true;
                }
            }
        }

        // For development environment, make it easier to test admin features
        if (app()->environment(['local', 'development', 'testing'])) {
            // In development, also check for common test admin emails
            $devAdminEmails = [
                'admin@localhost',
                'admin@test.com',
                'test@admin.com'
            ];

            foreach ($devAdminEmails as $devEmail) {
                if ($userEmail === strtolower($devEmail)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user can approve leave requests.
     */
    public function canApproveLeaveRequests(): bool
    {
        return $this->isAdmin(); // Or add more complex logic
    }

    /**
     * Check if user can be deleted by current admin.
     */
    public function canBeDeleted(): bool
    {
        // Prevent deleting yourself
        return $this->id !== auth()->id();
    }

    /**
     * Get current leave requests that overlap with the given date range.
     */
    public function getOverlappingLeaveRequests(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
    {
        return $this->leaveRequests()
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->get();
    }
}
