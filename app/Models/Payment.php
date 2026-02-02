<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'amount',
        'principal_payment',
        'interest_payment',
        'status',
        'due_date',
        'paid_date',
        'days_overdue',
        'late_fee',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'principal_payment' => 'decimal:2',
        'interest_payment' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    /**
     * Get loan relationship
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(?string $paymentMethod = null, ?string $transactionId = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'days_overdue' => 0,
        ]);

        // Update loan balance
        $this->loan->decrement('balance', $this->principal_payment);
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now()->toDateString();
    }

    /**
     * Calculate late fee
     */
    public function calculateLateFee(): float
    {
        if ($this->isOverdue()) {
            // Simple late fee calculation: 5% of amount or $25, whichever is higher
            return max((float)($this->amount * 0.05), 25);
        }

        return 0;
    }
}
