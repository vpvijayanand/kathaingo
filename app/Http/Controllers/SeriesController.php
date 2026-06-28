<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Category;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Display a listing of all series.
     */
    public function index()
    {
        $categories = Category::getActiveCategoriesForNavigation();
        
        $series = Series::where('status', 'active')
            ->with(['posts' => function($q) {
                $q->where('status', 'published')->with('tags');
            }])
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('series.index', compact('series', 'categories'));
    }

    /**
     * Display the specified series with chapters.
     */
    public function show($slug)
    {
        $categories = Category::getActiveCategoriesForNavigation();
        
        $series = Series::where('slug', $slug)
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->firstOrFail();

        $posts = $series->posts()
            ->where('status', 'published')
            ->with(['author', 'authorSubcategory', 'tags', 'category'])
            ->orderBy('chapter_number', 'asc')
            ->get();

        // Group posts by volume if volume is set
        $volumes = $posts->groupBy(function($post) {
            return $post->volume ?: __('பொதுவானவை');
        });

        return view('series.show', compact('series', 'posts', 'volumes', 'categories'));
    }
}
