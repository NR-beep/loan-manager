<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoanManager extends Component
{

    public $user_id, $loans, $loan_amount, $interest_rate, $loan_term, $start_date, $selected_id, $status;
    public $isEdit = false;
    public $editingId = null;

    public $schedule = [];

    protected $rules = [
        'loan_amount' => 'required|numeric|min:1',
        'interest_rate' => 'required|numeric|min:0',
        'loan_term' => 'required|integer|min:1',
        'start_date' => 'required|date',
    ];

    public function render()
    {
        $this->loans = Loan::where('user_id', Auth::id())->latest()->get();
        return view('livewire.loan-manager');
    }

    public function store()
    {
        $this->validate();
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'loan_term' => $this->loan_term,
            'start_date' => $this->start_date,
        ]);
        $this->schedule = $this->generatePayments($loan);
        $this->resetInput();
    }

    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $this->editingId = $loan->id;
        $this->selected_id = $id;
        $this->loan_amount = $loan->loan_amount;
        $this->interest_rate = $loan->interest_rate;
        $this->loan_term = $loan->loan_term;
        $this->start_date = $loan->start_date;
        $this->status = $loan->status;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'loan_term' => 'required|numeric',
            'start_date' => 'required|date',
        ]);

        $loan = Loan::findOrFail($this->editingId);
        $loan->update([
            'loan_amount' => $this->loan_amount,
            'interest_rate' => $this->interest_rate,
            'loan_term' => $this->loan_term,
            'start_date' => $this->start_date,
            'status' => $this->status ?? 'pending',
        ]);

        $this->resetForm();
        $this->loadLoans();
    }

    public function delete($id)
    {
        Loan::destroy($id);
    }

    public function resetInput()
    {
        $this->loan_amount = null;
        $this->interest_rate = null;
        $this->loan_term = null;
        $this->start_date = null;
        $this->selected_id = null;
        $this->isEdit = false;
    }

    public function updateStatus($loanId, $newStatus)
    {
        $loan = Loan::findOrFail($loanId);
        $loan->status = $newStatus;
        $loan->save();

        session()->flash('message', 'Loan status updated.');
    }

    public function generatePayments(Loan $loan)
    {
        $P = $loan->loan_amount;
        $r = $loan->interest_rate / 100 / 12;
        $n = $loan->loan_term;

        if ($r == 0) {
            $M = $P / $n;
        } else {
            $M = $P * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);
        }

        $schedule = [];
        $balance = $P;
        $paymentDate = Carbon::parse($loan->start_date)->addMonth();

        for ($i = 1; $i <= $n; $i++) {
            $interest = $balance * $r;
            $principal = $M - $interest;
            $balance -= $principal;

            $schedule[] = [
                'number' => $i,
                'date' => $paymentDate->toDateString(),
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'total' => round($M, 2),
                'balance' => round(max($balance, 0), 2),
            ];

            Payment::create([
                'loan_id' => $loan->id,
                'payment_date' => $paymentDate->copy(),
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'total_payment' => round($M, 2),
                'remaining_balance' => round(max($balance, 0), 2),
            ]);

            $paymentDate->addMonth();
        }

        return $schedule;
    }

    public function viewSchedule($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $this->schedule = $this->generatePayments($loan);
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->loan_amount = null;
        $this->interest_rate = null;
        $this->loan_term = null;
        $this->start_date = null;
        $this->status = null;
        $this->isEdit = false;
    }

    public function loadLoans()
    {
        $this->loans = Loan::all();
    }

}
