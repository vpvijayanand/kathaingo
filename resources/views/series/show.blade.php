<x-public-layout>
    <div class="py-24 bg-[#0B0F19] min-h-screen">
        <div class="max-w-4xl mx-auto px-6">
            <!-- Back to Series -->
            <div class="mb-8 mt-6">
                <a href="{{ route('series.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-burnt-orange transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>தொடர்களின் பட்டியலுக்கு திரும்பு</span>
                </a>
            </div>

            <!-- Series Detail Card -->
            <div class="bg-gray-900/40 border border-gray-800 rounded-3xl p-6 md:p-8 mb-12 shadow-2xl backdrop-blur relative overflow-hidden flex flex-col md:flex-row gap-8">
                <!-- Ambient background glow inside card -->
                <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full bg-burnt-orange/5 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-48 h-48 rounded-full bg-orange-500/5 blur-3xl pointer-events-none"></div>

                <!-- Cover Image -->
                <div class="w-full md:w-1/3 aspect-[3/4] rounded-2xl overflow-hidden bg-gray-950 border border-gray-850 shrink-0 shadow-lg relative group">
                    @php
                        $fallbackImg = null;
                        if (!$series->image_path) {
                            $firstPostWithImg = $posts->first(function($p) {
                                return !empty($p->image) || !empty($p->featured_image);
                            });
                            if ($firstPostWithImg) {
                                $fallbackImg = $firstPostWithImg->image ?: asset('storage/' . $firstPostWithImg->featured_image);
                            }
                        }
                    @endphp
                    @if($series->image_path)
                        <img src="{{ asset('storage/' . $series->image_path) }}" alt="{{ $series->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @elseif($fallbackImg)
                        <img src="{{ $fallbackImg }}" alt="{{ $series->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#f39c12] to-[#ff6b6b] text-white text-center p-6 border border-white/10 relative">
                            <!-- Premium decorative overlays -->
                            <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
                            <div class="absolute -bottom-12 -left-12 w-36 h-36 rounded-full bg-black/20 blur-xl"></div>
                            
                            <svg class="w-12 h-12 text-white/90 mb-4 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="text-lg font-black font-serif uppercase tracking-wider drop-shadow-lg leading-snug">
                                {{ $series->title }}
                            </span>
                            <span class="text-[10px] text-white/95 uppercase tracking-widest font-bold mt-2 bg-black/25 px-2.5 py-1 rounded-full border border-white/10">
                                {{ __('தொடர்') }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Metadata -->
                <div class="flex-1 flex flex-col justify-between relative z-10">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-block px-3 py-1 bg-burnt-orange/20 text-burnt-orange text-xs font-bold rounded-full">
                                {{ __('தொடர் / Series') }}
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-white mb-4 tracking-tight leading-tight">
                            {{ $series->title }}
                        </h1>

                        <!-- Author Badge -->
                        @php
                            $firstPost = $posts->first();
                            $author = $firstPost ? ($firstPost->authorSubcategory ?: $firstPost->author) : null;
                        @endphp
                        @if($author)
                            <div class="flex items-center gap-3 mb-6 bg-gray-950/40 border border-gray-800/80 rounded-2xl p-3 w-fit">
                                 @if(method_exists($author, 'getAvatarUrl') && $author->getAvatarUrl())
                                     <img src="{{ $author->getAvatarUrl() }}" alt="{{ $author->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-burnt-orange/40 bg-slate-900">
                                 @elseif(!method_exists($author, 'getAvatarUrl') && $author->image_path && file_exists(public_path('storage/' . $author->image_path)))
                                     <img src="{{ asset('storage/' . $author->image_path) }}" alt="{{ $author->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-burnt-orange/40 bg-slate-900">
                                 @else
                                     <div class="w-9 h-9 rounded-full bg-burnt-orange/10 border-2 border-burnt-orange/30 flex items-center justify-center text-xs font-black text-burnt-orange">
                                         {{ mb_substr($author->name, 0, 1, 'UTF-8') }}
                                     </div>
                                 @endif
                                <div>
                                    <span class="block text-[9px] text-gray-500 uppercase tracking-wider font-bold">{{ __('எழுத்தாளர் / Author') }}</span>
                                    @if(isset($author->slug))
                                        <a href="{{ route('authors.show', $author->slug) }}" class="text-sm font-bold text-white hover:text-burnt-orange transition-colors">
                                            {{ $author->name }}
                                        </a>
                                    @else
                                        <span class="text-sm font-bold text-white">{{ $author->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <p class="text-gray-300 leading-relaxed text-sm md:text-base">
                            {{ $series->description ?: __('விளக்கம் இன்னும் சேர்க்கப்படவில்லை.') }}
                        </p>
                    </div>

                    <div class="mt-8 border-t border-gray-800/80 pt-6 flex flex-wrap gap-6 justify-between items-center text-sm text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-burnt-orange shadow-lg shadow-burnt-orange/50"></span>
                            <span>{{ __('அத்தியாயங்கள்') }}: <strong class="text-white font-extrabold text-base">{{ $series->posts_count }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-lg shadow-blue-500/50"></span>
                            <span>{{ __('துவங்கிய நாள்') }}: <strong class="text-white font-semibold">{{ $series->created_at->format('M Y') }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chapters Section -->
            <div class="space-y-12">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-800 pb-4">
                    அத்தியாயங்கள் (Chapters)
                </h2>

                @if($posts->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        தற்போது பதிவுகள் ஏதும் தயாராக இல்லை. விரைவில் வெளியாகும்!
                    </div>
                @else
                    @foreach($volumes as $volumeName => $volumePosts)
                        @if(count($volumes) > 1 || $volumeName !== __('பொதுவானவை'))
                            <h3 class="text-lg font-bold text-orange-400 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                {{ $volumeName }}
                            </h3>
                        @endif

                        <div class="relative border-l border-gray-800 ml-4 pl-6 space-y-8 mb-12">
                            @foreach($volumePosts as $post)
                                <div class="relative group">
                                    <!-- Timeline Dot -->
                                    <div class="absolute -left-[33px] top-6 w-4 h-4 bg-gray-900 border-2 border-burnt-orange rounded-full group-hover:bg-burnt-orange transition duration-200 z-10"></div>

                                    <!-- Content Card -->
                                    <a href="{{ route('posts.show', $post->slug) }}" class="block bg-gray-900/30 border border-gray-800 rounded-2xl p-4 md:p-5 hover:border-burnt-orange/40 hover:bg-gray-900/50 transition duration-300 shadow-md">
                                        <div class="flex flex-col sm:flex-row gap-5 items-start">
                                            @php
                                                $postImg = $post->image ?: ($post->featured_image ? asset('storage/' . $post->featured_image) : null);
                                                // Fallback to series cover image if chapter has no image
                                                if (!$postImg) {
                                                    $postImg = $series->image_path ? asset('storage/' . $series->image_path) : null;
                                                }
                                            @endphp
                                            @if($postImg)
                                                <div class="w-full sm:w-36 aspect-[16/10] sm:aspect-square rounded-xl overflow-hidden bg-gray-950 border border-gray-850 shrink-0 shadow">
                                                    <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                </div>
                                            @else
                                                <!-- Gradient fallback if no image -->
                                                <div class="w-full sm:w-36 aspect-[16/10] sm:aspect-square rounded-xl overflow-hidden bg-gradient-to-br from-gray-850 to-gray-950 border border-gray-850 shrink-0 flex items-center justify-center text-gray-650 shadow">
                                                    <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif

                                            <div class="flex-1 flex flex-col justify-between self-stretch min-w-0">
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2 mb-2 text-xs text-gray-400">
                                                        @if($post->chapter_number)
                                                            <span class="text-burnt-orange font-bold">அத்தியாயம் {{ $post->chapter_number }}</span>
                                                            <span>•</span>
                                                        @endif
                                                        <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}</span>
                                                        @if($post->category)
                                                            <span>•</span>
                                                            <span class="px-2 py-0.5 bg-burnt-orange/10 text-burnt-orange text-[10px] font-bold uppercase rounded">{{ $post->category->name }}</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="text-lg font-bold text-white group-hover:text-burnt-orange transition duration-200 mb-2 truncate">
                                                        {{ $post->title }}
                                                    </h4>
                                                    <p class="text-xs text-gray-400 line-clamp-2 mb-4 leading-relaxed font-normal">
                                                        {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                                                    </p>
                                                </div>

                                                <!-- Card Footer -->
                                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-800/40">
                                                    <!-- Author Info -->
                                                    <div class="flex items-center gap-2">
                                                        @php
                                                            $author = $post->authorSubcategory ?: $post->author;
                                                        @endphp
                                                        @if($author)
                                                             @if(method_exists($author, 'getAvatarUrl') && $author->getAvatarUrl())
                                                                 <img src="{{ $author->getAvatarUrl() }}" alt="{{ $author->name }}" class="w-5 h-5 rounded-full object-cover bg-slate-900">
                                                             @elseif(!method_exists($author, 'getAvatarUrl') && $author->image_path && file_exists(public_path('storage/' . $author->image_path)))
                                                                 <img src="{{ asset('storage/' . $author->image_path) }}" alt="{{ $author->name }}" class="w-5 h-5 rounded-full object-cover bg-slate-900">
                                                             @else
                                                                 <div class="w-5 h-5 rounded-full bg-burnt-orange/10 border border-burnt-orange/30 flex items-center justify-center text-[8px] font-black text-burnt-orange">
                                                                     {{ mb_substr($author->name, 0, 1, 'UTF-8') }}
                                                                 </div>
                                                             @endif
                                                            <span class="text-xs text-gray-400 font-semibold">{{ $author->name }}</span>
                                                        @endif
                                                    </div>

                                                    <!-- Read link -->
                                                    <div class="text-burnt-orange font-bold text-xs flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                                        <span>{{ __('வாசிக்க') }}</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-public-layout>
