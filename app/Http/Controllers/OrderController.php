<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Tüm siparişleri görüntüle
     */
    public function index()
    {
        $orders = Order::with(['table', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * API: Aktif siparişleri getir
     */
    public function activeOrders()
    {
        $orders = Order::with(['table', 'items.product'])
            ->whereNotIn('status', ['kapandı', 'ödendi', 'iptal'])
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Yeni sipariş oluştur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Önce bu masada aktif bir sipariş var mı kontrol et
        $table = Table::findOrFail($request->table_id);
        $existingOrder = $table->activeOrder;

        if ($existingOrder) {
            return redirect()->back()->withErrors(['table_id' => 'Bu masada zaten aktif bir sipariş bulunuyor']);
        }

        // Yeni sipariş oluştur
        $order = Order::create([
            'table_id' => $request->table_id,
            'user_id' => Auth::id(),
            'note' => $request->note,
            'status' => 'hazırlanıyor',
            'total' => 0.00,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $lineTotal = $product->price * $item['quantity'];
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'status' => 'hazırlanıyor',
            ]);
            $total += $lineTotal;
        }
        $order->total = $total;
        $order->save();

        // Inertia ile siparişler listesine yönlendir
        return redirect()->route('orders.index')->with('success', 'Sipariş başarıyla oluşturuldu');
    }

    /**
     * Siparişe ürün ekle
     */
    public function addItem(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        // Siparişin durumunu kontrol et
        if (in_array($order->status, ['kapandı', 'ödendi', 'iptal'])) {
            return response()->json([
                'message' => 'Bu sipariş tamamlandı veya iptal edildi, yeni ürün eklenemez'
            ], 400);
        }

        // Ürünün bilgilerini al
        $product = Product::findOrFail($request->product_id);

        // Sipariş öğesini ekle
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
            'note' => $request->note,
            'status' => 'hazırlanıyor',
        ]);

        // Sipariş toplamını güncelle
        $order->total = $order->calculateTotal();
        $order->save();

        return response()->json([
            'message' => 'Ürün siparişe eklendi',
            'order_item' => $orderItem
        ]);
    }

    /**
     * Sipariş öğesini güncelle
     */
    public function updateItem(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'status' => 'required|in:hazırlanıyor,hazır,teslim,iptal',
        ]);

        // Order durumunu kontrol et
        if (in_array($orderItem->order->status, ['kapandı', 'ödendi', 'iptal'])) {
            return response()->json([
                'message' => 'Bu sipariş tamamlandı veya iptal edildi, güncelleme yapılamaz'
            ], 400);
        }

        $orderItem->update($validated);

        // Sipariş toplamını güncelle
        $order = $orderItem->order;
        $order->total = $order->calculateTotal();
        $order->save();

        return response()->json([
            'message' => 'Sipariş öğesi güncellendi',
            'order_item' => $orderItem
        ]);
    }

    /**
     * Sipariş öğesini sil
     */
    public function removeItem(OrderItem $orderItem)
    {
        // Order durumunu kontrol et
        if (in_array($orderItem->order->status, ['kapandı', 'ödendi', 'iptal'])) {
            return response()->json([
                'message' => 'Bu sipariş tamamlandı veya iptal edildi, silme işlemi yapılamaz'
            ], 400);
        }

        $order = $orderItem->order;

        $orderItem->delete();

        // Sipariş toplamını güncelle
        $order->total = $order->calculateTotal();
        $order->save();

        return response()->json([
            'message' => 'Sipariş öğesi silindi'
        ]);
    }

    /**
     * Belirli bir siparişi görüntüle
     */
    public function show(Order $order)
    {
        $order->load(['table', 'user', 'items.product', 'payments']);

        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * API: Sipariş detayını getir
     */
    public function detail(Order $order)
    {
        $order->load(['table', 'items.product', 'payments']);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Siparişi güncelle
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'note' => 'nullable|string',
            'status' => 'required|in:hazırlanıyor,hazır,teslim,kapandı,ödendi,iptal',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Aynı product_id'ye sahip item'ların quantity'sini birleştir
        $mergedItems = collect($request->items)
            ->groupBy('product_id')
            ->map(function ($group) {
                return [
                    'product_id' => $group[0]['product_id'],
                    'quantity' => $group->sum('quantity'),
                ];
            })->values()->all();

        // Sipariş durumu kapandı ise kapatma zamanını kaydet
        if ($request->status === 'kapandı' && $order->status !== 'kapandı') {
            $validated['closed_at'] = now();
        }

        $order->update([
            'note' => $request->note,
            'status' => $request->status,
            'closed_at' => $validated['closed_at'] ?? $order->closed_at,
        ]);

        // Sipariş ürünlerini güncelle
        $existingItems = $order->items()->get()->keyBy('product_id');
        $newProductIds = collect($mergedItems)->pluck('product_id')->all();

        // Silinen ürünleri kaldır
        foreach ($existingItems as $productId => $orderItem) {
            if (!in_array($productId, $newProductIds)) {
                $orderItem->delete();
            }
        }

        $total = 0;
        foreach ($mergedItems as $item) {
            $product = Product::findOrFail($item['product_id']);
            $lineTotal = $product->price * $item['quantity'];
            if (isset($existingItems[$item['product_id']])) {
                // Varsa güncelle
                $existingItems[$item['product_id']]->update([
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            } else {
                // Yoksa ekle
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'status' => 'hazırlanıyor',
                ]);
            }
            $total += $lineTotal;
        }
        $order->total = $total;
        $order->save();

        return redirect()->route('orders.show', $order->id)->with('success', 'Sipariş güncellendi');
    }

    /**
     * Siparişi iptal et
     */
    public function cancel(Order $order)
    {
        // Durumu kontrol et (ödendi durumunda iptal edilemez)
        if ($order->status === 'ödendi') {
            return response()->json([
                'message' => 'Ödenmiş sipariş iptal edilemez'
            ], 400);
        }

        $order->update([
            'status' => 'iptal',
            'closed_at' => now()
        ]);

        return response()->json([
            'message' => 'Sipariş iptal edildi',
            'order' => $order
        ]);
    }

    /**
     * Sipariş oluşturma formunu göster
     */
    public function create()
    {
        $tables = Table::all();
        // Sadece aktif ürünleri kategorilere göre gruplayarak al
        $categories = \App\Models\Category::with(['activeProducts'])->where('is_active', true)->get();
        // Kategoriye products yerine activeProducts olarak gönder
        $categories = $categories->map(function ($cat) {
            $cat->products = $cat->activeProducts;
            unset($cat->activeProducts);
            return $cat;
        });
        return Inertia::render('Orders/Create', [
            'tables' => $tables,
            'categories' => $categories
        ]);
    }

    /**
     * Sipariş düzenleme formunu göster
     */
    public function edit(Order $order)
    {
        $tables = Table::all();
        $categories = \App\Models\Category::with(['products'])->get();
        $order->load(['items']);
        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'tables' => $tables,
            'categories' => $categories
        ]);
    }
}
