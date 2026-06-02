<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id', 'nik', 'nomor_anggota', 'alamat_desa', 'tanggal_bergabung', 'total_poin', 'status_aktif', 'tier'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savings()
    {
        return $this->hasMany(MemberSaving::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Recalculate and save the member's tier based on current points.
     */
    public function recalculateTier()
    {
        $newTier = 'silver';
        if ($this->total_poin > 5000) {
            $newTier = 'platinum';
        } elseif ($this->total_poin > 1000) {
            $newTier = 'gold';
        }

        if ($this->tier !== $newTier) {
            $this->tier = $newTier;
            $this->save();
        }
    }

    /**
     * Get discount multiplier for retail purchases based on database tier.
     */
    public function getTierDiscountMultiplierAttribute()
    {
        switch ($this->tier) {
            case 'platinum': return 0.10; // 10% discount
            case 'gold': return 0.05;     // 5% discount
            default: return 0.00;
        }
    }
}
