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
     * Get the dynamic tier based on points.
     */
    public function getCalculatedTierAttribute()
    {
        if ($this->total_poin > 5000) return 'platinum';
        if ($this->total_poin > 1000) return 'gold';
        return 'silver';
    }

    /**
     * Get discount multiplier for retail purchases.
     */
    public function getTierDiscountMultiplierAttribute()
    {
        switch ($this->calculated_tier) {
            case 'platinum': return 0.10; // 10% discount
            case 'gold': return 0.05;     // 5% discount
            default: return 0.00;
        }
    }
}
