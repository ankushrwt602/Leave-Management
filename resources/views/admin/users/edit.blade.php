@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Update user information and permissions</p>
    </div>

    <!-- Edit User Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">User Information</h2>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Full Name *
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                               placeholder="Enter full name" value="{{ old('name', $user->name) }}">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address *
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror"
                               placeholder="Enter email address" value="{{ old('email', $user->email) }}">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password
                        </label>
                        <input type="password" id="password" name="password"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror"
                               placeholder="Leave blank to keep current password">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current password</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('password_confirmation') border-red-500 @enderror"
                               placeholder="Confirm new password">
                    </div>

                    <!-- Admin Role -->
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_admin" name="is_admin" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                   {{ old('is_admin', $user->isAdmin()) ? 'checked' : '' }}>
                            <label for="is_admin" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                Administrator Privileges
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Administrators can manage leave types, approve requests, and access user management features.
                        </p>
                    </div>
                </div>

                <!-- Leave Balance Management -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Leave Balance Management</h3>
                    <div class="space-y-4">
                        @php
                            $currentYear = now()->year;
                            $leaveTypes = App\Models\LeaveType::active()->get();
                        @endphp
                        @foreach($leaveTypes as $leaveType)
                            @php
                                $balance = $user->getLeaveBalance($leaveType, $currentYear);
                                $allocatedDays = $balance ? $balance->allocated_days : 0;
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $leaveType->name }} ({{ $currentYear }})
                                    </label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Currently allocated: {{ $allocatedDays }} days
                                    </p>
                                </div>
                                <input type="number" step="0.5" min="0"
                                       name="leave_allocation[{{ $leaveType->id }}]"
                                       value="{{ $allocatedDays }}"
                                       class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- User Statistics (Read-only) -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $user->leaveRequests->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Requests</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $user->leaveRequests->where('status', 'approved')->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Approved</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $user->leaveRequests->where('status', 'pending')->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.users') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection