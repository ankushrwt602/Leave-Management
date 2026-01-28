@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp
    <div class="mb-8">
        @if($isAdmin)
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage leave system and approve requests</p>
        @else
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Leave Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your leave requests and balances</p>
        @endif
    </div>

    <!-- Statistics Cards -->
    @if($isAdmin)
    <!-- Admin Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ App\Models\User::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Leave Types -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Leave Types</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ App\Models\LeaveType::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Approvals</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ App\Models\LeaveRequest::where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Approved This Month -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved This Month</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ App\Models\LeaveRequest::where('status', 'approved')->whereMonth('created_at', now()->month)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- User Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Allocated Days -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Allocated</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalAllocated }}</p>
                </div>
            </div>
        </div>

        <!-- Used Days -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Used This Year</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalUsed }}</p>
                </div>
            </div>
        </div>

        <!-- Remaining Days -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Remaining</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalRemaining }}</p>
                    @if($leaveBalances->count() > 0)
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @foreach($leaveBalances as $balance)
                                @if($balance->getRemainingBalance() > 0)
                                    {{ $balance->leaveType->name }}: {{ $balance->getRemainingBalance() }}
                                    @if(!$loop->last && $loop->remaining > 1) | @endif
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Requests</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalPendingDays }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($isAdmin)
    <!-- Admin Dashboard Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Overview</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Total Leave Requests</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">All time</p>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ App\Models\LeaveRequest::count() }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Approved This Year</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ now()->year }}</p>
                        </div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ App\Models\LeaveRequest::where('status', 'approved')->whereYear('created_at', now()->year)->count() }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Rejected This Year</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ now()->year }}</p>
                        </div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ App\Models\LeaveRequest::where('status', 'rejected')->whereYear('created_at', now()->year)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent System Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
            </div>
            <div class="p-6">
                @php
                    $recentActivity = App\Models\LeaveRequest::with(['user', 'leaveType'])
                        ->orderBy('updated_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @if($recentActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $activity->user->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $activity->leaveType->name }} - {{ $activity->total_days }} days
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $activity->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $activity->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $activity->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">No recent activity found.</p>
                @endif
            </div>
        </div>
    </div>
    @else
    <!-- User Dashboard Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Leave Balances -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Leave Balances ({{ now()->year }})</h2>
            </div>
            <div class="p-6">
                @if($leaveBalances->count() > 0)
                    <div class="space-y-4">
                        @foreach($leaveBalances as $balance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $balance->leaveType->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Allocated: {{ $balance->allocated_days }} |
                                    Used: {{ $balance->used_days }} |
                                    Pending: {{ $balance->pending_days }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $balance->getRemainingBalance() > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $balance->getRemainingBalance() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Remaining</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">No leave balances found for this year.</p>
                @endif
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Leave Requests</h2>
            </div>
            <div class="p-6">
                @if($recentLeaveRequests->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentLeaveRequests as $request)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $request->leaveType->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                    ({{ $request->total_days }} days)
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $request->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">No leave requests found.</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($isAdmin)
    <!-- Admin Quick Actions -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Admin Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('leave-approvals.index') }}"
                   class="flex items-center justify-center p-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pending Approvals
                    @php
                        $pendingCount = App\Models\LeaveRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('leave-types.index') }}"
                   class="flex items-center justify-center p-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                    Manage Leave Types
                </a>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center justify-center p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    System Statistics
                </a>

                <a href="{{ route('admin.users') }}"
                   class="flex items-center justify-center p-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    User Management
                    <span class="ml-2 bg-indigo-500 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\User::count() }}</span>
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- User Quick Actions -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('leave-requests.create') }}"
                   class="flex items-center justify-center p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Leave Request
                </a>

                <a href="{{ route('leave-requests.index') }}"
                   class="flex items-center justify-center p-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View All Requests
                </a>

                <a href="{{ route('balance') }}"
                   class="flex items-center justify-center p-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Leave Balance
                </a>
            </div>
        </div>
    </div>
    @endif
@endsection