@extends('layouts.app')

@section('title', 'New Leave Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">New Leave Request</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Submit a new leave request for approval</p>
        </div>

        <form action="{{ route('leave-requests.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Leave Type -->
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Leave Type *
                    </label>
                    <select name="leave_type_id" id="leave_type_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                {{ $leaveType->name }}
                                @if($leaveType->default_days_per_year > 0)
                                    ({{ $leaveType->default_days_per_year }} days/year)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range with Calendar -->
               <div class="space-y-4">
                   <!-- Date Selection with Calendar -->
                   <div>
                       <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                           Select Leave Dates *
                       </label>
                       
                       <!-- Date picker inputs -->
                       <div class="date-picker-container space-y-2">
                           <div>
                               <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                               <input type="text" name="start_date" id="start_date" required
                                      value="{{ old('start_date') }}"
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                      placeholder="Select start date">
                           </div>
                           
                           <div>
                               <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                               <input type="text" name="end_date" id="end_date" required
                                      value="{{ old('end_date') }}"
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                      placeholder="Select end date">
                           </div>
                       </div>
                       
                       <!-- Selected dates display -->
                       <div id="selected-dates-display" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md {{ old('start_date') && old('end_date') ? '' : 'hidden' }}">
                           <p class="text-sm text-blue-700 dark:text-blue-300">
                               <span id="selected-dates-text">
                                   @if(old('start_date') && old('end_date'))
                                       {{ \DateTime::createFromFormat('Y-m-d', old('start_date'))->format('M j, Y') }}
                                       to
                                       {{ \DateTime::createFromFormat('Y-m-d', old('end_date'))->format('M j, Y') }}
                                   @else
                                       No dates selected
                                   @endif
                               </span>
                               <span id="selected-days-count" class="ml-2 font-medium">
                                   @if(old('start_date') && old('end_date'))
                                       ({{ \DateTime::createFromFormat('Y-m-d', old('start_date'))->diff(\DateTime::createFromFormat('Y-m-d', old('end_date')))->days + 1 }} days)
                                   @endif
                               </span>
                           </p>
                       </div>
                       
                       @error('start_date')
                           <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                       @enderror
                       @error('end_date')
                           <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                       @enderror
                   </div>
               </div>

                <!-- Days Preview -->
                <div id="days-preview" class="hidden">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Leave Duration
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <p id="days-count">0 days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason *
                    </label>
                    <textarea name="reason" id="reason" rows="4" required
                              placeholder="Please provide a reason for your leave request..."
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emergency Contact -->
                <div>
                    <label for="emergency_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Emergency Contact
                    </label>
                    <input type="text" name="emergency_contact" id="emergency_contact"
                           value="{{ old('emergency_contact') }}"
                           placeholder="Phone number or email for emergencies"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('emergency_contact')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact During Leave -->
                <div>
                    <label for="contact_during_leave" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact During Leave
                    </label>
                    <input type="text" name="contact_during_leave" id="contact_during_leave"
                           value="{{ old('contact_during_leave') }}"
                           placeholder="How can you be reached during leave?"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('contact_during_leave')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Work Coverage -->
                <div>
                    <label for="work_coverage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Work Coverage
                    </label>
                    <textarea name="work_coverage" id="work_coverage" rows="3"
                              placeholder="Describe how your work will be covered during your absence..."
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('work_coverage') }}</textarea>
                    @error('work_coverage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('leave-requests.index') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Submit Leave Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Date Picker with Fallback -->
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" rel="stylesheet">
<style>
    /* Ensure date pickers are visible and properly styled */
    .date-picker-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    /* Make date inputs more prominent */
    #start_date, #end_date {
        padding: 0.75rem;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    #start_date:hover, #end_date:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Calendar icon indicator */
    .date-input-wrapper {
        position: relative;
    }
    
    .date-input-wrapper::after {
        content: '📅';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    
    /* Selected dates display enhancement */
    #selected-dates-display {
        transition: all 0.3s ease;
    }
    
    /* Fallback for browsers without JavaScript */
    .no-js-warning {
        display: none;
        background: #fef3c7;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        border: 1px solid #fde047;
    }
    
    .no-js-warning.show {
        display: block;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Show warning if JavaScript is disabled
    document.documentElement.classList.remove('no-js');
    document.documentElement.classList.add('js');
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📅 Date picker initialization started');
        
        // Check if Flatpickr is loaded
        if (typeof flatpickr === 'undefined') {
            console.error('❌ Flatpickr library not loaded');
            showFallbackWarning();
            return;
        }
        
        // Get DOM elements
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const selectedDatesDisplay = document.getElementById('selected-dates-display');
        const selectedDatesText = document.getElementById('selected-dates-text');
        const selectedDaysCount = document.getElementById('selected-days-count');
        const daysPreview = document.getElementById('days-preview');
        const daysCount = document.getElementById('days-count');
        
        if (!startDateInput || !endDateInput) {
            console.error('❌ Date input elements not found');
            showFallbackWarning();
            return;
        }
        
        console.log('✅ Date input elements found');
        
        try {
            // Initialize Flatpickr with enhanced configuration
            const startDatePicker = flatpickr(startDateInput, {
                minDate: "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                clickOpens: true,
                onReady: function() {
                    console.log('✅ Start date picker ready');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    console.log('📅 Start date selected:', dateStr);
                    
                    if (dateStr) {
                        // Set min date for end date picker
                        if (endDateInput._flatpickr) {
                            endDateInput._flatpickr.set('minDate', dateStr);
                        }
                        
                        // If end date is before start date, update it
                        if (endDateInput.value && endDateInput.value < dateStr) {
                            if (endDateInput._flatpickr) {
                                endDateInput._flatpickr.setDate(dateStr);
                            } else {
                                endDateInput.value = dateStr;
                            }
                        }
                        
                        calculateDays();
                    }
                }
            });
            
            const endDatePicker = flatpickr(endDateInput, {
                minDate: startDateInput.value || "today",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                clickOpens: true,
                onReady: function() {
                    console.log('✅ End date picker ready');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    console.log('📅 End date selected:', dateStr);
                    calculateDays();
                }
            });
            
            console.log('✅ Flatpickr date pickers initialized successfully');
            
            // Calculate days between start and end date
            function calculateDays() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                
                console.log('📊 Calculating days between:', startDate, 'and', endDate);
                
                if (startDate && endDate) {
                    try {
                        const start = new Date(startDate);
                        const end = new Date(endDate);
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                        console.log('📊 Total days:', diffDays);
                        
                        // Update display
                        selectedDatesDisplay.classList.remove('hidden');
                        daysPreview.classList.remove('hidden');
                        
                        const options = { year: 'numeric', month: 'short', day: 'numeric' };
                        selectedDatesText.textContent =
                            new Date(startDate).toLocaleDateString('en-US', options) +
                            ' to ' +
                            new Date(endDate).toLocaleDateString('en-US', options);
                        
                        selectedDaysCount.textContent = `(${diffDays} day${diffDays > 1 ? 's' : ''})`;
                        daysCount.textContent = `${diffDays} day${diffDays > 1 ? 's' : ''}`;
                        
                    } catch (error) {
                        console.error('❌ Error calculating days:', error);
                    }
                } else {
                    selectedDatesDisplay.classList.add('hidden');
                    daysPreview.classList.add('hidden');
                }
            }
            
            // Initialize with old values if available
            const oldStartDate = '{{ old('start_date') }}';
            const oldEndDate = '{{ old('end_date') }}';
            
            console.log('🔄 Checking for old values:', oldStartDate, oldEndDate);
            
            if (oldStartDate && oldEndDate) {
                calculateDays();
            }
            
        } catch (error) {
            console.error('❌ Error initializing date pickers:', error);
            showFallbackWarning();
        }
    });
    
    function showFallbackWarning() {
        console.warn('⚠️  Using fallback date inputs');
        // You could add a visual warning here if needed
    }
    
    console.log('🚀 Date picker script loaded and ready');
</script>

<!-- Add visual calendar indicators -->
<script>
    // Add calendar icons to date inputs for better UX
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const dateInputs = document.querySelectorAll('#start_date, #end_date');
            dateInputs.forEach(function(input) {
                if (input && !input.classList.contains('calendar-icon-added')) {
                    input.classList.add('calendar-icon-added');
                    // Flatpickr usually adds its own calendar icon, but this ensures visibility
                }
            });
        }, 1000);
    });
</script>
@endsection