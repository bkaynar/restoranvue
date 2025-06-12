<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Kategorilerin listesini görüntüle
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories
        ]);
    }

    /**
     * API: Tüm kategorileri getir
     */
    public function list()
    {
        $categories = Category::where('is_active', true)->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Yeni kategori ekleme formunu göster
     */
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Yeni kategori ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category = Category::create($validated);

        return redirect()->route('categories.index')
            ->with('message', 'Kategori başarıyla oluşturuldu');
    }

    /**
     * Belirli bir kategoriyi ve ürünlerini göster
     */
    public function show(Category $category)
    {
        $category->load('products');

        return Inertia::render('Categories/Show', [
            'category' => $category
        ]);
    }

    /**
     * API: Belirli bir kategorinin tüm ürünlerini getir
     */
    public function products(Category $category)
    {
        $products = $category->products()
            ->where('is_active', true)
            ->get();

        return response()->json([
            'category' => $category,
            'products' => $products
        ]);
    }

    /**
     * Kategori düzenleme formunu göster
     */
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category
        ]);
    }

    /**
     * Kategoriyi güncelle
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('message', 'Kategori başarıyla güncellendi');
    }

    /**
     * Kategoriyi sil
     */
    public function destroy(Category $category)
    {
        // Bu kategoride ürün var mı kontrol edelim
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Bu kategoride ürünler bulunduğu için silinemiyor');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('message', 'Kategori başarıyla silindi');
    }

    /**
     * Kategori durumunu değiştir (aktif/pasif)
     */
    public function toggleStatus(Category $category)
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()
            ->with('message', 'Kategori durumu değiştirildi');
    }
}
