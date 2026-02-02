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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('loan_officer_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Loan Details
            $table->enum('type', ['personal', 'auto', 'home', 'business', 'student']);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_months');
            $table->text('purpose');
            $table->string('employment_status')->nullable();
            
            // Status & Tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'completed', 'defaulted'])
                  ->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->date('start_date')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('borrower_id');
            $table->index('lender_id');
            $table->index('loan_officer_id');
            $table->index('status');
            $table->index(['borrower_id', 'status']);
            $table->index(['lender_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};