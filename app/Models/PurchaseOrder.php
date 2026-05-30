<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'product_id',
        'quantity',
        'cost_price',
        'selling_price_member',
        'selling_price_non_member',
        'total_cost',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
