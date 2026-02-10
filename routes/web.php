<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = \App\Models\Category::with('subcategories')->orderBy('order')->get();
    $posts = \App\Models\Post::with(['author', 'category', 'subcategory'])
        ->where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->limit(9)
        ->get();
    return view('welcome', compact('categories', 'posts'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'approved'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // We should probably check if user is admin here via middleware or inside controller
    // For now we assume the controller handles it or we add a simple middleware check
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/approve', [\App\Http\Controllers\AdminController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'delete'])->name('users.delete');
    
    // Category and Subcategory management
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('subcategories', \App\Http\Controllers\SubcategoryController::class);
});

// Blog Routes
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::post('/posts/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('posts.uploadImage');
    Route::resource('posts', \App\Http\Controllers\PostController::class);
});

require __DIR__.'/auth.php';
