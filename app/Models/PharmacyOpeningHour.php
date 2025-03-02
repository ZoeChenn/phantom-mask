<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyOpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'day_of_week',
        'open_time',
        'close_time'
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}