<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropAbsorption extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'member_id',
        'product_name',
        'quantity',
        'price_per_unit',
        'total_payout',
        'status',
        'absorption_date',
        'scale_image',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'total_payout' => 'decimal:2',
        'absorption_date' => 'datetime',
    ];

    /**
     * Get the branch this crop absorption belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the member (farmer) who sold this crop absorption.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
