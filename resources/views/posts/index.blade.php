<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
                {{ __('My Blog Posts') }}
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
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($posts->count() > 0)
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($posts as $post)
                                <div class="border rounded-lg p-4 shadow hover:shadow-lg transition">
                                    @if($post->image)
                                        <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-40 object-cover rounded mb-4">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 rounded mb-4 flex items-center justify-center text-gray-500">
                                            No Image
                                        </div>
                                    @endif
                                    <h3 class="font-bold text-lg mb-2 text-slate-gray">{{ $post->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Status: 
                                        <span class="px-2 py-1 rounded text-xs {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($post->status) }}
                                        </span>
                                    </p>
                                    <p class="text-gray-700 text-sm mb-4">{{ Str::limit($post->content, 100) }}</p>
                                    
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('posts.edit', $post->id) }}" class="text-burnt-orange hover:text-orange-600 text-sm font-semibold">Edit</a>
                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-burnt-orange hover:text-orange-600 text-sm font-semibold">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500">No posts found. Create your first post!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
