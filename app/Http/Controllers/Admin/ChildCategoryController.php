<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChildCategory;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ChildCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ChildCategory::with('subcategory.category');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->has('subcategory_id') && $request->subcategory_id != '') {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        $childCategories = $query->orderBy('order')->get();
        $categories = Category::with('subcategories')->orderBy('name')->get();

        return view('admin.child_categories.index', compact('childCategories', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.child_categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
        ]);

        ChildCategory::create([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'slug' => Str::utf8Slug($request->name),
            'order' => ChildCategory::where('subcategory_id', $request->subcategory_id)->max('order') + 1,
        ]);

        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChildCategory $childCategory)
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.child_categories.edit', compact('childCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChildCategory $childCategory)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
        ]);

        $childCategory->update([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'slug' => Str::utf8Slug($request->name),
        ]);

        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChildCategory $childCategory)
    {
        $childCategory->delete();
        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:child_categories,id',
        ]);

        foreach ($request->ids as $index => $id) {
            ChildCategory::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
