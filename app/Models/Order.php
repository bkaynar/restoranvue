<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'user_id',
        'note',
        'status',
        'total',
        'closed_at'
    ];

    /**
     * Tarihleri Carbon olarak formatla
     */
    protected $casts = [
        'closed_at' => 'datetime'
    ];

    /**
     * Siparişin ait olduğu masayı getir
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Siparişi alan kullanıcıyı getir
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Siparişe ait ürünleri getir
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Siparişe ait ödemeleri getir
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Siparişin toplam tutarını hesapla
     */
    public function calculateTotal()
    {
        $total = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return $total;
    }

    /**
     * Toplam ödenen tutarı hesapla
     */
    public function totalPaid()
    {
        return $this->payments->where('status', 'ödendi')->sum('amount');
    }

    /**
     * Kalan ödeme tutarını hesapla
     */
    public function remainingAmount()
    {
        return $this->total - $this->totalPaid();
    }
}
