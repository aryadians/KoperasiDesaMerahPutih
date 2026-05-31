<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    /**
     * Get the users associated with the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the products associated with the branch.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders associated with the branch.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the crop absorptions associated with the branch.
     */
    public function cropAbsorptions(): HasMany
    {
        return $this->hasMany(CropAbsorption::class);
    }

    /**
     * Get the loans associated with the branch.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
