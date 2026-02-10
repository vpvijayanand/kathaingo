<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'கதைங்கோ') }} - Inspiring Stories & Ideas</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            * { font-family: 'Inter', sans-serif; }
            .hero-gradient { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); }
            .text-gradient { background: linear-gradient(135deg, #f39c12 0%, #ff6b6b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
            .card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .card-hover:hover { transform: translateY(-12px); box-shadow: 0 25px 50px -12px rgba(243, 156, 18, 0.25); }
            .fade-in { animation: fadeIn 0.6s ease-in; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
            .backdrop-blur { backdrop-filter: blur(10px); }
            .category-badge { position: relative; overflow: hidden; }
            .category-badge::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; }
            .category-badge:hover::before { left: 100%; }
        </style>
    </head>
    <body class="antialiased bg-slate-gray text-white">
        <!-- Navigation -->
        <nav class="fixed w-full top-0 z-50 backdrop-blur bg-gray-900/90 border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <a href="/" class="text-3xl font-black tracking-tight">
                        <span class="text-gradient">கதைங்கோ</span>
                    </a>
                    
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="/" class="text-sm font-semibold text-white hover:text-burnt-orange transition">Home</a>
                        @foreach($categories as $category)
                            <div class="relative group">
                                <button class="text-sm font-semibold text-gray-300 hover:text-white transition flex items-center gap-1">
                                    {{ $category->name }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                @if($category->subcategories->count() > 0)
                                    <div class="absolute left-0 mt-3 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2">
                                        <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden">
                                            @foreach($category->subcategories as $subcategory)
                                                <a href="#" class="block px-5 py-3 text-sm text-gray-300 hover:bg-burnt-orange hover:text-white transition-all">
                                                    {{ $subcategory->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-300 hover:text-white transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition">Sign in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 rounded-full text-sm font-semibold transition transform hover:scale-105">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-gradient pt-32 pb-20 px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-burnt-orange rounded-full filter blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600 rounded-full filter blur-3xl"></div>
            </div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="max-w-4xl">
                    <h1 class="text-6xl lg:text-8xl font-black mb-8 leading-tight">
                        Stories that
                        <span class="text-gradient block">inspire change</span>
                    </h1>
                    <p class="text-xl lg:text-2xl text-gray-300 mb-12 max-w-2xl leading-relaxed">
                        Discover insights across technology, lifestyle, business, and creativity. Join thousands of readers exploring what matters.
                    </p>
                    @if (Route::has('register') && !Auth::check())
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                            Start Reading
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- Featured Post -->
        @if($posts->first())
            <section class="py-20 px-6 lg:px-8 bg-gray-900">
                <div class="max-w-7xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="order-2 lg:order-1">
                            @if($posts->first()->category)
                                <span class="category-badge inline-block px-4 py-1.5 bg-burnt-orange/20 text-burnt-orange text-xs font-bold uppercase tracking-wider rounded-full mb-6">
                                    Featured • {{ $posts->first()->category->name }}
                                </span>
                            @endif
                            <h2 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                                {{ $posts->first()->title }}
                            </h2>
                            <p class="text-lg text-gray-400 mb-8 leading-relaxed">
                                {{ Str::limit($posts->first()->content, 200) }}
                            </p>
                            <div class="flex items-center gap-6">
                                <a href="#" class="inline-flex items-center gap-2 text-burnt-orange hover:text-orange-400 font-semibold transition">
                                    Read Full Story
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                                <span class="text-gray-500 text-sm">{{ $posts->first()->published_at?->format('M d, Y') ?? 'Recently' }}</span>
                            </div>
                        </div>
                        <div class="order-1 lg:order-2">
                            @if($posts->first()->image)
                                <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
                                    <img src="{{ $posts->first()->image }}" alt="{{ $posts->first()->title }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-700">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Latest Stories Grid -->
        <section class="py-20 px-6 lg:px-8 bg-slate-gray">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-16">
                    <div>
                        <h2 class="text-5xl font-black mb-3">Latest Stories</h2>
                        <p class="text-gray-400 text-lg">Fresh perspectives and insights</p>
                    </div>
                </div>

                @if($posts->count() > 1)
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($posts->skip(1) as $post)
                            <article class="card-hover group">
                                @if($post->image)
                                    <div class="aspect-[16/10] rounded-xl overflow-hidden mb-6 bg-gray-800">
                                        <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                @endif
                                
                                <div class="space-y-4">
                                    @if($post->category)
                                        <span class="inline-block px-3 py-1 bg-gray-800 text-burnt-orange text-xs font-bold uppercase tracking-wider rounded-full">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    
                                    <h3 class="text-2xl font-bold leading-tight group-hover:text-burnt-orange transition">
                                        <a href="#">{{ $post->title }}</a>
                                    </h3>
                                    
                                    <p class="text-gray-400 line-clamp-3">
                                        {{ Str::limit($post->content, 140) }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                                        <span class="text-sm text-gray-500">{{ $post->published_at?->format('M d, Y') ?? 'Recently' }}</span>
                                        <a href="#" class="text-sm font-semibold text-burnt-orange hover:text-orange-400 transition flex items-center gap-1">
                                            Read
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 border-t border-gray-800 py-16 px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
                    <div class="lg:col-span-2">
                        <h3 class="text-3xl font-black mb-4">
                            <span class="text-gradient">கதைங்கோ</span>
                        </h3>
                        <p class="text-gray-400 max-w-sm">
                            A creative space for sharing knowledge and ideas across diverse topics. Join our community of readers and contributors.
                        </p>
                    </div>
                    @foreach($categories->take(3) as $category)
                        <div>
                            <h4 class="font-bold mb-4 text-white">{{ $category->name }}</h4>
                            <ul class="space-y-2">
                                @foreach($category->subcategories->take(4) as $subcategory)
                                    <li>
                                        <a href="#" class="text-gray-400 hover:text-burnt-orange text-sm transition">
                                            {{ $subcategory->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
                
                <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                    
                    <!-- Social Media Links -->
                    <div class="flex items-center gap-4">
                        <span class="text-gray-500 text-sm mr-2">Follow us:</span>
                        <a href="https://www.youtube.com/@your-channel" target="_blank" rel="noopener" class="text-gray-400 hover:text-red-500 transition" title="YouTube">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/your-page" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-500 transition" title="Facebook">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://twitter.com/your-handle" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition" title="Twitter/X">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/company/your-company" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-400 transition" title="LinkedIn">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/your-account" target="_blank" rel="noopener" class="text-gray-400 hover:text-pink-500 transition" title="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-white transition">Privacy</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">Terms</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
