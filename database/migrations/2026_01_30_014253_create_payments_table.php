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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('paid_by')->constrained('users')->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            
            // Payment Information
            $table->enum('status', ['pending', 'paid', 'late', 'missed'])->default('pending');
            $table->string('payment_method')->nullable(); // e.g., 'bank_transfer', 'cash', 'credit_card'
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            
            // Late Payment Tracking
            $table->decimal('late_fee', 15, 2)->default(0);
            $table->integer('days_late')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('loan_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('paid_date');
            $table->index(['loan_id', 'status']);
            $table->index(['loan_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};