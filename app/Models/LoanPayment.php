<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'amount_paid',
        'penalty',
        'installment_number',
        'payment_date',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'penalty' => 'decimal:2',
        'installment_number' => 'integer',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the loan associated with this payment.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
