<div class="space-y-8">
    <!-- Profile & Actions Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-gray-800 gap-4">
        <div>
            <h3 class="text-2xl font-serif text-burnt-orange font-semibold">Welcome back, {{ auth()->user()->name }}!</h3>
            <p class="text-sm text-gray-400 mt-1">Manage your drafts, track publication statuses, and view feedback from editors.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-burnt-orange hover:bg-orange-650 text-white rounded-md text-sm font-semibold transition shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Write New Post
            </a>
            @if(auth()->user()->authorProfile)
                <a href="{{ route('authors.edit', auth()->user()->authorProfile->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-750 text-gray-300 rounded-md text-sm font-semibold border border-gray-700 transition">
                    Profile Settings
                </a>
            @endif
        </div>
    </div>

    <!-- Active Drafts Section -->
    <div>
        <h4 class="text-lg font-semibold text-gray-200 mb-4 flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
            My Active Drafts ({{ $drafts->count() }})
        </h4>

        @if($drafts->isEmpty())
            <div class="p-8 text-center bg-gray-950/40 rounded-xl border border-gray-800">
                <p class="text-gray-400">You don't have any drafts at the moment.</p>
                <a href="{{ route('posts.create') }}" class="text-burnt-orange hover:underline text-sm font-semibold mt-2 inline-block">Create your first draft ↗</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($drafts as $draft)
                    <div class="p-5 bg-gray-950/40 rounded-xl border border-gray-800 flex flex-col justify-between hover:border-gray-700 transition">
                        <div>
                            <h5 class="font-bold text-gray-100 line-clamp-1 mb-2">{{ $draft->title }}</h5>
                            <p class="text-xs text-gray-400 line-clamp-3 mb-4">
                                {{ $draft->excerpt ?? strip_tags($draft->content) }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-850 pt-4 mt-2">
                            <span class="text-[10px] text-gray-500">Updated {{ $draft->updated_at->diffForHumans() }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('posts.edit', $draft->id) }}" class="text-xs text-burnt-orange hover:text-orange-400 font-semibold">Edit</a>
                                <form action="{{ route('posts.submit', $draft->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-500 hover:text-green-400 font-semibold">Submit</button>
                                </form>
                                <form action="{{ route('posts.destroy', $draft->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-400 font-semibold">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Submissions Tracker Section -->
    <div>
        <h4 class="text-lg font-semibold text-gray-200 mb-4 flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
            Submissions & Reviews History ({{ $submitted->count() }})
        </h4>

        @if($submitted->isEmpty())
            <div class="p-8 text-center bg-gray-950/40 rounded-xl border border-gray-800">
                <p class="text-gray-400">No submission records found.</p>
            </div>
        @else
            <div class="bg-gray-950/30 rounded-xl border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800 text-left">
                        <thead>
                            <tr class="bg-gray-950/80 text-gray-400 text-xs uppercase font-semibold">
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Submitted At</th>
                                <th class="px-6 py-4">Latest Feedback</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @foreach($submitted as $post)
                                @php
                                    $statusColor = 'bg-gray-800 text-gray-300';
                                    if ($post->status === 'submitted') $statusColor = 'bg-blue-950/40 text-blue-400 border border-blue-805';
                                    elseif ($post->status === 'under_review') $statusColor = 'bg-yellow-950/40 text-yellow-400 border border-yellow-805';
                                    elseif ($post->status === 'approved') $statusColor = 'bg-emerald-950/40 text-emerald-400 border border-emerald-805';
                                    elseif ($post->status === 'published') $statusColor = 'bg-green-950/40 text-green-400 border border-green-805';
                                    elseif ($post->status === 'rejected') $statusColor = 'bg-red-950/40 text-red-400 border border-red-805';
                                @endphp
                                <tr class="hover:bg-gray-900/30 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-100 max-w-xs truncate">{{ $post->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $post->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $post->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-6 py-4 max-w-sm">
                                        @if($post->status === 'rejected' && $post->feedback->isNotEmpty())
                                            <div class="text-xs bg-red-950/20 text-red-300 border border-red-900/40 p-2.5 rounded-lg">
                                                <strong>{{ $post->feedback->first()->user->name }}:</strong> 
                                                <span class="italic">"{{ $post->feedback->first()->comment }}"</span>
                                            </div>
                                        @elseif($post->status === 'published')
                                            <span class="text-xs text-green-500 italic">Live on Kathaingo</span>
                                        @else
                                            <span class="text-xs text-gray-500 italic">No feedback comments yet.</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="flex gap-3 items-center">
                                            @if($post->status === 'published')
                                                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="text-burnt-orange hover:underline font-semibold">View Post ↗</a>
                                            @endif
                                            <a href="{{ route('posts.edit', $post->id) }}" class="text-burnt-orange hover:underline font-semibold">Edit</a>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline font-semibold">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
