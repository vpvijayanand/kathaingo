<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Writer Engine Verification Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r shadow-sm transition-all duration-300">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($writers as $writer)
                    @php
                        $contributedCategories = $writer->authoredPosts->pluck('category')->unique('id')->filter();
                    @endphp
                    <div class="bg-white overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 rounded-xl border border-gray-100 flex flex-col justify-between">
                        <!-- Card Content -->
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Avatar -->
                                    <div class="relative w-16 h-16 flex-shrink-0">
                                        @if($writer->getAvatarUrl())
                                            <img class="w-full h-full rounded-full object-cover border-2 border-burnt-orange shadow-inner" src="{{ $writer->getAvatarUrl() }}" alt="{{ $writer->name }}" />
                                        @else
                                            <div class="w-full h-full rounded-full bg-orange-50 border-2 border-burnt-orange flex items-center justify-center shadow-inner">
                                                <span class="text-lg font-bold text-burnt-orange">{{ mb_substr($writer->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Featured badge -->
                                        @if($writer->is_featured)
                                            <span class="absolute -top-1 -right-1 bg-amber-500 text-white rounded-full p-1 shadow-md" title="Featured Writer">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                            {{ $writer->name }}
                                        </h3>
                                        <p class="text-xs text-gray-400">@if($writer->name_en) {{ $writer->name_en }} @else English name not set @endif</p>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <!-- Trust Level Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-burnt-orange text-white shadow-sm">
                                                Trust Level: {{ $writer->trust_level }}
                                            </span>
                                            
                                            <!-- Featured status Badge -->
                                            @if($writer->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Featured
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                    Standard
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Writer Stats Grid -->
                            <div class="mt-6 grid grid-cols-3 gap-2 bg-gray-50 rounded-lg p-3 text-center">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $writer->published_posts_count }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Reads</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $writer->total_reads ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Appreciation</span>
                                    <span class="text-lg font-bold text-gray-900">{{ $writer->total_likes ?? 0 }}</span>
                                </div>
                            </div>

                            <!-- Contributed Categories Section -->
                            <div class="mt-4">
                                <span class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Contributed Categories</span>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($contributedCategories as $cat)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-burnt-orange/10 text-burnt-orange">
                                            {{ $cat->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs italic text-gray-400">No published content yet</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Update Form -->
                        <div class="bg-gray-50 p-6 border-t border-gray-100 rounded-b-xl">
                            <form action="{{ route('admin.writers.verification.update', $writer->id) }}" method="POST" class="space-y-4">
                                @csrf
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Trust Level -->
                                    <div>
                                        <label for="trust_level_{{ $writer->id }}" class="block text-xs font-semibold text-gray-600 uppercase">Trust Level</label>
                                        <select id="trust_level_{{ $writer->id }}" name="trust_level" class="mt-1 block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange p-2">
                                            <option value="1" {{ $writer->trust_level == 1 ? 'selected' : '' }}>1 - Default / Guest</option>
                                            <option value="2" {{ $writer->trust_level == 2 ? 'selected' : '' }}>2 - Verified Author</option>
                                            <option value="3" {{ $writer->trust_level == 3 ? 'selected' : '' }}>3 - Trusted Editor / Admin</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Featured status -->
                                    <div>
                                        <label for="is_featured_{{ $writer->id }}" class="block text-xs font-semibold text-gray-600 uppercase">Featured status</label>
                                        <select id="is_featured_{{ $writer->id }}" name="is_featured" class="mt-1 block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange p-2">
                                            <option value="0" {{ !$writer->is_featured ? 'selected' : '' }}>No (Standard)</option>
                                            <option value="1" {{ $writer->is_featured ? 'selected' : '' }}>Yes (Featured)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Tamil Tagline -->
                                <div>
                                    <label for="tagline_{{ $writer->id }}" class="block text-xs font-semibold text-gray-600 uppercase">Tagline (Tamil)</label>
                                    <input type="text" id="tagline_{{ $writer->id }}" name="tagline" value="{{ $writer->getRawOriginal('tagline') }}" placeholder="எ.கா: முகநூல் பக்கங்களில் பகிரப்பட்ட சுவாரசியமான பதிவுகள்" class="mt-1 block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange p-2" />
                                </div>

                                <!-- English Tagline -->
                                <div>
                                    <label for="tagline_en_{{ $writer->id }}" class="block text-xs font-semibold text-gray-600 uppercase">Tagline (English - Optional)</label>
                                    <input type="text" id="tagline_en_{{ $writer->id }}" name="tagline_en" value="{{ $writer->getRawOriginal('tagline_en') }}" placeholder="e.g. Interesting posts shared on Facebook" class="mt-1 block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange p-2" />
                                    <p class="text-[10px] text-gray-400 mt-1">Leave empty to auto-translate from Tamil</p>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="w-full bg-burnt-orange hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:shadow transition-all duration-300 text-sm">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
