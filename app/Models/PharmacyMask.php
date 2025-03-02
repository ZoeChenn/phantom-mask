<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyMask extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_masks';

    protected $fillable = [
        'pharmacy_id',
        'mask_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function mask()
    {
        return $this->belongsTo(Mask::class);
    }
}