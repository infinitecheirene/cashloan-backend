<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentSchedulePDFController extends Controller
{
    /**
     * Export payment schedule as PDF
     */
    public function exportPaymentSchedulePDF($loanId)
    {
        try {
            // Get authenticated user
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Fetch the loan (ensure it belongs to the user if not admin)
            $loan = Loan::where('id', $loanId);
            
            // If not admin, only show user's own loans
            if ($user->role !== 'admin') {
                $loan = $loan->where('user_id', $user->id);
            }
            
            $loan = $loan->firstOrFail();

            // Fetch all payments for this loan
            $payments = Payment::where('loan_id', $loanId)
                ->orderBy('payment_number', 'asc')
                ->get();

            // If no payments exist in database, generate them
            if ($payments->isEmpty()) {
                $payments = $this->generatePaymentSchedule($loan);
            }

            // Calculate statistics
            $stats = [
                'total' => $payments->count(),
                'paid' => $payments->where('status', 'paid')->count(),
                'pending' => $payments->where('status', 'pending')->count(),
                'overdue' => $payments->where('status', 'overdue')->count(),
                'missed' => $payments->where('status', 'missed')->count(),
                'total_paid_amount' => $payments->where('status', 'paid')->sum('amount'),
                'outstanding_balance' => $payments->whereIn('status', ['pending', 'overdue', 'missed'])->sum('amount'),
            ];

            // Prepare data for PDF
            $data = [
                'loan' => $loan,
                'payments' => $payments,
                'stats' => $stats,
                'date' => now()->format('F d, Y'),
                'time' => now()->format('h:i A'),
                'generatedDateTime' => now()->format('F d, Y g:i A'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.payment-schedule-report', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 15)
                ->setOption('margin-bottom', 15)
                ->setOption('margin-left', 15)
                ->setOption('margin-right', 15);

            // Download PDF
            return $pdf->download('loan-' . $loan->loan_number . '-payment-schedule-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Loan not found for PDF generation', [
                'loan_id' => $loanId,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Loan not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error generating payment schedule PDF', [
                'loan_id' => $loanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate payment schedule if not in database
     */
    private function generatePaymentSchedule($loan)
    {
        $monthlyPayment = $loan->monthly_payment ?? 
            ($loan->amount / $loan->term_months);

        $startDate = $loan->disbursement_date 
            ? Carbon::parse($loan->disbursement_date)
            : Carbon::now();

        $payments = collect();
        $today = Carbon::now();

        for ($i = 1; $i <= $loan->term_months; $i++) {
            $dueDate = (clone $startDate)->addMonths($i);

            // Determine status
            $status = 'pending';
            if ($dueDate->lt($today)) {
                $daysPast = $today->diffInDays($dueDate);
                $status = $daysPast > 30 ? 'missed' : 'overdue';
            }

            $payment = (object) [
                'id' => $loan->id * 1000 + $i,
                'loan_id' => $loan->id,
                'amount' => number_format($monthlyPayment, 2, '.', ''),
                'due_date' => $dueDate->toDateString(),
                'paid_date' => null,
                'status' => $status,
                'payment_number' => $i,
            ];

            $payments->push($payment);
        }

        return $payments;
    }
}