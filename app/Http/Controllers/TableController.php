<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    /**
     * Masaların listesini görüntüle
     */
    public function index()
    {
        $tables = Table::withCount(['orders' => function ($query) {
            $query->whereNotIn('status', ['kapandı', 'ödendi', 'iptal']);
        }])->get();

        return Inertia::render('Tables/Index', [
            'tables' => $tables
        ]);
    }

    /**
     * API: Tüm masaları getir
     */
    public function list()
    {
        $tables = Table::withCount(['orders' => function ($query) {
            $query->whereNotIn('status', ['kapandı', 'ödendi', 'iptal']);
        }])->get();

        return response()->json([
            'tables' => $tables
        ]);
    }

    /**
     * Yeni masa ekleme formunu göster
     */
    public function create()
    {
        return Inertia::render('Tables/Create');
    }

    /**
     * Yeni masa ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $table = Table::create($validated);

        return redirect()->route('tables.index')
            ->with('message', 'Masa başarıyla oluşturuldu');
    }

    /**
     * Belirli bir masayı göster
     */
    public function show(Table $table)
    {
        $table->load(['activeOrder.items.product', 'activeOrder.payments']);

        return Inertia::render('Tables/Show', [
            'table' => $table
        ]);
    }

    /**
     * API: Belirli bir masayı getir
     */
    public function detail(Table $table)
    {
        $table->load(['activeOrder.items.product', 'activeOrder.payments']);

        return response()->json([
            'table' => $table
        ]);
    }

    /**
     * Masa düzenleme formunu göster
     */
    public function edit(Table $table)
    {
        return Inertia::render('Tables/Edit', [
            'table' => $table
        ]);
    }

    /**
     * Masayı güncelle
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('message', 'Masa başarıyla güncellendi');
    }

    /**
     * Masayı sil
     */
    public function destroy(Table $table)
    {
        // Masanın aktif siparişi var mı kontrol edelim
        if ($table->activeOrder) {
            return redirect()->route('tables.index')
                ->with('error', 'Bu masada aktif sipariş bulunduğu için silinemiyor');
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('message', 'Masa başarıyla silindi');
    }
}
