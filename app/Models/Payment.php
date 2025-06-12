<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'status',
        'note',
        'paid_at'
    ];

    /**
     * Tarihleri Carbon olarak formatla
     */
    protected $casts = [
        'paid_at' => 'datetime'
    ];

    /**
     * Bu ödemenin ait olduğu siparişi getir
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
