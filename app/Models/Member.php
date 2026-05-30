<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nik',
        'nomor_anggota',
        'alamat_desa',
        'tanggal_bergabung',
        'total_poin',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'status_aktif' => 'boolean',
        'total_poin' => 'integer',
    ];

    /**
     * Get the user that owns the member profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the savings recorded for this member.
     */
    public function savings(): HasMany
    {
        return $this->hasMany(MemberSaving::class);
    }

    /**
     * Get the loans associated with this member.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the crop absorption transactions for this member.
     */
    public function cropAbsorptions(): HasMany
    {
        return $this->hasMany(CropAbsorption::class);
    }
}
