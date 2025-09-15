<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
{
    /**
     * Display a listing of categories with pagination.
     */
    public function index()
    {
        $categories = Category::with('parent')->paginate(10);
        return view('pos.category_list', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('pos.add_category', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => 'nullable|image|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->status = true; // default active

        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Category created successfully',
                'redirect' => route('category.list')
            ]);
        }

        return redirect()->route('category.list')->with('success', 'Category created successfully');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $id) // prevent self-parent
            ->get();

        return view('pos.edit_category', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$id])],
            'image' => 'nullable|image|max:2048',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($category->image && Storage::exists('public/' . $category->image)) {
                Storage::delete('public/' . $category->image);
            }
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Category updated successfully',
                'redirect' => route('category.list')
            ]);
        }

        return redirect()->route('category.list')->with('success', 'Category updated successfully');
    }

    // Delete category
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && Storage::exists('public/' . $category->image)) {
            Storage::delete('public/' . $category->image);
        }

        $category->delete();

        return $request->ajax()
            ? response()->json(['message' => 'Category deleted successfully'])
            : redirect()->route('category.list')->with('success', 'Category deleted successfully');
    }

    // Toggle status
    public function toggleStatus(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status;
        $category->save();

        return $request->ajax()
            ? response()->json([
                'message' => 'Category status updated successfully',
                'status' => $category->status
            ])
            : redirect()->back()->with('success', 'Category status updated successfully');
    }
}
