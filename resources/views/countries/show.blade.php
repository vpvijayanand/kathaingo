<x-public-layout>
    <x-slot name="title">
        {{ config('app.name', 'கதைங்கோ') }} - {{ app()->getLocale() === 'en' ? "$countryNameEn ($countryNameTa)" : "$countryNameTa ($countryNameEn)" }}
    </x-slot>

    <x-slot name="styles">
        <style>
            .text-gradient {
                background: linear-gradient(135deg, #f39c12 0%, #ff6b6b 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .card-hover {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-hover:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px -10px rgba(243, 156, 18, 0.2);
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    </x-slot>

    <!-- Main Content -->
    <div class="pt-32 pb-16 px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <!-- Breadcrumbs -->
        <nav class="flex flex-wrap text-sm text-gray-400 gap-2 items-center mb-8 bg-gray-900/40 border border-gray-800/50 rounded-full px-5 py-2.5 w-fit">
            <a href="/" class="hover:text-burnt-orange transition">{{ __('Home') }}</a>
            <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('countries.index') }}" class="hover:text-burnt-orange transition">{{ __('Countries') }}</a>
            <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-white font-medium">{{ app()->getLocale() === 'en' ? $countryNameEn : $countryNameTa }}</span>
        </nav>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h1 class="text-4xl lg:text-5xl font-black mb-3">
                    @if(app()->getLocale() === 'en')
                        {{ $countryNameEn }} <span class="text-gray-400 text-2xl lg:text-3xl font-bold ml-1">({{ $countryNameTa }})</span>
                    @else
                        {{ $countryNameTa }} <span class="text-gray-400 text-2xl lg:text-3xl font-bold ml-1">({{ $countryNameEn }})</span>
                    @endif
                </h1>
                <p class="text-gray-400 text-sm">{{ __('Collection of published stories') }}</p>
            </div>

            <!-- Sorting Tabs -->
            <div class="flex items-center bg-gray-900/60 border border-gray-800 p-1 rounded-xl">
                <a href="{{ route('countries.show', ['country_code' => $country_code, 'sort' => 'latest']) }}"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $sort === 'latest' ? 'bg-burnt-orange text-white shadow-lg shadow-orange-600/20' : 'text-gray-400 hover:text-white' }}">
                    {{ __('Latest') }}
                </a>
                <a href="{{ route('countries.show', ['country_code' => $country_code, 'sort' => 'popular']) }}"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $sort === 'popular' ? 'bg-burnt-orange text-white shadow-lg shadow-orange-600/20' : 'text-gray-400 hover:text-white' }}">
                    {{ __('Popular') }}
                </a>
            </div>
        </div>

        <!-- Stories Grid -->
        @if($posts->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
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
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-12 text-center text-gray-500">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-sm">{{ __('No stories found for this country.') }}</p>
            </div>
        @endif
    </div>
</x-public-layout>
