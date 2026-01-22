<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of leave types.
     */
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('name')->paginate(15);

        return view('leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new leave type.
     */
    public function create()
    {
        return view('leave-types.create');
    }

    /**
     * Store a newly created leave type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'default_days_per_year' => 'required|integer|min:0',
            'max_consecutive_days' => 'nullable|integer|min:1',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000',
            'carry_forward_rules' => 'nullable|array',
        ]);

        LeaveType::create($request->all());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified leave type.
     */
    public function show(LeaveType $leaveType)
    {
        return view('leave-types.show', compact('leaveType'));
    }

    /**
     * Show the form for editing the specified leave type.
     */
    public function edit(LeaveType $leaveType)
    {
        return view('leave-types.edit', compact('leaveType'));
    }

    /**
     * Update the specified leave type.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $leaveType->id,
            'default_days_per_year' => 'required|integer|min:0',
            'max_consecutive_days' => 'nullable|integer|min:1',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000',
            'carry_forward_rules' => 'nullable|array',
        ]);

        $leaveType->update($request->all());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type.
     */
    public function destroy(LeaveType $leaveType)
    {
        // Check if leave type is being used
        if ($leaveType->leaveRequests()->count() > 0) {
            return back()->withErrors([
                'leave_type' => 'Cannot delete leave type that has existing leave requests.'
            ]);
        }

        $leaveType->delete();

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }

    /**
     * Toggle the active status of a leave type.
     */
    public function toggleStatus(LeaveType $leaveType)
    {
        $leaveType->update([
            'is_active' => !$leaveType->is_active
        ]);

        $status = $leaveType->is_active ? 'activated' : 'deactivated';

        return redirect()->route('leave-types.index')
            ->with('success', "Leave type {$status} successfully.");
    }
}