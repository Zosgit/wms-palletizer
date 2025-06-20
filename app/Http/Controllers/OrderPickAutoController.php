<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderPickAuto;
use Illuminate\Http\Request;

class OrderPickAutoController extends Controller
{
    public function index()
    {
        $orders = OrderPickAuto::where('confirmed', true)
            ->with('order')
            ->select('order_id')
            ->distinct()
            ->get();

        return view('orderpickauto.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = Order::with('order_details.product')->findOrFail($orderId);
        $entries = OrderPickAuto::where('order_id', $orderId)
            ->with('storeunit.storeunittype')
            ->get();

        return view('orderpickauto.show', compact('order', 'entries'));
    }
}
