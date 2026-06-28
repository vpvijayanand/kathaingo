<!-- Navigation -->
<nav x-data="{ mobileMenuOpen: false }" class="fixed w-full top-0 z-50 bg-slate-gray/90 backdrop-blur-md border-b border-slate-800/60 shadow-lg">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center py-0.5">
                    <img src="{{ asset('images/logo/logo-header.png') }}" alt="கதைங்கோ" class="h-16 w-auto">
                </a>
            </div>

            <!-- Center: Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}#about-kathaingo"
                    class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('home') ? 'text-burnt-orange font-bold' : '' }}">
                    {{ __('Home') }}
                </a>
                <a href="{{ route('stories.index') }}"
                    class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('stories.*') ? 'text-burnt-orange font-bold' : '' }}">
                    {{ __('Stories') }}
                </a>
                <a href="{{ url('/about#about') }}"
                    class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition">
                    {{ __('Writers') }}
                </a>
                <a href="{{ route('series.index') }}"
                    class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('series.*') ? 'text-burnt-orange font-bold' : '' }}">
                    {{ __('Series') }}
                </a>

                <button @click="$dispatch('open-search')" 
                    class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition flex items-center gap-1.5 cursor-pointer select-none bg-transparent border-0 p-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>{{ __('Search') }}</span>
                </button>

                @guest
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="text-sm font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('register') ? 'text-burnt-orange font-bold' : '' }}">
                            {{ __('Contribute') }}
                        </a>
                    @endif
                @endguest
            </div>

            <!-- Right: Desktop Controls -->
            <div class="hidden lg:flex items-center gap-6">
                <!-- Language Switcher -->
                @if(app()->getLocale() === 'en')
                    <a href="{{ route('lang.switch', 'ta') }}" class="text-sm font-bold text-gray-300 hover:text-burnt-orange transition">தமிழ்</a>
                @else
                    <a href="{{ route('lang.switch', 'en') }}" class="text-sm font-bold text-gray-300 hover:text-burnt-orange transition">English</a>
                @endif

                @auth
                    <a href="{{ route('posts.create') }}"
                        class="text-sm font-semibold text-burnt-orange hover:text-orange-400 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Write') }}
                    </a>
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-medium text-gray-300 hover:text-burnt-orange transition">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-300 hover:text-burnt-orange transition cursor-pointer bg-transparent border-0 p-0">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-gray-300 hover:text-burnt-orange transition">{{ __('Sign In') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 rounded-full text-sm font-semibold transition transform hover:scale-105">
                            {{ __('Contribute') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center lg:hidden gap-4">
                <!-- Language Switcher on Mobile Header -->
                @if(app()->getLocale() === 'en')
                    <a href="{{ route('lang.switch', 'ta') }}" class="text-xs font-bold text-gray-300 hover:text-burnt-orange transition">தமிழ்</a>
                @else
                    <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold text-gray-300 hover:text-burnt-orange transition">English</a>
                @endif

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-300 hover:text-burnt-orange focus:outline-none p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="lg:hidden bg-slate-gray/95 backdrop-blur-md border-t border-slate-800/60 px-6 py-6 space-y-4"
        style="display: none;"
    >
        <a href="{{ route('home') }}#about-kathaingo" @click="mobileMenuOpen = false"
            class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('home') ? 'text-burnt-orange font-bold' : '' }}">
            {{ __('Home') }}
        </a>
        <a href="{{ route('stories.index') }}" @click="mobileMenuOpen = false"
            class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('stories.*') ? 'text-burnt-orange font-bold' : '' }}">
            {{ __('Stories') }}
        </a>
        <a href="{{ url('/about#about') }}" @click="mobileMenuOpen = false"
            class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition">
            {{ __('Writers') }}
        </a>
        <a href="{{ route('series.index') }}" @click="mobileMenuOpen = false"
            class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('series.*') ? 'text-burnt-orange font-bold' : '' }}">
            {{ __('Series') }}
        </a>

        <button @click="mobileMenuOpen = false; $dispatch('open-search')" 
            class="w-full text-left text-base font-semibold text-gray-300 hover:text-burnt-orange transition flex items-center gap-2 bg-transparent border-0 p-0">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>{{ __('Search') }}</span>
        </button>

        @guest
            @if (Route::has('register'))
                <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition {{ request()->routeIs('register') ? 'text-burnt-orange font-bold' : '' }}">
                    {{ __('Contribute') }}
                </a>
            @endif
        @endguest

        <!-- Divider -->
        <div class="border-t border-gray-800 my-4"></div>

        <!-- Auth Controls Mobile -->
        <div class="space-y-4">
            @auth
                <a href="{{ route('posts.create') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-burnt-orange hover:text-orange-400 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Write') }}
                </a>
                <a href="{{ url('/dashboard') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition">{{ __('Dashboard') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="w-full text-left text-base font-semibold text-gray-300 hover:text-burnt-orange transition bg-transparent border-0 p-0">
                        {{ __('Log Out') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                    class="block text-base font-semibold text-gray-300 hover:text-burnt-orange transition">{{ __('Sign In') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                        class="inline-block px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 rounded-full text-sm font-semibold transition text-white">
                        {{ __('Contribute') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

<!-- Futuristic Search Overlay Modal -->
<div 
    x-data="{ 
        isOpen: false, 
        searchQuery: '', 
        results: { posts: [], authors: [], categories: [] }, 
        loading: false,
        selectedIndex: -1,
        totalItemsCount() {
            return this.results.posts.length + this.results.authors.length + this.results.categories.length;
        },
        getAllItems() {
            let items = [];
            this.results.categories.forEach(c => items.push({ type: 'category', title: c.name, subtitle: c.path, url: '/stories?' + c.type + '=' + c.slug }));
            this.results.authors.forEach(a => items.push({ type: 'author', title: a.name, url: '/authors/' + a.slug }));
            this.results.posts.forEach(p => items.push({ type: 'post', title: p.title, url: '/posts/' + p.slug }));
            return items;
        },
        navigateSelection(direction) {
            let count = this.totalItemsCount();
            if (count === 0) return;
            if (direction === 'down') {
                this.selectedIndex = (this.selectedIndex + 1) % count;
            } else {
                this.selectedIndex = (this.selectedIndex - 1 + count) % count;
            }
            this.scrollToSelected();
        },
        scrollToSelected() {
            this.$nextTick(() => {
                let activeEl = this.$refs.resultsContainer.querySelector('.active-search-item');
                if (activeEl) {
                    activeEl.scrollIntoView({ block: 'nearest' });
                }
            });
        },
        selectCurrent() {
            let items = this.getAllItems();
            if (this.selectedIndex >= 0 && this.selectedIndex < items.length) {
                window.location.href = items[this.selectedIndex].url;
            } else if (this.searchQuery.trim().length > 0) {
                this.submitSearch();
            }
        },
        submitSearch() {
            if (this.searchQuery.trim().length > 0) {
                window.location.href = '/stories?search=' + encodeURIComponent(this.searchQuery.trim());
            }
        },
        fetchSuggestions() {
            let query = this.searchQuery.trim();
            if (query.length < 2) {
                this.results = { posts: [], authors: [], categories: [] };
                this.selectedIndex = -1;
                return;
            }
            this.loading = true;
            fetch('/api/search?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.selectedIndex = -1;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        }
    }"
    x-ref="searchModal"
    @open-search.window="isOpen = true; $nextTick(() => { $refs.searchInput.focus(); })"
    @keydown.escape.window="const transDropdown = document.getElementById('lang-translit-dropdown'); const isTransOpen = transDropdown && !transDropdown.classList.contains('hidden'); if (isTransOpen) { return; } isOpen = false"
    @keydown.window.prevent.ctrl.k="isOpen = true; $nextTick(() => { $refs.searchInput.focus(); })"
    @keydown.window.prevent.slash="if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') { isOpen = true; $nextTick(() => { $refs.searchInput.focus(); }) }"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-[100] flex items-start justify-center pt-[10vh] px-4 overflow-y-auto"
    style="display: none;"
>
    <!-- Dark Backdrop -->
    <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-md transition-opacity" @click="isOpen = false"></div>

    <!-- Search Palette Panel -->
    <div class="relative w-full max-w-2xl bg-gray-900 border border-gray-800 rounded-2xl shadow-[0_0_50px_rgba(197,86,38,0.15)] overflow-hidden flex flex-col max-h-[75vh]">
        <!-- Search Header -->
        <div class="flex items-center px-4 py-3 border-b border-gray-800 bg-gray-900/50">
            <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
                x-ref="searchInput"
                x-model="searchQuery"
                @input.debounce.300ms="fetchSuggestions()"
                @keydown.down.prevent="navigateSelection('down')"
                @keydown.up.prevent="navigateSelection('up')"
                @keydown.enter.prevent="selectCurrent()"
                type="text" 
                placeholder="{{ app()->getLocale() === 'ta' ? 'கட்டுரைகள், எழுத்தாளர்கள் அல்லது தலைப்புகளைத் தேடுக...' : 'Search articles, writers, or topics...' }}"
                class="w-full bg-transparent border-0 outline-none focus:ring-0 text-white placeholder-gray-500 text-base py-1"
                autocomplete="off"
                data-kathaingo-transliterate="true"
            >
            <!-- Compact Transliteration Toggle Button -->
            <div class="kathaingo-emoji-toggle mr-2" title="Transliteration Mode">
                <button type="button" class="lang-toggle-btn px-1.5 py-0.5 rounded-full text-[10px] font-bold transition-all duration-200 cursor-pointer bg-burnt-orange text-white" data-lang="en">En</button>
                <button type="button" class="lang-toggle-btn px-1.5 py-0.5 rounded-full text-[10px] font-bold transition-all duration-200 cursor-pointer bg-transparent text-gray-400 hover:text-white" data-lang="ta">த</button>
            </div>
            <button x-show="searchQuery.length > 0" @click="searchQuery = ''; fetchSuggestions(); $refs.searchInput.focus()" class="p-1 rounded-full text-gray-400 hover:text-white transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="ml-4 flex items-center gap-1">
                <span class="text-[10px] text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded border border-gray-700">ESC</span>
            </div>
        </div>

        <!-- Search Suggestions Body -->
        <div 
            x-ref="resultsContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 max-h-[50vh] subcategory-scroll-container"
        >
            <!-- Loading Indicator -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-10 space-y-3">
                <div class="w-8 h-8 border-2 border-burnt-orange border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs text-gray-400">{{ app()->getLocale() === 'ta' ? 'தேடுகிறது...' : 'Searching...' }}</span>
            </div>

            <!-- Empty / Default state -->
            <div x-show="!loading && searchQuery.length < 2" class="text-center py-10 text-gray-400">
                <svg class="w-10 h-10 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-sm font-medium">{{ app()->getLocale() === 'ta' ? 'குறைந்தது 2 எழுத்துக்களை உள்ளிடவும்' : 'Enter at least 2 characters to search' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale() === 'ta' ? 'கட்டுரைகள், எழுத்தாளர்கள் அல்லது தலைப்புகளைத் தேடத் தொடங்குங்கள்' : 'Type to search stories, bloggers, categories, and more' }}</p>
            </div>

            <!-- No Results Found -->
            <div x-show="!loading && searchQuery.length >= 2 && totalItemsCount() === 0" class="text-center py-10 text-gray-400">
                <svg class="w-10 h-10 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">{{ app()->getLocale() === 'ta' ? 'தேடல் முடிவுகள் எதுவும் இல்லை' : 'No results found' }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ app()->getLocale() === 'ta' ? 'வேறு வார்த்தைகளை முயலவும் அல்லது முழுத் தேடலுக்கு Enter அழுத்தவும்' : 'Try adjusting your terms, or press Enter to search everything' }}
                </p>
            </div>

            <!-- Suggestions List -->
            <div x-show="!loading && totalItemsCount() > 0" class="space-y-4">
                <!-- 1. Categories Section -->
                <template x-if="results.categories.length > 0">
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase px-2">🏷️ {{ app()->getLocale() === 'ta' ? 'தலைப்புகள்' : 'Categories' }}</h3>
                        <div class="space-y-1">
                            <template x-for="(cat, idx) in results.categories" :key="cat.slug + cat.type">
                                <a 
                                    :href="'/stories?' + cat.type + '=' + cat.slug"
                                    class="block px-3 py-2 rounded-xl transition-all border border-transparent cursor-pointer"
                                    :class="selectedIndex === idx ? 'bg-gray-800/80 border-gray-700 active-search-item' : 'hover:bg-gray-800/40'"
                                    @mouseenter="selectedIndex = idx"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-semibold text-white" x-text="cat.name"></div>
                                            <div class="text-xs text-gray-500" x-text="cat.path"></div>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- 2. Authors Section -->
                <template x-if="results.authors.length > 0">
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase px-2">✍️ {{ app()->getLocale() === 'ta' ? 'எழுத்தாளர்கள்' : 'Writers' }}</h3>
                        <div class="space-y-1">
                            <template x-for="(author, idx) in results.authors" :key="author.slug">
                                <a 
                                    :href="'/authors/' + author.slug"
                                    class="block px-3 py-2 rounded-xl transition-all border border-transparent cursor-pointer"
                                    :class="selectedIndex === (results.categories.length + idx) ? 'bg-gray-800/80 border-gray-700 active-search-item' : 'hover:bg-gray-800/40'"
                                    @mouseenter="selectedIndex = results.categories.length + idx"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-full bg-burnt-orange/20 text-burnt-orange font-bold text-xs flex items-center justify-center border border-burnt-orange/30" x-text="author.name.charAt(0)"></div>
                                            <div class="text-sm font-semibold text-white" x-text="author.name"></div>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- 3. Posts Section -->
                <template x-if="results.posts.length > 0">
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold tracking-wider text-gray-500 uppercase px-2">📰 {{ app()->getLocale() === 'ta' ? 'கட்டுரைகள்' : 'Articles' }}</h3>
                        <div class="space-y-1">
                            <template x-for="(post, idx) in results.posts" :key="post.slug">
                                <a 
                                    :href="'/posts/' + post.slug"
                                    class="block px-3 py-2 rounded-xl transition-all border border-transparent cursor-pointer"
                                    :class="selectedIndex === (results.categories.length + results.authors.length + idx) ? 'bg-gray-800/80 border-gray-700 active-search-item' : 'hover:bg-gray-800/40'"
                                    @mouseenter="selectedIndex = results.categories.length + results.authors.length + idx"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="pr-4">
                                            <div class="text-sm font-semibold text-white" x-text="post.title"></div>
                                            <div class="text-xs text-gray-500 mt-0.5 line-clamp-1" x-text="post.excerpt"></div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-[10px] text-burnt-orange font-semibold" x-text="post.author_name"></span>
                                            </div>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Search Footer -->
        <div class="px-4 py-2 border-t border-gray-800 bg-gray-950/40 flex justify-between items-center text-[10px] text-gray-500">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <span class="border border-gray-700 bg-gray-800 px-1 py-0.5 rounded">↑↓</span> {{ app()->getLocale() === 'ta' ? 'நகர்த்த' : 'navigate' }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="border border-gray-700 bg-gray-800 px-1.5 py-0.5 rounded">⏎ Enter</span> {{ app()->getLocale() === 'ta' ? 'தேர்ந்தெடுக்க' : 'select' }}
                </span>
            </div>
            <div>
                <span>{{ app()->getLocale() === 'ta' ? 'முழுத் தேடலுக்கு Enter அழுத்தவும்' : 'Press Enter to search entire site' }}</span>
            </div>
        </div>
    </div>
</div>
