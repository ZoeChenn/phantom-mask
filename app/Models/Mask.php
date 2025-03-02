<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mask extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'color',
        'quantity_per_pack'
    ];

    public function pharmacies()
    {
        return $this->belongsToMany(Pharmacy::class, 'pharmacy_masks')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function pharmacyMasks()
    {
        return $this->hasMany(PharmacyMask::class);
    }

    public function getPriceAtPharmacy($pharmacyId)
    {
        $pharmacyMask = $this->pharmacyMasks()
            ->where('pharmacy_id', $pharmacyId)
            ->first();

        return $pharmacyMask ? $pharmacyMask->price : null;
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand} ({$this->color}) ({$this->quantity_per_pack} per pack)";
    }
}