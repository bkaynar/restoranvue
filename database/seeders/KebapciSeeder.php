<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KebapciSeeder extends Seeder
{
    public function run(): void
    {
        // Masalar
        foreach (range(1, 10) as $i) {
            Table::create([
                'name' => 'Masa ' . $i
            ]);
        }

        // Kategoriler ve ürünler
        $kategoriler = [
            'Kebaplar' => [
                ['name' => 'Adana Kebap', 'price' => 180, 'stock' => 50],
                ['name' => 'Urfa Kebap', 'price' => 175, 'stock' => 40],
                ['name' => 'Çöp Şiş', 'price' => 160, 'stock' => 30],
                ['name' => 'Patlıcanlı Kebap', 'price' => 200, 'stock' => 20],
            ],
            'Pide & Lahmacun' => [
                ['name' => 'Lahmacun', 'price' => 60, 'stock' => 100],
                ['name' => 'Kuşbaşılı Pide', 'price' => 120, 'stock' => 40],
                ['name' => 'Kaşarlı Pide', 'price' => 110, 'stock' => 35],
            ],
            'İçecekler' => [
                ['name' => 'Ayran', 'price' => 25, 'stock' => 100],
                ['name' => 'Kola', 'price' => 35, 'stock' => 80],
                ['name' => 'Su', 'price' => 15, 'stock' => 200],
            ],
        ];

        foreach ($kategoriler as $kategori => $urunler) {
            $cat = Category::create([
                'name' => $kategori,
                'is_active' => true
            ]);
            foreach ($urunler as $urun) {
                Product::create([
                    'category_id' => $cat->id,
                    'name' => $urun['name'],
                    'price' => $urun['price'],
                    'stock' => $urun['stock'],
                    'is_active' => true
                ]);
            }
        }

        // Basit bir yönetici kullanıcı
        User::create([
            'name' => 'Yönetici',
            'email' => 'admin@kebapci.com',
            'password' => Hash::make('password'),
        ]);
    }
}
