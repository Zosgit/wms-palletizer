<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderPickAuto;
use App\Models\OrderPickAutoProduct;
use Illuminate\Http\Request;

class OrderPickAutoController extends Controller
{
    public function index()
    {
        $orders = OrderPickAuto::where('confirmed', true)
            ->with('order')
            ->select('order_id')
            ->distinct()
            ->orderByDesc('order_id')
            ->get();

        return view('orderpickauto.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = Order::with('order_details.product')->findOrFail($orderId);
        $entries = OrderPickAuto::where('order_id', $orderId)
            ->with(['storeunit.storeunittype', 'products'])
            ->get();
        // Pobierz wszystkie produkty przypisane do tych kompletacji
        $productsByEntry = [];

        foreach ($entries as $entry) {
            $productsByEntry[$entry->id] = OrderPickAutoProduct::with('product')
                ->where('order_pick_auto_id', $entry->id)
                ->get();
        }

    return view('orderpickauto.show', compact('order', 'entries', 'productsByEntry'));
    }
}
