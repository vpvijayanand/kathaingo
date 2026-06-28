<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "1. Creating Test Hierarchy...\n";
    $cat = \App\Models\Category::firstOrCreate(['name' => 'Filter Logic Cat', 'slug' => 'filter-logic-cat']);
    $sub = $cat->subcategories()->firstOrCreate(['name' => 'Filter Logic Sub', 'slug' => 'filter-logic-sub']);
    $child = $sub->childCategories()->firstOrCreate(['name' => 'Filter Logic Child', 'slug' => 'filter-logic-child']);

    // Create another category to ensure filtering works (exclude this one)
    $catOther = \App\Models\Category::firstOrCreate(['name' => 'Other Cat', 'slug' => 'other-cat']);
    $subOther = $catOther->subcategories()->firstOrCreate(['name' => 'Other Sub', 'slug' => 'other-sub']);

    // 2. Test Subcategory Filter
    echo "2. Testing Subcategory Filter...\n";
    $request = \Illuminate\Http\Request::create('/admin/subcategories', 'GET', ['category_id' => $cat->id]);
    $controller = new \App\Http\Controllers\SubcategoryController();
    $view = $controller->index($request);
    $data = $view->getData();
    $subcategories = $data['subcategories'];

    if ($subcategories->contains($sub) && !$subcategories->contains($subOther)) {
        echo "   SUCCESS: Filtered subcategories correctly.\n";
    } else {
        echo "   FAILURE: Filter logic failed for Subcategory.\n";
        echo "   Count for target: " . $subcategories->where('id', $sub->id)->count() . "\n";
        echo "   Count for other: " . $subcategories->where('id', $subOther->id)->count() . "\n";
    }

    // 3. Test Grandchild Filter Logic
    echo "3. Testing Grandchild Filter Logic (Category Level)...\n";
    $grand = \App\Models\GrandchildCategory::create([
        'child_category_id' => $child->id,
        'name' => 'Filter Logic Grand ' . time(),
        'slug' => 'filter-logic-grand-' . time(),
        'order' => 1
    ]);

    $request = \Illuminate\Http\Request::create('/admin/grandchild-categories', 'GET', ['category_id' => $cat->id]);
    $controller = new \App\Http\Controllers\Admin\GrandchildCategoryController();
    $view = $controller->index($request);
    $data = $view->getData();
    $grands = $data['grandchildCategories'];

    if ($grands->contains($grand)) {
        echo "   SUCCESS: Grandchild found filtering by Category.\n";
    } else {
        echo "   FAILURE: Grandchild NOT found filtering by Category.\n";
    }

    // Cleanup
    $grand->delete();
    // $child->delete();
    // $sub->delete();
    // $cat->delete();

    echo "4. Test Complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
