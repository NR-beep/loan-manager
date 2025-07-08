<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Loan;

class LoanPayments extends Component
{
    public $loan;
    public $payments = [];

    public function mount($loanId)
    {
        $this->loan = Loan::with('payments')->findOrFail($loanId);
        $this->payments = $this->loan->payments;
    }

    public function render()
    {
        return view('livewire.loan-payments');
    }
}
