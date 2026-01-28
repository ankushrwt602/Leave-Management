@extends('layouts.app')
@section('title', 'Edit Leave Type')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Leave Type</h1>
        <a href="{{ route('leave-types.index') }}" class="bg-gray-600 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
            Back to List
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <form action="{{ route('leave-types.update', $leaveType) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $leaveType->name) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 px-3 py-2" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $leaveType->code) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 px-3 py-2" required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="default_days_per_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Days Per Year *</label>
                    <input type="number" name="default_days_per_year" id="default_days_per_year" value="{{ old('default_days_per_year', $leaveType->default_days_per_year) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 px-3 py-2" required>
                    @error('default_days_per_year')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="max_consecutive_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Consecutive Days</label>
                    <input type="number" name="max_consecutive_days" id="max_consecutive_days" value="{{ old('max_consecutive_days', $leaveType->max_consecutive_days) }}" min="1" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 px-3 py-2">
                    @error('max_consecutive_days')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank for unlimited consecutive days</p>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 px-3 py-2">{{ old('description', $leaveType->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="requires_approval" id="requires_approval" value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <label for="requires_approval" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            Requires Approval
                        </label>
                    </div>
                    @error('requires_approval')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $leaveType->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            Active
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('leave-types.show', $leaveType) }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Update Leave Type
                    </button>
                </div>
            </form>
        </div>
</div>
@endsection