@extends('layouts.app')

@section('title', 'Leave Balance')

@section('content')
<div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Leave Balance Details</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Detailed view of your leave balances</p>
    </div>

    @if($balancesByYear->count() > 0)
        @foreach($balancesByYear as $year => $balances)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">{{ $year }} Leave Balances</h2>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($balances as $balance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $balance->leaveType->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Allocated: {{ $balance->allocated_days }} |
                                    Used: {{ $balance->used_days }} |
                                    Pending: {{ $balance->pending_days }} |
                                    Carried Forward: {{ $balance->carried_forward_days }} |
                                    Adjustments: {{ $balance->adjustment_days }}
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
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
            <p class="text-gray-600 dark:text-gray-400 text-center">No leave balances found.</p>
        </div>
    @endif

    <div class="mt-8">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>
@endsection