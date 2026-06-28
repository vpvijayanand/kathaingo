<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrandchildCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = GrandchildCategory::with('childCategory.subcategory.category');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('childCategory.subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->has('subcategory_id') && $request->subcategory_id != '') {
            $query->whereHas('childCategory', function ($q) use ($request) {
                $q->where('subcategory_id', $request->subcategory_id);
            });
        }

        if ($request->has('child_category_id') && $request->child_category_id != '') {
            $query->where('child_category_id', $request->child_category_id);
        }

        $grandchildCategories = $query->orderBy('order')->get();
        // Eager load nested relationships for the filter dropdowns
        $categories = Category::with('subcategories.childCategories')->orderBy('name')->get();

        return view('admin.grandchild_categories.index', compact('grandchildCategories', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('subcategories.childCategories')->orderBy('name')->get();
        return view('admin.grandchild_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_category_id' => 'required|exists:child_categories,id',
            'name' => 'required|unique:grandchild_categories,name',
        ]);

        $maxOrder = GrandchildCategory::where('child_category_id', $request->child_category_id)->max('order');

        GrandchildCategory::create([
            'child_category_id' => $request->child_category_id,
            'name' => $request->name,
            'slug' => Str::utf8Slug($request->name),
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.grandchild-categories.index')->with('success', 'Grandchild Category created successfully.');
    }

    public function edit(GrandchildCategory $grandchildCategory)
    {
        $categories = Category::with('subcategories.childCategories')->orderBy('name')->get();
        return view('admin.grandchild_categories.edit', compact('grandchildCategory', 'categories'));
    }

    public function update(Request $request, GrandchildCategory $grandchildCategory)
    {
        $request->validate([
            'child_category_id' => 'required|exists:child_categories,id',
            'name' => 'required|unique:grandchild_categories,name,' . $grandchildCategory->id,
        ]);

        $grandchildCategory->update([
            'child_category_id' => $request->child_category_id,
            'name' => $request->name,
            'slug' => Str::utf8Slug($request->name),
        ]);

        return redirect()->route('admin.grandchild-categories.index')->with('success', 'Grandchild Category updated successfully.');
    }

    public function destroy(GrandchildCategory $grandchildCategory)
    {
        $grandchildCategory->delete();
        return redirect()->route('admin.grandchild-categories.index')->with('success', 'Grandchild Category deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:grandchild_categories,id',
        ]);

        foreach ($request->order as $index => $id) {
            GrandchildCategory::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['status' => 'success']);
    }
}
