<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Authenticated users can view leave requests
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can view their own requests, admins can view all
        return $user->id === $leaveRequest->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Authenticated users can create leave requests
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can only update their own pending requests
        return $user->id === $leaveRequest->user_id && $leaveRequest->isPending();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can only cancel their own pending requests
        return $user->id === $leaveRequest->user_id && $leaveRequest->isPending();
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can approve if they have approval rights and it's not their own request
        return $user->canApproveLeaveRequests() &&
               $user->id !== $leaveRequest->user_id &&
               $leaveRequest->isPending();
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        // Same logic as approve
        return $this->approve($user, $leaveRequest);
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can only cancel their own pending requests
        return $user->id === $leaveRequest->user_id && $leaveRequest->isPending();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return false; // Not needed for leave requests
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false; // Not needed for leave requests
    }
}