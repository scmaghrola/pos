<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(3); // fetch all products from DB
        return view('pos.list_product', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createOld()
    {
        $categories = Category::whereNull('parent_id')->get();

        return view('pos.add_product', compact('categories'));
    }
    /**
     * Toggle the status of the specified product.
     */
    public function toggleStatus(Request $request, Product $product)
    {
        $product->status = !$product->status;
        $product->save();

        return response()->json([
            'success' => true,
            'status' => $product->status,
            'message' => $product->status ? 'Product activated!' : 'Product deactivated!',
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function storeOld(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
            'sku' => 'required|string|max:50',
            'weight' => 'nullable|string|max:50',
        ]);

        $product = new Product();
        $product->title = $request->title;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->weight = $request->weight;

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!'
            ]);
        }

        return redirect()->route('pos.products.index')->with('success', 'Product added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::whereNull('parent_id')->get();

        return view('pos.edit_product', compact('product', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric',
            'compare_price' => 'nullable|numeric',
            'image'         => 'nullable|image|max:2048',
            'sku'           => 'required|string|max:50',
            'weight'        => 'nullable|string|max:50',
        ]);

        // Update fields
        $product->title         = $request->title;
        $product->category_id   = $request->category_id;
        $product->price         = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku           = $request->sku;
        $product->weight        = $request->weight;

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        // Return JSON if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!',
                'product' => $product
            ]);
        }
        return redirect()->route('pos.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);
        }
        return redirect()->route('pos.products.index')->with('success', 'Product deleted successfully!');
    }



    // Display all products
    public function indexNEw()
    {
        $products = Product::all();
        return view('pos.list_product', compact('products'));
    }

    // Show create form
    public function create()
    {
        $categories =Category::all();
        return view('pos.add_product', compact('categories'));
    }

    // Store product with image
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'image' => 'nullable|string', // filename from Dropzone
        ]);

        $product = new Product();
        $product->title = $request->title;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->weight = $request->weight;
        $product->image = $request->image; // Dropzone uploaded filename
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully!'
        ]);
    }

    // Dropzone image upload
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/products', $filename);

            return response()->json([
                'success' => true,
                'filename' => $filename
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image uploaded'
        ], 400);
    }
}
