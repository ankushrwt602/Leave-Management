<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;

class LeaveApprovalController extends Controller
{
    /**
     * Display a listing of leave requests pending approval.
     */
    public function index()
    {
        $pendingRequests = Auth::user()->approvableLeaveRequests()
            ->with(['user', 'leaveType'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('leave-approvals.index', compact('pendingRequests'));
    }

    /**
     * Display the specified leave request for approval.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        // Ensure user can approve this request
        if (!$this->canApproveRequest($leaveRequest)) {
            abort(403);
        }

        $leaveRequest->load(['user', 'leaveType']);

        return view('leave-approvals.show', compact('leaveRequest'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        // Ensure user can approve this request
        if (!$this->canApproveRequest($leaveRequest)) {
            abort(403);
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Approve the request
            $leaveRequest->approve(Auth::user(), $request->approval_notes);

            // Send approval email to the user
            Mail::to($leaveRequest->user->email)->send(new LeaveRequestApproved($leaveRequest));

            return redirect()->route('leave-approvals.index')
                ->with('success', 'Leave request approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to approve leave request. Please try again.']);
        }
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        // Ensure user can approve this request
        if (!$this->canApproveRequest($leaveRequest)) {
            abort(403);
        }

        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        try {
            // Reject the request
            $leaveRequest->reject(Auth::user(), $request->approval_notes);

            // Send rejection email to the user
            Mail::to($leaveRequest->user->email)->send(new LeaveRequestRejected($leaveRequest));

            return redirect()->route('leave-approvals.index')
                ->with('success', 'Leave request rejected successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reject leave request. Please try again.']);
        }
    }

    /**
     * Bulk approve leave requests.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:leave_requests,id',
        ]);

        $approved = 0;
        $failed = 0;

        foreach ($request->request_ids as $requestId) {
            $leaveRequest = LeaveRequest::find($requestId);

            if ($leaveRequest && $this->canApproveRequest($leaveRequest)) {
                try {
                    $leaveRequest->approve(Auth::user());
                    // Send approval email to the user
                    Mail::to($leaveRequest->user->email)->send(new LeaveRequestApproved($leaveRequest));
                    $approved++;
                } catch (\Exception $e) {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        $message = "Approved {$approved} leave request(s).";
        if ($failed > 0) {
            $message .= " Failed to approve {$failed} request(s).";
        }

        return redirect()->route('leave-approvals.index')
            ->with('success', $message);
    }

    /**
     * Bulk reject leave requests.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:leave_requests,id',
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $rejected = 0;
        $failed = 0;

        foreach ($request->request_ids as $requestId) {
            $leaveRequest = LeaveRequest::find($requestId);

            if ($leaveRequest && $this->canApproveRequest($leaveRequest)) {
                try {
                    $leaveRequest->reject(Auth::user(), $request->rejection_reason);
                    // Send rejection email to the user
                    Mail::to($leaveRequest->user->email)->send(new LeaveRequestRejected($leaveRequest));
                    $rejected++;
                } catch (\Exception $e) {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        $message = "Rejected {$rejected} leave request(s).";
        if ($failed > 0) {
            $message .= " Failed to reject {$failed} request(s).";
        }

        return redirect()->route('leave-approvals.index')
            ->with('success', $message);
    }

    /**
     * Check if current user can approve the given leave request.
     */
    private function canApproveRequest(LeaveRequest $leaveRequest): bool
    {
        // User must be able to approve requests in general
        if (!Auth::user()->canApproveLeaveRequests()) {
            return false;
        }

        // Can't approve own requests
        if ($leaveRequest->user_id === Auth::id()) {
            return false;
        }

        // Request must be pending
        if (!$leaveRequest->isPending()) {
            return false;
        }

        return true;
    }
}