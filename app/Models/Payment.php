<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'loan_id', 'payment_date', 'principal', 'interest', 'total_payment', 'remaining_balance'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
