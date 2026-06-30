<x-public-layout>
    <x-slot name="title">
        {{ config('app.name', 'கதைங்கோ') }} - Inspiring Stories & Ideas
    </x-slot>

    <x-slot name="styles">
        <style>
            .featured-post-title {
                filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.95)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.8));
                -webkit-text-stroke: 1px rgba(0, 0, 0, 0.85);
                color: #ffffff;
            }

            .featured-post-desc {
                font-size: 1.25rem;
                font-weight: 800;
                color: #f39c12 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
            }

            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .backdrop-blur {
                backdrop-filter: blur(10px);
            }

            .category-badge {
                position: relative;
                overflow: hidden;
            }

            .category-badge::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .category-badge:hover::before {
                left: 100%;
            }
        </style>
    </x-slot>

    <!-- Hero Section -->
    <section x-data="{
        images: {{ $heroImages->toJson() }},
        currentIndex: 0,
        init() {
            if (this.images.length > 1) {
                setInterval(() => {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                }, 7000);
            }
        }
    }" class="relative h-[85vh] min-h-[600px] flex items-center overflow-hidden">

        <!-- Background Slideshow -->
        <div class="absolute inset-0 z-0 bg-gray-900">
            <template x-for="(image, index) in images" :key="image.id">
                <div x-show="currentIndex === index" x-transition:enter="transition ease-in-out duration-[3000ms]"
                    x-transition:enter-start="opacity-0 scale-105 blur-sm"
                    x-transition:enter-end="opacity-100 scale-100 blur-0"
                    x-transition:leave="transition ease-in-out duration-[3000ms]"
                    x-transition:leave-start="opacity-100 scale-100 blur-0"
                    x-transition:leave-end="opacity-0 scale-110 blur-sm"
                    class="absolute inset-0 bg-cover bg-center origin-center will-change-transform"
                    :style="`background-image: url('${image.image_path}')`">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/20 to-transparent"></div>
                </div>
            </template>
            <!-- Fallback if no images -->
            <div x-show="images.length === 0" class="absolute inset-0 hero-gradient"></div>
            
            <!-- Ambient Glow Overlay (Always active) -->
            <div class="hero-glow-overlay"></div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full">
            <div class="max-w-4xl">
                <h1 class="hero-title text-5xl lg:text-7xl font-black mb-8 leading-normal">
                    {{ __('Stories &') }}
                    <span class="text-gradient block mt-2 pb-2">{{ __('Learning') }}</span>
                </h1>
                <p class="hero-desc text-xl lg:text-2xl text-gray-200 mb-12 max-w-2xl leading-relaxed">
                    {{ __('A sparrow that absorbs, digests, cooks stories, and sows them as seeds') }}
                </p>
                <a href="#stories"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                    {{ __('Start Reading') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <div id="stories">
    <!-- Featured Posts (Only on page 1) -->
    @if($posts->onFirstPage() && $featuredPosts->isNotEmpty())
        <section class="py-16 px-6 lg:px-8 bg-slate-gray">
            <div class="max-w-7xl mx-auto">
                <!-- Section Header -->
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white">
                        <span class="text-gradient">{{ __('Featured Stories') }}</span>
                    </h2>
                    <p class="stylish-desc text-sm lg:text-base max-w-2xl mt-2">
                        {{ __('Most liked, appreciated, and must-read stories across different categories.') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredPosts as $post)
                        <div class="bg-gray-900/60 border border-gray-800/80 rounded-2xl p-6 shadow-2xl backdrop-blur-sm flex flex-col justify-between h-full group hover:border-burnt-orange/40 transition duration-500">
                            <div>
                                <!-- Image -->
                                @php
                                    $featuredImg = $post->image ?: ($post->featured_image ? asset('storage/' . $post->featured_image) : null);
                                @endphp
                                @if($featuredImg)
                                    <div class="aspect-[16/10] rounded-xl overflow-hidden shadow-md bg-gray-800 border border-gray-800/80 mb-5 relative">
                                        <a href="{{ route('posts.show', $post->slug) }}">
                                            <img src="{{ $featuredImg }}" alt="{{ $post->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                        </a>
                                        <!-- Category Badge -->
                                        @if($post->category)
                                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-burnt-orange/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="aspect-[16/10] rounded-xl overflow-hidden shadow-md bg-gray-850 border border-gray-800/80 mb-5 flex items-center justify-center text-gray-500 text-xs relative">
                                        <img src="{{ asset('images/logo/logo-header.png') }}" alt="Kathaingo" class="w-1/2 h-auto opacity-30 grayscale object-contain drop-shadow-md">
                                        @if($post->category)
                                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-burnt-orange/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Title & Excerpt -->
                                <h3 class="text-lg font-bold text-white leading-snug group-hover:text-burnt-orange transition duration-300 mb-3 line-clamp-2">
                                    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-gray-300 text-xs leading-relaxed font-normal mb-5 line-clamp-3">
                                    {{ Str::limit(strip_tags($post->content), 120) }}
                                </p>
                            </div>

                            <!-- Footer & Engagement -->
                            <div class="pt-4 border-t border-gray-800/60 mt-auto flex flex-col gap-3">
                                <!-- Engagement Indicators -->
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @php
                                        $commentsCount = $post->comments_count ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="flex items-center gap-1 bg-gray-950/40 border border-gray-800/50 rounded-full px-2.5 py-0.5" title="{{ __('Comments') }}">
                                            <span>💬</span>
                                            <span class="font-bold text-gray-300">{{ $commentsCount }}</span>
                                        </span>
                                    </div>
                                    
                                    @if($post->tags->isNotEmpty())
                                        <div class="text-[11px] text-blue-400 font-bold tracking-wide flex gap-1.5 flex-wrap">
                                            @foreach($post->tags->take(3) as $tag)
                                                <a href="{{ url('/stories?tag=' . $tag->slug) }}" class="hover:text-blue-300 transition">
                                                    #{{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @elseif($post->hashtags)
                                        <span class="text-[11px] text-blue-400 font-bold tracking-wide">
                                            {{ $post->hashtags }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                                            {{ $post->category?->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Author & Date -->
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-2">
                                        @if($post->authorSubcategory)
                                            <a href="{{ route('authors.show', $post->authorSubcategory->slug) }}" class="flex items-center gap-2 text-[10px] text-gray-400 hover:text-burnt-orange font-bold transition">
                                                @if($post->authorSubcategory->getAvatarUrl())
                                                    <img src="{{ $post->authorSubcategory->getAvatarUrl() }}" alt="" class="w-5 h-5 rounded-full object-cover border border-burnt-orange/30 bg-slate-900">
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-burnt-orange/10 border border-burnt-orange/30 flex items-center justify-center text-[7px] font-black text-burnt-orange">
                                                        {{ mb_substr($post->authorSubcategory->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                @endif
                                                <span>{{ $post->authorSubcategory->name }}</span>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <span class="text-gray-500 text-[10px] font-semibold">
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
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Latest Stories Grid -->
    <section class="py-20 px-6 lg:px-8 bg-slate-gray">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-4">
                <div>
                    <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white">
                        <span class="text-gradient">{{ __('Latest Stories') }}</span>
                    </h2>
                    <p class="stylish-desc text-sm lg:text-base mt-2">{{ __('Fresh perspectives and insights') }}</p>
                </div>
                @if(isset($activeFilter) && $activeFilter)
                    <div class="flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-full px-5 py-2">
                        <span class="text-sm text-gray-400">{{ __('Filtered by:') }}</span>
                        <span class="text-sm font-bold text-burnt-orange">{{ $activeFilter }}</span>
                        <a href="{{ url('/stories') }}" class="text-gray-400 hover:text-white transition-all text-sm ml-2 font-bold" title="{{ __('Clear Filter') }}">
                            {{ __('Clear Filter') }} (❌)
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column (Stories) -->
                <div class="lg:col-span-9 space-y-12">
                    @if($posts->count() > 0)
                        @php
                            $gridPosts = $posts;
                        @endphp
                        
                        @if($gridPosts->count() > 0)
                            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
                                @foreach($gridPosts as $post)
                                    <article class="card-hover group bg-gray-900/40 border border-gray-800/60 rounded-xl p-3.5 flex flex-col justify-between h-full">
                                        <div>
                                            @php
                                                $featuredImg = $post->image ?: ($post->featured_image ? asset('storage/' . $post->featured_image) : null);
                                            @endphp
                                            @if($featuredImg)
                                                <div class="aspect-[16/9] rounded-lg overflow-hidden mb-3 bg-gray-800 relative">
                                                    <a href="{{ route('posts.show', $post->slug) }}">
                                                        <img src="{{ $featuredImg }}" alt="{{ $post->title }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                    </a>
                                                    <!-- Category Badge -->
                                                    @if($post->category)
                                                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-burnt-orange/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                            {{ $post->category->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="aspect-[16/9] rounded-lg overflow-hidden mb-3 bg-gray-855 border border-gray-800/80 flex items-center justify-center text-gray-500 text-xs relative">
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
                            <div class="mt-16">
                                {{ $posts->links() }}
                            </div>
                        @else
                            @if($posts->onFirstPage())
                                <p class="text-center text-gray-500 py-12">{{ __('No other stories found.') }}</p>
                            @endif
                        @endif
                    @else
                        <p class="text-center text-gray-500 py-12">{{ __('No stories found.') }}</p>
                    @endif
                </div>

                <!-- Right Column (Sidebar with Filters and Calendar) -->
                <div class="lg:col-span-3 bg-gray-900 border border-gray-800 rounded-2xl p-4 shadow-xl space-y-6 sticky top-28 max-w-[280px] ml-auto w-full backdrop-blur-md bg-opacity-70">
                    <!-- Advanced Filters Form -->
                    <form action="{{ route('stories.index') }}" method="GET" class="space-y-4">
                        <div>
                            <h3 class="text-sm font-bold mb-1 flex items-center gap-1.5 text-white">
                                <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <span>வடிகட்டிகள் (Filters)</span>
                            </h3>
                            <p class="text-gray-400 text-[10px]">{{ __('Refine stories list.') }}</p>
                        </div>

                        <!-- Category Selector -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">பகுதி (Category)</label>
                            <select name="category" onchange="this.form.submit()" class="w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-lg text-xs py-1.5">
                                <option value="">அனைத்தும் (All Categories)</option>
                                @foreach($allCategories as $c)
                                    <option value="{{ $c->slug }}" {{ request('category') == $c->slug ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category-Specific Metadata -->
                        @if(request('category'))
                            @php
                                $selectedCatModel = $allCategories->firstWhere('slug', request('category'));
                            @endphp
                            @if($selectedCatModel && $selectedCatModel->metadataTypes->isNotEmpty())
                                <div class="border-t border-gray-800 pt-3 space-y-3">
                                    @foreach($selectedCatModel->metadataTypes as $type)
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">{{ $type->name }}</label>
                                            <select name="metadata_values[]" onchange="this.form.submit()" class="w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-lg text-xs py-1.5">
                                                <option value="">{{ __('All') }} {{ $type->name }}</option>
                                                @foreach($type->values as $value)
                                                    <option value="{{ $value->slug }}" {{ in_array($value->slug, (array)request('metadata_values')) ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <!-- Series Selector -->
                        <div class="border-t border-gray-800 pt-3">
                            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">தொடர்கள் (Series)</label>
                            <select name="series" onchange="this.form.submit()" class="w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-lg text-xs py-1.5">
                                <option value="">அனைத்தும் (All Series)</option>
                                @foreach($allSeries as $s)
                                    <option value="{{ $s->slug }}" {{ request('series') == $s->slug ? 'selected' : '' }}>
                                        {{ $s->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Selector -->
                        <div class="border-t border-gray-800 pt-3">
                            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">காலம் (Date Range)</label>
                            <select name="date_range" onchange="this.form.submit()" class="w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-lg text-xs py-1.5">
                                <option value="">அனைத்தும் (All Time)</option>
                                <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>இந்த வாரம் (This Week)</option>
                                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>இந்த மாதம் (This Month)</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['category', 'metadata_values', 'tag', 'series', 'author', 'date_range', 'date', 'search']))
                            <div class="pt-2 border-t border-gray-800">
                                <a href="{{ route('stories.index') }}" class="block text-center text-[10px] font-bold text-burnt-orange hover:text-orange-400 transition uppercase bg-burnt-orange/10 border border-burnt-orange/30 py-1.5 rounded-lg">
                                    வடிகட்டல்களை நீக்கு (Clear All)
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- Calendar Widget -->
                    <div class="border-t border-gray-800 pt-4">
                        <div class="mb-3">
                            <h3 class="text-xs font-bold mb-0.5 flex items-center gap-1 text-white">
                                <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Calendar') }}
                            </h3>
                            <p class="text-gray-400 text-[9px]">{{ __('View posts by date.') }}</p>
                        </div>

                        <!-- Alpine.js Calendar Widget -->
                        <div x-data="calendarWidget({{ json_encode($publishedDates) }}, '{{ request('date') }}', '{{ app()->getLocale() }}')" class="space-y-2.5">
                            <style>
                                .scrollbar-none::-webkit-scrollbar {
                                    display: none;
                                }
                                .scrollbar-none {
                                    -ms-overflow-style: none;
                                    scrollbar-width: none;
                                }
                            </style>

                            <!-- Scroll-Wheel Month & Year Pickers -->
                            <div class="flex gap-2 justify-center items-center py-1 bg-gray-950/60 rounded-xl border border-gray-800/80 mb-3 relative overflow-hidden h-[96px]">
                                <!-- Cylinder Fade Top -->
                                <div class="absolute top-0 inset-x-0 h-6 bg-gradient-to-b from-gray-950 via-gray-950/80 to-transparent pointer-events-none z-10"></div>
                                
                                <!-- Cylinder Fade Bottom -->
                                <div class="absolute bottom-0 inset-x-0 h-6 bg-gradient-to-t from-gray-950 via-gray-950/80 to-transparent pointer-events-none z-10"></div>

                                <!-- Center Highlight Band -->
                                <div class="absolute inset-x-0 top-[calc(50%-13px)] h-[26px] bg-burnt-orange/15 border-y border-burnt-orange/30 pointer-events-none z-0"></div>

                                <!-- Month Wheel -->
                                <div class="flex flex-col items-center w-24 z-20">
                                    <span class="text-[8px] uppercase tracking-wider text-gray-500 font-bold mb-0.5">{{ __('Month') }}</span>
                                    <div 
                                        x-ref="monthWheel"
                                        @scroll="onMonthScroll"
                                        class="h-[66px] overflow-y-scroll scrollbar-none snap-y snap-mandatory w-full text-center relative scroll-smooth"
                                    >
                                        <div class="h-[20px] shrink-0"></div>
                                        <template x-for="(name, idx) in monthNames" :key="idx">
                                            <div 
                                                @click="setMonth(idx)"
                                                :class="currentMonth === idx ? 'text-burnt-orange font-bold text-xs' : 'text-gray-400 text-[10px] hover:text-white'"
                                                class="h-[26px] flex items-center justify-center snap-center cursor-pointer select-none transition-all duration-150"
                                                x-text="name"
                                            ></div>
                                        </template>
                                        <div class="h-[20px] shrink-0"></div>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="w-px h-8 bg-gray-800 mt-2 z-20"></div>

                                <!-- Year Wheel -->
                                <div class="flex flex-col items-center w-20 z-20">
                                    <span class="text-[8px] uppercase tracking-wider text-gray-500 font-bold mb-0.5">{{ __('Year') }}</span>
                                    <div 
                                        x-ref="yearWheel"
                                        @scroll="onYearScroll"
                                        class="h-[66px] overflow-y-scroll scrollbar-none snap-y snap-mandatory w-full text-center relative scroll-smooth"
                                    >
                                        <div class="h-[20px] shrink-0"></div>
                                        <template x-for="yr in yearRange" :key="yr">
                                            <div 
                                                @click="setYear(yr)"
                                                :class="currentYear === yr ? 'text-burnt-orange font-bold text-xs' : 'text-gray-400 text-[10px] hover:text-white'"
                                                class="h-[26px] flex items-center justify-center snap-center cursor-pointer select-none transition-all duration-150"
                                                x-text="yr"
                                            ></div>
                                        </template>
                                        <div class="h-[20px] shrink-0"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Days of Week Headers -->
                            <div class="grid grid-cols-7 gap-0.5 text-center text-[9px] font-bold text-gray-500 uppercase">
                                @if(app()->getLocale() === 'en')
                                    <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                @else
                                    <div>ஞா</div><div>தி</div><div>செ</div><div>பு</div><div>வி</div><div>வெ</div><div>ச</div>
                                @endif
                            </div>

                            <!-- Days Grid -->
                            <div class="grid grid-cols-7 gap-0.5">
                                <template x-for="dayObj in days" :key="dayObj.day + '-' + dayObj.isCurrentMonth + '-' + dayObj.dateStr">
                                    <div class="aspect-square flex items-center justify-center relative">
                                        <button 
                                            @click="selectDate(dayObj)"
                                            :disabled="!dayObj.isCurrentMonth"
                                            :class="{
                                                'text-gray-700 cursor-default pointer-events-none': !dayObj.isCurrentMonth,
                                                'text-white hover:bg-gray-850 rounded-full': dayObj.isCurrentMonth && !dayObj.hasPost && !dayObj.isSelected,
                                                'text-burnt-orange font-black bg-burnt-orange/10 border border-burnt-orange/30 hover:bg-burnt-orange hover:text-white rounded-full transition-all': dayObj.isCurrentMonth && dayObj.hasPost && !dayObj.isSelected,
                                                'bg-burnt-orange text-white font-black rounded-full shadow-lg shadow-orange-600/30 border border-white/10': dayObj.isSelected
                                            }"
                                            class="w-5 h-5 text-[9px] font-semibold focus:outline-none flex flex-col items-center justify-center transition"
                                        >
                                            <span x-text="dayObj.day"></span>
                                            <!-- Dot indicator for posts -->
                                            <template x-if="dayObj.isCurrentMonth && dayObj.hasPost && !dayObj.isSelected">
                                                <span class="w-0.5 h-0.5 bg-burnt-orange rounded-full mt-0.5"></span>
                                            </template>
                                            <template x-if="dayObj.isSelected">
                                                <span class="w-0.5 h-0.5 bg-white rounded-full mt-0.5"></span>
                                            </template>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    <x-slot name="scripts">
        <script>
            function calendarWidget(publishedDates, selectedDateStr, locale) {
                return {
                    publishedDates: publishedDates || [],
                    selectedDateStr: selectedDateStr || '',
                    currentYear: new Date().getFullYear(),
                    currentMonth: new Date().getMonth(),
                    yearRange: [],
                    days: [],
                    monthNames: (locale === 'en')
                        ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                        : ['ஜன', 'பிப்', 'மார்', 'ஏப்', 'மே', 'ஜூன்', 'ஜூலை', 'ஆக', 'செப்', 'அக்', 'நவ', 'டிச'],

                    init() {
                        if (this.selectedDateStr) {
                            const selDate = new Date(this.selectedDateStr);
                            if (!isNaN(selDate.getTime())) {
                                this.currentYear = selDate.getFullYear();
                                this.currentMonth = selDate.getMonth();
                            }
                        }
                        this.yearRange = this.getYearRange();
                        this.generateCalendar();
                        setTimeout(() => {
                            this.syncWheelsToState();
                        }, 50);
                    },

                    getYearRange() {
                        const startYear = 2000;
                        const endYear = 2050;
                        const range = [];
                        for (let y = startYear; y <= endYear; y++) {
                            range.push(y);
                        }
                        return range;
                    },

                    generateCalendar() {
                        const firstDayIndex = new Date(this.currentYear, this.currentMonth, 1).getDay();
                        const totalDays = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                        const prevTotalDays = new Date(this.currentYear, this.currentMonth, 0).getDate();

                        const daysArray = [];

                        // Previous month filler days
                        for (let i = firstDayIndex - 1; i >= 0; i--) {
                            daysArray.push({
                                day: prevTotalDays - i,
                                isCurrentMonth: false,
                                dateStr: ''
                            });
                        }

                        // Current month days
                        for (let i = 1; i <= totalDays; i++) {
                            const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                            const hasPost = this.publishedDates.includes(dateStr);
                            const isSelected = this.selectedDateStr === dateStr;
                            daysArray.push({
                                day: i,
                                isCurrentMonth: true,
                                dateStr: dateStr,
                                hasPost: hasPost,
                                isSelected: isSelected
                            });
                        }

                        // Pad standard 6-week layout (42 cells)
                        const remainingCells = 42 - daysArray.length;
                        for (let i = 1; i <= remainingCells; i++) {
                            daysArray.push({
                                day: i,
                                isCurrentMonth: false,
                                dateStr: ''
                            });
                        }

                        this.days = daysArray;
                    },

                    syncWheelsToState() {
                        if (this.$refs.monthWheel) {
                            this.$refs.monthWheel.scrollTop = this.currentMonth * 26;
                        }
                        const yearIdx = this.yearRange.indexOf(this.currentYear);
                        if (yearIdx > -1 && this.$refs.yearWheel) {
                            this.$refs.yearWheel.scrollTop = yearIdx * 26;
                        }
                    },

                    setMonth(idx) {
                        if (this.currentMonth !== idx) {
                            this.currentMonth = idx;
                            this.generateCalendar();
                            this.$nextTick(() => this.syncWheelsToState());
                        }
                    },

                    setYear(yr) {
                        if (this.currentYear !== yr) {
                            this.currentYear = yr;
                            this.generateCalendar();
                            this.$nextTick(() => this.syncWheelsToState());
                        }
                    },

                    onMonthScroll(e) {
                        const scrollTop = e.target.scrollTop;
                        const idx = Math.round(scrollTop / 26);
                        if (idx >= 0 && idx < this.monthNames.length && this.currentMonth !== idx) {
                            this.currentMonth = idx;
                            this.generateCalendar();
                        }
                    },

                    onYearScroll(e) {
                        const scrollTop = e.target.scrollTop;
                        const idx = Math.round(scrollTop / 26);
                        if (idx >= 0 && idx < this.yearRange.length) {
                            const yr = this.yearRange[idx];
                            if (this.currentYear !== yr) {
                                this.currentYear = yr;
                                this.generateCalendar();
                            }
                        }
                    },

                    selectDate(dayObj) {
                        if (dayObj.isCurrentMonth) {
                            window.location.href = `/stories?date=${dayObj.dateStr}`;
                        }
                    }
                }
            }
        </script>
    </x-slot>
</x-public-layout>