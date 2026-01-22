<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'annual',
                'default_days_per_year' => 20,
                'max_consecutive_days' => 10,
                'requires_approval' => true,
                'is_active' => true,
                'description' => 'Standard annual vacation leave',
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'sick',
                'default_days_per_year' => 10,
                'max_consecutive_days' => 5,
                'requires_approval' => false,
                'is_active' => true,
                'description' => 'Leave for medical reasons or illness',
            ],
            [
                'name' => 'Casual Leave',
                'code' => 'casual',
                'default_days_per_year' => 7,
                'max_consecutive_days' => 3,
                'requires_approval' => true,
                'is_active' => true,
                'description' => 'Short leave for personal matters',
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'maternity',
                'default_days_per_year' => 90,
                'max_consecutive_days' => null,
                'requires_approval' => true,
                'is_active' => true,
                'description' => 'Leave for childbirth and childcare',
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'paternity',
                'default_days_per_year' => 10,
                'max_consecutive_days' => 5,
                'requires_approval' => true,
                'is_active' => true,
                'description' => 'Leave for new fathers',
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'emergency',
                'default_days_per_year' => 3,
                'max_consecutive_days' => 3,
                'requires_approval' => true,
                'is_active' => true,
                'description' => 'Leave for urgent personal emergencies',
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}