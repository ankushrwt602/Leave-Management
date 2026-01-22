@extends('layouts.app')

@section('title', 'Review Leave Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Leave Request</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Detailed review before approval or rejection</p>
    </div>

    <!-- Leave Request Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Leave Request Details</h2>
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    Pending Approval
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Employee Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Employee Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Employee ID</label>
                            <p class="text-gray-900 dark:text-white">#{{ $leaveRequest->user->id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Leave Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Leave Details</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Leave Type</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->leaveType->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->total_days }} day{{ $leaveRequest->total_days > 1 ? 's' : '' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->start_date->format('l, F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">End Date</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->end_date->format('l, F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</label>
                            <p class="text-gray-900 dark:text-white">{{ $leaveRequest->created_at->format('l, F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            @if($leaveRequest->reason)
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reason for Leave</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-700 dark:text-gray-300">{{ $leaveRequest->reason }}</p>
                </div>
            </div>
            @endif

            <!-- Approval Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Approval Actions</h3>
                <div class="flex flex-wrap gap-4">
                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('leave-approvals.approve', $leaveRequest) }}" class="flex-1 min-w-0">
                        @csrf
                        <div class="mb-4">
                            <label for="approval_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Approval Notes (Optional)
                            </label>
                            <textarea id="approval_notes" name="approval_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="Add any notes about this approval..."></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                            ✅ Approve Leave Request
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form method="POST" action="{{ route('leave-approvals.reject', $leaveRequest) }}" class="flex-1 min-w-0">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rejection Reason (Required)
                            </label>
                            <textarea id="rejection_notes" name="approval_notes" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                            ❌ Reject Leave Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('leave-approvals.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to All Requests
                </a>
            </div>
        </div>
    </div>
</div>
@endsection