<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContentProcessorService;
use App\Services\ContentCategorizerService;
use Illuminate\Support\Facades\Gate;
use App\Models\PostRevision;
use App\Models\PostFeedback;
use App\Models\Post;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\MetadataType;
use App\Models\MetadataValue;
use App\Models\Tag;
use App\Models\Series;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Post::class);

        $user = auth()->user();
        $query = Post::with(['author', 'authorSubcategory'])->orderBy('created_at', 'desc');

        if (!($user->isAdmin() || $user->isEditor() || $user->isSeoManager())) {
            $query->where('author_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Post::class);

        $categories = Category::where('slug', '!=', 'pathivargal')->orderBy('order')->get();
        $authorsCategory = Category::where('slug', 'pathivargal')->with('subcategories')->first();
        $authors = $authorsCategory ? $authorsCategory->subcategories : collect();
        $series = Series::where('status', 'active')->orderBy('title')->get();
        $metadataTypes = MetadataType::with('values')->get();

        return view('posts.create', compact('categories', 'authors', 'series', 'metadataTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ContentProcessorService $contentProcessor, ContentCategorizerService $categorizer)
    {
        Gate::authorize('create', Post::class);

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|url',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'grandchild_category_id' => 'nullable|exists:grandchild_categories,id',
            'author_subcategory_id' => 'nullable|exists:subcategories,id',
            'published_at' => 'nullable|date',
            'country_code' => 'nullable|string|size:2',
            'status' => 'required|in:draft,submitted,under_review,approved,published,rejected',
            'series_id' => 'nullable|exists:series,id',
            'volume' => 'nullable|string|max:255',
            'chapter_number' => 'nullable|integer',
            'metadata_value_ids' => 'nullable|array',
            'metadata_value_ids.*' => 'exists:metadata_values,id',
            'tags' => 'nullable|string',
            'hashtags' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $words = preg_split('/[\s,\#]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) > 3) {
                        $fail('ஹேஷ்டேகுகள் அதிகபட்சம் 3 வார்த்தைகள் மட்டுமே இருக்க வேண்டும். (Maximum 3 hashtags allowed)');
                    }
                }
            ],
        ]);

        $status = $request->status;
        $tempPost = new Post(['author_id' => auth()->id()]);

        if (!Gate::allows('changeStatus', [$tempPost, $status])) {
            if (in_array($status, ['published', 'submitted'])) {
                $status = 'submitted';
            } else {
                $status = 'draft';
            }
        }

        $publishedAt = null;
        if ($status === 'published') {
            $publishedAt = $request->filled('published_at') ? $request->published_at : now();
        }

        $categoryId = $request->category_id;
        $subcategoryId = $request->subcategory_id;
        $childCategoryId = $request->child_category_id;
        $grandchildCategoryId = $request->grandchild_category_id;
        $metadataValueIds = $request->input('metadata_value_ids', []);
        $suggestedTags = [];

        if (empty($categoryId)) {
            $legacyClassification = $categorizer->categorize($request->title, $request->content);
            $classification = $categorizer->classify($request->title, $request->content);

            if ($legacyClassification['category_id'] && Category::where('id', $legacyClassification['category_id'])->exists()) {
                $categoryId = $legacyClassification['category_id'];
            } else {
                $categoryId = $classification['category_id'];
            }

            $subcategoryId = $legacyClassification['subcategory_id'];
            $childCategoryId = $legacyClassification['child_category_id'];
            $grandchildCategoryId = $legacyClassification['grandchild_category_id'];

            $metadataValueIds = $classification['metadata_value_ids'];
            $suggestedTags = $classification['tags'];
        }

        $authorSubcategoryId = $request->author_subcategory_id;
        if (empty($authorSubcategoryId)) {
            $authorProfile = auth()->user()->authorProfile;
            if ($authorProfile) {
                $authorSubcategoryId = $authorProfile->id;
            }
        }

        $post = new Post($request->except(['featured_image', 'published_at', 'status', 'category_id', 'subcategory_id', 'child_category_id', 'grandchild_category_id', 'author_subcategory_id', 'country_code', 'series_id', 'volume', 'chapter_number', 'metadata_value_ids', 'tags']));
        $post->status = $status;
        $post->published_at = $publishedAt;
        $post->category_id = $categoryId;
        $post->subcategory_id = $subcategoryId;
        $post->child_category_id = $childCategoryId;
        $post->grandchild_category_id = $grandchildCategoryId;
        $post->author_subcategory_id = $authorSubcategoryId;
        $post->country_code = $request->country_code ? strtolower($request->country_code) : null;
        $post->series_id = $request->series_id;
        $post->volume = $request->volume;
        $post->chapter_number = $request->chapter_number;

        $post->content = $contentProcessor->process($request->content);
        $post->author_id = auth()->id();
        $post->slug = \Illuminate\Support\Str::utf8Slug($request->title) . '-' . time();

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            $post->featured_image = $path;
        }

        $post->save();

        if (!empty($metadataValueIds)) {
            $post->metadataValues()->sync($metadataValueIds);
        }

        $tagInput = $request->input('tags');
        $tagIds = [];
        if ($tagInput) {
            $tagsArray = array_filter(array_map('trim', explode(',', $tagInput)));
            foreach ($tagsArray as $rawTagName) {
                $tagName = $this->normalizeTagName($rawTagName);
                if (empty($tagName)) continue;
                $slug = \Illuminate\Support\Str::slug($tagName) ?: \Illuminate\Support\Str::utf8Slug($tagName);
                $tag = Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
        } elseif (!empty($suggestedTags)) {
            foreach ($suggestedTags as $rawTagName) {
                $tagName = $this->normalizeTagName($rawTagName);
                if (empty($tagName)) continue;
                $slug = \Illuminate\Support\Str::slug($tagName) ?: \Illuminate\Support\Str::utf8Slug($tagName);
                $tag = Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
        }
        $post->tags()->sync($tagIds);

        if (!empty($tagIds)) {
            $names = [];
            foreach ($tagIds as $id) {
                if ($t = Tag::find($id)) {
                    $names[] = $t->getRawOriginal('name');
                }
            }
            $post->hashtags = implode(' ', array_map(fn($name) => '#' . trim($name), array_slice($names, 0, 3)));
            $post->saveQuietly();
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['tags', 'series', 'comments' => function ($q) {
            $q->with(['replies' => function ($q2) {
                $q2->with('reactions');
            }, 'reactions']);
        }]);

        $categories = \App\Models\Category::getActiveCategoriesForNavigation();

        $previousChapter = null;
        $nextChapter     = null;

        if ($post->series_id) {
            // Use chapter_number-based queries so gaps in numbering (e.g. 1, 3, 5) work correctly.
            // Falls back to id ordering when chapter_number is null.
            $previousChapter = Post::where('series_id', $post->series_id)
                ->where('status', 'published')
                ->where(function ($q) use ($post) {
                    $q->where('chapter_number', '<', $post->chapter_number ?? 2147483647)
                      ->orWhere(function ($q2) use ($post) {
                          $q2->whereNull('chapter_number')
                             ->where('id', '<', $post->id);
                      });
                })
                ->orderByDesc('chapter_number')
                ->orderByDesc('id')
                ->first();

            $nextChapter = Post::where('series_id', $post->series_id)
                ->where('status', 'published')
                ->where(function ($q) use ($post) {
                    $q->where('chapter_number', '>', $post->chapter_number ?? -1)
                      ->orWhere(function ($q2) use ($post) {
                          $q2->whereNull('chapter_number')
                             ->where('id', '>', $post->id);
                      });
                })
                ->orderBy('chapter_number')
                ->orderBy('id')
                ->first();
        }

        $reactionCounts = \App\Models\PostReaction::where('post_id', $post->id)
            ->selectRaw('reaction_type, count(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();

        $validTypes = ['agree', 'disagree', 'love', 'clap', 'care', 'sad', 'condemn', 'laugh', 'congratulations', 'awesome', 'thought_provoking', 'wow', 'like', 'escape'];
        foreach ($validTypes as $type) {
            if (!isset($reactionCounts[$type])) {
                $reactionCounts[$type] = 0;
            }
        }

        $userReactions = [];
        if (auth()->check()) {
            $userReactions = \App\Models\PostReaction::where('post_id', $post->id)
                ->where('user_id', auth()->id())
                ->pluck('reaction_type')
                ->toArray();
        }

        return view('posts.show', compact('post', 'categories', 'previousChapter', 'nextChapter', 'reactionCounts', 'userReactions'));
    }

    public function like(Post $post)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $reaction = \App\Models\PostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->where('reaction_type', 'like')
            ->first();

        if ($reaction) {
            $reaction->delete();
            $action = 'removed';
        } else {
            \App\Models\PostReaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'reaction_type' => 'like'
            ]);
            $action = 'added';
        }

        $count = \App\Models\PostReaction::where('post_id', $post->id)->where('reaction_type', 'like')->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $count
        ]);
    }

    public function react(Request $request, Post $post)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $type = $request->input('type');
        $validTypes = ['agree', 'disagree', 'love', 'clap', 'care', 'sad', 'condemn', 'laugh', 'congratulations', 'awesome', 'thought_provoking', 'wow', 'like', 'escape'];
        
        if (!in_array($type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid reaction type'], 400);
        }

        $userId = auth()->id();
        $reaction = \App\Models\PostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->where('reaction_type', $type)
            ->first();

        if ($reaction) {
            $reaction->delete();
            $action = 'removed';
        } else {
            \App\Models\PostReaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'reaction_type' => $type
            ]);
            $action = 'added';
        }

        $count = \App\Models\PostReaction::where('post_id', $post->id)
            ->where('reaction_type', $type)
            ->count();

        $userActiveReactions = \App\Models\PostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->pluck('reaction_type')
            ->toArray();

        return response()->json([
            'success' => true,
            'action' => $action,
            'reaction_type' => $type,
            'count' => $count,
            'user_reactions' => $userActiveReactions
        ]);
    }

    public function reactToComment(Request $request, \App\Models\Comment $comment)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $type = $request->input('type');
        $validTypes = ['agree', 'disagree', 'love', 'clap', 'care', 'sad', 'condemn', 'laugh', 'congratulations', 'awesome', 'thought_provoking', 'wow', 'like', 'escape'];
        
        if (!in_array($type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid reaction type'], 400);
        }

        $userId = auth()->id();
        $reaction = \App\Models\CommentReaction::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->where('reaction_type', $type)
            ->first();

        if ($reaction) {
            $reaction->delete();
            $action = 'removed';
        } else {
            \App\Models\CommentReaction::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'reaction_type' => $type
            ]);
            $action = 'added';
        }

        $count = \App\Models\CommentReaction::where('comment_id', $comment->id)
            ->where('reaction_type', $type)
            ->count();

        $userActiveReactions = \App\Models\CommentReaction::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->pluck('reaction_type')
            ->toArray();

        return response()->json([
            'success' => true,
            'action' => $action,
            'reaction_type' => $type,
            'count' => $count,
            'user_reactions' => $userActiveReactions
        ]);
    }

    public function share(Post $post)
    {
        $post->increment('shares_count');
        return response()->json([
            'success' => true,
            'shares_count' => $post->shares_count
        ]);
    }

    public function recordRead(Post $post)
    {
        $userId = auth()->id();
        $sessionKey = 'viewed_post_' . $post->id;
        $incremented = false;

        if ($userId) {
            $cacheKey = "post_read_{$post->id}_{$userId}";
            if (!\Cache::has($cacheKey)) {
                \Cache::forever($cacheKey, true);
                $post->increment('views_count');
                $incremented = true;
            }
        } else {
            if (!session()->has($sessionKey)) {
                session()->put($sessionKey, true);
                $post->increment('views_count');
                $incremented = true;
            }
        }

        return response()->json([
            'success' => true,
            'incremented' => $incremented,
            'views_count' => $post->fresh()->views_count
        ]);
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'author_name' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = new \App\Models\Comment([
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'author_name' => $request->author_name,
            'content' => $request->content,
            'is_approved' => true
        ]);
        $comment->save();

        return back()->with('success', 'உங்கள் கருத்து வெற்றிகரமாகப் பதிவிடப்பட்டது!');
    }

    /**
     * Get suggestions and detect typing language/style.
     */
    public function suggestLanguage(Request $request)
    {
        if (\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') !== '1') {
            return response()->json([
                'success' => true,
                'detected' => null,
                'suggestion' => null,
                'candidates' => []
            ]);
        }

        if ($request->has('word')) {
            $request->validate([
                'word' => 'required|string|max:255',
            ]);
            $word = $request->input('word');
            $service = new \App\Services\LanguageHelperService();
            $candidates = $service->getCandidates($word);
            return response()->json([
                'success' => true,
                'candidates' => $candidates
            ]);
        }

        $request->validate([
            'text' => 'required|string|max:10000',
        ]);

        $text = $request->input('text');
        $service = new \App\Services\LanguageHelperService();
        $detected = $service->detectStyle($text);
        $suggestion = $service->suggest($text);

        return response()->json([
            'success' => true,
            'detected' => $detected,
            'suggestion' => $suggestion
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::where('slug', '!=', 'pathivargal')->orderBy('order')->get();
        $authorsCategory = Category::where('slug', 'pathivargal')->with('subcategories')->first();
        $authors = $authorsCategory ? $authorsCategory->subcategories : collect();
        $series = Series::where('status', 'active')->orderBy('title')->get();
        $metadataTypes = MetadataType::with('values')->get();

        return view('posts.edit', compact('post', 'categories', 'authors', 'series', 'metadataTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, ContentProcessorService $contentProcessor, ContentCategorizerService $categorizer)
    {
        Gate::authorize('update', $post);

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'grandchild_category_id' => 'nullable|exists:grandchild_categories,id',
            'author_subcategory_id' => 'nullable|exists:subcategories,id',
            'published_at' => 'nullable|date',
            'country_code' => 'nullable|string|size:2',
            'status' => 'required|in:draft,submitted,under_review,approved,published,rejected',
            'series_id' => 'nullable|exists:series,id',
            'volume' => 'nullable|string|max:255',
            'chapter_number' => 'nullable|integer',
            'metadata_value_ids' => 'nullable|array',
            'metadata_value_ids.*' => 'exists:metadata_values,id',
            'tags' => 'nullable|string',
            'hashtags' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $words = preg_split('/[\s,\#]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) > 3) {
                        $fail('ஹேஷ்டேகுகள் அதிகபட்சம் 3 வார்த்தைகள் மட்டுமே இருக்க வேண்டும். (Maximum 3 hashtags allowed)');
                    }
                }
            ],
        ]);

        $status = $request->status;

        if ($post->status !== $status) {
            if (!Gate::allows('changeStatus', [$post, $status])) {
                if ($status === 'published' && Gate::allows('changeStatus', [$post, 'submitted'])) {
                    $status = 'submitted';
                } else {
                    Gate::authorize('changeStatus', [$post, $status]);
                }
            }
        }

        // Log revision before updating
        PostRevision::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'title' => $post->title,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'created_at' => now(),
        ]);

        $publishedAt = $post->published_at;
        if ($status === 'published' && !$publishedAt) {
            $publishedAt = $request->filled('published_at') ? $request->published_at : now();
        } elseif ($status === 'draft') {
            $publishedAt = null;
        }

        $categoryId = $request->category_id;
        $subcategoryId = $request->subcategory_id;
        $childCategoryId = $request->child_category_id;
        $grandchildCategoryId = $request->grandchild_category_id;
        $metadataValueIds = $request->input('metadata_value_ids', []);
        $suggestedTags = [];

        if (empty($categoryId)) {
            $legacyClassification = $categorizer->categorize($request->title, $request->content);
            $classification = $categorizer->classify($request->title, $request->content);

            if ($legacyClassification['category_id'] && Category::where('id', $legacyClassification['category_id'])->exists()) {
                $categoryId = $legacyClassification['category_id'];
            } else {
                $categoryId = $classification['category_id'];
            }

            $subcategoryId = $legacyClassification['subcategory_id'];
            $childCategoryId = $legacyClassification['child_category_id'];
            $grandchildCategoryId = $legacyClassification['grandchild_category_id'];

            $metadataValueIds = $classification['metadata_value_ids'];
            $suggestedTags = $classification['tags'];
        }

        $authorSubcategoryId = $request->author_subcategory_id;
        if (empty($authorSubcategoryId)) {
            $authorProfile = auth()->user()->authorProfile;
            if ($authorProfile) {
                $authorSubcategoryId = $authorProfile->id;
            }
        }

        $post->fill($request->except(['featured_image', 'published_at', 'status', 'category_id', 'subcategory_id', 'child_category_id', 'grandchild_category_id', 'author_subcategory_id', 'country_code', 'series_id', 'volume', 'chapter_number', 'metadata_value_ids', 'tags']));
        $post->status = $status;
        $post->published_at = $publishedAt;
        $post->category_id = $categoryId;
        $post->subcategory_id = $subcategoryId;
        $post->child_category_id = $childCategoryId;
        $post->grandchild_category_id = $grandchildCategoryId;
        $post->author_subcategory_id = $authorSubcategoryId;
        $post->country_code = $request->country_code ? strtolower($request->country_code) : null;
        $post->series_id = $request->series_id;
        $post->volume = $request->volume;
        $post->chapter_number = $request->chapter_number;
        $post->content = $contentProcessor->process($request->content);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image && \Storage::disk('public')->exists($post->featured_image)) {
                \Storage::disk('public')->delete($post->featured_image);
            }

            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename, 'public');
            $post->featured_image = $path;
        }

        $post->save();

        $post->metadataValues()->sync($metadataValueIds);

        $tagInput = $request->input('tags');
        $tagIds = [];
        if ($tagInput) {
            $tagsArray = array_filter(array_map('trim', explode(',', $tagInput)));
            foreach ($tagsArray as $rawTagName) {
                $tagName = $this->normalizeTagName($rawTagName);
                if (empty($tagName)) continue;
                $slug = \Illuminate\Support\Str::slug($tagName) ?: \Illuminate\Support\Str::utf8Slug($tagName);
                $tag = Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
        } elseif (!empty($suggestedTags)) {
            foreach ($suggestedTags as $rawTagName) {
                $tagName = $this->normalizeTagName($rawTagName);
                if (empty($tagName)) continue;
                $slug = \Illuminate\Support\Str::slug($tagName) ?: \Illuminate\Support\Str::utf8Slug($tagName);
                $tag = Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
        }
        $post->tags()->sync($tagIds);

        if (!empty($tagIds)) {
            $names = [];
            foreach ($tagIds as $id) {
                if ($t = Tag::find($id)) {
                    $names[] = $t->getRawOriginal('name');
                }
            }
            $post->hashtags = implode(' ', array_map(fn($name) => '#' . trim($name), array_slice($names, 0, 3)));
            $post->saveQuietly();
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

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

    /**
     * Submit a post for editorial review.
     */
    public function submitForReview(Post $post)
    {
        Gate::authorize('changeStatus', [$post, 'submitted']);

        $oldStatus = $post->status;
        $post->status = 'submitted';
        $post->save();

        $post->author->notify(new \App\Notifications\PostStatusChanged($post, $oldStatus));

        return redirect()->back()->with('success', 'Post submitted for review successfully.');
    }

    /**
     * Update status with feedback comment (Editor/Admin only).
     */
    public function statusUpdate(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,under_review,approved,published,rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        $status = $request->status;
        Gate::authorize('changeStatus', [$post, $status]);

        $oldStatus = $post->status;
        $post->status = $status;
        if ($status === 'published') {
            if (!$post->published_at) {
                $post->published_at = now();
            }
        } elseif ($status === 'draft') {
            $post->published_at = null;
        }
        $post->save();

        if ($request->filled('comment')) {
            PostFeedback::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'comment' => $request->comment,
                'created_at' => now(),
            ]);
        }

        $post->author->notify(new \App\Notifications\PostStatusChanged($post, $oldStatus, $request->comment));

        return redirect()->back()->with('success', 'Post status updated to ' . ucfirst(str_replace('_', ' ', $status)) . ' successfully.');
    }

    /**
     * Approve a pending post (Admin only - Legacy compatibility).
     */
    public function approve(Post $post)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $oldStatus = $post->status;
        $post->status = 'published';
        if (!$post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        $post->author->notify(new \App\Notifications\PostStatusChanged($post, $oldStatus));

        return redirect()->back()->with('success', 'Post approved and published successfully.');
    }

    /**
     * Classify content and return predictions via JSON API.
     */
    public function classify(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $categorizer = app(ContentCategorizerService::class);
        $result = $categorizer->classify($request->title, $request->content);

        return response()->json($result);
    }

    /**
     * Dynamically create a metadata value for a metadata type.
     */
    public function addMetadataValue(Request $request, MetadataType $metadataType)
    {
        $request->validate([
            'name' => 'required|string|min:1',
        ]);

        $nameInput = trim($request->input('name'));

        // Bidirectional translation: Detect if English (Latin characters/spaces/punctuation)
        $isEnglish = preg_match('/^[a-zA-Z0-9\s\-\_\,\.\'\"]+$/', $nameInput);

        $translator = app(\App\Services\TranslationService::class);

        if ($isEnglish) {
            $nameEn = $nameInput;
            $nameTa = $translator->translate($nameEn, 'en', 'ta');
        } else {
            $nameTa = $nameInput;
            $nameEn = $translator->translate($nameTa, 'ta', 'en');
        }

        $nameTa = trim($nameTa);
        $nameEn = trim($nameEn);

        if (empty($nameTa)) {
            $nameTa = $nameInput;
        }
        if (empty($nameEn)) {
            $nameEn = $nameInput;
        }

        // Generate dynamic slug based on the English translation
        $slug = \Illuminate\Support\Str::slug($nameEn);
        if (empty($slug)) {
            $slug = 'val-' . substr(md5($nameTa), 0, 8);
        }

        // Ensure uniqueness of the slug within the specific metadata type
        $originalSlug = $slug;
        $counter = 1;
        while (MetadataValue::where('metadata_type_id', $metadataType->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create the metadata value
        $metadataValue = MetadataValue::create([
            'metadata_type_id' => $metadataType->id,
            'name' => $nameTa,
            'name_en' => $nameEn,
            'slug' => $slug,
        ]);

        return response()->json($metadataValue, 201);
    }

    /**
     * Store a new series dynamically.
     */
    public function addSeries(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:1|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $titleInput = trim($request->input('title'));
        $descriptionInput = trim($request->input('description', ''));

        // Bidirectional translation for title
        $isEnglishTitle = preg_match('/^[a-zA-Z0-9\s\-\_\,\.\'\"]+$/', $titleInput);
        $translator = app(\App\Services\TranslationService::class);

        if ($isEnglishTitle) {
            $titleEn = $titleInput;
            $titleTa = $translator->translate($titleEn, 'en', 'ta');
        } else {
            $titleTa = $titleInput;
            $titleEn = $translator->translate($titleTa, 'ta', 'en');
        }

        $titleTa = trim($titleTa);
        $titleEn = trim($titleEn);

        if (empty($titleTa)) {
            $titleTa = $titleInput;
        }
        if (empty($titleEn)) {
            $titleEn = $titleInput;
        }

        // Bidirectional translation for description
        $descriptionTa = null;
        $descriptionEn = null;
        if (!empty($descriptionInput)) {
            $isEnglishDesc = preg_match('/^[a-zA-Z0-9\s\-\_\,\.\'\"\?\!\(\)\#\&\%\*]+$/', $descriptionInput);
            if ($isEnglishDesc) {
                $descriptionEn = $descriptionInput;
                $descriptionTa = $translator->translate($descriptionEn, 'en', 'ta');
            } else {
                $descriptionTa = $descriptionInput;
                $descriptionEn = $translator->translate($descriptionTa, 'ta', 'en');
            }
            $descriptionTa = trim($descriptionTa);
            $descriptionEn = trim($descriptionEn);
        }

        // Generate dynamic slug based on the English title
        $slug = \Illuminate\Support\Str::slug($titleEn);
        if (empty($slug)) {
            $slug = \Illuminate\Support\Str::utf8Slug($titleTa);
        }
        if (empty($slug)) {
            $slug = 'series-' . substr(md5($titleTa), 0, 8);
        }

        // Ensure uniqueness of the slug within series
        $originalSlug = $slug;
        $counter = 1;
        while (\App\Models\Series::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $imagePath = $imageFile->storeAs('series', $filename, 'public');
        }

        // Create the series
        $series = \App\Models\Series::create([
            'title' => $titleTa,
            'title_en' => $titleEn,
            'slug' => $slug,
            'description' => $descriptionTa,
            'description_en' => $descriptionEn,
            'image_path' => $imagePath,
            'status' => 'active',
        ]);

        return response()->json($series, 201);
    }

    /**
     * Normalize a raw tag name to prevent duplicates.
     *
     * Pipeline:
     *  1. Trim leading/trailing whitespace.
     *  2. Replace underscores and hyphens with a single space.
     *  3. Collapse any run of whitespace characters into a single space.
     *
     * This means all of the following resolve to the same canonical tag name
     * and therefore the same slug, preventing duplicate rows in the tags table:
     *   "தமிழ் சினிமா", "தமிழ்_சினிமா", "தமிழ்-சினிமா",
     *   "  தமிழ்  சினிமா  ", "Tamil Cinema", "tamil_cinema", "tamil-cinema"
     *
     * NOTE: We deliberately do NOT lowercase Tamil text because Tamil Unicode
     * characters are case-insensitive by nature. For English tags, Str::slug()
     * already lowercases during slug generation, so the slug-based firstOrCreate
     * lookup still deduplicates them correctly even if display names differ in case.
     */
    private function normalizeTagName(string $raw): string
    {
        // Step 1: Trim outer whitespace
        $name = trim($raw);

        // Step 2: Replace underscores and hyphens with a space
        $name = str_replace(['_', '-'], ' ', $name);

        // Step 3: Collapse multiple consecutive whitespace chars to a single space
        $name = preg_replace('/\s+/u', ' ', $name);

        // Final trim after replacements
        return trim($name);
    }
}

