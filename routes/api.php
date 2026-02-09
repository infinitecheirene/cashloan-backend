<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\LenderController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanOfficerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', fn() => response()->json(['status' => 'OK']));

// ------------------
// Public Auth Routes
// ------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// ------------------
// Protected Routes
// ------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('resend-email-verification', [AuthController::class, 'resendEmailVerification']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::get('profile', [AuthController::class, 'profile']);
    });

    // ------------------
    // Loan Routes
    // ------------------
    Route::prefix('loans')->group(function () {

        // Statistics
        Route::get('statistics/user', [LoanController::class, 'statistics']);

        // CRUD
        Route::get('/', [LoanController::class, 'index']);
        Route::post('/', [LoanController::class, 'store']);
        Route::get('{loan}', [LoanController::class, 'show']);
        Route::put('{loan}', [LoanController::class, 'update']);

        // Actions
        Route::post('{loan}/approve', [LoanController::class, 'approve']);
        Route::post('{loan}/reject', [LoanController::class, 'reject']);
        Route::post('{loan}/activate', [LoanController::class, 'activate']);

        // Documents
        Route::get('{loan}/documents/{document}/download', [LoanController::class, 'downloadDocument']);

        // Loan payments
        Route::get('{loan}/payments', [PaymentController::class, 'index']);
        Route::post('{loan}/payments', [PaymentController::class, 'store']);
    });

    // ------------------
    // Payment Routes
    // ------------------
    Route::get('payments/upcoming', [PaymentController::class, 'upcoming']);
    Route::get('payments/overdue', [PaymentController::class, 'overdue']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments', [PaymentController::class, 'recordPayment']);

    // ------------------
    // Users / Borrowers / Lenders / Officers
    // ------------------
    Route::get('user', fn(Request $request) => $request->user());

    Route::get('borrowers', [BorrowerController::class, 'index']);
    Route::get('borrowers/{borrower}/loans', [LoanController::class, 'getBorrowerLoans']);

    Route::get('lenders', [LenderController::class, 'index']);
    Route::get('lender/dashboard', [LenderController::class, 'dashboard']);
    Route::get('loan-officers', [LoanOfficerController::class, 'index']);

    // ------------------
    // Admin dashboard
    // ------------------
    Route::get('admin/dashboard', function () {
        try {
            $users = App\Models\User::all();
            $loans = App\Models\Loan::with(['borrower', 'lender', 'loanOfficer'])->get();

            return response()->json([
                'users' => $users,
                'loans' => $loans,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error fetching dashboard data',
                'message' => $e->getMessage(),
            ], 500);
        }
    });
});
