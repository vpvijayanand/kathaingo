<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categoryCount = \App\Models\Category::where('name', 'like', '%India%')->orWhere('name', 'like', '%இந்தியா%')->count();
$catNames = \App\Models\Category::where('name', 'like', '%India%')->orWhere('name', 'like', '%இந்தியா%')->pluck('name')->implode(', ');

$subcategoryCount = \App\Models\Subcategory::where('name', 'like', '%India%')->orWhere('name', 'like', '%இந்தியா%')->count();
$subNames = \App\Models\Subcategory::where('name', 'like', '%India%')->orWhere('name', 'like', '%இந்தியா%')->pluck('name')->implode(', ');

echo "Categories matching India: $categoryCount ($catNames)\n";
echo "Subcategories matching India: $subcategoryCount ($subNames)\n";
