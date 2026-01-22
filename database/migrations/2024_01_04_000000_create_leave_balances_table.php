<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->decimal('allocated_days', 5, 1)->default(0); // Total days allocated for the year
            $table->decimal('used_days', 5, 1)->default(0); // Days used so far
            $table->decimal('pending_days', 5, 1)->default(0); // Days in pending requests
            $table->decimal('carried_forward_days', 5, 1)->default(0); // Days carried forward from previous year
            $table->decimal('adjustment_days', 5, 1)->default(0); // Manual adjustments by admin
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure unique combination of user, leave_type, and year
            $table->unique(['user_id', 'leave_type_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};