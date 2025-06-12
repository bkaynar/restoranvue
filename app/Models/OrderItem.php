<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'note',
        'status'
    ];

    /**
     * Bu ürünün ait olduğu siparişi getir
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Bu sipariş ürününün ilişkili olduğu ürünü getir
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Toplam tutarı hesapla
     */
    public function total()
    {
        return $this->price * $this->quantity;
    }
}
