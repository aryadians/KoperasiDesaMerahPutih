<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSaving extends Model
{
    use HasFactory;

    protected $table = 'member_savings';

    protected $fillable = [
        'member_id',
        'type', // pokok, wajib, sukarela
        'amount',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the member associated with this savings transaction.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
