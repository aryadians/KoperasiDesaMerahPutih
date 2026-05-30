<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price_member',
        'price_non_member',
        'current_stock',
        'unit',
        'is_local_product',
    ];

    protected $casts = [
        'price_member' => 'decimal:2',
        'price_non_member' => 'decimal:2',
        'current_stock' => 'integer',
        'is_local_product' => 'boolean',
    ];

    /**
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
