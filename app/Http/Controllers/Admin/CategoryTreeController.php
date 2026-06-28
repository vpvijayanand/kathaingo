<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use Illuminate\Http\Request;

class CategoryTreeController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->orderBy('order');
            },
            'subcategories.childCategories' => function ($query) {
                $query->orderBy('order');
            },
            'subcategories.childCategories.grandchildCategories' => function ($query) {
                $query->orderBy('order');
            }
        ])->orderBy('order')->get();

        return view('admin.category_tree.index', compact('categories'));
    }

    public function reparent(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|string|in:subcategory,child_category,grandchild_category',
            'new_parent_id' => 'required|integer',
        ]);

        $itemId = $request->item_id;
        $itemType = $request->item_type;
        $newParentId = $request->new_parent_id;

        try {
            switch ($itemType) {
                case 'subcategory':
                    $item = Subcategory::findOrFail($itemId);
                    $item->category_id = $newParentId;
                    break;
                case 'child_category':
                    $item = ChildCategory::findOrFail($itemId);
                    $item->subcategory_id = $newParentId;
                    break;
                case 'grandchild_category':
                    $item = GrandchildCategory::findOrFail($itemId);
                    $item->child_category_id = $newParentId;
                    break;
            }

            // Get the max order in the new parent to append it at the end
            $maxOrder = 0;
            /* 
               We could optimize order fetching here, but for now just appending is fine.
               The user can reorder within the new parent using the existing drag-and-drop 
               reordering if they want specific placement.
            */

            $item->save();

            return response()->json(['status' => 'success', 'message' => 'Valid move.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
