<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Helpers\CountryHelper;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Helper to get categories for standard header/navigation.
     */
    protected function getNavigationCategories()
    {
        return Category::getActiveCategoriesForNavigation();
    }

    /**
     * Display the map view with highlighted countries.
     */
    public function index()
    {
        $categories = $this->getNavigationCategories();

        $activeCountries = Post::where('status', 'published')
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->groupBy('country_code')
            ->selectRaw('country_code, count(*) as post_count')
            ->pluck('post_count', 'country_code')
            ->toArray();

        // Convert keys to lowercase to match SVG IDs
        $activeCountries = array_change_key_case($activeCountries, CASE_LOWER);

        return view('countries.index', compact('categories', 'activeCountries'));
    }

    /**
     * Show posts associated with the specified country.
     */
    public function show($country_code)
    {
        $country_code = strtolower($country_code);

        if (!CountryHelper::has($country_code)) {
            abort(404);
        }

        $categories = $this->getNavigationCategories();
        $countryName = CountryHelper::getName($country_code);
        $countryNameTa = CountryHelper::getNameTa($country_code);
        $countryNameEn = CountryHelper::getNameEn($country_code);

        $sort = request('sort', 'latest');

        $query = Post::with(['author', 'category', 'subcategory', 'childCategory', 'grandchildCategory', 'authorSubcategory', 'tags'])
            ->withCount(['comments', 'reactions'])
            ->where('status', 'published')
            ->where('country_code', $country_code);

        if ($sort === 'popular') {
            $query->orderBy('views_count', 'desc')->orderBy('reactions_count', 'desc');
        } else {
            $query->orderBy('published_at', 'desc');
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('countries.show', compact('posts', 'categories', 'country_code', 'countryName', 'countryNameTa', 'countryNameEn', 'sort'));
    }
}
