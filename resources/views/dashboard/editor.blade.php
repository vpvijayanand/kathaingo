<div class="space-y-6" x-data="{ activePost: null, compareRevisionId: '', compareData: {} }">
    <div class="pb-6 border-b border-gray-800">
        <h3 class="text-2xl font-serif text-burnt-orange font-semibold">Content Review & Editing Workspace</h3>
        <p class="text-sm text-gray-400 mt-1">Review drafts submitted by authors, compare edits, suggest changes, and approve/publish articles.</p>
    </div>

    <!-- Review Queue -->
    <div>
        <h4 class="text-lg font-semibold text-gray-200 mb-4 flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500"></span>
            Submitted Queue ({{ $reviewQueue->count() }})
        </h4>

        @if($reviewQueue->isEmpty())
            <div class="p-8 text-center bg-gray-950/40 rounded-xl border border-gray-800">
                <p class="text-gray-400">The queue is currently empty. No articles pending review!</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($reviewQueue as $post)
                    <div class="bg-gray-950/30 rounded-xl border border-gray-800 overflow-hidden hover:border-gray-750 transition duration-300">
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-950/50">
                            <div class="space-y-1">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $post->status === 'under_review' ? 'bg-yellow-950/40 text-yellow-400 border-yellow-800' : 'bg-blue-950/40 text-blue-400 border-blue-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $post->status)) }}
                                </span>
                                <h5 class="text-lg font-bold text-gray-100 mt-1">{{ $post->title }}</h5>
                                <p class="text-xs text-gray-400">
                                    Submitted by <span class="text-burnt-orange font-semibold">{{ $post->author->name }}</span> 
                                    ({{ $post->authorSubcategory ? $post->authorSubcategory->name : 'General' }}) 
                                    • {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="button" @click="activePost === {{ $post->id }} ? activePost = null : activePost = {{ $post->id }}" 
                                        class="px-4 py-2 bg-gray-800 hover:bg-gray-750 text-gray-300 text-xs font-semibold rounded-lg transition border border-gray-700">
                                    <span x-text="activePost === {{ $post->id }} ? 'Close Review Panel' : 'Review & Action'"></span>
                                </button>
                                <a href="{{ route('posts.edit', $post->id) }}" class="px-4 py-2 bg-burnt-orange hover:bg-orange-650 text-white text-xs font-semibold rounded-lg transition shadow-md">
                                    Edit Article
                                </a>
                            </div>
                        </div>

                        <!-- Review Action Panel -->
                        <div x-show="activePost === {{ $post->id }}" x-collapse class="border-t border-gray-850 p-6 space-y-6 bg-gray-950/25">
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Status Action Form -->
                                <div class="space-y-4">
                                    <h6 class="text-sm font-semibold text-gray-200 border-b border-gray-850 pb-2">Status Action / பின்னூட்டம்</h6>
                                    
                                    <form action="{{ route('posts.statusUpdate', $post->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        
                                        <div>
                                            <label class="block text-xs text-gray-400 font-semibold mb-2">Change Status To:</label>
                                            <select name="status" required class="block w-full bg-gray-900 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm text-sm">
                                                <option value="under_review" {{ $post->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                                <option value="approved" {{ $post->status === 'approved' ? 'selected' : '' }}>Approve (Ready to Publish)</option>
                                                <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Publish Live</option>
                                                <option value="rejected" {{ $post->status === 'rejected' ? 'selected' : '' }}>Reject / Request Revisions</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-400 font-semibold mb-2">Comment / Reviewer Feedback (Visible to author on rejection):</label>
                                            <textarea name="comment" rows="4" placeholder="Describe edits made or reasons for rejection..." class="block w-full bg-gray-900 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm text-sm"></textarea>
                                        </div>

                                        <button type="submit" class="w-full py-2 bg-green-700 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition shadow-md">
                                            Apply Review Action
                                        </button>
                                    </form>
                                </div>

                                <!-- Article Details & Revision comparison -->
                                <div class="space-y-4">
                                    <h6 class="text-sm font-semibold text-gray-200 border-b border-gray-850 pb-2">Revision Logs & History</h6>
                                    
                                    @if($post->revisions->isEmpty())
                                        <div class="p-6 text-center bg-gray-900/40 rounded-lg border border-gray-850">
                                            <p class="text-xs text-gray-400">No revisions logged yet. Revisions are created automatically when edits are saved.</p>
                                        </div>
                                    @else
                                        <div class="space-y-3">
                                            <div class="max-h-48 overflow-y-auto space-y-2 pr-1 border border-gray-850 rounded-lg p-2 bg-gray-900/20">
                                                @foreach($post->revisions as $rev)
                                                    <div class="p-2.5 bg-gray-900/40 border border-gray-850 rounded-md flex justify-between items-center text-xs">
                                                        <div>
                                                            <p class="font-semibold text-gray-200">Edited by {{ $rev->user->name }}</p>
                                                            <p class="text-[10px] text-gray-500">{{ $rev->created_at->diffForHumans() }}</p>
                                                        </div>
                                                        <button type="button" 
                                                                @click="compareRevisionId = '{{ $rev->id }}'; 
                                                                        compareData = {
                                                                            title: '{{ addslashes($rev->title) }}', 
                                                                            excerpt: '{{ addslashes($rev->excerpt ?? '') }}', 
                                                                            content: '{{ addslashes(preg_replace('/\s+/', ' ', strip_tags($rev->content))) }}'
                                                                        }"
                                                                class="px-2.5 py-1 bg-gray-800 hover:bg-gray-750 text-burnt-orange hover:text-orange-400 font-semibold rounded border border-gray-700 transition">
                                                            Compare
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Comparison Drawer -->
                                            <div x-show="compareRevisionId !== ''" class="p-3 bg-gray-900 border border-gray-800 rounded-lg text-xs space-y-3">
                                                <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                                                    <span class="font-bold text-burnt-orange text-xs">Side-by-Side Comparison</span>
                                                    <button type="button" @click="compareRevisionId = ''" class="text-gray-400 hover:text-white">✕ Close</button>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4 divide-x divide-gray-800">
                                                    <div class="space-y-2 pr-2">
                                                        <p class="font-semibold text-gray-400 border-b border-gray-850 pb-1">Historical Revision</p>
                                                        <p class="font-bold text-gray-200" x-text="compareData.title"></p>
                                                        <p class="text-[10px] text-gray-400 italic" x-text="compareData.excerpt || 'No excerpt'"></p>
                                                        <p class="text-[10px] text-gray-400 line-clamp-6" x-text="compareData.content"></p>
                                                    </div>
                                                    <div class="space-y-2 pl-4">
                                                        <p class="font-semibold text-gray-400 border-b border-gray-850 pb-1">Current Version</p>
                                                        <p class="font-bold text-gray-200">{{ $post->title }}</p>
                                                        <p class="text-[10px] text-gray-400 italic">{{ $post->excerpt ?? 'No excerpt' }}</p>
                                                        <p class="text-[10px] text-gray-400 line-clamp-6">{{ strip_tags($post->content) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Post Content Preview Container -->
                            <div class="mt-4 p-4 bg-gray-900/40 border border-gray-850 rounded-xl">
                                <h6 class="text-xs font-semibold text-gray-400 mb-2">Article Preview / பதிவு முன்னோட்டம்:</h6>
                                <div class="max-h-60 overflow-y-auto pr-2 text-sm text-gray-300 font-serif leading-relaxed border border-gray-850 p-4 rounded bg-gray-950/60 prose prose-invert max-w-none">
                                    {!! $post->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
