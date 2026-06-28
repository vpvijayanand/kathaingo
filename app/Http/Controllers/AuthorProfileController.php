<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthorProfileController extends Controller
{
    /**
     * Display the public profile of a writer/author (subcategory).
     */
    public function show(Subcategory $subcategory)
    {
        // Check if this subcategory belongs to the 'pathivargal' (Writers) category
        $writersCategory = Category::where('slug', 'pathivargal')->first();
        if ($writersCategory && $subcategory->category_id !== $writersCategory->id) {
            abort(404);
        }

        // Fetch selected category filter
        $selectedCategorySlug = request('category');
        $postsQuery = $subcategory->authoredPosts()
            ->with(['category', 'subcategory', 'childCategory', 'grandchildCategory', 'tags'])
            ->withCount('comments')
            ->where('status', 'published');

        if ($selectedCategorySlug) {
            $postsQuery->whereHas('subcategory', function ($q) use ($selectedCategorySlug) {
                $q->where('slug', $selectedCategorySlug);
            });
        }

        // Fetch published posts written by this author
        $posts = $postsQuery->orderBy('published_at', 'desc')->paginate(9);

        // Total count for "All" tile
        $postsCountAll = $subcategory->authoredPosts()->where('status', 'published')->count();

        // Fetch content categories (subcategories under category 'pathivugal') where this author has written posts
        $postsCategory = Category::where('slug', 'pathivugal')->first();
        $bloggerSubcategories = collect();
        if ($postsCategory) {
            $contentCategoryIds = $postsCategory->subcategories->pluck('id');
            $bloggerSubcategories = Subcategory::whereIn('id', $contentCategoryIds)
                ->whereHas('posts', function ($query) use ($subcategory) {
                    $query->where('author_subcategory_id', $subcategory->id)
                          ->where('status', 'published');
                })
                ->withCount(['posts as posts_count_by_author' => function ($query) use ($subcategory) {
                    $query->where('author_subcategory_id', $subcategory->id)
                          ->where('status', 'published');
                }])
                ->orderBy('order')
                ->get();
        }

        // Fetch categories and sort writers in navigation by their post count
        $categories = Category::getActiveCategoriesForNavigation();

        return view('authors.show', compact('subcategory', 'posts', 'categories', 'bloggerSubcategories', 'postsCountAll', 'selectedCategorySlug'));
    }

    /**
     * Show the edit form for the author profile.
     */
    public function edit(Subcategory $subcategory)
    {
        // Verify authorization: logged in user must own the profile or be admin
        if (auth()->id() !== $subcategory->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::with('subcategories.childCategories.grandchildCategories')->orderBy('name')->get();

        return view('authors.edit', compact('subcategory', 'categories'));
    }

    /**
     * Update the author profile.
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        // Verify authorization: logged-in user must own the profile or be admin
        if (auth()->id() !== $subcategory->user_id && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'topics' => 'nullable|string|max:5000',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subcategory->name = $request->name;
        $subcategory->email = $request->email;
        $subcategory->phone = $request->phone;
        $subcategory->facebook_url = $request->facebook_url;
        $subcategory->instagram_url = $request->instagram_url;
        $subcategory->linkedin_url = $request->linkedin_url;
        $subcategory->topics = $request->topics;
        $subcategory->description = $request->description;
        
        // Regenerate slug dynamically only if name is changed and they are not referencing old slug
        $subcategory->slug = Str::utf8Slug($request->name);

        // Process cropped image if present
        if ($request->filled('cropped_image')) {
            // Delete old profile picture if exists
            if ($subcategory->image_path && Storage::disk('public')->exists($subcategory->image_path)) {
                Storage::disk('public')->delete($subcategory->image_path);
            }

            $base64Data = $request->input('cropped_image');
            @list($type, $imageData) = explode(';', $base64Data);
            @list(, $imageData)      = explode(',', $imageData);
            
            $extension = 'jpg';
            if (strpos($type, 'png') !== false) {
                $extension = 'png';
            } elseif (strpos($type, 'gif') !== false) {
                $extension = 'gif';
            }
            
            $filename = 'images/' . time() . '_' . Str::random(10) . '.' . $extension;
            Storage::disk('public')->put($filename, base64_decode($imageData));
            $subcategory->image_path = $filename;
        } elseif ($request->hasFile('image')) {
            // Delete old profile picture if exists
            if ($subcategory->image_path && Storage::disk('public')->exists($subcategory->image_path)) {
                Storage::disk('public')->delete($subcategory->image_path);
            }

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            $subcategory->image_path = $path;
        }

        $subcategory->save();

        return redirect()->route('authors.show', $subcategory->slug)->with('success', 'Profile updated successfully.');
    }
}
