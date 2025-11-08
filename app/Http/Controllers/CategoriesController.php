<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    /**
     * Display a listing of categories with pagination.
     */
    public function index(Request $request)
    {
        $categories = Category::with('parent')->paginate(9);

        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories->items(),
                'pagination' => (string) $categories->links('pagination::bootstrap-5'),
            ]);
        }

        return view('pos.category_list', compact('categories'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'CSV file is empty.'
            ], 400);
        }

        $header = array_map('strtolower', array_shift($data));
        $categoryMap = [];
        $csvData = [];

        // Validate all rows first
        foreach ($data as $index => $row) {
            $rowData = array_combine($header, $row);

            if (empty($rowData['name'])) {
                $rowNumber = $index + 2; // +2 for header
                return response()->json([
                    'success' => false,
                    'message' => "Invalid CSV: 'name' column is empty at row $rowNumber."
                ], 400);
            }

            $csvData[] = $rowData;
        }

        DB::beginTransaction();
        try {
            foreach ($csvData as $cat) {
                $parentName = $cat['parent_category'] ?? null;
                $image = $cat['image'] ?? null;

                if (!empty($parentName)) {
                    // Handle parent category
                    $parent = $categoryMap[$parentName] ?? Category::firstOrCreate(
                        ['name' => $parentName, 'parent_id' => null],
                        ['image' => 'default.png'] // default image for parent
                    );

                    // Handle child category
                    $category = Category::firstOrCreate(
                        ['name' => $cat['name'], 'parent_id' => $parent->id],
                        ['image' => $image ?: 'default.png']
                    );

                    $categoryMap[$cat['name']] = $category;
                } else {
                    // Handle parent (root) category if no parent_category column value
                    $category = Category::firstOrCreate(
                        ['name' => $cat['name'], 'parent_id' => null],
                        ['image' => $image ?: 'default.png'] // default if empty
                    );

                    $categoryMap[$cat['name']] = $category;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'CSV Imported Successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export categories to CSV.
     */
    public function export()
    {
        $categories = Category::all();
        $filename = 'categories_export_' . date('Ymd_His') . '.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, ['id', 'name', 'parent_id', 'status', 'created_at', 'updated_at']);

        foreach ($categories as $category) {
            fputcsv($handle, [
                $category->id,
                $category->name,
                $category->parent_id,
                $category->status ? 'active' : 'inactive',
                $category->created_at,
                $category->updated_at
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
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
    public function edit(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'category' => $category,
                'categories' => $categories
            ]);
        }

        return view('pos.edit_category', compact('category', 'categories'));
    }

    public function show(Category $category)
    {
        return response()->json([
            'category' => $category
        ]);
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
    public function toggleStatus(Category $category)
    {
        $category->status = !$category->status;
        $category->save();

        return response()->json(['status' => $category->status]);
    }
}
