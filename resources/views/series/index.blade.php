<x-public-layout>
    <div class="py-24 bg-[#0B0F19] min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-16 mt-6">
                <h1 class="section-title text-4xl md:text-5xl lg:text-6xl font-black mb-4 text-white">
                    <span class="text-gradient">{{ __('தொடர்கள் / Series') }}</span>
                </h1>
                <p class="stylish-desc text-sm lg:text-base max-w-2xl mx-auto mt-2 leading-relaxed">
                    {{ __('கதைங்கோவில் வெளியாகும் தொடர் கட்டுரைகள் மற்றும் தொடர்கதைகளை இங்கே ஒரே இடத்தில் வரிசையாக வாசிக்கலாம்.') }}
                </p>
            </div>


            <!-- Series Grid -->
            @if($series->isEmpty())
                <div class="text-center py-12 bg-gray-950/40 border border-gray-800 rounded-3xl p-8 backdrop-blur">
                    <p class="text-gray-400 text-lg">{{ __('தற்போது தொடர்கள் ஏதும் இல்லை.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($series as $s)
                        <a href="{{ route('series.show', $s->slug) }}" class="group card-hover bg-gray-900/40 border border-gray-850 rounded-2xl p-4 flex flex-col justify-between h-full transition duration-500 shadow-xl backdrop-blur-sm">
                            <div>
                                <!-- Image / Cover Section -->
                                <div class="aspect-[16/10] rounded-xl overflow-hidden shadow-md bg-gray-950 border border-gray-800/80 mb-5 relative">
                                    @php
                                        $fallbackImg = null;
                                        if (!$s->image_path) {
                                            $firstPostWithImg = $s->posts->first(function($p) {
                                                return !empty($p->image) || !empty($p->featured_image);
                                            });
                                            if ($firstPostWithImg) {
                                                $fallbackImg = $firstPostWithImg->image ?: asset('storage/' . $firstPostWithImg->featured_image);
                                            }
                                        }
                                    @endphp
                                    @if($s->image_path)
                                        <img src="{{ asset('storage/' . $s->image_path) }}" alt="{{ $s->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    @elseif($fallbackImg)
                                        <img src="{{ $fallbackImg }}" alt="{{ $s->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    @else
                                        <!-- Cheerful, premium, and vibrant gradient placeholder -->
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#f39c12]/20 via-[#ff6b6b]/15 to-[#1a1a2e] text-white p-6 text-center select-none">
                                            <div class="w-12 h-12 rounded-full bg-burnt-orange/15 border border-burnt-orange/30 flex items-center justify-center mb-3 group-hover:bg-burnt-orange/20 transition duration-300">
                                                <svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-black font-serif text-gray-300 uppercase tracking-widest group-hover:text-burnt-orange transition-colors duration-300">
                                                {{ $s->title }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Category Badge (Orange Category on the Top Right) -->
                                    <span class="absolute top-3 right-3 px-2.5 py-1 bg-burnt-orange text-white text-[10px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                        {{ __('தொடர்') }}
                                    </span>
                                    
                                    <!-- Posts count badge (Bottom Left) -->
                                    <span class="absolute bottom-3 left-3 px-2.5 py-1 bg-gray-950/80 border border-gray-800/60 text-white text-[10px] font-bold rounded-md backdrop-blur-sm shadow-md">
                                        {{ $s->posts_count }} {{ __('அத்தியாயங்கள்') }}
                                    </span>
                                </div>

                                <!-- Details -->
                                <div class="space-y-3 px-1">
                                    <h3 class="text-lg font-bold text-white leading-snug group-hover:text-burnt-orange transition duration-300 line-clamp-2">
                                        {{ $s->title }}
                                    </h3>
                                    <p class="text-gray-400 text-xs leading-relaxed line-clamp-3">
                                        {{ $s->description ?: __('விளக்கம் இன்னும் சேர்க்கப்படவில்லை.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer & Tags -->
                            <div class="pt-4 border-t border-gray-800/60 mt-6 flex flex-col gap-3 px-1">
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @php
                                        $seriesTags = $s->posts->flatMap->tags->unique('id')->take(3);
                                    @endphp
                                    @if($seriesTags->isNotEmpty())
                                        <div class="text-xs text-blue-400 font-bold tracking-wide flex gap-1.5 flex-wrap">
                                            @foreach($seriesTags as $tag)
                                                <span class="hover:text-blue-300 transition">#{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-blue-400 font-bold tracking-wide">
                                            #தொடர் #கதைங்கோ #வாசிப்பு
                                        </span>
                                    @endif
                                    
                                    <span class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                                        {{ $s->created_at->format('M Y') }}
                                    </span>
                                </div>

                                <!-- Read Button / Link -->
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $firstPost = $s->posts->first();
                                            $author = $firstPost ? ($firstPost->authorSubcategory ?: $firstPost->author) : null;
                                        @endphp
                                        @if($author)
                                            <div class="flex items-center gap-2 text-xs text-gray-300 font-bold">
                                                @if(method_exists($author, 'getAvatarUrl') && $author->getAvatarUrl())
                                                    <img src="{{ $author->getAvatarUrl() }}" alt="" class="w-5 h-5 rounded-full object-cover border border-burnt-orange/30 bg-slate-900">
                                                @elseif(!method_exists($author, 'getAvatarUrl') && $author->image_path && file_exists(public_path('storage/' . $author->image_path)))
                                                    <img src="{{ asset('storage/' . $author->image_path) }}" alt="" class="w-5 h-5 rounded-full object-cover border border-burnt-orange/30 bg-slate-900">
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-burnt-orange/10 border border-burnt-orange/30 flex items-center justify-center text-[8px] font-black text-burnt-orange">
                                                        {{ mb_substr($author->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                @endif
                                                <span>{{ $author->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-1.5 text-burnt-orange font-bold text-xs group-hover:gap-2.5 transition-all duration-300">
                                        <span>{{ __('வாசிக்க') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $series->links() }}
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
