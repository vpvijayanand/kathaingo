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

            /* Magical Treasure Lamp Container styling (no card borders/backgrounds) */
            .treasure-lamp-container {
                position: relative;
                display: inline-block;
                transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
                cursor: pointer;
            }

            .treasure-lamp-container:hover {
                transform: translateY(-3px);
            }

            /* Ambient Glow overlay under the lamp */
            .lamp-shadow-glow {
                position: absolute;
                bottom: 8px;
                left: 50%;
                transform: translateX(-50%) scale(1);
                width: 150px;
                height: 15px;
                background: radial-gradient(ellipse at center, rgba(242, 140, 40, 0.45) 0%, transparent 75%);
                filter: blur(6px);
                opacity: 0.5;
                animation: glow-pulse 4s ease-in-out infinite;
                z-index: 1;
                pointer-events: none;
            }

            @keyframes glow-pulse {
                0%, 100% { opacity: 0.4; transform: translateX(-50%) scale(1); }
                50% { opacity: 0.65; transform: translateX(-50%) scale(1.15); }
            }

            /* Lamp Image styling */
            .lamp-image {
                filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.65));
                z-index: 2;
                position: relative;
                transition: filter 0.4s ease;
            }

            /* Hover states */
            .treasure-lamp-container:hover .lamp-image {
                filter: drop-shadow(0 12px 20px rgba(242, 140, 40, 0.2)) drop-shadow(0 10px 15px rgba(0, 0, 0, 0.5));
            }

            .treasure-lamp-container:hover .lamp-shadow-glow {
                opacity: 0.85;
                transform: translateX(-50%) scale(1.35);
                background: radial-gradient(ellipse at center, rgba(242, 140, 40, 0.75) 0%, transparent 70%);
                animation: none; /* Pause pulsing on hover for constant warm glow */
            }

            /* Smoke strands layout and animations */
            .lamp-smoke-overlay {
                position: absolute;
                top: 48.9%;
                left: 83.3%;
                width: 120px;
                height: 180px;
                transform: translate(-90px, -100%); /* Position so smoke flows correctly */
                pointer-events: none;
                z-index: 10;
                overflow: visible;
            }

            .smoke-svg {
                width: 100%;
                height: 100%;
                overflow: visible;
            }

            .smoke-strand {
                stroke-dasharray: 300;
                stroke-dashoffset: 300;
                filter: blur(7px);
                transform-origin: 50% 100%;
                opacity: 0;
                transition: filter 0.4s ease;
            }

            .strand-1 {
                animation: smoke-rise 5s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
            }

            .strand-2 {
                animation: smoke-rise 6s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
                animation-delay: 1s;
            }

            .strand-3 {
                animation: smoke-rise 7s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
                animation-delay: 2s;
            }

            .strand-4 {
                animation: smoke-rise 5.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
                animation-delay: 0.5s;
            }

            .strand-5 {
                animation: smoke-rise 6.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
                animation-delay: 1.7s;
            }

            @keyframes smoke-rise {
                0%   { stroke-dashoffset: 300; opacity: 0;    transform: translateY(0px) scaleX(0.8); }
                8%   { opacity: 0.6; }
                40%  { opacity: 0.85; stroke-dashoffset: 150; transform: translateY(-18px) scaleX(1.2); }
                70%  { opacity: 0.5; transform: translateY(-32px) scaleX(1.6); }
                90%  { opacity: 0.15; }
                100% { stroke-dashoffset: 0;   opacity: 0;    transform: translateY(-50px) scaleX(2.0); }
            }

            .treasure-lamp-container:hover .smoke-strand {
                filter: blur(6px);
                opacity: 0.9;
            }

            /* Active Transition Classes */
            .treasure-lamp-container.magical-activating {
                pointer-events: none;
            }

            .treasure-lamp-container.magical-activating .lamp-image {
                filter: brightness(2) drop-shadow(0 0 45px rgba(242, 140, 40, 0.9));
                transform: scale(1.05);
                transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            }

            .treasure-lamp-container.magical-activating .lamp-shadow-glow {
                opacity: 1;
                transform: translateX(-50%) scale(2.2);
                background: radial-gradient(ellipse at center, rgba(242, 140, 40, 0.95) 0%, transparent 70%);
                transition: all 0.4s ease;
            }

            .treasure-lamp-container.magical-activating .smoke-strand {
                stroke-width: 6;
                filter: blur(1.5px);
                opacity: 0;
                transform: scale(1.6) translate(-25px, -35px);
                transition: all 0.9s cubic-bezier(0.25, 1, 0.5, 1);
            }

            /* Glitter Animation */
            .jewel-overlay {
                position: absolute;
                width: 6px;
                height: 6px;
                background-color: white;
                border-radius: 50%;
                box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.9), 0 0 15px 4px rgba(242, 140, 40, 0.6);
                opacity: 0;
                animation: twinkle 2.5s infinite ease-in-out;
                animation-delay: var(--glitter-delay, 0s);
                pointer-events: none;
                z-index: 15;
            }
            .jewel-overlay::after {
                content: '';
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%) rotate(45deg);
                width: 18px; height: 1.5px;
                background: white;
                border-radius: 50%;
                box-shadow: 0 0 5px rgba(255,255,255,0.8);
            }
            .jewel-overlay::before {
                content: '';
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                width: 18px; height: 1.5px;
                background: white;
                border-radius: 50%;
                box-shadow: 0 0 5px rgba(255,255,255,0.8);
            }
            @keyframes twinkle {
                0%, 100% { opacity: 0; transform: scale(0.5) rotate(0deg); }
                50% { opacity: 1; transform: scale(1.2) rotate(45deg); box-shadow: 0 0 12px 4px rgba(255, 255, 255, 1), 0 0 25px 8px rgba(242, 140, 40, 0.9); }
            }
        </style>
    </x-slot>

    <!-- SECTION 1 – HERO BANNER -->
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
                
                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 mt-8">
                    <a href="{{ route('stories.index') }}"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                        {{ __('வாசிங்கோ') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </a>
                    <a href="{{ auth()->check() ? route('posts.create') : route('register') }}"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-gray-700/40 hover:bg-gray-950/80 border border-gray-700 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl text-white">
                        {{ __('எழுதுங்கோ') }}
                        <svg class="w-5 h-5 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2 – WHAT IS கதைங்கோ? -->
    <section id="about-kathaingo" class="py-24 px-6 lg:px-8 bg-gray-950 border-b border-gray-900" style="scroll-margin-top: 180px;">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white">
                    <span class="text-gradient">{{ __('கதைங்கோ?') }}</span>
                </h2>
                <div class="w-24 h-1 bg-burnt-orange mx-auto rounded-full"></div>
            </div>
            
            <div class="bg-gray-900/50 border border-gray-800/80 rounded-2xl p-8 lg:p-12 shadow-2xl backdrop-blur-sm">
                <div class="text-gray-350 space-y-6 text-base lg:text-lg leading-loose text-justify tracking-wide">
                    <p class="first-letter:text-5xl first-letter:font-black first-letter:text-burnt-orange first-letter:mr-3 first-letter:float-left">
                        <strong>கதைங்கோ</strong> – கதைகளின் பெருவெளி. ஊர்க்குருவி ஒன்று காற்றில் மிதந்து வந்து கதைகளைச் சேகரிக்கிறது. அது கேட்ட கதைகள், கண்ட காட்சிகள், உணர்ந்த உணர்வுகள் யாவும் அதன் நெஞ்சில் தங்கிவிடுகின்றன.
                    </p>
                    <p>
                        அந்தக் கதைகளை அது ஜீரணித்து, பக்குவமாகச் சமைத்து, பின் ஒரு விதையாக இந்த மண்ணில் தூவுகிறது. அந்த விதைகள் முளைத்து இன்று ஒரு பெரிய சோலையாக, "கதைங்கோ" என்ற இந்த இணையத் தளமாக உருவெடுத்துள்ளது.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3 – LATEST ARTICLES -->
    <section class="py-24 px-6 lg:px-8 bg-slate-gray border-b border-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white flex items-center justify-center gap-3">
                    <svg class="w-8 h-8 lg:w-10 h-10 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Paper Sheet -->
                        <path d="M4 3c0-1.1.9-2 2-2h8l6 6v14c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V3z" fill="#FFFFFF" stroke="#475569" stroke-width="2" stroke-linejoin="round"/>
                        <!-- Folded corner for paper -->
                        <path d="M14 1v6h6" stroke="#475569" stroke-width="2" stroke-linejoin="round" fill="#F1F5F9"/>
                        <!-- Lines on paper -->
                        <line x1="7" y1="11" x2="13" y2="11" stroke="#94A3B8" stroke-width="2" stroke-linecap="round"/>
                        <line x1="7" y1="15" x2="15" y2="15" stroke="#94A3B8" stroke-width="2" stroke-linecap="round"/>
                        <line x1="7" y1="19" x2="11" y2="19" stroke="#94A3B8" stroke-width="2" stroke-linecap="round"/>
                        <!-- Blue Pen / Pencil overlapping -->
                        <g transform="translate(1, -1)">
                            <!-- Pen body -->
                            <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L13 14.5l-4 1 1-4 8.5-8.5z" fill="#2563EB" stroke="#1D4ED8" stroke-width="1.5" stroke-linejoin="round"/>
                            <!-- Tip detail -->
                            <path d="M9 15.5l2-2" stroke="#1D4ED8" stroke-width="1.5"/>
                            <path d="M17 4l3 3" stroke="#FFFFFF" stroke-width="1.2"/>
                        </g>
                    </svg>
                    <span class="text-gradient">{{ __('Latest Articles') }}</span>
                </h2>
                <div class="w-32 h-1 bg-burnt-orange mx-auto rounded-full mb-4"></div>
                <p class="stylish-desc text-sm lg:text-base max-w-2xl mx-auto mt-2 leading-relaxed">
                    {{ __('Fresh perspectives and insights from our community') }}
                </p>
            </div>

            @if($latestPosts->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($latestPosts as $post)
                        <article class="card-hover group bg-gray-900/40 border border-gray-800/60 rounded-xl p-4 flex flex-col justify-between h-full">
                            <div>
                                @php
                                    $featuredImg = $post->image ?: ($post->featured_image ? asset('storage/' . $post->featured_image) : null);
                                @endphp
                                @if($featuredImg)
                                    <div class="aspect-[16/9] rounded-lg overflow-hidden mb-4 bg-gray-800 relative">
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
                                    <div class="aspect-[16/9] rounded-lg overflow-hidden mb-4 bg-gray-855 border border-gray-800/80 flex items-center justify-center text-gray-500 text-sm relative">
                                        <img src="{{ asset('images/logo/logo-header.png') }}" alt="Kathaingo" class="w-1/2 h-auto opacity-30 grayscale object-contain drop-shadow-md">
                                        @if($post->category)
                                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-burnt-orange/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-md backdrop-blur-sm shadow-md">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="space-y-3">
                                    <h3 class="text-lg font-bold leading-snug group-hover:text-burnt-orange transition line-clamp-2">
                                        <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>

                                    <p class="text-gray-400 text-sm leading-relaxed line-clamp-3">
                                        {{ Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer & Engagement -->
                            <div class="pt-3 mt-4 border-t border-gray-800/80 flex flex-col gap-3">
                                <!-- Engagement Indicators -->
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    @php
                                        $commentsCount = $post->comments_count ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="flex items-center gap-1 bg-gray-950/40 border border-gray-800/50 rounded-full px-2.5 py-0.5" title="{{ __('Comments') }}">
                                            <span>💬</span>
                                            <span class="font-bold text-gray-300 text-xs">{{ $commentsCount }}</span>
                                        </span>
                                    </div>
                                    
                                    @if($post->tags->isNotEmpty())
                                        <div class="text-xs text-blue-400 font-bold tracking-wide flex gap-1.5 flex-wrap">
                                            @foreach($post->tags->take(3) as $tag)
                                                <a href="{{ url('/stories?tag=' . $tag->slug) }}" class="hover:text-blue-300 transition">
                                                    #{{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @elseif($post->hashtags)
                                        <span class="text-xs text-blue-400 font-bold tracking-wide">
                                            {{ $post->hashtags }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                                            {{ $post->category?->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Author & Date -->
                                <div class="flex items-center justify-between pt-0.5">
                                    <div class="flex items-center gap-2">
                                        @if($post->authorSubcategory)
                                            <a href="{{ route('authors.show', $post->authorSubcategory->slug) }}" class="flex items-center gap-2 text-xs text-gray-300 hover:text-burnt-orange font-bold transition">
                                                @if($post->authorSubcategory->getAvatarUrl())
                                                    <img src="{{ $post->authorSubcategory->getAvatarUrl() }}" alt="" class="w-5 h-5 rounded-full object-cover border border-burnt-orange/30">
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-burnt-orange/10 border border-burnt-orange/30 flex items-center justify-center text-[8px] font-black text-burnt-orange">
                                                        {{ mb_substr($post->authorSubcategory->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                @endif
                                                <span>{{ $post->authorSubcategory->name }}</span>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <span class="text-gray-500 text-xs font-semibold">
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
                
                <div class="mt-16 text-center">
                    <a href="{{ route('stories.index') }}"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                        {{ __('அனைத்துப் பதிவுகளையும் பார்க்க') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 py-12">{{ __('No stories found.') }}</p>
            @endif
        </div>
    </section>

    <!-- SECTION 4 – EXPLORE BY CATEGORY -->
    <section class="py-24 px-6 lg:px-8 bg-gray-950 border-b border-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white flex items-center justify-center gap-3">
                    <span class="shrink-0 text-3xl lg:text-4xl">🏺</span>
                    <span class="text-gradient">{{ __('Explore by Category') }}</span>
                </h2>
                <div class="w-32 h-1 bg-burnt-orange mx-auto rounded-full mb-4"></div>
                <p class="stylish-desc text-sm lg:text-base max-w-2xl mx-auto mt-2 leading-relaxed">
                    {{ __('Find stories by your favorite topics') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($exploreCategories as $exploreCat)
                    <a href="{{ $exploreCat['url'] }}"
                        class="group relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800/80 hover:border-burnt-orange/50 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-burnt-orange/10 flex flex-col justify-between min-h-[140px]">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-burnt-orange/5 rounded-full blur-xl group-hover:bg-burnt-orange/10 transition-all"></div>
                        
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest block mb-2">
                                {{ $exploreCat['type'] === 'subcategory' ? __('Category') : __('Topic') }}
                            </span>
                            <h3 class="text-lg lg:text-xl font-bold group-hover:text-burnt-orange transition-colors">
                                {{ $exploreCat['name'] }}
                            </h3>
                            @if(isset($exploreCat['name_en']) && $exploreCat['name_en'] !== $exploreCat['name'])
                                <span class="text-xs text-gray-500 block mt-1 font-medium">{{ $exploreCat['name_en'] }}</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-end mt-4">
                            <span class="text-xs bg-burnt-orange/10 text-burnt-orange px-2.5 py-1 rounded-md font-bold">
                                {{ $exploreCat['posts_count'] }} {{ $exploreCat['posts_count'] === 1 ? __('Story') : __('Stories') }}
                            </span>
                            <span class="text-burnt-orange opacity-0 group-hover:opacity-100 transition-opacity font-bold text-lg">→</span>
                        </div>
                    </a>
                @endforeach


            </div>

            <!-- Centered Kathaingo's Magical Treasure Lamp -->
            <div class="flex justify-center mt-14">
                <a href="{{ route('about') }}#kathaingos-universe" id="treasure-lamp-link"
                    class="treasure-lamp-container group block relative overflow-visible" onclick="triggerMagicalTransition(event, this)">
                    
                    <!-- Glowing shadow under the lamp -->
                    <div class="lamp-shadow-glow"></div>
                    
                    <!-- Transparent Lamp Image -->
                    <img src="{{ asset('images/treasure-lamp.png') }}" alt="Kathaingo's Magical Treasure Lamp" 
                        class="lamp-image w-[360px] md:w-[420px] h-auto object-contain select-none pointer-events-none transition-all duration-500 ease-out" />
                    
                    <!-- Smoke Overlay -->
                    <div class="lamp-smoke-overlay" style="top: auto; bottom: 40%; left: 68%; transform: translateX(-50%) scale(2.2); transform-origin: bottom center;">
                        <svg class="smoke-svg" viewBox="0 0 100 150" fill="none" xmlns="http://www.w3.org/2000/svg" style="overflow: visible;">
                            <defs>
                                <linearGradient id="magic-smoke-grad" x1="50%" y1="100%" x2="50%" y2="0%">
                                    <stop offset="0%" stop-color="#FFE699" stop-opacity="0.95" />
                                    <stop offset="35%" stop-color="#FDBA74" stop-opacity="0.85" />
                                    <stop offset="70%" stop-color="#C084FC" stop-opacity="0.5" />
                                    <stop offset="100%" stop-color="#818CF8" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path class="smoke-strand strand-1" d="M90,140 C60,110 30,90 10,60 C-10,35 -20,15 -30,-10" stroke="url(#magic-smoke-grad)" stroke-width="4.5" stroke-linecap="round" />
                            <path class="smoke-strand strand-2" d="M90,140 C75,115 50,90 40,60 C30,35 25,15 20,-10" stroke="url(#magic-smoke-grad)" stroke-width="3.5" stroke-linecap="round" />
                            <path class="smoke-strand strand-3" d="M90,140 C85,110 80,85 75,55 C70,30 65,15 60,-10" stroke="url(#magic-smoke-grad)" stroke-width="2.8" stroke-linecap="round" />
                            <path class="smoke-strand strand-4" d="M90,140 C100,115 110,90 120,60 C130,35 140,15 150,-10" stroke="url(#magic-smoke-grad)" stroke-width="3.8" stroke-linecap="round" />
                            <path class="smoke-strand strand-5" d="M90,140 C110,110 130,90 150,60 C170,35 190,15 210,-10" stroke="url(#magic-smoke-grad)" stroke-width="4.2" stroke-linecap="round" />
                        </svg>
                    </div>
                    

                    <!-- Jewel Glows -->
                    <div class="jewel-overlay" style="top: 44%; left: 45%;"></div>
                    <div class="jewel-overlay" style="top: 55%; left: 55%;"></div>
                    <div class="jewel-overlay" style="top: 31%; left: 52%;"></div>
                    <div class="jewel-overlay" style="top: 63%; left: 47%;"></div>
                    <div class="jewel-overlay" style="top: 50%; left: 64%;"></div>
                    <div class="jewel-overlay" style="top: 73%; left: 38%;"></div>
                    
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION 5 – FEATURED WRITERS -->
    <section class="py-24 px-6 lg:px-8 bg-slate-gray border-b border-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="section-title text-4xl lg:text-5xl font-black mb-3 text-white flex items-center justify-center gap-3">
                    <span class="shrink-0 text-3xl lg:text-4xl">⭐</span>
                    <span class="text-gradient">{{ __('Featured Writers') }}</span>
                </h2>
                <div class="w-32 h-1 bg-burnt-orange mx-auto rounded-full mb-4"></div>
                <p class="stylish-desc text-sm lg:text-base max-w-2xl mx-auto mt-2 leading-relaxed">
                    {{ __('Meet the voices shaping our stories') }}
                </p>
            </div>

            @if($featuredWriters->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-8">
                    @foreach($featuredWriters as $writer)
                        <a href="{{ route('authors.show', $writer->slug) }}"
                            class="group flex flex-col items-center text-center p-6 bg-gray-900/40 border border-gray-800/60 rounded-2xl transition duration-300 hover:border-burnt-orange/50 hover:bg-gray-900/80 hover:-translate-y-1">
                            
                            <div class="relative mb-4">
                                @if($writer->getAvatarUrl())
                                    <img src="{{ $writer->getAvatarUrl() }}" alt="{{ $writer->name }}"
                                        class="w-24 h-24 rounded-full object-cover border-2 border-gray-800 group-hover:border-burnt-orange transition duration-300 shadow-lg">
                                @else
                                    <div class="w-24 h-24 rounded-full bg-burnt-orange/10 border-2 border-gray-850 flex items-center justify-center text-3xl font-black text-burnt-orange group-hover:border-burnt-orange transition duration-300 shadow-lg">
                                        {{ mb_substr($writer->name, 0, 1, 'UTF-8') }}
                                    </div>
                                @endif
                                <div class="absolute inset-0 rounded-full bg-burnt-orange/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <h3 class="text-base font-bold text-white group-hover:text-burnt-orange transition duration-300">
                                {{ $writer->name }}
                            </h3>
                            <span class="text-xs text-gray-400 mt-1">
                                {{ $writer->authored_posts_count ?? $writer->authoredPosts()->where('status', 'published')->count() }} {{ __('Stories') }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-16 text-center">
                    <a href="{{ route('about') }}#core-categories-featured-bloggers"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-gray-900 hover:bg-gray-850 border border-gray-700 hover:border-burnt-orange text-burnt-orange rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                        {{ __('எழுத்தாளர்கள் பட்டியல்') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 py-12">{{ __('No writers found.') }}</p>
            @endif
        </div>
    </section>

    <!-- SECTION 6 – JOIN THE COMMUNITY -->
    <section class="py-24 px-6 lg:px-8 bg-gradient-to-br from-gray-900 via-gray-950 to-gray-900 border-b border-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-5 pointer-events-none"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h2 class="text-5xl lg:text-6xl font-black mb-6 leading-relaxed text-white">
                <span class="text-gradient">{{ __('உங்கள் வரவு நல்வரவாகுக!!!') }}</span>
            </h2>
            <p class="font-extrabold text-xl lg:text-2xl mb-10 max-w-4xl mx-auto leading-relaxed" style="background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.95)); letter-spacing: 0.025em;">
                {{ __('உங்கள் கதைகளுக்காகவும் கருத்துக்களுக்காகவும் கதைங்கோ உங்களைத் தன் இருகரம் நீட்டி வரவேற்கிறது. துள்ளி வருக! எழுதித் தள்ளுக!') }}
            </p>
            <a href="{{ auth()->check() ? route('posts.create') : route('register') }}"
                class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                {{ __('பதிவராக இணையுங்கள்') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
    </section>

    <script>
        function triggerMagicalTransition(event, element) {
            event.preventDefault();
            element.classList.add('magical-activating');
            const targetUrl = element.getAttribute('href');
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 1000);
        }

        // Assign random glitter delays to each gemstone for a natural twinkle
        document.addEventListener('DOMContentLoaded', () => {
            const gems = document.querySelectorAll('.jewel-overlay');
            gems.forEach(gem => {
                const delay = (Math.random() * 2).toFixed(2) + 's';
                gem.style.setProperty('--glitter-delay', delay);
            });
        });    </script>

</x-public-layout>