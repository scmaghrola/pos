<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('pos.pos', compact('customers'));
    }
    
    public function getProducts(Request $request)
    {
        $query = Product::query();

        // Filter by category if provided
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Search by title if provided
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Only return necessary columns
        $products = $query->select('id', 'title', 'price', 'image', 'category_id', 'sku')->get();

        return response()->json($products);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
        ]);

        $total = 0;

        // Validate stock
        foreach ($data['cart'] as $item) {
            $product = Product::find($item['id']);

            if ($product->sku < $item['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => "{$product->title} is out of stock (available: {$product->sku})"
                ], 400);
            }

            $total += $item['price'] * $item['qty'];
        }

        // Create Order
        $order = Order::create([
            'customer_name' => $data['customer_name'] ?? 'Walk-in',
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'total' => $total,
            'status' => 'Pending',
        ]);

        // Create items & reduce stock
        foreach ($data['cart'] as $item) {
            $product = Product::find($item['id']);
            $product->decrement('sku', $item['qty']); // reduce stock

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
