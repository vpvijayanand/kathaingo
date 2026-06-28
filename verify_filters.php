<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "1. Creating Test Hierarchy...\n";
    $cat = \App\Models\Category::firstOrCreate(['name' => 'Filter Cat', 'slug' => 'filter-cat']);
    $sub = $cat->subcategories()->firstOrCreate(['name' => 'Filter Sub', 'slug' => 'filter-sub']);
    $child = $sub->childCategories()->firstOrCreate(['name' => 'Filter Child', 'slug' => 'filter-child']);
    $grand = \App\Models\GrandchildCategory::create([
        'child_category_id' => $child->id,
        'name' => 'Filter Grand ' . time(),
        'slug' => 'filter-grand-' . time(),
        'order' => 1
    ]);

    echo "2. Testing Subcategory Filter...\n";
    $request = \Illuminate\Http\Request::create('/admin/subcategories', 'GET', ['category_id' => $cat->id]);
    $controller = new \App\Http\Controllers\SubcategoryController(); // Note: Not in Admin namespace
    $view = $controller->index($request);
    $content = $view->render();

    if (strpos($content, $sub->name) !== false) {
        echo "   SUCCESS: Subcategory found in filtered view.\n";
    } else {
        echo "   FAILURE: Subcategory NOT found.\n";
    }
    // Check if dropdown selected
    if (strpos($content, 'value="' . $cat->id . '" selected') !== false) {
        echo "   SUCCESS: Category dropdown selected correctly.\n";
    }

    echo "3. Testing Grandchild Filter (3 Levels)...\n";
    $request = \Illuminate\Http\Request::create('/admin/grandchild-categories', 'GET', [
        'category_id' => $cat->id,
        'subcategory_id' => $sub->id,
        'child_category_id' => $child->id
    ]);
    $controller = new \App\Http\Controllers\Admin\GrandchildCategoryController();
    $view = $controller->index($request);
    $content = $view->render();

    if (strpos($content, $grand->name) !== false) {
        echo "   SUCCESS: Grandchild found in filtered view.\n";
    } else {
        echo "   FAILURE: Grandchild NOT found.\n";
    }

    // Check if Child Category dropdown is enabled and selected
    if (strpos($content, 'name="child_category_id"') !== false && strpos($content, 'selected') !== false) {
        echo "   SUCCESS: Filter dropdowns rendered.\n";
    }

    // Cleanup
    $grand->delete(); // Manual delete
    // Keep parents

    echo "4. Test Complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
