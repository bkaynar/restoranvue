<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    /**
     * Masaya ait tüm siparişleri getir
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Masanın aktif siparişini getir
     */
    public function activeOrder()
    {
        return $this->hasOne(Order::class)->whereNotIn('status', ['kapandı', 'ödendi', 'iptal'])->latest();
    }
}
