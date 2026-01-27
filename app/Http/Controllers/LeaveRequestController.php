<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestSubmitted;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the user's leave requests.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is admin and redirect to admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Admins cannot access this page.');
        }

        $leaveRequests = $user->leaveRequests()
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('leave-requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $leaveTypes = LeaveType::active()->get();
        
        // Get user's approved leave requests for calendar display
        $user = Auth::user();
        $approvedLeaveRequests = $user->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '>=', now()->startOfMonth())
            ->where('end_date', '<=', now()->addMonths(3)->endOfMonth())
            ->with('leaveType')
            ->get();
        
        // Format for calendar display
        $calendarEvents = $approvedLeaveRequests->map(function ($request) {
            return [
                'title' => $request->leaveType->name . ' (' . $request->total_days . ' days)',
                'start' => $request->start_date->toDateString(),
                'end' => $request->end_date->addDay()->toDateString(), // Full day event
                'backgroundColor' => $this->getLeaveTypeColor($request->leaveType->code),
                'borderColor' => $this->getLeaveTypeColor($request->leaveType->code),
            ];
        });

        return view('leave-requests.create', compact('leaveTypes', 'calendarEvents'));
    }
    
    /**
     * Get color for leave type (for calendar display).
     */
    private function getLeaveTypeColor(string $leaveTypeCode): string
    {
        $colors = [
            'annual' => '#3B82F6', // Blue
            'sick' => '#EF4444',   // Red
            'casual' => '#10B981', // Green
            'maternity' => '#F59E0B', // Yellow
            'paternity' => '#8B5CF6', // Purple
            'emergency' => '#F97316', // Orange
        ];

        return $colors[$leaveTypeCode] ?? '#6B7280'; // Default gray
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'emergency_contact' => 'nullable|string|max:255',
            'contact_during_leave' => 'nullable|string|max:255',
            'work_coverage' => 'nullable|array',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Calculate total days
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check for maximum consecutive days if set
        if ($leaveType->getMaxConsecutiveDays() && $totalDays > $leaveType->getMaxConsecutiveDays()) {
            return back()->withErrors([
                'end_date' => "Leave duration cannot exceed {$leaveType->getMaxConsecutiveDays()} consecutive days for {$leaveType->name}."
            ])->withInput();
        }

        // Check for overlapping approved leave
        $overlappingRequests = Auth::user()->getOverlappingLeaveRequests($startDate, $endDate);
        if ($overlappingRequests->count() > 0) {
            return back()->withErrors([
                'start_date' => 'You already have approved leave during this period.'
            ])->withInput();
        }

        // Check leave balance
        $balance = Auth::user()->getOrCreateLeaveBalance($leaveType, $startDate->year);
        if (!$balance->hasEnoughBalance($totalDays)) {
            return back()->withErrors([
                'start_date' => "Insufficient leave balance. You have {$balance->getRemainingBalance()} days remaining for {$leaveType->name} in {$startDate->year}."
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $leaveType, $startDate, $endDate, $totalDays, $balance) {
                // Create leave request
                $leaveRequest = LeaveRequest::create([
                    'user_id' => Auth::id(),
                    'leave_type_id' => $request->leave_type_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'reason' => $request->reason,
                    'emergency_contact' => $request->emergency_contact,
                    'contact_during_leave' => $request->contact_during_leave,
                    'work_coverage' => $request->work_coverage,
                ]);

                // Reserve the days in balance
                $balance->addPendingDays($totalDays);
            });

            // Send confirmation email
            Mail::to(Auth::user()->email)->send(new LeaveRequestSubmitted($leaveRequest));

            return redirect()->route('leave-requests.index')
                ->with('success', 'Leave request submitted successfully and is pending approval.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit leave request. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        // Ensure user can only view their own requests (unless they're an admin)
        if ($leaveRequest->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $leaveRequest->load(['leaveType', 'approver']);

        return view('leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        // Only allow editing pending requests by the owner
        if ($leaveRequest->user_id !== Auth::id() || !$leaveRequest->isPending()) {
            abort(403);
        }

        $leaveTypes = LeaveType::active()->get();

        return view('leave-requests.edit', compact('leaveRequest', 'leaveTypes'));
    }

    /**
     * Update the specified leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // Only allow updating pending requests by the owner
        if ($leaveRequest->user_id !== Auth::id() || !$leaveRequest->isPending()) {
            abort(403);
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'emergency_contact' => 'nullable|string|max:255',
            'contact_during_leave' => 'nullable|string|max:255',
            'work_coverage' => 'nullable|array',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Calculate total days
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check for maximum consecutive days if set
        if ($leaveType->getMaxConsecutiveDays() && $totalDays > $leaveType->getMaxConsecutiveDays()) {
            return back()->withErrors([
                'end_date' => "Leave duration cannot exceed {$leaveType->getMaxConsecutiveDays()} consecutive days for {$leaveType->name}."
            ])->withInput();
        }

        // Check for overlapping approved leave (excluding current request)
        $overlappingRequests = Auth::user()->getOverlappingLeaveRequests($startDate, $endDate)
            ->where('id', '!=', $leaveRequest->id);

        if ($overlappingRequests->count() > 0) {
            return back()->withErrors([
                'start_date' => 'You already have approved leave during this period.'
            ])->withInput();
        }

        // Check leave balance (need to account for currently pending days)
        $balance = Auth::user()->getLeaveBalance($leaveType, $startDate->year);
        $currentPendingDays = $leaveRequest->total_days;
        $availableBalance = $balance ? $balance->getAvailableBalance() + $currentPendingDays : $currentPendingDays;

        if ($totalDays > $availableBalance) {
            return back()->withErrors([
                'start_date' => "Insufficient leave balance. You have {$availableBalance} days available for {$leaveType->name} in {$startDate->year}."
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $leaveRequest, $startDate, $endDate, $totalDays, $balance, $currentPendingDays) {
                // Update leave request
                $leaveRequest->update([
                    'leave_type_id' => $request->leave_type_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'reason' => $request->reason,
                    'emergency_contact' => $request->emergency_contact,
                    'contact_during_leave' => $request->contact_during_leave,
                    'work_coverage' => $request->work_coverage,
                ]);

                // Adjust pending days in balance
                if ($currentPendingDays !== $totalDays) {
                    $balance->removePendingDays($currentPendingDays);
                    $balance->addPendingDays($totalDays);
                }
            });

            return redirect()->route('leave-requests.index')
                ->with('success', 'Leave request updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update leave request. Please try again.'])->withInput();
        }
    }

    /**
     * Cancel the specified leave request.
     */
    public function cancel(LeaveRequest $leaveRequest)
    {
        // Only allow cancelling pending requests by the owner
        if ($leaveRequest->user_id !== Auth::id() || !$leaveRequest->isPending()) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($leaveRequest) {
                // Cancel the request
                $leaveRequest->cancel();

                // Remove pending days from balance
                $balance = Auth::user()->getLeaveBalance(
                    $leaveRequest->leaveType,
                    $leaveRequest->start_date->year
                );

                if ($balance) {
                    $balance->removePendingDays($leaveRequest->total_days);
                }
            });

            return redirect()->route('leave-requests.index')
                ->with('success', 'Leave request cancelled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel leave request. Please try again.']);
        }
    }
}