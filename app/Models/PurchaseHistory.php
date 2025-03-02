<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'pharmacy_id',
        'mask_id',
        'transaction_amount',
        'transaction_date'
    ];
    
    protected $casts = [
        'transaction_date' => 'datetime'
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
    
    public function mask()
    {
        return $this->belongsTo(Mask::class);
    }
}