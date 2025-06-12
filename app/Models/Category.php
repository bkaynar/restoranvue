<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'is_active'];

    /**
     * Kategoriye ait tüm ürünleri getir
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Yalnızca aktif ürünleri getir
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }
}
