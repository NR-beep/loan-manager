<?php

namespace App\Http\Controllers;

use App\Models\Loan;

class LoanPaymentController extends Controller
{
    public function index($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $payments = $loan->payments;
        return view('loans.payments', compact('loan', 'payments'));
    }
}
