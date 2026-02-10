<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Payment Schedule Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #000;
            padding: 20px;
        }

        .header-container {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a5490;
            padding-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            padding-right: 20px;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        .date-time {
            font-size: 9px;
            margin-bottom: 10px;
            color: #666;
        }

        .institution-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #1a5490;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            text-align: center;
            color: #1a5490;
        }

        .report-subtitle {
            font-size: 10px;
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }

        .loan-info-section {
            border: 2px solid #1a5490;
            padding: 12px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding: 4px 5px;
            color: #1a5490;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            padding: 4px 5px;
            border-bottom: 1px solid #ccc;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border: 1px solid #1a5490;
            margin-bottom: 20px;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #1a5490;
            text-align: center;
            width: 25%;
        }

        .stats-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 18px;
            font-weight: bold;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #1a5490;
        }

        .payment-table th {
            background-color: #1a5490;
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #1a5490;
        }

        .payment-table td {
            padding: 8px 6px;
            border: 1px solid #1a5490;
            font-size: 9px;
        }

        .payment-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-pending {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .status-missed {
            background-color: #e5e7eb;
            color: #1f2937;
            border: 1px solid #6b7280;
        }

        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .summary-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a5490;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #000;
            padding: 10px 20px;
            background-color: white;
        }

        .page-number {
            position: fixed;
            bottom: 35px;
            right: 20px;
            font-size: 9px;
        }

        .amount-cell {
            text-align: right;
            font-weight: bold;
        }

        .center-cell {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-container">
        <div class="header-left">
            <div class="date-time">{{ $generatedDateTime }}</div>
            <div class="institution-name">LOAN MANAGEMENT SYSTEM</div>
            <div class="institution-name" style="font-size: 10px; color: #000;">Payment Schedule Department</div>
        </div>
        <div class="header-right">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
            @else
                <div style="width: 100px; height: 100px; border: 2px solid #1a5490; display: inline-block;"></div>
            @endif
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">Loan Payment Schedule Report</div>
    <div class="report-subtitle">Complete Payment Schedule for Loan #{{ $loan->loan_number }}</div>

    <!-- Loan Information -->
    <div class="loan-info-section">
        <div class="info-row">
            <div class="info-label">Loan Number:</div>
            <div class="info-value">{{ $loan->loan_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Loan Type:</div>
            <div class="info-value">{{ $loan->type }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Loan Amount:</div>
            <div class="info-value">₱{{ number_format($loan->amount, 2) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Interest Rate:</div>
            <div class="info-value">{{ $loan->interest_rate }}%</div>
        </div>
        <div class="info-row">
            <div class="info-label">Term:</div>
            <div class="info-value">{{ $loan->term_months }} months</div>
        </div>
        <div class="info-row">
            <div class="info-label">Monthly Payment:</div>
            <div class="info-value">₱{{ number_format($loan->monthly_payment ?? ($loan->amount / $loan->term_months), 2) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Report Generated:</div>
            <div class="info-value">{{ $date }} at {{ $time }}</div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stats-cell">
                <div class="stats-label">Total Payments</div>
                <div class="stats-value" style="color: #1a5490;">{{ $stats['total'] }}</div>
            </div>
            <div class="stats-cell">
                <div class="stats-label">Paid</div>
                <div class="stats-value" style="color: #059669;">{{ $stats['paid'] }}</div>
            </div>
            <div class="stats-cell">
                <div class="stats-label">Pending</div>
                <div class="stats-value" style="color: #2563eb;">{{ $stats['pending'] }}</div>
            </div>
            <div class="stats-cell">
                <div class="stats-label">Overdue</div>
                <div class="stats-value" style="color: #dc2626;">{{ $stats['overdue'] }}</div>
            </div>
        </div>
    </div>

    <!-- Payment Schedule Table -->
    @if(count($payments) > 0)
        <table class="payment-table">
            <thead>
                <tr>
                    <th style="width: 8%;" class="center-cell">Payment #</th>
                    <th style="width: 20%;" class="amount-cell">Amount</th>
                    <th style="width: 18%;">Due Date</th>
                    <th style="width: 18%;">Paid Date</th>
                    <th style="width: 15%;" class="center-cell">Status</th>
                    <th style="width: 21%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td class="center-cell">{{ $payment->payment_number }}</td>
                        <td class="amount-cell">₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                        <td>{{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('M d, Y') : '-' }}</td>
                        <td class="center-cell">
                            <span class="status-badge status-{{ $payment->status }}">
                                {{ strtoupper($payment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($payment->status === 'paid')
                                Payment completed
                            @elseif($payment->status === 'overdue')
                                {{ \Carbon\Carbon::parse($payment->due_date)->diffInDays(now()) }} days overdue
                            @elseif($payment->status === 'missed')
                                Payment missed
                            @else
                                Due {{ \Carbon\Carbon::parse($payment->due_date)->diffForHumans() }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; border: 1px solid #ccc;">
            <p style="font-weight: bold;">No payment schedule found</p>
            <p>This loan does not have any payment records.</p>
        </div>
    @endif

    <!-- Payment Summary -->
    <div class="summary-section">
        <div class="summary-title">Payment Summary</div>
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 50%; padding: 5px;">
                    <strong>Total Amount Paid:</strong>
                </div>
                <div style="display: table-cell; width: 50%; padding: 5px; text-align: right;">
                    ₱{{ number_format($stats['total_paid_amount'], 2) }}
                </div>
            </div>
            <div style="display: table-row;">
                <div style="display: table-cell; width: 50%; padding: 5px;">
                    <strong>Outstanding Balance:</strong>
                </div>
                <div style="display: table-cell; width: 50%; padding: 5px; text-align: right;">
                    ₱{{ number_format($stats['outstanding_balance'], 2) }}
                </div>
            </div>
            <div style="display: table-row;">
                <div style="display: table-cell; width: 50%; padding: 5px;">
                    <strong>Payments Remaining:</strong>
                </div>
                <div style="display: table-cell; width: 50%; padding: 5px; text-align: right;">
                    {{ $stats['total'] - $stats['paid'] }} of {{ $stats['total'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the Loan Management System</p>
        <p>© {{ date('Y') }} All rights reserved. | Confidential Document</p>
    </div>

    <!-- Page Number -->
    <div class="page-number">Page 1 of 1</div>
</body>
</html>