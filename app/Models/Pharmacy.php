<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cash_balance'
    ];

    public function openingHours()
    {
        return $this->hasMany(PharmacyOpeningHour::class);
    }

    public function masks()
    {
        return $this->belongsToMany(Mask::class, 'pharmacy_masks')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function pharmacyMasks()
    {
        return $this->hasMany(PharmacyMask::class);
    }

    public function getMaskPrice($maskId)
    {
        $pharmacyMask = $this->pharmacyMasks()
            ->where('mask_id', $maskId)
            ->first();

        return $pharmacyMask ? $pharmacyMask->price : null;
    }
}