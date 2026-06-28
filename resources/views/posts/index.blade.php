<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
                @if(request()->query('status') === 'submitted')
                    {{ __('Pending Approvals / ஒப்புதல்கள்') }}
                @else
                    {{ __('My Blog Posts') }}
                @endif
            </h2>
            <a href="{{ route('posts.create') }}" class="bg-burnt-orange hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                Create New Post
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('success') }}
                </div>
                <script>
                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && key.startsWith('kathaingo_draft_autosave_')) {
                            localStorage.removeItem(key);
                            i--; // adjust index after removal
                        }
                    }
                </script>
            @endif


            <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    @if($posts->count() > 0)
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($posts as $post)
                                <div class="bg-gray-950 border border-gray-800/80 rounded-xl p-5 shadow-md hover:shadow-xl hover:border-gray-700/80 transition-all duration-300 flex flex-col justify-between">
                                    <div>
                                        @if($post->image)
                                            <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-900">
                                        @elseif($post->featured_image)
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-900">
                                        @else
                                            <div class="w-full h-40 bg-gray-900 border border-gray-800 rounded-lg mb-4 flex items-center justify-center text-gray-500 font-medium">
                                                No Featured Image
                                            </div>
                                        @endif
                                        
                                        <h3 class="font-bold text-xl mb-3 text-white line-clamp-2">{{ $post->title }}</h3>
                                        
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">
                                                Status
                                            </span>
                                            @php
                                                $statusClasses = 'bg-gray-950/40 text-gray-400 border-gray-800';
                                                if ($post->status === 'published') {
                                                    $statusClasses = 'bg-green-950/40 text-green-400 border-green-800';
                                                } elseif ($post->status === 'submitted') {
                                                    $statusClasses = 'bg-blue-950/40 text-blue-400 border-blue-800';
                                                } elseif ($post->status === 'under_review') {
                                                    $statusClasses = 'bg-yellow-950/40 text-yellow-400 border-yellow-800';
                                                } elseif ($post->status === 'approved') {
                                                    $statusClasses = 'bg-emerald-950/40 text-emerald-400 border-emerald-800';
                                                } elseif ($post->status === 'rejected') {
                                                    $statusClasses = 'bg-red-950/40 text-red-400 border-red-800';
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusClasses }} border">
                                                {{ ucfirst(str_replace('_', ' ', $post->status)) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-300 text-sm mb-6 line-clamp-3">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-t border-gray-800/60 pt-4">
                                        <div class="flex gap-3 items-center">
                                            <a href="{{ route('posts.show', $post->slug) }}" class="text-gray-400 hover:text-white text-sm font-semibold transition" target="_blank">View</a>
                                            @if(in_array($post->status, ['submitted', 'under_review', 'approved']) && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                                                <form action="{{ route('posts.statusUpdate', $post->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="published">
                                                    <button type="submit" class="bg-burnt-orange hover:bg-orange-600 text-white text-xs font-bold py-1 px-3 rounded-full transition shadow-md">
                                                        Publish
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="{{ route('posts.edit', $post->id) }}" class="text-burnt-orange hover:text-orange-400 text-sm font-bold transition">Edit</a>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-semibold transition">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-400 py-12">
                            @if(request()->query('status') === 'submitted')
                                {{ __('No pending posts awaiting approval.') }}
                            @else
                                {{ __('No posts found. Create your first post!') }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
