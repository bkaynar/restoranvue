<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'image_path',
        'description',
        'price',
        'stock',
        'is_active'
    ];

    /**
     * Ürünün ait olduğu kategoriyi getir
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Bu ürünün yer aldığı sipariş öğelerini getir
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
