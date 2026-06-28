<x-public-layout>
    <x-slot name="title">
        {{ $post->title }} - {{ config('app.name', 'கதைங்கோ') }}
    </x-slot>

    <x-slot name="styles">
        <style>
            .post-title {
                filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.95)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.8));
                -webkit-text-stroke: 1px rgba(0, 0, 0, 0.85);
                color: #ffffff;
            }

            article.prose p:first-of-type {
                font-size: 1.35rem !important;
                font-weight: 800 !important;
                color: #f39c12 !important;
                line-height: 1.7 !important;
                margin-bottom: 2rem !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
            }

            .backdrop-blur {
                backdrop-filter: blur(10px);
            }

            .prose a {
                color: #f39c12;
                text-decoration: underline;
            }

            .prose a:hover {
                color: #ff6b6b;
            }

            /* Custom Rich Text (Prose) styling overrides to counter Tailwind preflight resets */
            .prose p {
                margin-bottom: 1.5rem !important;
                line-height: 1.8;
            }

            .prose p:last-child {
                margin-bottom: 0 !important;
            }

            .prose strong, .prose b {
                font-weight: 800 !important;
                color: #ffffff;
            }

            .prose em, .prose i {
                font-style: italic !important;
            }

            .prose u {
                text-decoration: underline !important;
            }

            .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
                color: #ffffff;
                font-weight: 800 !important;
                margin-top: 2rem !important;
                margin-bottom: 1rem !important;
                line-height: 1.3;
            }

            .prose h1 { font-size: 2.25rem !important; }
            .prose h2 { font-size: 1.875rem !important; }
            .prose h3 { font-size: 1.5rem !important; }
            .prose h4 { font-size: 1.25rem !important; }

            .prose ul {
                list-style-type: disc !important;
                margin-left: 1.5rem !important;
                margin-bottom: 1.5rem !important;
            }

            .prose ol {
                list-style-type: decimal !important;
                margin-left: 1.5rem !important;
                margin-bottom: 1.5rem !important;
            }

            .prose li {
                margin-bottom: 0.5rem !important;
                line-height: 1.7;
            }

            .prose blockquote {
                border-left: 4px solid #f39c12 !important;
                padding-left: 1.5rem !important;
                margin: 1.5rem 0 !important;
                color: #cbd5e1 !important;
                font-style: italic !important;
            }

            .prose pre {
                background-color: #0f172a !important;
                padding: 1rem !important;
                border-radius: 0.5rem !important;
                overflow-x: auto !important;
                margin-bottom: 1.5rem !important;
            }

            .prose code {
                font-family: monospace !important;
                background-color: #1e293b !important;
                padding: 0.2rem 0.4rem !important;
                border-radius: 0.25rem !important;
                font-size: 0.875em !important;
            }
        </style>
    </x-slot>

    <!-- Main Content Container -->
    <main class="pt-32 pb-24 px-6 lg:px-8 bg-slate-gray min-h-[85vh]">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumbs -->
            <nav class="flex flex-wrap text-sm text-gray-400 gap-2 items-center mb-8 bg-gray-900/40 border border-gray-800/50 rounded-full px-5 py-2.5 w-fit">
                <a href="/" class="hover:text-white transition font-medium">Home</a>
                @if($post->category)
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ url('/stories?category=' . $post->category->slug) }}" class="hover:text-white transition font-medium text-burnt-orange">{{ $post->category->name }}</a>
                @endif
                @if($post->subcategory)
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ url('/stories?subcategory=' . $post->subcategory->slug) }}" class="hover:text-white transition">{{ $post->subcategory->name }}</a>
                @endif
                @if($post->childCategory)
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ url('/stories?child_category=' . $post->childCategory->slug) }}" class="hover:text-white transition">{{ $post->childCategory->name }}</a>
                @endif
                @if($post->grandchildCategory)
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ url('/stories?grandchild_category=' . $post->grandchildCategory->slug) }}" class="hover:text-white transition">{{ $post->grandchildCategory->name }}</a>
                @endif
                @if($post->tags->isNotEmpty())
                    <span class="w-px h-4 bg-gray-700 mx-2"></span>
                    <span class="flex items-center gap-1.5 flex-wrap">
                        @foreach($post->tags as $tag)
                            <a href="{{ url('/stories?tag=' . $tag->slug) }}" class="bg-burnt-orange hover:bg-orange-600 text-white text-xs font-semibold px-2.5 py-1 rounded transition">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </span>
                @endif
            </nav>

            <!-- Article Header -->
            <header class="mb-12">
                <h1 class="post-title text-3xl md:text-5xl font-black mb-6 leading-tight">
                    {{ $post->title }}
                </h1>
                
                <div class="flex items-center gap-4 text-gray-400">
                    <div class="flex items-center gap-3">
                        @if($post->authorSubcategory && $post->authorSubcategory->getAvatarUrl())
                            <img src="{{ $post->authorSubcategory->getAvatarUrl() }}" alt="" class="w-10 h-10 rounded-full object-cover border border-burnt-orange bg-slate-900">
                        @else
                            <div class="w-10 h-10 rounded-full bg-burnt-orange/20 border border-burnt-orange flex items-center justify-center font-bold text-burnt-orange">
                                {{ mb_substr($post->authorSubcategory ? $post->authorSubcategory->name : $post->author->name, 0, 1, 'UTF-8') }}
                            </div>
                        @endif
                        
                        @if($post->authorSubcategory)
                            <a href="{{ route('authors.show', $post->authorSubcategory->slug) }}" class="font-semibold text-gray-300 hover:text-burnt-orange transition">
                                {{ $post->authorSubcategory->name }}
                            </a>
                        @else
                            <span class="font-semibold text-gray-300">{{ $post->author->name }}</span>
                        @endif
                    </div>
                    <span class="text-gray-600 mx-4">•</span>
                    <time class="text-sm">{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</time>
                </div>
            </header>

            <!-- Featured Image -->
            @if($post->image)
                <div class="w-full rounded-2xl overflow-hidden shadow-2xl mb-12 bg-gray-800 border border-gray-800">
                    <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full max-h-[500px] object-cover">
                </div>
            @elseif($post->featured_image)
                <div class="w-full rounded-2xl overflow-hidden shadow-2xl mb-12 bg-gray-800 border border-gray-800">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-[500px] object-cover">
                </div>
            @endif

            <!-- Article Body -->
            <article class="prose prose-invert prose-lg max-w-none text-gray-300 leading-relaxed bg-gray-900/30 border border-gray-800/40 rounded-3xl p-8 md:p-12 shadow-xl">
                {!! $post->content !!}
            </article>

            <!-- Embedded Social Media & Videos (Displayed BELOW the article) -->
            @php
                $embeds = [];
                if (!empty($post->video_url)) {
                    // Split by newline or comma
                    $urls = preg_split('/[\n,]+/', $post->video_url);
                    foreach ($urls as $url) {
                        $url = trim($url);
                        if (empty($url)) continue;

                        $embedHtml = null;
                        $platform = null;

                        // 1. YouTube Video
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches)) {
                            $videoId = $matches[1];
                            $platform = 'youtube';
                            $embedHtml = '<div class="aspect-video w-full rounded-2xl overflow-hidden shadow-2xl mb-6 bg-gray-900 border border-gray-800">
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>';
                        }
                        // 2. YouTube Shorts
                        elseif (preg_match('/youtube\.com\/shorts\/([^"&?\/ ]{11})/', $url, $matches)) {
                            $videoId = $matches[1];
                            $platform = 'youtube';
                            $embedHtml = '<div class="flex justify-center mb-6">
                                <div class="w-full max-w-[360px] aspect-[9/16] rounded-2xl overflow-hidden shadow-2xl bg-gray-900 border border-gray-800">
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>';
                        }
                        // 3. Instagram
                        elseif (preg_match('/instagram\.com\/(?:p|reel)\/([^"&?\/ ]+)/', $url, $matches)) {
                            $igId = $matches[1];
                            $platform = 'instagram';
                            $embedHtml = '<div class="flex justify-center mb-6">
                                <div class="w-full max-w-[500px] border border-gray-850 rounded-2xl overflow-hidden bg-gray-950/40 p-2 shadow-2xl">
                                    <iframe class="w-full min-h-[450px]" src="https://www.instagram.com/p/' . $igId . '/embed" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
                                </div>
                            </div>';
                        }
                        // 4. Twitter / X
                        elseif (preg_match('/(?:twitter|x)\.com\/[^\/]+\/status\/(\d+)/', $url, $matches)) {
                            $platform = 'twitter';
                            $embedHtml = '<div class="flex justify-center mb-6">
                                <div class="w-full max-w-[550px] bg-transparent">
                                    <blockquote class="twitter-tweet" data-theme="dark" data-align="center"><a href="' . $url . '"></a></blockquote>
                                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                                </div>
                            </div>';
                        }
                        // 5. Facebook
                        elseif (str_contains($url, 'facebook.com')) {
                            $platform = 'facebook';
                            $encodedUrl = urlencode($url);
                            if (str_contains($url, '/videos/') || str_contains($url, 'watch') || str_contains($url, 'fb.watch')) {
                                $embedHtml = '<div class="flex justify-center mb-6">
                                    <div class="w-full max-w-[500px] aspect-video rounded-2xl overflow-hidden shadow-2xl bg-gray-900 border border-gray-800">
                                        <iframe src="https://www.facebook.com/plugins/video.php?href=' . $encodedUrl . '&show_text=false&width=500" class="w-full h-full" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                                    </div>
                                </div>';
                            } else {
                                $embedHtml = '<div class="flex justify-center mb-6">
                                    <div class="w-full max-w-[500px] min-h-[500px] border border-gray-800 rounded-2xl overflow-hidden bg-gray-900 shadow-2xl">
                                        <iframe src="https://www.facebook.com/plugins/post.php?href=' . $encodedUrl . '&show_text=true&width=500" class="w-full min-h-[500px]" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                                    </div>
                                </div>';
                            }
                        }
                        // 6. TikTok
                        elseif (preg_match('/tiktok\.com\/@[^\/]+\/video\/(\d+)/', $url, $matches)) {
                            $videoId = $matches[1];
                            $platform = 'tiktok';
                            $embedHtml = '<div class="flex justify-center mb-6">
                                <div class="w-full max-w-[325px] min-h-[580px] bg-transparent">
                                    <blockquote class="tiktok-embed" cite="' . $url . '" data-video-id="' . $videoId . '" style="max-width: 325px; min-width: 325px;"><section></section></blockquote>
                                    <script async src="https://www.tiktok.com/embed.js"></script>
                                </div>
                            </div>';
                        }
                        // 7. Generic Link fallback
                        else {
                            $platform = 'generic';
                            $embedHtml = '<div class="mb-6 p-4 bg-gray-900/60 border border-gray-850 rounded-2xl text-center shadow-lg">
                                <a href="' . $url . '" target="_blank" class="inline-flex items-center gap-2 text-burnt-orange hover:text-orange-400 font-bold transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span class="truncate max-w-[280px] sm:max-w-md">' . $url . '</span>
                                </a>
                            </div>';
                        }

                        if ($embedHtml) {
                            $embeds[] = [
                                'url' => $url,
                                'html' => $embedHtml,
                                'platform' => $platform
                            ];
                        }
                    }
                }
            @endphp

            @if(!empty($embeds))
                <div class="mt-12 space-y-6">
                    @foreach($embeds as $embed)
                        {!! $embed['html'] !!}
                    @endforeach
                </div>
            @endif


            @if($post->series_id && $post->series)
                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- SERIES CONSTELLATION CARD                               --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                <div class="mt-10 relative overflow-hidden rounded-3xl border border-orange-500/20 shadow-2xl"
                     style="background: radial-gradient(ellipse at top left, rgba(194,91,27,0.18) 0%, rgba(15,23,42,0.97) 55%), linear-gradient(135deg, #0f172a 0%, #1a1f35 100%);">

                    {{-- Decorative starfield dots --}}
                    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
                        @php
                            $stars = [[8,12],[18,70],[27,35],[35,88],[48,15],[55,60],[62,40],[70,80],[80,20],[90,55],[15,50],[40,5],[60,92],[85,38],[92,72]];
                        @endphp
                        @foreach($stars as [$x,$y])
                            <div class="absolute rounded-full bg-orange-300/20 animate-pulse"
                                 style="width:{{ [2,2,3,2,3][($loop->index) % 5] }}px; height:{{ [2,2,3,2,3][($loop->index) % 5] }}px; left:{{$x}}%; top:{{$y}}%; animation-delay:{{ $loop->index * 0.3 }}s; animation-duration:{{ 2 + ($loop->index % 3) }}s;"></div>
                        @endforeach
                    </div>

                    <div class="relative z-10 p-6 md:p-8">

                        {{-- Series Header --}}
                        <div class="flex items-start gap-4 mb-6 pb-5 border-b border-orange-500/15">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg"
                                 style="background: linear-gradient(135deg, #c25b1b, #e07b30);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-orange-400/70 mb-1">
                                    தொடர் · Series
                                </p>
                                <a href="{{ route('series.show', $post->series->slug) }}"
                                   class="text-lg md:text-xl font-black text-white hover:text-orange-400 transition-colors leading-tight block truncate">
                                    {{ $post->series->getRawOriginal('title') }}
                                </a>
                                <div class="flex flex-wrap items-center gap-3 mt-2">
                                    @if($post->volume)
                                        <span class="inline-flex items-center gap-1 text-xs text-orange-300/80 bg-orange-500/10 border border-orange-500/20 px-2.5 py-0.5 rounded-full font-semibold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                                            {{ $post->volume }}
                                        </span>
                                    @endif
                                    @if($post->chapter_number)
                                        <span class="inline-flex items-center gap-1 text-xs text-white/70 bg-white/5 border border-white/10 px-2.5 py-0.5 rounded-full font-semibold">
                                            அத்தியாயம் · Chapter {{ $post->chapter_number }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- View all series link --}}
                            <a href="{{ route('series.show', $post->series->slug) }}"
                               class="flex-shrink-0 hidden sm:flex items-center gap-1.5 text-xs text-orange-400 hover:text-orange-300 border border-orange-500/30 hover:border-orange-400/60 px-3 py-1.5 rounded-full transition-all font-semibold">
                                அனைத்தும்
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Chapter Navigation --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            {{-- ← Previous Chapter --}}
                            @if($previousChapter)
                                <a href="{{ route('posts.show', $previousChapter->slug) }}"
                                   class="group flex items-center gap-4 p-4 rounded-2xl border border-white/5 hover:border-orange-500/40 transition-all duration-300 hover:shadow-lg"
                                   style="background: rgba(255,255,255,0.03);"
                                   onmouseover="this.style.background='rgba(194,91,27,0.1)'"
                                   onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                    {{-- Arrow --}}
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center border border-orange-500/30 group-hover:border-orange-400 group-hover:bg-orange-500/20 transition-all duration-300"
                                         style="background: rgba(194,91,27,0.1);">
                                        <svg class="w-5 h-5 text-orange-400 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-orange-400/60 mb-0.5">
                                            முந்தைய அத்தியாயம் · Previous Chapter
                                        </p>
                                        <p class="text-sm font-bold text-gray-200 group-hover:text-white transition-colors leading-snug truncate">
                                            {{ $previousChapter->getRawOriginal('title') }}
                                        </p>
                                        @if($previousChapter->chapter_number)
                                            <p class="text-[11px] text-gray-500 mt-0.5">அத்தியாயம் {{ $previousChapter->chapter_number }}</p>
                                        @endif
                                    </div>
                                </a>
                            @else
                                <div class="flex items-center gap-4 p-4 rounded-2xl border border-white/5 opacity-30 cursor-not-allowed"
                                     style="background: rgba(255,255,255,0.02);">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center border border-white/10"
                                         style="background: rgba(255,255,255,0.03);">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-gray-700 mb-0.5">முந்தைய அத்தியாயம் · Previous Chapter</p>
                                        <p class="text-xs text-gray-700 font-semibold">முதல் அத்தியாயம் · First Chapter</p>
                                    </div>
                                </div>
                            @endif

                            {{-- → Next Chapter --}}
                            @if($nextChapter)
                                <a href="{{ route('posts.show', $nextChapter->slug) }}"
                                   class="group flex items-center gap-4 p-4 rounded-2xl border border-white/5 hover:border-orange-500/40 transition-all duration-300 hover:shadow-lg sm:flex-row-reverse sm:text-right"
                                   style="background: rgba(255,255,255,0.03);"
                                   onmouseover="this.style.background='rgba(194,91,27,0.1)'"
                                   onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                    {{-- Arrow --}}
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center border border-orange-500/30 group-hover:border-orange-400 group-hover:bg-orange-500/20 transition-all duration-300"
                                         style="background: rgba(194,91,27,0.1);">
                                        <svg class="w-5 h-5 text-orange-400 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-orange-400/60 mb-0.5">
                                            அடுத்த அத்தியாயம் · Next Chapter
                                        </p>
                                        <p class="text-sm font-bold text-gray-200 group-hover:text-white transition-colors leading-snug truncate">
                                            {{ $nextChapter->getRawOriginal('title') }}
                                        </p>
                                        @if($nextChapter->chapter_number)
                                            <p class="text-[11px] text-gray-500 mt-0.5">அத்தியாயம் {{ $nextChapter->chapter_number }}</p>
                                        @endif
                                    </div>
                                </a>
                            @else
                                <div class="flex items-center gap-4 p-4 rounded-2xl border border-white/5 opacity-30 cursor-not-allowed sm:flex-row-reverse"
                                     style="background: rgba(255,255,255,0.02);">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center border border-white/10"
                                         style="background: rgba(255,255,255,0.03);">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-[0.15em] font-bold text-gray-700 mb-0.5">அடுத்த அத்தியாயம் · Next Chapter</p>
                                        <p class="text-xs text-gray-700 font-semibold">இறுதி அத்தியாயம் · Final Chapter</p>
                                    </div>
                                </div>
                            @endif

                        </div>{{-- end chapter nav grid --}}
                    </div>{{-- end relative z-10 --}}
                </div>{{-- end constellation card --}}
            @endif


            <!-- Engagement Bar (Likes, Shares, Reads) -->
            <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-2xl p-6 flex flex-wrap justify-between items-center gap-6 shadow-lg backdrop-blur">
                <div class="flex items-center gap-6">
                    <!-- Reads Count -->
                    <div class="flex items-center gap-2 text-gray-400" title="{{ __('Reads') }}">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-sm font-semibold"><span id="views-count-val">{{ $post->views_count }}</span> {{ __('Reads') }}</span>
                    </div>

                    <!-- Reactions Wrapper -->
                    <style>
                        @keyframes fadeInScale {
                            from {
                                opacity: 0;
                                transform: scale(0.95);
                            }
                            to {
                                opacity: 1;
                                transform: scale(1);
                            }
                        }
                        .animate-fade-in-scale {
                            animation: fadeInScale 0.2s ease-out forwards;
                        }
                    </style>
                    @php
                        $primaryReactions = [
                            [
                                'type' => 'agree',
                                'emoji' => '💯',
                                'label' => app()->getLocale() === 'ta' ? 'அதே தான்' : 'Exactly right',
                                'tooltip' => app()->getLocale() === 'ta' ? 'அதே தான்' : 'Exactly right.',
                            ],
                            [
                                'type' => 'disagree',
                                'emoji' => '🙏',
                                'label' => app()->getLocale() === 'ta' ? 'முரண்படுகிறேன்' : 'I respectfully disagree',
                                'tooltip' => app()->getLocale() === 'ta' ? 'முரண்படுகிறேன்' : 'I respectfully disagree.',
                            ],
                            [
                                'type' => 'love',
                                'emoji' => '❤️',
                                'label' => app()->getLocale() === 'ta' ? 'மனம் கவர்ந்தது' : 'I loved this',
                                'tooltip' => app()->getLocale() === 'ta' ? 'மனம் கவர்ந்தது' : 'I loved this.',
                            ],
                            [
                                'type' => 'clap',
                                'emoji' => '👏',
                                'label' => app()->getLocale() === 'ta' ? 'அட்டகாசம்' : 'Well written',
                                'tooltip' => app()->getLocale() === 'ta' ? 'அட்டகாசம்' : 'Well written.',
                            ],
                            [
                                'type' => 'care',
                                'emoji' => '🤗',
                                'label' => app()->getLocale() === 'ta' ? 'மனதை தொட்டது' : 'This touched me',
                                'tooltip' => app()->getLocale() === 'ta' ? 'மனதை தொட்டது' : 'This touched me.',
                            ],
                            [
                                'type' => 'sad',
                                'emoji' => '😢',
                                'label' => app()->getLocale() === 'ta' ? 'வருந்துகிறேன்' : 'This saddened me',
                                'tooltip' => app()->getLocale() === 'ta' ? 'வருந்துகிறேன்' : 'This saddened me.',
                            ],
                            [
                                'type' => 'condemn',
                                'emoji' => '😡',
                                'label' => app()->getLocale() === 'ta' ? 'கண்டிக்கிறேன்' : 'I condemn this',
                                'tooltip' => app()->getLocale() === 'ta' ? 'கண்டிக்கிறேன்' : 'I condemn this.',
                            ],
                        ];

                        $secondaryReactions = [
                            [
                                'type' => 'laugh',
                                'emoji' => '😂',
                                'label' => app()->getLocale() === 'ta' ? 'கிகிகி...' : 'This made me laugh',
                                'tooltip' => app()->getLocale() === 'ta' ? 'கிகிகி...' : 'This made me laugh.',
                            ],
                            [
                                'type' => 'congratulations',
                                'emoji' => '🎉',
                                'label' => app()->getLocale() === 'ta' ? 'வாழ்த்துகள்' : 'Congratulations',
                                'tooltip' => app()->getLocale() === 'ta' ? 'வாழ்த்துகள்' : 'Congratulations.',
                            ],
                            [
                                'type' => 'awesome',
                                'emoji' => '💪',
                                'label' => app()->getLocale() === 'ta' ? 'செம!' : 'Awesome',
                                'tooltip' => app()->getLocale() === 'ta' ? 'செம!' : 'Awesome.',
                            ],
                            [
                                'type' => 'thought_provoking',
                                'emoji' => '🤔',
                                'label' => app()->getLocale() === 'ta' ? 'யோசிக்கணும்' : 'This made me think',
                                'tooltip' => app()->getLocale() === 'ta' ? 'யோசிக்கணும்' : 'This made me think.',
                            ],
                            [
                                'type' => 'wow',
                                'emoji' => '😲',
                                'label' => app()->getLocale() === 'ta' ? 'ஆத்தாடி!' : 'Wow!',
                                'tooltip' => app()->getLocale() === 'ta' ? 'ஆத்தாடி!' : 'Wow!',
                            ],
                            [
                                'type' => 'like',
                                'emoji' => '👍',
                                'label' => app()->getLocale() === 'ta' ? 'ரசித்தேன்' : 'I enjoyed this',
                                'tooltip' => app()->getLocale() === 'ta' ? 'ரசித்தேன்' : 'I enjoyed this.',
                            ],
                            [
                                'type' => 'escape',
                                'emoji' => '🏃',
                                'label' => app()->getLocale() === 'ta' ? 'நான் வரலப்பா!' : "I'm staying out of this!",
                                'tooltip' => app()->getLocale() === 'ta' ? 'நான் வரலப்பா!' : "I'm staying out of this!",
                            ],
                        ];
                    @endphp
                    <div class="flex flex-wrap items-center gap-2 bg-gray-950/45 border border-gray-800 rounded-3xl px-3.5 py-1.5 shadow-inner">
                        @foreach($primaryReactions as $react)
                            @php
                                $type = $react['type'];
                                $isActive = in_array($type, $userReactions ?? []);
                                $count = $reactionCounts[$type] ?? 0;
                                $btnClass = $isActive 
                                    ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange' 
                                    : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                            @endphp
                            <button onclick="handleReactionClick('{{ $type }}')" 
                                id="btn-reaction-{{ $type }}"
                                class="relative group flex items-center gap-1.5 px-3 py-1.5 rounded-full border transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                data-tooltip="{{ $react['tooltip'] }}">
                                
                                @if($type === 'care')
                                    <img src="/images/reactions/care.png" alt="Care" 
                                         class="w-5 h-5 object-contain transition group-hover:scale-125 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-reaction-care">
                                @else
                                    <span class="text-base transition-transform group-hover:scale-125 duration-200">{{ $react['emoji'] }}</span>
                                @endif
                                
                                <span class="text-xs font-bold text-gray-300" id="count-{{ $type }}">{{ $count }}</span>
                            </button>
                        @endforeach

                        {{-- Toggle Button for Secondary Reactions --}}
                        <button onclick="toggleSecondaryReactions(this, 'post-{{ $post->id }}')" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-800 text-gray-400 hover:text-white hover:bg-gray-850 transition-all duration-200 cursor-pointer text-xs font-bold focus:outline-none">
                            <span>➕ {{ app()->getLocale() === 'ta' ? 'மேலும்' : 'More' }}</span>
                        </button>

                        @foreach($secondaryReactions as $react)
                            @php
                                $type = $react['type'];
                                $isActive = in_array($type, $userReactions ?? []);
                                $count = $reactionCounts[$type] ?? 0;
                                $btnClass = $isActive 
                                    ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange' 
                                    : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                            @endphp
                            <button onclick="handleReactionClick('{{ $type }}')" 
                                id="btn-reaction-{{ $type }}"
                                class="secondary-reaction-post-{{ $post->id }} hidden relative group flex items-center gap-1.5 px-3 py-1.5 rounded-full border transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                data-tooltip="{{ $react['tooltip'] }}">
                                
                                @if($type === 'care')
                                    <img src="/images/reactions/care.png" alt="Care" 
                                         class="w-5 h-5 object-contain transition group-hover:scale-125 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-reaction-care">
                                @else
                                    <span class="text-base transition-transform group-hover:scale-125 duration-200">{{ $react['emoji'] }}</span>
                                @endif
                                
                                <span class="text-xs font-bold text-gray-300" id="count-{{ $type }}">{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Share Buttons -->
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-400 font-medium">{{ __('Share:') }}</span>
                    <span class="text-xs text-gray-500 mr-2"><span id="shares-count-val">{{ $post->shares_count }}</span> {{ __('Shares') }}</span>
                    
                    <!-- Facebook Share -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" onclick="trackShare()" class="p-2 bg-blue-900/30 text-blue-400 hover:bg-blue-600 hover:text-white rounded-lg transition" title="{{ __('Share on Facebook') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.95z"/></svg>
                    </a>

                    <!-- Twitter / X Share -->
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" onclick="trackShare()" class="p-2 bg-gray-800/50 text-white hover:bg-white hover:text-black rounded-lg transition" title="{{ __('Share on Twitter/X') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>

                    <!-- WhatsApp Share -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . request()->fullUrl()) }}" target="_blank" onclick="trackShare()" class="p-2 bg-green-900/30 text-green-400 hover:bg-green-600 hover:text-white rounded-lg transition" title="{{ __('Share on WhatsApp') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.859-4.407 9.862-9.823.002-2.623-1.018-5.089-2.872-6.944C16.607 1.982 14.15 1.01 11.538 1.01 6.1 1.01 1.677 5.418 1.674 10.835c-.001 1.702.449 3.366 1.303 4.834L2.008 21.92l6.59-1.766z"/></svg>
                    </a>

                    <!-- Copy Link -->
                    <button onclick="copyPostLink()" class="p-2 bg-gray-800/50 text-gray-300 hover:bg-burnt-orange hover:text-white rounded-lg transition" title="{{ __('Copy Link') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-12 bg-gray-900/30 border border-gray-800/40 rounded-3xl p-8 md:p-12 shadow-xl">
                <h3 class="text-2xl font-black mb-8 flex items-center gap-3">
                    <svg class="w-6 h-6 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ __('Comments') }} ({{ $post->comments->count() }})
                </h3>

                @if(session('success'))
                    <div class="bg-green-900/30 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl mb-6 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Comments List -->
                <div class="space-y-6 mb-8 max-h-[600px] overflow-y-auto pr-2">
                    @forelse($post->comments->where('parent_id', null) as $comment)
                        <div class="bg-gray-950/40 border border-gray-850 p-6 rounded-2xl space-y-4">
                            <!-- Comment Header & Metadata -->
                            <div class="flex justify-between items-start">
                                <span class="font-bold text-burnt-orange text-base">{{ $comment->author_name }}</span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <!-- Comment Content -->
                            <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</p>
                            
                            <!-- Comment Actions (Reactions & Reply Trigger) -->
                            <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-800/40">
                                <!-- Comment Reactions -->
                                <div class="flex flex-wrap items-center gap-1.5 bg-gray-950/30 border border-gray-900 rounded-2xl px-3 py-1.5">
                                    @foreach($primaryReactions as $react)
                                        @php
                                            $type = $react['type'];
                                            $cReactions = $comment->reactions ?? collect();
                                            $isActive = auth()->check() && $cReactions->where('user_id', auth()->id())->where('reaction_type', $type)->isNotEmpty();
                                            $count = $cReactions->where('reaction_type', $type)->count();
                                            $btnClass = $isActive 
                                                ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange scale-100' 
                                                : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                                        @endphp
                                        <button onclick="handleCommentReactionClick({{ $comment->id }}, '{{ $type }}')" 
                                            id="btn-comment-reaction-{{ $comment->id }}-{{ $type }}"
                                            class="relative group flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                            data-tooltip="{{ $react['tooltip'] }}">
                                            
                                            @if($type === 'care')
                                                <img src="/images/reactions/care.png" alt="Care" 
                                                     class="w-3.5 h-3.5 object-contain transition group-hover:scale-110 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-comment-reaction-care-{{ $comment->id }}">
                                            @else
                                                <span class="text-xs transition-transform group-hover:scale-110 duration-200">{{ $react['emoji'] }}</span>
                                            @endif
                                            
                                            <span class="font-bold text-gray-300" id="count-comment-{{ $comment->id }}-{{ $type }}">{{ $count }}</span>
                                        </button>
                                    @endforeach

                                    {{-- Toggle Button for Comment Secondary Reactions --}}
                                    <button type="button" onclick="toggleSecondaryReactions(this, 'comment-{{ $comment->id }}')" 
                                        class="flex items-center gap-1 px-2 py-0.5 rounded-full border border-gray-800 text-gray-400 hover:text-white hover:bg-gray-850 transition-all duration-200 cursor-pointer text-[10px] font-bold focus:outline-none">
                                        <span>➕ {{ app()->getLocale() === 'ta' ? 'மேலும்' : 'More' }}</span>
                                    </button>

                                    @foreach($secondaryReactions as $react)
                                        @php
                                            $type = $react['type'];
                                            $cReactions = $comment->reactions ?? collect();
                                            $isActive = auth()->check() && $cReactions->where('user_id', auth()->id())->where('reaction_type', $type)->isNotEmpty();
                                            $count = $cReactions->where('reaction_type', $type)->count();
                                            $btnClass = $isActive 
                                                ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange scale-100' 
                                                : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                                        @endphp
                                        <button onclick="handleCommentReactionClick({{ $comment->id }}, '{{ $type }}')" 
                                            id="btn-comment-reaction-{{ $comment->id }}-{{ $type }}"
                                            class="secondary-reaction-comment-{{ $comment->id }} hidden relative group flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                            data-tooltip="{{ $react['tooltip'] }}">
                                            
                                            @if($type === 'care')
                                                <img src="/images/reactions/care.png" alt="Care" 
                                                     class="w-3.5 h-3.5 object-contain transition group-hover:scale-110 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-comment-reaction-care-{{ $comment->id }}">
                                            @else
                                                <span class="text-xs transition-transform group-hover:scale-110 duration-200">{{ $react['emoji'] }}</span>
                                            @endif
                                            
                                            <span class="font-bold text-gray-300" id="count-comment-{{ $comment->id }}-{{ $type }}">{{ $count }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                
                                <!-- Reply Button Trigger -->
                                <button onclick="toggleReplyForm({{ $comment->id }})" class="text-xs font-semibold text-gray-400 hover:text-burnt-orange transition flex items-center gap-1 cursor-pointer bg-transparent border-0 p-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    <span>{{ app()->getLocale() === 'ta' ? 'பதிலளி' : 'Reply' }}</span>
                                </button>
                            </div>

                            <!-- Inline Reply Form (Hidden by default) -->
                            <div id="reply-form-{{ $comment->id }}" class="hidden pt-4 border-t border-gray-800/30">
                                <form action="{{ route('posts.storeComment', $post->slug) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div class="md:col-span-1">
                                            <input class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-xs py-2" 
                                                type="text" name="author_name" placeholder="{{ __('Name') }}" required />
                                        </div>
                                        <div class="md:col-span-2 flex flex-col gap-1.5">
                                            @if(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1')
                                                <div class="flex justify-end select-none">
                                                    <div class="flex items-center gap-1 bg-gray-950 border border-gray-800/80 rounded-full p-0.5">
                                                        <button type="button" class="lang-toggle-btn px-2 py-0.5 rounded-full text-[9px] font-bold border-0 cursor-pointer transition-all duration-150 bg-burnt-orange text-white" data-lang="en">En</button>
                                                        <button type="button" class="lang-toggle-btn px-2 py-0.5 rounded-full text-[9px] font-bold border-0 cursor-pointer transition-all duration-150 bg-transparent text-gray-400 hover:text-white" data-lang="ta">த</button>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="flex gap-2">
                                                <input class="comment-input-field block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-xs py-2" 
                                                    id="reply_content_{{ $comment->id }}" type="text" name="content" placeholder="{{ app()->getLocale() === 'ta' ? 'உங்கள் பதில்...' : 'Your reply...' }}" required />
                                                <button type="submit" class="px-4 py-2 bg-burnt-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition whitespace-nowrap border-0 cursor-pointer">
                                                    {{ app()->getLocale() === 'ta' ? 'அனுப்பு' : 'Send' }}
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>

                            <!-- Nested Replies -->
                            @if($comment->replies->isNotEmpty())
                                <div class="mt-4 space-y-3 border-l-2 border-gray-800 pl-4 md:pl-6 bg-gray-950/20 p-4 rounded-xl">
                                    @foreach($comment->replies as $reply)
                                        <div class="space-y-2.5">
                                            <div class="flex justify-between items-start">
                                                <span class="font-bold text-burnt-orange text-xs">{{ $reply->author_name }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-300 text-xs leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</p>
                                            
                                            <!-- Reply Reactions -->
                                            <div class="flex flex-wrap items-center gap-1.5 bg-gray-950/40 border border-gray-900/60 rounded-2xl px-3 py-1.5 w-fit">
                                                @foreach($primaryReactions as $react)
                                                    @php
                                                        $type = $react['type'];
                                                        $rReactions = $reply->reactions ?? collect();
                                                        $isActive = auth()->check() && $rReactions->where('user_id', auth()->id())->where('reaction_type', $type)->isNotEmpty();
                                                        $count = $rReactions->where('reaction_type', $type)->count();
                                                        $btnClass = $isActive 
                                                            ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange scale-100' 
                                                            : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                                                    @endphp
                                                    <button onclick="handleCommentReactionClick({{ $reply->id }}, '{{ $type }}')" 
                                                        id="btn-comment-reaction-{{ $reply->id }}-{{ $type }}"
                                                        class="relative group flex items-center gap-1.5 px-2 py-0.5 rounded-full border text-[9px] transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                                        data-tooltip="{{ $react['tooltip'] }}">
                                                        
                                                        @if($type === 'care')
                                                            <img src="/images/reactions/care.png" alt="Care" 
                                                                 class="w-3.5 h-3.5 object-contain transition group-hover:scale-110 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-comment-reaction-care-{{ $reply->id }}">
                                                        @else
                                                            <span class="text-[10px] transition-transform group-hover:scale-110 duration-200">{{ $react['emoji'] }}</span>
                                                        @endif
                                                        
                                                        <span class="font-bold text-gray-300" id="count-comment-{{ $reply->id }}-{{ $type }}">{{ $count }}</span>
                                                    </button>
                                                @endforeach

                                                {{-- Toggle Button for Reply Secondary Reactions --}}
                                                <button type="button" onclick="toggleSecondaryReactions(this, 'reply-{{ $reply->id }}')" 
                                                    class="flex items-center gap-1 px-1.5 py-0.5 rounded-full border border-gray-800 text-gray-400 hover:text-white hover:bg-gray-850 transition-all duration-200 cursor-pointer text-[9px] font-bold focus:outline-none">
                                                    <span>➕ {{ app()->getLocale() === 'ta' ? 'மேலும்' : 'More' }}</span>
                                                </button>

                                                @foreach($secondaryReactions as $react)
                                                    @php
                                                        $type = $react['type'];
                                                        $rReactions = $reply->reactions ?? collect();
                                                        $isActive = auth()->check() && $rReactions->where('user_id', auth()->id())->where('reaction_type', $type)->isNotEmpty();
                                                        $count = $rReactions->where('reaction_type', $type)->count();
                                                        $btnClass = $isActive 
                                                            ? 'bg-burnt-orange/20 border-burnt-orange/40 text-burnt-orange scale-100' 
                                                            : 'hover:bg-gray-850 text-gray-400 hover:text-white border-transparent';
                                                    @endphp
                                                    <button onclick="handleCommentReactionClick({{ $reply->id }}, '{{ $type }}')" 
                                                        id="btn-comment-reaction-{{ $reply->id }}-{{ $type }}"
                                                        class="secondary-reaction-reply-{{ $reply->id }} hidden relative group flex items-center gap-1.5 px-2 py-0.5 rounded-full border text-[9px] transition-all duration-200 focus:outline-none cursor-pointer {{ $btnClass }}"
                                                        data-tooltip="{{ $react['tooltip'] }}">
                                                        
                                                        @if($type === 'care')
                                                            <img src="/images/reactions/care.png" alt="Care" 
                                                                 class="w-3.5 h-3.5 object-contain transition group-hover:scale-110 duration-200 {{ $isActive ? '' : 'grayscale-[20%]' }}" id="img-comment-reaction-care-{{ $reply->id }}">
                                                        @else
                                                            <span class="text-[10px] transition-transform group-hover:scale-110 duration-200">{{ $react['emoji'] }}</span>
                                                        @endif
                                                        
                                                        <span class="font-bold text-gray-300" id="count-comment-{{ $reply->id }}-{{ $type }}">{{ $count }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="border-t border-gray-900/40 my-2"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-6 text-sm">{{ __('No comments yet. Be the first to comment!') }}</p>
                    @endif
                </div>

                <!-- Comment Form -->
                <form action="{{ route('posts.storeComment', $post->slug) }}" method="POST" class="space-y-4 border-t border-gray-800/60 pt-8">
                    @csrf
                    <div>
                        <label for="comment_author_name" class="block text-sm font-semibold text-gray-300 mb-1">{{ __('Name') }}</label>
                        <input id="comment_author_name" class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm" type="text" name="author_name" value="{{ old('author_name') }}" required />
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="comment_content" class="block text-sm font-semibold text-gray-300">{{ __('Your Comment') }}</label>
                            @if(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1')
                                <div class="flex items-center gap-1 bg-gray-950 border border-gray-800/80 rounded-full p-0.5 select-none">
                                    <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-burnt-orange text-white" data-lang="en">En</button>
                                    <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-transparent text-gray-400 hover:text-white" data-lang="ta">த</button>
                                </div>
                            @endif
                        </div>
                        <textarea id="comment_content" name="content" required
                            class="comment-input-field block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm"
                            rows="4">{{ old('content') }}</textarea>
                    </div>

                    <button type="submit" class="px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 rounded-full text-sm font-semibold transition transform hover:scale-105">
                        {{ __('Submit Comment') }}
                    </button>
                </form>
            </div>

            <!-- Actions -->
            <div class="mt-12 flex justify-between items-center border-t border-gray-800 pt-8">
                <a href="/" class="inline-flex items-center gap-2 text-burnt-orange hover:text-orange-400 font-semibold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Stories') }}
                </a>

                @auth
                    @if(auth()->id() === $post->author_id || auth()->user()->is_admin)
                        <div class="flex items-center gap-4">
                            <a href="{{ route('posts.edit', $post->id) }}" class="px-6 py-2 bg-gray-850 hover:bg-gray-800 border border-gray-700 rounded-full text-sm font-semibold text-gray-300 hover:text-white transition">
                                {{ __('Edit Post') }}
                            </a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-6 py-2 bg-red-950/40 hover:bg-red-900/40 border border-red-900 text-red-400 rounded-full text-sm font-semibold transition cursor-pointer">
                                    {{ __('Delete Post') }}
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            <script>
                const isAuthenticated = @json(auth()->check());

                function toggleSecondaryReactions(btn, groupId) {
                    const elements = document.querySelectorAll('.secondary-reaction-' + groupId);
                    elements.forEach(el => {
                        el.classList.remove('hidden');
                        el.classList.add('animate-fade-in-scale');
                    });
                    btn.classList.add('hidden');
                }

                function handleReactionClick(type) {
                    if (!isAuthenticated) {
                        openAuthModal();
                        return;
                    }
                    
                    const btn = document.getElementById('btn-reaction-' + type);
                    const countEl = document.getElementById('count-' + type);
                    if (!btn || !countEl) return;
                    
                    // Instant micro-animation scale feedback
                    btn.classList.add('scale-110');
                    setTimeout(() => btn.classList.remove('scale-110'), 200);

                    fetch('{{ route("posts.react", $post->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ type: type })
                    })
                    .then(response => {
                        if (response.status === 401) {
                            openAuthModal();
                            throw new Error('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            countEl.innerText = data.count;
                            
                            const isActive = data.user_reactions.includes(type);
                            const careImg = document.getElementById('img-reaction-care');
                            
                            if (isActive) {
                                btn.classList.add('bg-burnt-orange/20', 'border-burnt-orange/40', 'text-burnt-orange');
                                btn.classList.remove('hover:bg-gray-850', 'text-gray-400', 'hover:text-white', 'border-transparent');
                                if (type === 'care' && careImg) {
                                    careImg.classList.remove('grayscale-[20%]');
                                }
                            } else {
                                btn.classList.remove('bg-burnt-orange/20', 'border-burnt-orange/40', 'text-burnt-orange');
                                btn.classList.add('hover:bg-gray-850', 'text-gray-400', 'hover:text-white', 'border-transparent');
                                if (type === 'care' && careImg) {
                                    careImg.classList.add('grayscale-[20%]');
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error toggling reaction:', err);
                    });
                }

                function openAuthModal() {
                    const modal = document.getElementById('auth-modal');
                    const content = document.getElementById('auth-modal-content');
                    if (modal && content) {
                        modal.classList.remove('hidden');
                        // Force reflow
                        modal.offsetHeight;
                        content.classList.remove('scale-95', 'opacity-0');
                        content.classList.add('scale-100', 'opacity-100');
                        document.body.classList.add('overflow-hidden');
                    }
                }

                function closeAuthModal() {
                    const modal = document.getElementById('auth-modal');
                    const content = document.getElementById('auth-modal-content');
                    if (modal && content) {
                        content.classList.remove('scale-100', 'opacity-100');
                        content.classList.add('scale-95', 'opacity-0');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }, 300);
                    }
                }

                function trackShare() {
                    fetch('{{ route("posts.share", $post->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('shares-count-val').innerText = data.shares_count;
                        }
                    });
                }

                function copyPostLink() {
                    navigator.clipboard.writeText('{{ request()->fullUrl() }}').then(() => {
                        const btn = document.querySelector('button[onclick="copyPostLink()"]');
                        if (btn) {
                            const originalTooltip = btn.getAttribute('data-tooltip') || btn.getAttribute('title') || '{{ __("Copy Link") }}';
                            btn.setAttribute('data-tooltip', '{{ __("Link Copied!") }}');
                            btn.removeAttribute('title');
                            
                            if (window.KathaingoTooltip) {
                                window.KathaingoTooltip.show(btn, '{{ __("Link Copied!") }}');
                            }
                            
                            setTimeout(() => {
                                btn.setAttribute('data-tooltip', originalTooltip);
                                if (window.KathaingoTooltip && window.KathaingoTooltip.getCurrentTarget() === btn) {
                                    window.KathaingoTooltip.show(btn, originalTooltip);
                                }
                            }, 2000);
                        }
                        
                        trackShare();
                    });
                }

                function toggleReplyForm(id) {
                    const el = document.getElementById('reply-form-' + id);
                    if (el) {
                        el.classList.toggle('hidden');
                    }
                }

                function handleCommentReactionClick(commentId, type) {
                    if (!isAuthenticated) {
                        openAuthModal();
                        return;
                    }

                    const btn = document.getElementById('btn-comment-reaction-' + commentId + '-' + type);
                    const countEl = document.getElementById('count-comment-' + commentId + '-' + type);
                    if (!btn || !countEl) return;

                    // Instant micro-animation scale feedback
                    btn.classList.add('scale-110');
                    setTimeout(() => btn.classList.remove('scale-110'), 200);

                    let url = '{{ route("comments.react", ":comment") }}';
                    url = url.replace(':comment', commentId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ type: type })
                    })
                    .then(response => {
                        if (response.status === 401) {
                            openAuthModal();
                            throw new Error('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            countEl.innerText = data.count;
                            
                            const isActive = data.user_reactions.includes(type);
                            const careImg = document.getElementById('img-comment-reaction-care-' + commentId);
                            
                            if (isActive) {
                                btn.classList.add('bg-burnt-orange/20', 'border-burnt-orange/40', 'text-burnt-orange');
                                btn.classList.remove('hover:bg-gray-850', 'text-gray-400', 'hover:text-white', 'border-transparent');
                                if (type === 'care' && careImg) {
                                    careImg.classList.remove('grayscale-[20%]');
                                }
                            } else {
                                btn.classList.remove('bg-burnt-orange/20', 'border-burnt-orange/40', 'text-burnt-orange');
                                btn.classList.add('hover:bg-gray-850', 'text-gray-400', 'hover:text-white', 'border-transparent');
                                if (type === 'care' && careImg) {
                                    careImg.classList.add('grayscale-[20%]');
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error toggling comment reaction:', err);
                    });
                }

                // --- Telemetry-based Read Count Tracking ---
                (function() {
                    let hasRecordedRead = false;
                    let readTimer = null;

                    function triggerRead() {
                        if (hasRecordedRead) return;
                        hasRecordedRead = true;

                        // Clean up
                        window.removeEventListener('scroll', handleScroll);
                        if (readTimer) {
                            clearTimeout(readTimer);
                        }

                        fetch('{{ route("posts.read", $post->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const viewsVal = document.getElementById('views-count-val');
                                if (viewsVal) {
                                    viewsVal.innerText = data.views_count;
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Error recording read:', err);
                            hasRecordedRead = false;
                        });
                    }

                    function handleScroll() {
                        const article = document.querySelector('article');
                        if (!article) return;

                        const rect = article.getBoundingClientRect();
                        const scrollTop = window.scrollY || document.documentElement.scrollTop;
                        const articleTop = rect.top + scrollTop;
                        const articleHeight = rect.height;
                        const viewportBottom = scrollTop + window.innerHeight;

                        if (viewportBottom >= articleTop + (articleHeight * 0.5)) {
                            triggerRead();
                        }
                    }

                    readTimer = setTimeout(triggerRead, 25000);
                    window.addEventListener('scroll', handleScroll, { passive: true });
                    handleScroll();
                })();

                // --- Smart Language Helper (Real-time Gmail-style Transliteration) ---
                const isLanguageHelperEnabled = @json(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1');
                
                if (isLanguageHelperEnabled) {
                    // Global tracker for language helper input mode (defaults to 'en')
                    let currentInputMode = 'en';

                    // Safe localStorage wrappers to prevent SecurityError exceptions in Private/Incognito modes
                    function safeGetItem(key, defaultValue) {
                        try {
                            return localStorage.getItem(key) || defaultValue;
                        } catch (e) {
                            return defaultValue;
                        }
                    }

                    function safeSetItem(key, value) {
                        try {
                            localStorage.setItem(key, value);
                        } catch (e) {}
                    }

                    // --- Input Mode Toggle Sync & LocalStorage Setup ---
                    function syncLangToggles(mode) {
                        currentInputMode = mode;
                        safeSetItem('kathaingo_input_mode', mode);
                        document.querySelectorAll('.lang-toggle-btn').forEach(btn => {
                            const btnLang = btn.getAttribute('data-lang');
                            if (btnLang === mode) {
                                btn.classList.remove('bg-transparent', 'text-gray-400', 'hover:text-white');
                                btn.classList.add('bg-burnt-orange', 'text-white');
                            } else {
                                btn.classList.remove('bg-burnt-orange', 'text-white');
                                btn.classList.add('bg-transparent', 'text-gray-400', 'hover:text-white');
                            }
                        });
                    }

                    // Default to 'en' as requested by the user
                    currentInputMode = safeGetItem('kathaingo_input_mode', 'en');
                    setTimeout(() => syncLangToggles(currentInputMode), 0);

                    // Delegate click event for dynamic or static toggle buttons
                    document.addEventListener('click', function(e) {
                        const btn = e.target.closest('.lang-toggle-btn');
                        if (!btn) return;
                        e.preventDefault();
                        const mode = btn.getAttribute('data-lang');
                        syncLangToggles(mode);
                    });

                    // --- Selection Learning/Ranking Logic ---
                    function recordCandidateSelection(word, selectedCandidate) {
                        if (!word || !selectedCandidate) return;
                        let selections = {};
                        try {
                            selections = JSON.parse(safeGetItem('kathaingo_word_selections', '{}')) || {};
                        } catch (e) {}
                        
                        const wordKey = word.toLowerCase();
                        if (!selections[wordKey]) {
                            selections[wordKey] = {};
                        }
                        selections[wordKey][selectedCandidate] = (selections[wordKey][selectedCandidate] || 0) + 1;
                        safeSetItem('kathaingo_word_selections', JSON.stringify(selections));
                    }

                    function getPersonalRankedCandidates(word, candList) {
                        if (!candList || candList.length === 0) return candList;
                        let selections = {};
                        try {
                            selections = JSON.parse(safeGetItem('kathaingo_word_selections', '{}')) || {};
                        } catch (e) {}
                        
                        const wordKey = word.toLowerCase();
                        const wordHistory = selections[wordKey];
                        if (!wordHistory) return candList;
                        
                        // Sort based on selection counts in descending order, preserving stable index order
                        return [...candList].sort((a, b) => {
                            const countA = wordHistory[a] || 0;
                            const countB = wordHistory[b] || 0;
                            if (countB !== countA) {
                                return countB - countA;
                            }
                            return candList.indexOf(a) - candList.indexOf(b);
                        });
                    }

                    const localConsonants = {
                        'ng': { dot: 'ங்', base: 'ங்க' },
                        'nj': { dot: 'ஞ்', base: 'ஞ்ச' },
                        'ngny': { dot: 'ஞ்', base: 'ஞ்ஞ' },
                        'ngy': { dot: 'ஞ்', base: 'ஞ்ஞ' },
                        'gny': { dot: 'ஞ்', base: 'ஞ' },
                        'ny': { dot: 'ஞ்', base: 'ஞ' },
                        'gn': { dot: 'ஞ்', base: 'ஞ' },
                        'ndr': { dot: 'ன்ற்', base: 'ன்ற' },
                        'ndh': { dot: 'ந்த்', base: 'ந்த' },
                        'nd': { dot: 'ந்த்', base: 'ந்த' },
                        'th': { dot: 'த்', base: 'த' },
                        'zh': { dot: 'ழ்', base: 'ழ' },
                        'sh': { dot: 'ஷ்', base: 'ஷ' },
                        'ch': { dot: 'ச்', base: 'ச' },
                        'kh': { dot: 'க்', base: 'க' },
                        'ph': { dot: 'ஃப்', base: 'ஃப' },
                        'gh': { dot: 'க்', base: 'க' },
                        'lh': { dot: 'ள்', base: 'ள' },
                        'dh': { dot: 'த்', base: 'த' },
                        'k': { dot: 'க்', base: 'க' },
                        'g': { dot: 'க்', base: 'க' },
                        'c': { dot: 'ச்', base: 'ச' },
                        's': { dot: 'ச்', base: 'ச' },
                        'j': { dot: 'ஜ்', base: 'ஜ' },
                        't': { dot: 'ட்', base: 'ட' },
                        'd': { dot: 'ட்', base: 'ட' },
                        'n': { dot: 'ன்', base: 'ன' },
                        'p': { dot: 'ப்', base: 'ப' },
                        'b': { dot: 'ப்', base: 'ப' },
                        'f': { dot: 'ஃப்', base: 'ஃப' },
                        'm': { dot: 'ம்', base: 'ம' },
                        'y': { dot: 'ய்', base: 'ய' },
                        'r': { dot: 'ர்', base: 'ர' },
                        'l': { dot: 'ல்', base: 'ல' },
                        'v': { dot: 'வ்', base: 'வ' },
                        'w': { dot: 'வ்', base: 'வ' },
                        'h': { dot: 'ஹ்', base: 'ஹ' },
                        'z': { dot: 'ஜ்', base: 'ஜ' },
                        'q': { dot: 'ஃ', base: 'ஃ' }
                    };

                    const localVowels = {
                        'aa': { ind: 'ஆ', sign: 'ா' },
                        'ee': { ind: 'ஈ', sign: 'ீ' },
                        'ea': { ind: 'ஏ', sign: 'ே' },
                        'oo': { ind: 'ஊ', sign: 'ூ' },
                        'ae': { ind: 'ஏ', sign: 'ே' },
                        'ai': { ind: 'ஐ', sign: 'ை' },
                        'au': { ind: 'ஔ', sign: 'ௌ' },
                        'oa': { ind: 'ஓ', sign: 'ோ' },
                        'oh': { ind: 'ஓ', sign: 'ோ' },
                        'ou': { ind: 'ஔ', sign: 'ௌ' },
                        'ow': { ind: 'ஔ', sign: 'ௌ' },
                        'a': { ind: 'அ', sign: '' },
                        'i': { ind: 'இ', sign: 'ி' },
                        'u': { ind: 'உ', sign: 'ு' },
                        'e': { ind: 'எ', sign: 'ெ' },
                        'o': { ind: 'ஒ', sign: 'ொ' }
                    };

                    const localDict = {
                        'naan': 'நான்',
                        'nan': 'நான்',
                        'unga': 'உங்கள்',
                        'romba': 'ரொம்ப',
                        'nalla': 'நல்லா',
                        'ezhuthi': 'எழுதி',
                        'eluthi': 'எழுதி',
                        'enakku': 'எனக்கு',
                        'enaku': 'எனக்கு',
                        'pidichirukku': 'பிடிச்சிருக்கு',
                        'adei': 'அடேய்',
                        'manjakattu': 'மஞ்சக்காட்டு',
                        'manjakkattu': 'மஞ்சக்காட்டு',
                        'manjakkaattu': 'மஞ்சக்காட்டு',
                        'maina': 'மைனா',
                        'mainaa': 'மைனா',
                        'manina': 'மைனா',
                        'maninaa': 'மைனா',
                        'ennai': 'என்னை',
                        'ennaik': 'என்னைக்',
                        'konji': 'கொஞ்சி',
                        'konjik': 'கொஞ்சிக்',
                        'konjip': 'கொஞ்சிப்',
                        'pona': 'போன',
                        'ponaa': 'போனா',
                        'mustafa': 'முஸ்தஃபா',
                        'mustafaa': 'முஸ்தஃபா',
                        'mustafah': 'முஸ்தஃபா',
                        'musthafaa': 'முஸ்தஃபா',
                        'musthafaah': 'முஸ்தஃபா',
                        'dont': 'டோன்ட்',
                        'vory': 'வொரி',
                        'tholan': 'தோழன்',
                        'thozhan': 'தோழன்',
                        'moolgaatha': 'மூழ்காத',
                        'moolgatha': 'மூழ்காத',
                        'moozhgaatha': 'மூழ்காத',
                        'moozhgaadha': 'மூழ்காத',
                        'moolgaada': 'மூழ்காத',
                        'moolkaadha': 'மூழ்காத',
                        'moolkaatha': 'மூழ்காத',
                        'moozhgatha': 'மூழ்காத',
                        'moozhgadha': 'மூழ்காத',
                        'moozhgada': 'மூழ்காத',
                        'moolgada': 'மூழ்காத',
                        'friendshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                        'frendshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                        'frandshippaa': 'ஃப்ரண்ட்ஷிப்பா',
                        'friendship': 'ஃப்ரெண்ட்ஷிப்',
                        'frendship': 'ஃப்ரெண்ட்ஷிப்',
                        'frandship': 'ஃப்ரெண்ட்ஷிப்',
                        'kariveppila': 'கறிவேப்பில',
                        'kariveppilai': 'கறிவேப்பிலை',
                        'karivepila': 'கறிவேப்பில',
                        'karivepilai': 'கறிவேப்பிலை',
                        'veppila': 'வேப்பில',
                        'veppilai': 'வேப்பிலை',
                        'vepila': 'வேப்பில',
                        'vepilai': 'வேப்பிலை',
                        'pena': 'பேனா',
                        'penai': 'பேனா',
                        'paena': 'பேனா',
                        'paenai': 'பேனா',
                        'take': 'டேக்',
                        'tak': 'டேக்',
                        'it': 'இட்',
                        'nyayiru': 'ஞாயிறு',
                        'gnayiru': 'ஞாயிறு',
                        'nyaayiru': 'ஞாயிறு',
                        'gnaayiru': 'ஞாயிறு',
                        'gnyayiru': 'ஞாயிறு',
                        'gnyaayiru': 'ஞாயிறு',
                        'nyabagam': 'ஞாபகம்',
                        'gnabagam': 'ஞாபகம்',
                        'nyaabagam': 'ஞாபகம்',
                        'gnaabagam': 'ஞாபகம்',
                        'gnyabagam': 'ஞாபகம்',
                        'gnyaabagam': 'ஞாபகம்',
                        'nyanam': 'ஞானம்',
                        'gnanam': 'ஞானம்',
                        'nyaanam': 'ஞானம்',
                        'gnaanam': 'ஞானம்',
                        'gnyanam': 'ஞானம்',
                        'gnyaanam': 'ஞானம்',
                        'vingyaanam': 'விஞ்ஞானம்',
                        'vingyanam': 'விஞ்ஞானம்',
                        'vingnyaanam': 'விஞ்ஞானம்',
                        'vingnyanam': 'விஞ்ஞானம்',
                        'vignyaanam': 'விஞ்ஞானம்',
                        'vignanam': 'விஞ்ஞானம்',
                        'angyaanam': 'அஞ்ஞானம்',
                        'angyanam': 'அஞ்ஞானம்',
                        'angnyaanam': 'அஞ்ஞானம்',
                        'angnyanam': 'அஞ்ஞானம்',
                        'agnyaanam': 'அஞ்ஞானம்',
                        'agnanam': 'அஞ்ஞானம்'
                    };

                    function localTransliterate(word) {
                        if (!word) return '';
                        const lowercaseWord = word.toLowerCase();
                        if (localDict[lowercaseWord]) {
                            return localDict[lowercaseWord];
                        }
                        
                        const len = lowercaseWord.length;
                        let i = 0;
                        let output = '';
                        
                        const consonantsKeys = Object.keys(localConsonants).sort((a, b) => b.length - a.length);
                        const vowelsKeys = Object.keys(localVowels).sort((a, b) => b.length - a.length);
                        
                        while (i < len) {
                            let matchedConsonant = null;
                            let consonantLen = 0;
                            
                            for (const key of consonantsKeys) {
                                const kLen = key.length;
                                if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                                    matchedConsonant = localConsonants[key];
                                    consonantLen = kLen;
                                    break;
                                }
                            }
                            
                            if (matchedConsonant) {
                                i += consonantLen;
                                
                                let matchedVowel = null;
                                let vowelLen = 0;
                                
                                for (const key of vowelsKeys) {
                                    const kLen = key.length;
                                    if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                                        matchedVowel = localVowels[key];
                                        vowelLen = kLen;
                                        break;
                                    }
                                }
                                
                                if (matchedVowel) {
                                    i += vowelLen;
                                    let base;
                                    if (consonantLen === 1 && lowercaseWord[i - vowelLen - 1] === 'n' && (i - vowelLen - 1 === 0)) {
                                        base = 'ந';
                                    } else {
                                        base = matchedConsonant.base;
                                    }
                                    output += base + matchedVowel.sign;
                                } else {
                                    if (consonantLen === 1 && lowercaseWord[i - 1] === 'n' && (i - 1 === 0)) {
                                        output += 'ந்';
                                    } else {
                                        output += matchedConsonant.dot;
                                    }
                                }
                            } else {
                                let matchedVowel = null;
                                let vowelLen = 0;
                                
                                for (const key of vowelsKeys) {
                                    const kLen = key.length;
                                    if (i + kLen <= len && lowercaseWord.substring(i, i + kLen) === key) {
                                        matchedVowel = localVowels[key];
                                        vowelLen = kLen;
                                        break;
                                    }
                                }
                                
                                if (matchedVowel) {
                                    i += vowelLen;
                                    output += matchedVowel.ind;
                                } else {
                                    output += word[i];
                                    i++;
                                }
                            }
                        }
                        return output;
                    }

                    let activeInputEl = null;
                    let activeWord = '';
                    let activeWordStart = 0;
                    let activeWordEnd = 0;
                    let candidates = [];
                    let selectedIndex = -1;
                    let debounceTimer = null;
                    const suggestCache = {};
                    let mirrorDiv = null;


                    // Create global dropdown element
                    let dropdownEl = document.getElementById('lang-translit-dropdown');
                    if (!dropdownEl) {
                        dropdownEl = document.createElement('div');
                        dropdownEl.id = 'lang-translit-dropdown';
                        dropdownEl.className = 'absolute hidden bg-gray-950/95 border border-gray-800 rounded-2xl shadow-2xl p-1.5 w-56 text-xs font-semibold select-none flex flex-col z-[99999] backdrop-blur-md transition-all duration-150 transform scale-95 opacity-0';
                        document.body.appendChild(dropdownEl);
                    }

                    function getCaretCoordinates(element, position) {
                        try {
                            if (!mirrorDiv) {
                                mirrorDiv = document.createElement('div');
                                mirrorDiv.id = 'lang-translit-mirror';
                                mirrorDiv.style.position = 'absolute';
                                mirrorDiv.style.visibility = 'hidden';
                                mirrorDiv.style.left = '-9999px';
                                document.body.appendChild(mirrorDiv);
                            }
                            
                            const div = mirrorDiv;
                            div.innerHTML = '';
                            
                            const style = window.getComputedStyle(element);
                            const properties = [
                                'direction', 'boxSizing', 'width', 'height', 'overflowX', 'overflowY',
                                'borderWidth', 'borderStyle', 'borderColor',
                                'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
                                'fontFamily', 'fontSize', 'fontWeight', 'fontStyle', 'fontVariant', 'fontStretch',
                                'lineHeight', 'textTransform', 'wordBreak', 'wordWrap', 'whiteSpace',
                                'letterSpacing', 'textIndent', 'textRendering'
                            ];
                            
                            properties.forEach(prop => {
                                try {
                                    if (style[prop] !== undefined) {
                                        div.style[prop] = style[prop];
                                    }
                                } catch (e) {}
                            });
                            
                            div.style.position = 'absolute';
                            div.style.visibility = 'hidden';
                            div.style.left = '-9999px';
                            
                            const text = element.value.substring(0, position);
                            div.textContent = text;
                            
                            const span = document.createElement('span');
                            span.textContent = element.value.substring(position) || '.';
                            div.appendChild(span);
                            
                            const spanLeft = span.offsetLeft;
                            const spanTop = span.offsetTop;
                            
                            const rect = element.getBoundingClientRect();
                            
                            return {
                                top: rect.top + window.scrollY + spanTop - element.scrollTop,
                                left: rect.left + window.scrollX + spanLeft - element.scrollLeft
                            };
                        } catch (err) {
                            console.error('getCaretCoordinates failed, returning fallback rect coordinates:', err);
                            const rect = element.getBoundingClientRect();
                            return {
                                top: rect.bottom + window.scrollY,
                                left: rect.left + window.scrollX
                            };
                        }
                    }

                    function showDropdown(inputEl) {
                        if (!candidates || candidates.length === 0) {
                            hideDropdown();
                            return;
                        }

                        // Build candidates list HTML
                        dropdownEl.innerHTML = '';
                        
                        const listWrapper = document.createElement('div');
                        listWrapper.className = 'flex flex-col gap-0.5 max-h-[200px] overflow-y-auto';

                        candidates.forEach((cand, idx) => {
                            const optionEl = document.createElement('div');
                            optionEl.className = 'lang-candidate-option flex items-center justify-between px-3.5 py-2 hover:bg-burnt-orange hover:text-white rounded-xl cursor-pointer text-gray-300 transition-colors duration-150 font-bold';
                            if (idx === selectedIndex) {
                                optionEl.classList.add('bg-burnt-orange', 'text-white');
                            }

                            const textSpan = document.createElement('span');
                            textSpan.textContent = (idx + 1) + '. ' + cand;
                            optionEl.appendChild(textSpan);

                            // Sync selection index and highlight visually on mouseenter
                            optionEl.addEventListener('mouseenter', function() {
                                selectedIndex = idx;
                                dropdownEl.querySelectorAll('.lang-candidate-option').forEach((el, index) => {
                                    if (index === idx) {
                                        el.classList.add('bg-burnt-orange', 'text-white');
                                    } else {
                                        el.classList.remove('bg-burnt-orange', 'text-white');
                                    }
                                });
                            });

                            // Mousedown to prevent input blur and select option instantly
                            optionEl.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                selectCandidate(idx);
                            });

                            listWrapper.appendChild(optionEl);
                        });

                        dropdownEl.appendChild(listWrapper);

                        // Footer Arrow Buttons for touch screen convenience
                        const footerEl = document.createElement('div');
                        footerEl.className = 'flex justify-start gap-1 p-1 border-t border-gray-800/40 mt-1';
                        
                        const btnUp = document.createElement('button');
                        btnUp.type = 'button';
                        btnUp.className = 'p-1 hover:bg-gray-800 rounded-lg text-gray-400 cursor-pointer text-xs font-bold border-0 bg-transparent flex items-center justify-center';
                        btnUp.innerHTML = `
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                            </svg>
                        `;
                        btnUp.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            if (selectedIndex === -1) {
                                selectedIndex = candidates.length - 1;
                            } else {
                                selectedIndex = (selectedIndex - 1 + candidates.length) % candidates.length;
                            }
                            showDropdown(inputEl);
                        });

                        const btnDown = document.createElement('button');
                        btnDown.type = 'button';
                        btnDown.className = 'p-1 hover:bg-gray-800 rounded-lg text-gray-400 cursor-pointer text-xs font-bold border-0 bg-transparent flex items-center justify-center';
                        btnDown.innerHTML = `
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        `;
                        btnDown.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            if (selectedIndex === -1) {
                                selectedIndex = 0;
                            } else {
                                selectedIndex = (selectedIndex + 1) % candidates.length;
                            }
                            showDropdown(inputEl);
                        });


                        footerEl.appendChild(btnUp);
                        footerEl.appendChild(btnDown);
                        dropdownEl.appendChild(footerEl);

                        // Position dropdown
                        const coords = getCaretCoordinates(inputEl, activeWordEnd);
                        dropdownEl.classList.remove('hidden');
                        
                        // Force layout
                        dropdownEl.offsetHeight;

                        const dropdownHeight = dropdownEl.offsetHeight || 220;
                        const dropdownWidth = dropdownEl.offsetWidth || 224;
                        const viewportHeight = window.innerHeight;
                        const viewportWidth = window.innerWidth;

                        let top = coords.top + 20; // 20px below caret
                        if (top + dropdownHeight > viewportHeight + window.scrollY) {
                            top = coords.top - dropdownHeight - 5; // render above caret
                        }

                        let left = coords.left;
                        if (left + dropdownWidth > viewportWidth + window.scrollX) {
                            left = viewportWidth + window.scrollX - dropdownWidth - 15;
                        }
                        if (left < window.scrollX) {
                            left = window.scrollX + 10;
                        }

                        dropdownEl.style.top = top + 'px';
                        dropdownEl.style.left = left + 'px';

                        dropdownEl.classList.remove('opacity-0', 'scale-95');
                        dropdownEl.classList.add('opacity-100', 'scale-100');
                    }

                    function hideDropdown() {
                        dropdownEl.classList.remove('opacity-100', 'scale-100');
                        dropdownEl.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => {
                            if (dropdownEl.classList.contains('opacity-0')) {
                                dropdownEl.classList.add('hidden');
                            }
                        }, 150);
                    }

                    function selectCandidate(idx) {
                        if (!activeInputEl || !candidates[idx]) return;
                        
                        const replacement = candidates[idx];
                        recordCandidateSelection(activeWord, replacement);
                        
                        const text = activeInputEl.value;
                        const newText = text.substring(0, activeWordStart) + replacement + text.substring(activeWordEnd);
                        activeInputEl.value = newText;
                        
                        const nextCursorPos = activeWordStart + replacement.length;
                        activeInputEl.selectionStart = activeInputEl.selectionEnd = nextCursorPos;
                        
                        hideDropdown();
                        activeInputEl.dispatchEvent(new Event('input'));
                        activeInputEl.focus();
                    }

                    function checkActiveWord(inputEl) {
                        // Check if input mode is En (English) using in-memory tracker
                        if (currentInputMode !== 'ta') {
                            hideDropdown();
                            return;
                        }

                        activeInputEl = inputEl;
                        const cursor = inputEl.selectionStart;
                        const textBeforeCursor = inputEl.value.substring(0, cursor);
                        
                        // Match trailing english/letters word
                        const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                        
                        if (match) {
                            activeWord = match[1];
                            activeWordStart = cursor - activeWord.length;
                            activeWordEnd = cursor;
                            
                            // If the word contains Tamil characters (Unicode range \u0B80-\u0BFF), bypass completely
                            if (/[\u0B80-\u0BFF]/.test(activeWord)) {
                                hideDropdown();
                                return;
                            }

                            // Trigger debounced candidates fetch if word length >= 2
                            if (activeWord.length >= 2) {
                                // Show local candidates instantly!
                                const localCand = localTransliterate(activeWord);
                                if (localCand && localCand !== activeWord) {
                                    // Seed candidates with local option immediately
                                    candidates = [localCand, activeWord];
                                    selectedIndex = -1;
                                    showDropdown(inputEl);
                                }

                                // Check in-memory cache first for instant UX
                                const cached = suggestCache[activeWord];
                                if (cached) {
                                    if (debounceTimer) clearTimeout(debounceTimer);
                                    candidates = getPersonalRankedCandidates(activeWord, cached);
                                    selectedIndex = -1;
                                    showDropdown(inputEl);
                                    return;
                                }

                                if (debounceTimer) clearTimeout(debounceTimer);
                                debounceTimer = setTimeout(() => {
                                    const relativeUrl = '{{ route("api.language-helper.suggest", [], false) }}';
                                    fetch(relativeUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ word: activeWord })
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success && data.candidates && data.candidates.length > 0) {
                                            // Store in cache
                                            suggestCache[activeWord] = data.candidates;
                                            
                                            // Check if user hasn't moved cursor away since request started
                                            if (inputEl.selectionStart === activeWordEnd) {
                                                // Apply client-side learning/ranking
                                                candidates = getPersonalRankedCandidates(activeWord, data.candidates);
                                                selectedIndex = -1;
                                                showDropdown(inputEl);
                                            }
                                        } else {
                                            // Don't hide dropdown if we already show local candidates
                                            if (!candidates || candidates.length === 0) {
                                                hideDropdown();
                                            }
                                        }
                                    })
                                    .catch(err => {
                                        console.error('Dropdown fetch error:', err);
                                        if (!candidates || candidates.length === 0) {
                                            hideDropdown();
                                        }
                                    });
                                }, 150); // fast 150ms debounce
                            } else {
                                hideDropdown();
                            }
                        } else {
                            hideDropdown();
                        }
                    }

                    // Event Listeners for inputs
                    document.addEventListener('input', function(event) {
                        if (!event.target.classList.contains('comment-input-field')) return;
                        checkActiveWord(event.target);
                    });

                    document.addEventListener('click', function(event) {
                        // Close dropdown if clicking outside dropdown or comment boxes
                        if (dropdownEl && !dropdownEl.contains(event.target) && !event.target.classList.contains('comment-input-field')) {
                            hideDropdown();
                        }
                    });

                    document.addEventListener('keydown', function(event) {
                        if (!event.target.classList.contains('comment-input-field')) return;
                        
                        const inputEl = event.target;
                        const isDropdownOpen = !dropdownEl.classList.contains('hidden') && dropdownEl.classList.contains('opacity-100');

                        // Space key handler for Tamil input mode to support instant transliteration on spacebar
                        if (currentInputMode === 'ta' && event.key === ' ') {
                            const cursor = inputEl.selectionStart;
                            const textBeforeCursor = inputEl.value.substring(0, cursor);
                            const match = textBeforeCursor.match(/([a-zA-Z']+)$/);
                            if (match) {
                                const word = match[1];
                                if (word.length >= 2 && !/[\u0B80-\u0BFF]/.test(word)) {
                                    event.preventDefault();
                                    const wordStart = cursor - word.length;
                                    const wordEnd = cursor;
                                    
                                    // Use highlighted candidate if dropdown is open, otherwise local transliteration
                                    let replacement = '';
                                    if (isDropdownOpen && candidates && candidates.length > 0) {
                                        const idx = selectedIndex !== -1 ? selectedIndex : 0;
                                        replacement = candidates[idx] || localTransliterate(word);
                                    } else {
                                        replacement = localTransliterate(word);
                                    }
                                    
                                    recordCandidateSelection(word, replacement);
                                    
                                    const text = inputEl.value;
                                    const newText = text.substring(0, wordStart) + replacement + ' ' + text.substring(wordEnd);
                                    inputEl.value = newText;
                                    
                                    const nextCursorPos = wordStart + replacement.length + 1;
                                    inputEl.selectionStart = inputEl.selectionEnd = nextCursorPos;
                                    
                                    hideDropdown();
                                    inputEl.dispatchEvent(new Event('input'));
                                    inputEl.focus();
                                    return;
                                }
                            }
                        }

                        if (isDropdownOpen) {
                            // Arrow Down
                            if (event.key === 'ArrowDown') {
                                event.preventDefault();
                                if (selectedIndex === -1) {
                                    selectedIndex = 0;
                                } else {
                                    selectedIndex = (selectedIndex + 1) % candidates.length;
                                }
                                showDropdown(inputEl);
                            }
                            // Arrow Up
                            else if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                if (selectedIndex === -1) {
                                    selectedIndex = candidates.length - 1;
                                } else {
                                    selectedIndex = (selectedIndex - 1 + candidates.length) % candidates.length;
                                }
                                showDropdown(inputEl);
                            }
                            // Enter or Tab key
                            else if (event.key === 'Enter' || event.key === 'Tab') {
                                if (selectedIndex !== -1) {
                                    event.preventDefault();
                                    selectCandidate(selectedIndex);
                                } else {
                                    // Default to first candidate if nothing highlighted
                                    if (candidates && candidates[0]) {
                                        event.preventDefault();
                                        selectCandidate(0);
                                    } else {
                                        hideDropdown();
                                    }
                                }
                            }
                            // Escape key
                            else if (event.key === 'Escape') {
                                event.preventDefault();
                                hideDropdown();
                            }
                            // Numbers 1-6 keys
                            else if (event.key >= '1' && event.key <= '6') {
                                const idx = parseInt(event.key) - 1;
                                if (candidates[idx]) {
                                    event.preventDefault();
                                    selectCandidate(idx);
                                }
                            }
                        }
                    });
                }
            </script>
        </div>
    </main>

    <!-- Guest Authentication Modal -->
    <div id="auth-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Backdrop -->
        <div onclick="closeAuthModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-gray-900/95 border border-gray-800 rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl backdrop-blur-md transform transition-all duration-300 scale-95 opacity-0" id="auth-modal-content">
            <!-- Close Button -->
            <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <div class="text-center">
                <!-- Icon -->
                <div class="w-16 h-16 bg-burnt-orange/10 border border-burnt-orange/30 rounded-full flex items-center justify-center mx-auto mb-4 text-burnt-orange text-2xl">
                    🤗
                </div>
                
                <h3 class="text-xl font-bold text-white mb-2">
                    {{ app()->getLocale() === 'ta' ? 'உரையாடலில் இணையுங்கள்' : 'Join the conversation' }}
                </h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    {{ app()->getLocale() === 'ta' ? 'இந்த இடுகைக்கு எதிர்வினை புரிய அல்லது கருத்து தெரிவிக்க உள்நுழையவும் அல்லது பதிவு செய்யவும்.' : 'Please sign in or register to react or comment on this story.' }}
                </p>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full py-3 bg-burnt-orange hover:bg-orange-600 text-white font-bold rounded-xl transition text-center shadow-lg shadow-orange-600/20">
                        {{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}" class="w-full py-3 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-xl transition text-center border border-gray-700">
                        {{ __('Register') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
