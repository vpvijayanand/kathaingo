<x-public-layout>
    <x-slot name="title">
        {{ $subcategory->name }} - {{ __('Author Profile') }} - {{ config('app.name', 'கதைங்கோ') }}
    </x-slot>

    <!-- Author Hero Section -->
    <section class="relative h-[65vh] min-h-[480px] flex items-center overflow-hidden">
        <!-- Background Cover Image with blur/overlay -->
        <div class="absolute inset-0 bg-cover bg-center origin-center" style="background-image: url('{{ $subcategory->image_path ? asset('storage/' . $subcategory->image_path) : 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1920' }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-gray via-slate-gray/30 to-black/10"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full pt-20">
            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
                <!-- Profile Avatar -->
                <div class="shrink-0">
                    @if($subcategory->getAvatarUrl())
                        <img src="{{ $subcategory->getAvatarUrl() }}" alt="{{ $subcategory->name }}" class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border-4 border-burnt-orange shadow-2xl transform hover:scale-105 transition duration-500 bg-slate-900">
                    @else
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-burnt-orange/10 border-4 border-burnt-orange flex items-center justify-center text-4xl font-black text-burnt-orange shadow-2xl">
                            {{ mb_substr($subcategory->name, 0, 1, 'UTF-8') }}
                        </div>
                    @endif
                </div>

                <!-- Profile Information -->
                <div class="flex-grow space-y-4 w-full">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white drop-shadow-md">{{ $subcategory->name }}</h1>
                            <p class="text-burnt-orange text-sm font-extrabold uppercase tracking-wider mt-1">{{ __('Writer') }}</p>
                        </div>
                        
                        <!-- Edit Button (Only visible to owner or admin) -->
                        @auth
                            @if(auth()->id() === $subcategory->user_id || auth()->user()->is_admin)
                                <a href="{{ route('authors.edit', $subcategory->slug) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900/80 hover:bg-gray-800 border border-gray-700 rounded-full text-xs font-bold text-gray-200 transition-all self-center md:self-start backdrop-blur">
                                    <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('Edit Settings') }}
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Description/Bio -->
                    @if($subcategory->description)
                        <p class="text-gray-200 text-sm md:text-base leading-relaxed max-w-3xl drop-shadow">
                            {{ $subcategory->description }}
                        </p>
                    @else
                        <p class="text-gray-400 text-sm italic">
                            {{ __('Description details not added yet.') }}
                        </p>
                    @endif

                    <!-- Topics usually writes about -->
                    @if($subcategory->topics)
                        <div class="bg-gray-950/70 border border-gray-800 rounded-xl p-4 mt-2 max-w-3xl backdrop-blur-sm shadow-xl">
                            <span class="block text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">{{ __('Usually Writes About') }}:</span>
                            <p class="text-gray-200 text-sm leading-relaxed">{{ $subcategory->topics }}</p>
                        </div>
                    @endif

                    <!-- Contact & Social Links -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-2 text-sm text-gray-300 drop-shadow">
                        @if($subcategory->email)
                            <a href="mailto:{{ $subcategory->email }}" class="flex items-center gap-1.5 hover:text-white transition">
                                <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $subcategory->email }}
                            </a>
                        @endif

                        @if($subcategory->phone)
                            <span class="hidden md:inline text-gray-700">|</span>
                            <a href="tel:{{ $subcategory->phone }}" class="flex items-center gap-1.5 hover:text-white transition">
                                <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $subcategory->phone }}
                            </a>
                        @endif

                        <!-- Social Media Links -->
                        @if($subcategory->facebook_url || $subcategory->instagram_url || $subcategory->linkedin_url)
                            <span class="hidden md:inline text-gray-700">|</span>
                            <div class="flex items-center gap-3">
                                @if($subcategory->facebook_url)
                                    <a href="{{ $subcategory->facebook_url }}" target="_blank" rel="noopener" class="hover:text-blue-500 transition text-gray-300" title="Facebook">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                @endif
                                @if($subcategory->instagram_url)
                                    <a href="{{ $subcategory->instagram_url }}" target="_blank" rel="noopener" class="hover:text-pink-500 transition text-gray-300" title="Instagram">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                @endif
                                @if($subcategory->linkedin_url)
                                    <a href="{{ $subcategory->linkedin_url }}" target="_blank" rel="noopener" class="hover:text-blue-400 transition text-gray-300" title="LinkedIn">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Container (shifted below the full-width hero header) -->
    <main class="py-16 px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-950/40 border border-green-800 text-green-400 px-4 py-3 rounded-xl mb-8">
                {{ session('success') }}
            </div>
        @endif

        <!-- Categories Tiles Section -->
        @if($bloggerSubcategories->count() > 0)
            <section class="mb-16">
                <h2 class="text-2xl font-black mb-6 text-white tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>{{ __('Categories') }}</span>
                </h2>
                
                @php
                    if (!function_exists('getCategoryIcon')) {
                        function getCategoryIcon($slug, $name) {
                            $slug = strtolower($slug);
                            $name = strtolower($name);
                            if (strpos($slug, 'cinema') !== false || strpos($slug, 'movi') !== false || strpos($name, 'சினிமா') !== false) {
                                return '<svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1-1H4v14a1 1 0 001 1z"/></svg>';
                            }
                            if (strpos($slug, 'travel') !== false || strpos($slug, 'payan') !== false || strpos($name, 'பயண') !== false || strpos($slug, 'tour') !== false) {
                                return '<svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2.945M11 3.935A9.003 9.003 0 0120 12c0 4.184-2.853 7.7-6.733 8.78M11 3.935A9.003 9.003 0 003 12c0 4.184 2.853 7.7 6.733 8.78"/></svg>';
                            }
                            return '<svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
                        }
                    }
                @endphp
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <!-- "All" Tile -->
                    <a href="{{ route('authors.show', $subcategory->slug) }}#stories-section" class="flex items-center gap-4 p-5 bg-gray-900 border {{ !$selectedCategorySlug ? 'border-burnt-orange' : 'border-gray-800' }} hover:border-burnt-orange rounded-2xl shadow-xl transition-all card-hover">
                        <div class="p-3 rounded-xl bg-burnt-orange/10 shrink-0">
                            <svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <div>
                            <span class="block text-sm font-extrabold text-white">{{ __('All') }}</span>
                            <span class="text-xs text-gray-400 font-semibold">{{ $postsCountAll }} {{ __('Stories') }}</span>
                        </div>
                    </a>

                    <!-- Category Tiles -->
                    @foreach($bloggerSubcategories as $blogSub)
                        @php
                            $isSelected = $selectedCategorySlug === $blogSub->slug;
                        @endphp
                        <a href="{{ route('authors.show', ['subcategory' => $subcategory->slug, 'category' => $blogSub->slug]) }}#stories-section" class="flex items-center gap-4 p-5 bg-gray-900 border {{ $isSelected ? 'border-burnt-orange' : 'border-gray-800' }} hover:border-burnt-orange rounded-2xl shadow-xl transition-all card-hover">
                            <div class="p-3 rounded-xl bg-burnt-orange/10 shrink-0">
                                {!! getCategoryIcon($blogSub->slug, $blogSub->name) !!}
                            </div>
                            <div>
                                <span class="block text-sm font-extrabold text-white leading-tight">{{ $blogSub->name }}</span>
                                <span class="text-xs text-gray-400 font-semibold">{{ $blogSub->posts_count_by_author }} {{ __('Stories') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Author's Posts Section -->
        <section id="stories-section">
            <h2 class="text-3xl font-black mb-8 pb-3 border-b border-gray-800 flex items-center justify-between">
                <span>{{ __('Stories by :name', ['name' => $subcategory->name]) }}</span>
                <span class="text-sm font-semibold text-gray-500 bg-gray-900 border border-gray-850 px-3.5 py-1.5 rounded-full">
                    {{ __('Total') }}: {{ $posts->total() }}
                </span>
            </h2>

            @if($posts->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($posts as $post)
                        <article class="card-hover group bg-gray-900/40 border border-gray-800/60 rounded-xl p-3.5 flex flex-col justify-between h-full">
                            <div>
                                @php
                                    $featuredImg = $post->image ?: ($post->featured_image ? asset('storage/' . $post->featured_image) : null);
                                @endphp
                                @if($featuredImg)
                                    <div class="aspect-[16/9] rounded-lg overflow-hidden mb-3 bg-gray-800 relative">
                                        <a href="{{ route('posts.show', $post->slug) }}">
                                            <img src="{{ $featuredImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        </a>
                                        <!-- Category Badge -->
                                        @if($post->category)
                                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-burnt-orange/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="aspect-[16/9] rounded-lg overflow-hidden mb-3 bg-gray-850 border border-gray-800/80 flex items-center justify-center text-gray-500 text-xs relative">
                                        <img src="{{ asset('images/logo/logo-header.png') }}" alt="Kathaingo" class="w-1/2 h-auto opacity-30 grayscale object-contain drop-shadow-md">
                                        @if($post->category)
                                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-burnt-orange/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <h3 class="text-sm font-bold leading-snug group-hover:text-burnt-orange transition line-clamp-2">
                                        <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>

                                    <p class="text-gray-400 text-xs leading-relaxed line-clamp-2">
                                        {{ Str::limit(strip_tags($post->content), 75) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer & Engagement -->
                            <div class="pt-2.5 mt-3 border-t border-gray-800/80 flex flex-col gap-2.5">
                                <!-- Engagement Indicators -->
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    @php
                                        $commentsCount = $post->comments_count ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="flex items-center gap-0.5 bg-gray-950/40 border border-gray-800/50 rounded-full px-2 py-0.5" title="{{ __('Comments') }}">
                                            <span>💬</span>
                                            <span class="font-bold text-gray-300 text-[10px]">{{ $commentsCount }}</span>
                                        </span>
                                    </div>
                                    
                                    @if($post->tags->isNotEmpty())
                                        <div class="text-[10px] text-blue-400 font-bold tracking-wide flex gap-1.5 flex-wrap">
                                            @foreach($post->tags->take(3) as $tag)
                                                <a href="{{ url('/stories?tag=' . $tag->slug) }}" class="hover:text-blue-300 transition">
                                                    #{{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @elseif($post->hashtags)
                                        <span class="text-[10px] text-blue-400 font-bold tracking-wide">
                                            {{ $post->hashtags }}
                                        </span>
                                    @else
                                        <span class="text-[9px] text-gray-500 font-semibold uppercase tracking-wider">
                                            {{ $post->category?->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Author & Date -->
                                <div class="flex items-center justify-between pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        @if($post->authorSubcategory)
                                            <a href="{{ route('authors.show', $post->authorSubcategory->slug) }}" class="flex items-center gap-1.5 text-[10px] text-gray-400 hover:text-burnt-orange font-bold transition">
                                                @if($post->authorSubcategory->getAvatarUrl())
                                                    <img src="{{ $post->authorSubcategory->getAvatarUrl() }}" alt="" class="w-4 h-4 rounded-full object-cover border border-burnt-orange/30 bg-slate-900">
                                                @else
                                                    <div class="w-4 h-4 rounded-full bg-burnt-orange/10 border border-burnt-orange/30 flex items-center justify-center text-[6px] font-black text-burnt-orange">
                                                        {{ mb_substr($post->authorSubcategory->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                @endif
                                                <span>{{ $post->authorSubcategory->name }}</span>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <span class="text-gray-500 text-[9px] font-semibold">
                                        {{ $post->published_at?->format('M d, Y') ?? __('Recently') }}
                                    </span>
                                </div>

                                @auth
                                    @if(auth()->id() === $post->author_id || auth()->user()->is_admin)
                                        <div class="flex items-center justify-end gap-3 pt-2.5 mt-2 border-t border-gray-800/60">
                                            <a href="{{ route('posts.edit', $post->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-burnt-orange hover:text-orange-400 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                            <span class="text-gray-800">|</span>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-500 hover:text-red-400 transition bg-transparent border-0 p-0 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    <span>{{ __('Delete') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-12 text-center text-gray-500">
                    <p class="text-lg">{{ __('No stories found.') }}</p>
                </div>
            @endif
        </section>
    </main>

</x-public-layout>
