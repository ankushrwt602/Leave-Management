<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's leave summary for current year
        $currentYear = now()->year;

        $leaveBalances = $user->leaveBalances()
            ->where('year', $currentYear)
            ->with('leaveType')
            ->get();

        // Only create balances if none exist for the user in current year
        if ($leaveBalances->isEmpty()) {
            $activeLeaveTypes = LeaveType::active()->get();
            foreach ($activeLeaveTypes as $leaveType) {
                LeaveBalance::getOrCreateForUserAndYear($user, $leaveType, $currentYear);
            }
            // Re-fetch after creation
            $leaveBalances = $user->leaveBalances()
                ->where('year', $currentYear)
                ->with('leaveType')
                ->get();
        }

        // Get recent leave requests
        $recentLeaveRequests = $user->leaveRequests()
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending leave requests (for admins/managers)
        $pendingApprovals = collect();
        if ($user->canApproveLeaveRequests()) {
            $pendingApprovals = LeaveRequest::pending()
                ->with(['user', 'leaveType'])
                ->orderBy('created_at', 'asc')
                ->limit(10)
                ->get();
        }

        // Calculate leave statistics
        $totalApprovedDays = $user->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days');

        $totalPendingDays = $user->leaveRequests()
            ->where('status', 'pending')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days');

        // Get upcoming leave (next 30 days)
        $upcomingLeave = $user->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(30))
            ->with('leaveType')
            ->orderBy('start_date')
            ->get();

        // Leave balance summary
        $totalAllocated = $leaveBalances->sum(function ($balance) {
            return $balance->allocated_days + $balance->adjustment_days + $balance->carried_forward_days;
        });
        $totalUsed = $leaveBalances->sum('used_days');
        $totalRemaining = $leaveBalances->sum(function ($balance) {
            return $balance->getRemainingBalance();
        });

        return view('dashboard', compact(
            'leaveBalances',
            'recentLeaveRequests',
            'pendingApprovals',
            'totalApprovedDays',
            'totalPendingDays',
            'upcomingLeave',
            'totalAllocated',
            'totalUsed',
            'totalRemaining'
        ));
    }

    /**
     * Display leave calendar view.
     */
    public function calendar()
    {
        $user = Auth::user();

        // Get approved leave requests for calendar display
        $leaveRequests = $user->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '>=', now()->startOfMonth())
            ->where('end_date', '<=', now()->addMonths(3)->endOfMonth())
            ->with('leaveType')
            ->get();

        // Format for calendar display
        $calendarEvents = $leaveRequests->map(function ($request) {
            return [
                'title' => $request->leaveType->name . ' (' . $request->total_days . ' days)',
                'start' => $request->start_date->toDateString(),
                'end' => $request->end_date->addDay()->toDateString(), // Full day event
                'backgroundColor' => $this->getLeaveTypeColor($request->leaveType->code),
                'borderColor' => $this->getLeaveTypeColor($request->leaveType->code),
            ];
        });

        return view('calendar', compact('calendarEvents'));
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
     * Display leave balance details.
     */
    public function balance()
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Get leave balances for current and previous year
        $balances = $user->leaveBalances()
            ->whereIn('year', [$currentYear, $currentYear - 1])
            ->with('leaveType')
            ->join('leave_types', 'leave_balances.leave_type_id', '=', 'leave_types.id')
            ->orderBy('leave_balances.year', 'desc')
            ->orderBy('leave_types.name', 'asc')
            ->select('leave_balances.*')
            ->get();

        // Only create balances if none exist for the user in current year
        if ($balances->where('year', $currentYear)->isEmpty()) {
            $activeLeaveTypes = LeaveType::active()->get();
            foreach ($activeLeaveTypes as $leaveType) {
                LeaveBalance::getOrCreateForUserAndYear($user, $leaveType, $currentYear);
            }
            // Re-fetch after creation
            $balances = $user->leaveBalances()
                ->whereIn('year', [$currentYear, $currentYear - 1])
                ->with('leaveType')
                ->join('leave_types', 'leave_balances.leave_type_id', '=', 'leave_types.id')
                ->orderBy('leave_balances.year', 'desc')
                ->orderBy('leave_types.name', 'asc')
                ->select('leave_balances.*')
                ->get();
        }

        // Group by year for display
        $balancesByYear = $balances->groupBy('year');

        return view('balance', compact('balancesByYear', 'currentYear'));
    }

    /**
     * Display user management interface.
     */
    public function users()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $query = User::with(['leaveRequests', 'leaveBalances'])
            ->withCount(['leaveRequests as pending_requests' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->withCount(['leaveRequests as approved_requests' => function ($query) {
                $query->where('status', 'approved');
            }]);

        // Only order by is_admin if the column exists (for backward compatibility)
        if (\Schema::hasColumn('users', 'is_admin')) {
            $query->orderBy('is_admin', 'desc');
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create new user.
     */
    public function createUser()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.users.create');
    }

    /**
     * Store new user.
     */
    public function storeUser(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Only validate is_admin if the column exists
        if (\Schema::hasColumn('users', 'is_admin')) {
            $validationRules['is_admin'] = ['boolean'];
        }

        $validated = $request->validate($validationRules);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        // Only set is_admin if the column exists
        if (\Schema::hasColumn('users', 'is_admin')) {
            $userData['is_admin'] = $validated['is_admin'] ?? false;
        }

        $user = User::create($userData);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    /**
     * Show form to edit user.
     */
    public function editUser(User $user)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, User $user)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'leave_allocation' => ['nullable', 'array'],
            'leave_allocation.*' => ['numeric', 'min:0']
        ];

        // Only validate is_admin if the column exists
        if (\Schema::hasColumn('users', 'is_admin')) {
            $validationRules['is_admin'] = ['boolean'];
        }

        $validated = $request->validate($validationRules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only set is_admin if the column exists
        if (\Schema::hasColumn('users', 'is_admin')) {
            $updateData['is_admin'] = $validated['is_admin'] ?? false;
        }

        if ($validated['password']) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update leave allocations
        if (isset($validated['leave_allocation'])) {
            $currentYear = now()->year;
            $leaveTypes = LeaveType::active()->get();

            foreach ($leaveTypes as $leaveType) {
                $allocatedDays = $validated['leave_allocation'][$leaveType->id] ?? 0;

                $balance = LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $currentYear,
                    ],
                    [
                        'allocated_days' => 0,
                        'used_days' => 0,
                        'pending_days' => 0,
                        'carried_forward_days' => 0,
                        'adjustment_days' => 0,
                    ]
                );

                $balance->update(['allocated_days' => $allocatedDays]);
            }
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}