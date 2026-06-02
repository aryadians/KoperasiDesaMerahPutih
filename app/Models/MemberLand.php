<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLand extends Model
{
    protected $fillable = [
        'member_id',
        'location_name',
        'coordinates',
        'area_m2',
        'commodity_type',
        'last_planting_date',
        'status',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
