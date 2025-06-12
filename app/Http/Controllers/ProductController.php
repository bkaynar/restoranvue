<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Ürünlerin listesini görüntüle
     */
    public function index()
    {
        $products = Product::with('category')->get();

        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    /**
     * API: Tüm aktif ürünleri getir
     */
    public function list()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Yeni ürün ekleme formunu göster
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Products/Create', [
            'categories' => $categories
        ]);
    }

    public function manage()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::with('category')->get();
        return Inertia::render('Products/Manage', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * Yeni ürün ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Resim yükleme işlemi
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $path;
        }

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('message', 'Ürün başarıyla oluşturuldu');
    }

    /**
     * Belirli bir ürünü göster
     */
    public function show(Product $product)
    {
        $product->load('category');

        return Inertia::render('Products/Show', [
            'product' => $product
        ]);
    }

    /**
     * API: Belirli bir ürünün detayını getir
     */
    public function detail(Product $product)
    {
        $product->load('category');

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Ürün düzenleme formunu göster
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Ürünü güncelle
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Resim yükleme işlemi
        if ($request->hasFile('image')) {
            // Eski resmi silme
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $path = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $path;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('message', 'Ürün başarıyla güncellendi');
    }

    /**
     * Ürünü sil
     */
    public function destroy(Product $product)
    {
        // Ürün daha önce sipariş edilmiş mi kontrol et
        if ($product->orderItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Bu ürün daha önce sipariş edildiği için silinemiyor');
        }

        // Ürün resmini sil
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('message', 'Ürün başarıyla silindi');
    }

    /**
     * Ürün durumunu değiştir (aktif/pasif)
     */
    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return redirect()->back()
            ->with('message', 'Ürün durumu değiştirildi');
    }

    /**
     * Kategoriye göre ürünleri listele (ana ürün sayfası)
     */
    public function byCategory()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::with('category')->get();
        return Inertia::render('Products/ByCategory', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
