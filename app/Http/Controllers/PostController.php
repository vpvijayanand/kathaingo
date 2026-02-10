<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->is_admin) {
             $posts = \App\Models\Post::with('author')->orderBy('created_at', 'desc')->get();
        } else {
             $posts = \App\Models\Post::where('author_id', auth()->id())->orderBy('created_at', 'desc')->get();
        }
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::orderBy('name')->get();
        return view('posts.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|url',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        $post = new \App\Models\Post($request->except('featured_image'));
        $post->author_id = auth()->id();
        $post->slug = \Illuminate\Support\Str::slug($request->title) . '-' . time();
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            $post->featured_image = $path;
        }
        
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Post $post)
    {
         if ($post->author_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        $categories = \App\Models\Category::orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::orderBy('name')->get();
        return view('posts.edit', compact('post', 'categories', 'subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Post $post)
    {
        if ($post->author_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        $post->fill($request->except('featured_image'));
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image && \Storage::disk('public')->exists($post->featured_image)) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            $post->featured_image = $path;
        }
        
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Post $post)
    {
        if ($post->author_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }

    /**
     * Upload image for TinyMCE editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
