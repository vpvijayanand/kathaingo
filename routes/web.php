<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = \App\Models\Category::getActiveCategoriesForNavigation();
    $heroImages = \App\Models\HeroImage::where('is_active', true)->orderBy('order')->get();

    // 1. Fetch exactly the 6 most recent published posts
    $latestPosts = \App\Models\Post::with(['author', 'category', 'subcategory', 'childCategory', 'grandchildCategory', 'authorSubcategory', 'tags'])
        ->withCount('comments')
        ->where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->take(6)
        ->get();

    $bloggersCategory = \App\Models\Category::where('slug', 'pathivargal')->first();
    $bloggersCategoryId = $bloggersCategory ? $bloggersCategory->id : 4;

    // 2. Fetch up to 8 writers under pathivargal category with published posts
    $featuredWriters = \App\Models\Subcategory::where('category_id', $bloggersCategoryId)
        ->whereHas('authoredPosts', function($q) {
            $q->where('status', 'published');
        })
        ->orderBy('order')
        ->take(8)
        ->get();

    // 3. Fetch explore categories (main subcategories + active child categories)
    $subcategories = \App\Models\Subcategory::where('category_id', '!=', $bloggersCategoryId)
        ->whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->withCount(['posts' => function($q) { $q->where('status', 'published'); }])
        ->get();

    $exploreCategories = [];
    foreach ($subcategories as $sub) {
        $exploreCategories[] = [
            'name' => $sub->name,
            'name_en' => $sub->name_en ?: $sub->name,
            'slug' => $sub->slug,
            'type' => 'subcategory',
            'posts_count' => $sub->posts_count,
            'url' => route('stories.index', ['subcategory' => $sub->slug])
        ];
    }

    $childCategories = \App\Models\ChildCategory::whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->withCount(['posts' => function($q) { $q->where('status', 'published'); }])
        ->take(6 - count($exploreCategories))
        ->get();

    foreach ($childCategories as $child) {
        $exploreCategories[] = [
            'name' => $child->name,
            'name_en' => $child->name_en ?: $child->name,
            'slug' => $child->slug,
            'type' => 'child_category',
            'posts_count' => $child->posts_count,
            'url' => route('stories.index', ['child_category' => $child->slug])
        ];
    }

    return view('welcome', compact('categories', 'heroImages', 'latestPosts', 'featuredWriters', 'exploreCategories'));
})->name('home');

Route::get('/stories', function (\Illuminate\Http\Request $request) {
    $categories = \App\Models\Category::getActiveCategoriesForNavigation();
    
    $query = \App\Models\Post::with(['author', 'category', 'subcategory', 'childCategory', 'grandchildCategory', 'authorSubcategory', 'tags'])
        ->withCount('comments')
        ->where('status', 'published');

    $isFiltered = $request->has('search') || 
                  $request->has('category') || 
                  $request->has('subcategory') || 
                  $request->has('child_category') || 
                  $request->has('grandchild_category') || 
                  $request->has('metadata_values') || 
                  $request->has('tag') || 
                  $request->has('series') || 
                  $request->has('author') || 
                  $request->has('date_range') || 
                  $request->has('date');

    $featuredPosts = collect();
    $featuredIds = [];

    if (!$isFiltered) {
        $allPublishedPosts = \App\Models\Post::with(['author', 'category', 'subcategory', 'childCategory', 'grandchildCategory', 'authorSubcategory', 'tags'])
            ->withCount(['comments', 'reactions'])
            ->where('status', 'published')
            ->get();

        $sortedPosts = $allPublishedPosts->sortByDesc(function ($post) {
            return $post->reactions_count + $post->comments_count;
        });

        $seenCategories = [];
        foreach ($sortedPosts as $post) {
            if ($post->category_id !== null && !in_array($post->category_id, $seenCategories)) {
                $featuredPosts->push($post);
                $seenCategories[] = $post->category_id;
                if ($featuredPosts->count() >= 3) {
                    break;
                }
            }
        }
        $featuredIds = $featuredPosts->pluck('id')->toArray();
    }

    $activeFilter = null;

    if ($request->has('search')) {
        $search = $request->query('search');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('content', 'like', '%' . $search . '%')
              ->orWhereHas('author', function($aq) use ($search) {
                  $aq->where('name', 'like', '%' . $search . '%');
              })
              ->orWhereHas('category', function($cq) use ($search) {
                  $cq->where('name', 'like', '%' . $search . '%');
              })
              ->orWhereHas('subcategory', function($sq) use ($search) {
                  $sq->where('name', 'like', '%' . $search . '%');
              })
              ->orWhereHas('childCategory', function($ccq) use ($search) {
                  $ccq->where('name', 'like', '%' . $search . '%');
              })
              ->orWhereHas('grandchildCategory', function($gcq) use ($search) {
                  $gcq->where('name', 'like', '%' . $search . '%');
              });
        });
        $activeFilter = app()->getLocale() === 'ta' 
            ? ('"' . $search . '" க்கான தேடல் முடிவுகள்') 
            : ('Search results for "' . $search . '"');
    }

    if ($request->has('category')) {
        $categoryParam = $request->query('category');
        $categoryQuery = \App\Models\Category::where('slug', $categoryParam);
        if (is_numeric($categoryParam)) {
            $categoryQuery->orWhere('id', (int) $categoryParam);
        }
        $category = $categoryQuery->first();
        if ($category) {
            $query->where(function($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereHas('tags', function($sq) use ($category) {
                      $sq->where('slug', $category->slug)
                        ->orWhere('name', $category->name)
                        ->orWhere('slug', $category->name);
                  });
            });
            $activeFilter = $category->name;
        }
    }

    if ($request->has('subcategory')) {
        $subcategory = \App\Models\Subcategory::where('slug', $request->query('subcategory'))->first();
        if ($subcategory) {
            $query->where('subcategory_id', $subcategory->id);
            $activeFilter = $subcategory->name;
        }
    }

    if ($request->has('child_category')) {
        $childCategory = \App\Models\ChildCategory::where('slug', $request->query('child_category'))->first();
        if ($childCategory) {
            $query->where('child_category_id', $childCategory->id);
            $activeFilter = $childCategory->name;
        }
    }

    if ($request->has('grandchild_category')) {
        $grandchildCategory = \App\Models\GrandchildCategory::where('slug', $request->query('grandchild_category'))->first();
        if ($grandchildCategory) {
            $query->where('grandchild_category_id', $grandchildCategory->id);
            $activeFilter = $grandchildCategory->name;
        }
    }

    if ($request->has('metadata_values')) {
        $metadataValues = (array) $request->query('metadata_values');
        $numericValues = array_filter($metadataValues, 'is_numeric');
        $query->whereHas('metadataValues', function($q) use ($metadataValues, $numericValues) {
            $q->whereIn('metadata_values.slug', $metadataValues);
            if (!empty($numericValues)) {
                $q->orWhereIn('metadata_values.id', array_map('intval', $numericValues));
            }
        });
        $activeFilter = __('வடிகட்டப்பட்டது (Filtered)');
    }

    if ($request->has('tag')) {
        $tagSlug = $request->query('tag');
        $tagModel = \App\Models\Tag::where('slug', $tagSlug)->orWhere('name', $tagSlug)->first();
        if ($tagModel) {
            $query->where(function($q) use ($tagModel) {
                $q->whereHas('tags', function($sq) use ($tagModel) {
                    $sq->where('tags.id', $tagModel->id);
                })
                ->orWhereHas('category', function($sq) use ($tagModel) {
                    $sq->where('slug', $tagModel->slug)
                      ->orWhere('name', $tagModel->name)
                      ->orWhere('name_en', $tagModel->name);
                })
                ->orWhereHas('subcategory', function($sq) use ($tagModel) {
                    $sq->where('slug', $tagModel->slug)
                      ->orWhere('name', $tagModel->name)
                      ->orWhere('name_en', $tagModel->name);
                });
            });
            $activeFilter = '#' . $tagModel->name;
        }
    }

    if ($request->has('series')) {
        $seriesParam = $request->query('series');
        $seriesQuery = \App\Models\Series::where('slug', $seriesParam);
        if (is_numeric($seriesParam)) {
            $seriesQuery->orWhere('id', (int) $seriesParam);
        }
        $seriesModel = $seriesQuery->first();
        if ($seriesModel) {
            $query->where('series_id', $seriesModel->id);
            $activeFilter = $seriesModel->title;
        }
    }

    if ($request->has('author')) {
        $authorParam = $request->query('author');
        $query->where(function($q) use ($authorParam) {
            $q->where('author_id', $authorParam)
              ->orWhereHas('authorSubcategory', function($sq) use ($authorParam) {
                  $sq->where('slug', $authorParam);
              });
        });
        $activeFilter = __('எழுத்தாளர் வாரியாக (By Writer)');
    }

    if ($request->has('date_range')) {
        $range = $request->query('date_range');
        if ($range === 'this_week') {
            $query->where('published_at', '>=', now()->startOfWeek());
        } elseif ($range === 'this_month') {
            $query->where('published_at', '>=', now()->startOfMonth());
        }
        $activeFilter = __('தேதி வாரியாக (By Date Range)');
    }

    if ($request->has('date')) {
        $date = $request->query('date');
        $query->whereDate('published_at', $date);
        try {
            $activeFilter = \Carbon\Carbon::parse($date)->translatedFormat('F d, Y');
        } catch (\Exception $e) {
            $activeFilter = $date;
        }
    }

    if (!$isFiltered && !empty($featuredIds)) {
        $query->whereNotIn('id', $featuredIds);
    }

    $posts = $query->orderBy('published_at', 'desc')->paginate(9)->withQueryString();
    $heroImages = \App\Models\HeroImage::where('is_active', true)->orderBy('order')->get();
    
    $publishedDates = \App\Models\Post::where('status', 'published')
        ->whereNotNull('published_at')
        ->selectRaw('DATE(published_at) as pub_date')
        ->distinct()
        ->pluck('pub_date')
        ->toArray();

    $allCategories = \App\Models\Category::where('slug', '!=', 'pathivargal')->with('metadataTypes.values')->orderBy('order')->get();
    $allSeries = \App\Models\Series::where('status', 'active')->orderBy('title')->get();
    $allTags = \App\Models\Tag::has('posts')->orderBy('name')->get();
    
    return view('stories.index', compact('categories', 'posts', 'heroImages', 'activeFilter', 'publishedDates', 'featuredPosts', 'allCategories', 'allSeries', 'allTags'));
})->name('stories.index');

Route::get('/about', function () {
    $categories = \App\Models\Category::getActiveCategoriesForNavigation();

    $heroImages = \App\Models\HeroImage::where('is_active', true)->orderBy('order')->get();

    $bloggersCategory = \App\Models\Category::where('slug', 'pathivargal')->first();
    $bloggersCategoryId = $bloggersCategory ? $bloggersCategory->id : 4;

    // Fetch subcategories (excluding writers) with published posts
    $subcategories = \App\Models\Subcategory::where('category_id', '!=', $bloggersCategoryId)
        ->whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->get(['name', 'slug'])
        ->map(function($sub) {
            return [
                'name' => $sub->name,
                'slug' => $sub->slug,
                'type' => 'subcategory',
                'url' => '/stories?subcategory=' . $sub->slug
            ];
        });

    // Fetch child categories with published posts
    $childCategories = \App\Models\ChildCategory::whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->get(['name', 'slug'])
        ->map(function($child) {
            return [
                'name' => $child->name,
                'slug' => $child->slug,
                'type' => 'child_category',
                'url' => '/stories?child_category=' . $child->slug
            ];
        });

    // Fetch grandchild categories with published posts
    $grandchildCategories = \App\Models\GrandchildCategory::whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->get(['name', 'slug'])
        ->map(function($grand) {
            return [
                'name' => $grand->name,
                'slug' => $grand->slug,
                'type' => 'grandchild_category',
                'url' => '/stories?grandchild_category=' . $grand->slug
            ];
        });

    // Fetch metadata values with published posts
    $metadataValues = \App\Models\MetadataValue::whereHas('posts', function($q) {
            $q->where('status', 'published');
        })
        ->with('type')
        ->get()
        ->map(function($val) {
            $typeName = $val->type ? $val->type->name : 'Metadata';
            return [
                'name' => $val->name . ' (' . $typeName . ')',
                'slug' => $val->slug,
                'type' => 'metadata',
                'url' => '/stories?metadata_values[]=' . $val->slug
            ];
        });

    // Merge categories
    $universeCategories = $subcategories
        ->concat($childCategories)
        ->concat($grandchildCategories)
        ->concat($metadataValues)
        ->toArray();

    // Fetch writers (subcategories of the pathivargal category) with published posts
    $universeWriters = $bloggersCategory
        ? \App\Models\Subcategory::where('category_id', $bloggersCategory->id)
            ->whereHas('authoredPosts', function($q) {
                $q->where('status', 'published');
            })
            ->get(['name', 'slug'])
            ->toArray()
        : [];

    return view('story', compact('categories', 'heroImages', 'universeCategories', 'universeWriters'));
})->name('about');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'approved'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // We should probably check if user is admin here via middleware or inside controller
    // For now we assume the controller handles it or we add a simple middleware check
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'index'])->name('users.index');
    Route::get('/writers-verification', [\App\Http\Controllers\AdminController::class, 'writersVerification'])->name('writers.verification');
    Route::post('/writers-verification/{subcategory}', [\App\Http\Controllers\AdminController::class, 'updateWriterVerification'])->name('writers.verification.update');
    Route::post('/users/{user}/approve', [\App\Http\Controllers\AdminController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/role', [\App\Http\Controllers\AdminController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'delete'])->name('users.delete');
    Route::post('/users/{user}/link-author', [\App\Http\Controllers\AdminController::class, 'linkAuthor'])->name('users.linkAuthor');
    Route::post('/posts/{post}/approve', [\App\Http\Controllers\PostController::class, 'approve'])->name('posts.approve');

    // Category and Subcategory management
    Route::post('categories/reorder', [\App\Http\Controllers\CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::post('subcategories/reorder', [\App\Http\Controllers\SubcategoryController::class, 'reorder'])->name('subcategories.reorder');
    Route::resource('subcategories', \App\Http\Controllers\SubcategoryController::class);

    // Hero Images
    Route::post('hero-images/reorder', [\App\Http\Controllers\Admin\HeroImageController::class, 'reorder'])->name('hero-images.reorder');
    Route::resource('hero-images', \App\Http\Controllers\Admin\HeroImageController::class);

    // Child Categories
    Route::post('child-categories/reorder', [\App\Http\Controllers\Admin\ChildCategoryController::class, 'reorder'])->name('child-categories.reorder');
    Route::resource('child-categories', \App\Http\Controllers\Admin\ChildCategoryController::class);

    // Category Tree
    Route::get('category-tree', [\App\Http\Controllers\Admin\CategoryTreeController::class, 'index'])->name('category-tree.index');
    Route::post('category-tree/reparent', [\App\Http\Controllers\Admin\CategoryTreeController::class, 'reparent'])->name('category-tree.reparent');

    // Grandchild Categories
    Route::post('grandchild-categories/reorder', [\App\Http\Controllers\Admin\GrandchildCategoryController::class, 'reorder'])->name('grandchild-categories.reorder');
    Route::resource('grandchild-categories', \App\Http\Controllers\Admin\GrandchildCategoryController::class);

    // Global Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

// Blog Routes
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::post('/posts/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('posts.uploadImage');
    Route::post('/api/posts/classify', [\App\Http\Controllers\PostController::class, 'classify'])->name('api.posts.classify');
    Route::post('/api/metadata-types/{metadataType}/values', [\App\Http\Controllers\PostController::class, 'addMetadataValue'])->name('api.metadata-values.store');
    Route::post('/api/series', [\App\Http\Controllers\PostController::class, 'addSeries'])->name('api.series.store');
    Route::post('/posts/{post}/submit', [\App\Http\Controllers\PostController::class, 'submitForReview'])->name('posts.submit');
    Route::post('/posts/{post}/status', [\App\Http\Controllers\PostController::class, 'statusUpdate'])->name('posts.statusUpdate');
    Route::resource('posts', \App\Http\Controllers\PostController::class)->except(['show']);
    Route::get('/author/settings/{subcategory:slug}/edit', [\App\Http\Controllers\AuthorProfileController::class, 'edit'])->name('authors.edit');
    Route::put('/author/settings/{subcategory:slug}', [\App\Http\Controllers\AuthorProfileController::class, 'update'])->name('authors.update');

    // Writing Assistant
    Route::post('/api/writing-assistant/check-block', [\App\Http\Controllers\WritingAssistantController::class, 'checkBlock'])->name('api.writing-assistant.check-block');
    Route::post('/api/writing-assistant/dictionary/add', [\App\Http\Controllers\WritingAssistantController::class, 'addToDictionary'])->name('api.writing-assistant.dictionary.add');
    Route::post('/api/writing-assistant/suggest-word', [\App\Http\Controllers\WritingAssistantController::class, 'suggestWord'])->name('api.writing-assistant.suggest-word');
    Route::post('/api/writing-assistant/learn-correction', [\App\Http\Controllers\WritingAssistantController::class, 'learnCorrection'])->name('api.writing-assistant.learn-correction');
    Route::post('/api/writing-assistant/analyze-consistency', [\App\Http\Controllers\WritingAssistantController::class, 'analyzeConsistency'])->name('api.writing-assistant.analyze-consistency');
    Route::post('/api/writing-assistant/review-article', [\App\Http\Controllers\WritingAssistantController::class, 'reviewArticle'])->name('api.writing-assistant.review-article');
});

// Public Countries Map & Articles Routes
Route::get('/countries', [\App\Http\Controllers\CountryController::class, 'index'])->name('countries.index');
Route::get('/countries/{country_code}', [\App\Http\Controllers\CountryController::class, 'show'])->name('countries.show');

// Public Series Routes
Route::get('/series', [\App\Http\Controllers\SeriesController::class, 'index'])->name('series.index');
Route::get('/series/{slug}', [\App\Http\Controllers\SeriesController::class, 'show'])->name('series.show');

// Public Localization Language Switch Route
Route::get('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('lang.switch');

// Public Blog Engagement Routes (positioned below resources to avoid conflicts)
Route::get('/authors/{subcategory:slug}', [\App\Http\Controllers\AuthorProfileController::class, 'show'])->name('authors.show');
Route::post('/posts/{post:slug}/like', [\App\Http\Controllers\PostController::class, 'like'])->name('posts.like');
Route::post('/posts/{post:slug}/react', [\App\Http\Controllers\PostController::class, 'react'])->name('posts.react');
Route::post('/posts/{post:slug}/share', [\App\Http\Controllers\PostController::class, 'share'])->name('posts.share');
Route::post('/posts/{post:slug}/read', [\App\Http\Controllers\PostController::class, 'recordRead'])->name('posts.read');
Route::post('/posts/{post:slug}/comments', [\App\Http\Controllers\PostController::class, 'storeComment'])->name('posts.storeComment');
Route::post('/comments/{comment}/react', [\App\Http\Controllers\PostController::class, 'reactToComment'])->name('comments.react');
Route::post('/api/language-helper/suggest', [\App\Http\Controllers\PostController::class, 'suggestLanguage'])->name('api.language-helper.suggest');
Route::get('/posts/{post:slug}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// Public Search Suggestions API Route
Route::get('/api/search', [\App\Http\Controllers\SearchController::class, 'suggest'])->name('api.search');

require __DIR__ . '/auth.php';
