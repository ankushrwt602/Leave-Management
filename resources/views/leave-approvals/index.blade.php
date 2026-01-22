@extends('layouts.app')

@section('title', 'Leave Approvals')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Leave Approvals</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Review and approve pending leave requests</p>
    </div>

    <!-- Bulk Actions -->
    @if($pendingRequests->count() > 0)
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bulk Actions</h2>
        <form id="bulk-approval-form" method="POST" action="{{ route('leave-approvals.bulk-approve') }}" class="mb-4">
            @csrf
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center">
                    <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="select-all" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select All</label>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Approve Selected
                </button>
                <button type="button" id="bulk-reject-btn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Reject Selected
                </button>
            </div>
        </form>

        <!-- Bulk Reject Form (hidden initially) -->
        <form id="bulk-reject-form" method="POST" action="{{ route('leave-approvals.bulk-reject') }}" style="display: none;">
            @csrf
            <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <label for="rejection-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rejection Reason (Required)
                </label>
                <textarea id="rejection-reason" name="rejection_reason" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                          placeholder="Please provide a reason for rejection..." required></textarea>
                <div class="mt-3 flex justify-end space-x-3">
                    <button type="button" id="cancel-reject-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Reject Selected
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- Leave Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Pending Leave Requests ({{ $pendingRequests->total() }})
            </h2>
        </div>

        @if($pendingRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <input type="checkbox" id="request-checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Employee
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Leave Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Dates
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Submitted
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pendingRequests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="request_ids[]" value="{{ $request->id }}"
                                       form="bulk-approval-form" class="request-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($request->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $request->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $request->leaveType->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $request->total_days }} day{{ $request->total_days > 1 ? 's' : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div>{{ $request->start_date->format('M d, Y') }}</div>
                                <div class="text-gray-400">to {{ $request->end_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('leave-approvals.show', $request) }}"
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        View
                                    </a>
                                    <form method="POST" action="{{ route('leave-approvals.approve', $request) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('leave-approvals.reject', $request) }}" class="inline">
                                        @csrf
                                        <button type="button"
                                                onclick="showRejectForm(this, {{ $request->id }})"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $pendingRequests->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <div class="text-gray-400 dark:text-gray-500 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No pending leave requests</p>
                <p class="text-gray-500 dark:text-gray-500 mt-2">All leave requests have been processed.</p>
            </div>
        @endif
    </div>
</div>

<script>
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Bulk reject functionality
    document.getElementById('bulk-reject-btn').addEventListener('click', function() {
        const selectedRequests = document.querySelectorAll('.request-checkbox:checked');
        if (selectedRequests.length === 0) {
            alert('Please select at least one request to reject.');
            return;
        }

        // Add selected request IDs to the bulk reject form
        const bulkRejectForm = document.getElementById('bulk-reject-form');
        const existingInputs = bulkRejectForm.querySelectorAll('input[name="request_ids[]"]');
        existingInputs.forEach(input => input.remove());

        selectedRequests.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'request_ids[]';
            input.value = checkbox.value;
            bulkRejectForm.appendChild(input);
        });

        // Show the bulk reject form
        document.getElementById('bulk-reject-form').style.display = 'block';
    });

    document.getElementById('cancel-reject-btn').addEventListener('click', function() {
        document.getElementById('bulk-reject-form').style.display = 'none';
        document.getElementById('rejection-reason').value = '';
    });

    // Individual reject functionality
    function showRejectForm(button, requestId) {
        // You can implement individual reject modal here if needed
        alert('Please use bulk reject for multiple requests, or approve individual requests.');
    }
</script>
@endsection