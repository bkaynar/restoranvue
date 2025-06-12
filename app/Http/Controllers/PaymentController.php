<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Ödemelerin listesini görüntüle
     */
    public function index()
    {
        $payments = Payment::with(['order.table'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Payments/Index', [
            'payments' => $payments
        ]);
    }

    /**
     * Belirli bir siparişe ödeme ekleme formunu göster
     */
    public function create(Order $order)
    {
        $order->load(['table', 'items', 'payments']);
        $remainingAmount = $order->remainingAmount();

        return Inertia::render('Payments/Create', [
            'order' => $order,
            'remainingAmount' => $remainingAmount
        ]);
    }

    /**
     * Siparişe ödeme ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:nakit,kredi_kartı,online,fiş,diğer',
            'note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Sipariş durumunu kontrol et
        if (in_array($order->status, ['iptal'])) {
            return redirect()->back()->withErrors(['order_id' => 'İptal edilmiş siparişe ödeme eklenemez']);
        }

        // Ödeme miktarını kontrol et
        $remainingAmount = $order->remainingAmount();
        if ($request->amount > $remainingAmount) {
            return redirect()->back()->withErrors(['amount' => 'Ödeme miktarı kalan miktardan fazla olamaz']);
        }

        // Ödeme oluştur
        $payment = Payment::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'ödendi',
            'note' => $request->note,
            'paid_at' => now(),
        ]);

        // Eğer tüm ödeme yapıldıysa, siparişin durumunu güncelle
        if ($order->remainingAmount() <= 0) {
            $order->update([
                'status' => 'ödendi',
                'closed_at' => now()
            ]);
        } else {
            $order->update([
                'status' => 'kapandı'
            ]);
        }

        return redirect()->back()->with('success', 'Ödeme başarıyla alındı');
    }

    /**
     * Belirli bir ödemeyi görüntüle
     */
    public function show(Payment $payment)
    {
        $payment->load('order.table');

        return Inertia::render('Payments/Show', [
            'payment' => $payment
        ]);
    }

    /**
     * API: Belirli bir ödemenin detayını getir
     */
    public function detail(Payment $payment)
    {
        $payment->load('order.table');

        return response()->json([
            'payment' => $payment
        ]);
    }

    /**
     * Ödemeyi güncelle
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:nakit,kredi_kartı,online,fiş,diğer',
            'status' => 'required|in:beklemede,ödendi,iptal',
            'note' => 'nullable|string',
        ]);

        $payment->update($validated);

        // Siparişin durumunu kontrol et ve güncelle
        $order = $payment->order;
        if ($order->remainingAmount() <= 0 && $payment->status === 'ödendi') {
            $order->update([
                'status' => 'ödendi'
            ]);
        } else if ($order->status === 'ödendi' && $order->remainingAmount() > 0) {
            $order->update([
                'status' => 'kapandı'
            ]);
        }

        return response()->json([
            'message' => 'Ödeme güncellendi',
            'payment' => $payment
        ]);
    }

    /**
     * Ödemeyi iptal et
     */
    public function cancel(Payment $payment)
    {
        $payment->update([
            'status' => 'iptal'
        ]);

        // Siparişin durumunu güncelle
        $order = $payment->order;
        if ($order->status === 'ödendi' && $order->remainingAmount() > 0) {
            $order->update([
                'status' => 'kapandı'
            ]);
        }

        return response()->json([
            'message' => 'Ödeme iptal edildi',
            'payment' => $payment
        ]);
    }

    /**
     * Hızlı ödeme ekranı için tüm masaları ve aktif siparişleri getir
     */
    public function quickPayment()
    {
        // Tüm masaları ve aktif siparişlerini getir
        $tables = \App\Models\Table::with(['activeOrder' => function ($q) {
            $q->whereNotIn('status', ['kapandı', 'ödendi', 'iptal']);
        }])->get();
        return Inertia::render('Payments/Create', [
            'tables' => $tables
        ]);
    }
}
