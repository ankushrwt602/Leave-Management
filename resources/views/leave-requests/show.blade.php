@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Leave Request Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Request #{{ $leaveRequest->id }}</p>
                </div>
                <a href="{{ route('leave-requests.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Back to Requests
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Leave Request Information</h2>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        {{ $leaveRequest->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                        {{ $leaveRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                        {{ $leaveRequest->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                        {{ $leaveRequest->status === 'cancelled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Leave Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Leave Type
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ $leaveRequest->leaveType->name }}</p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $leaveRequest->start_date->format('M d, Y') }} - {{ $leaveRequest->end_date->format('M d, Y') }}
                        </p>
                    </div>

                    <!-- Total Days -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Total Days
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ $leaveRequest->total_days }} day{{ $leaveRequest->total_days > 1 ? 's' : '' }}</p>
                    </div>

                    <!-- Requested Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Requested Date
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ $leaveRequest->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($leaveRequest->approver)
                    <!-- Approved/Rejected By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $leaveRequest->status === 'approved' ? 'Approved' : 'Rejected' }} By
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ $leaveRequest->approver->name }}</p>
                    </div>

                    <!-- Approval Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $leaveRequest->status === 'approved' ? 'Approved' : 'Rejected' }} Date
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ $leaveRequest->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Reason -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4">
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $leaveRequest->reason }}</p>
                    </div>
                </div>

                @if($leaveRequest->emergency_contact)
                <!-- Emergency Contact -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Emergency Contact
                    </label>
                    <p class="text-gray-900 dark:text-white">{{ $leaveRequest->emergency_contact }}</p>
                </div>
                @endif

                @if($leaveRequest->contact_during_leave)
                <!-- Contact During Leave -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact During Leave
                    </label>
                    <p class="text-gray-900 dark:text-white">{{ $leaveRequest->contact_during_leave }}</p>
                </div>
                @endif

                @if($leaveRequest->work_coverage)
                <!-- Work Coverage -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Work Coverage
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4">
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $leaveRequest->work_coverage }}</p>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                @if($leaveRequest->isPending() && $leaveRequest->user_id === Auth::id())
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('leave-requests.edit', $leaveRequest) }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                            Edit Request
                        </a>

                        <form action="{{ route('leave-requests.cancel', $leaveRequest) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium"
                                    onclick="return confirm('Are you sure you want to cancel this leave request?')">
                                Cancel Request
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection