<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::latest()->get();
        return view('pos.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('pos.show', compact('order'));
    }


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.list')->with('success', 'Order deleted successfully.');
    }
}
