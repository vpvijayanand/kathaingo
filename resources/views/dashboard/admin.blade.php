<div class="space-y-8">
    <!-- Stats Cards Grid -->
    <div class="grid gap-6 sm:grid-cols-3">
        <div class="p-6 bg-gray-950/40 rounded-xl border border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase font-semibold">Total Users / பயனர்கள்</p>
                <h4 class="text-3xl font-bold text-gray-100 mt-1">{{ $usersCount }}</h4>
            </div>
            <div class="p-3 bg-burnt-orange/10 rounded-lg text-burnt-orange">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <div class="p-6 bg-gray-950/40 rounded-xl border border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase font-semibold">Total Articles / பதிவுகள்</p>
                <h4 class="text-3xl font-bold text-gray-100 mt-1">{{ $postsCount }}</h4>
            </div>
            <div class="p-3 bg-emerald-500/10 rounded-lg text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
        </div>

        <a href="{{ route('posts.index', ['status' => 'submitted']) }}" class="p-6 bg-gray-950/40 rounded-xl border border-gray-800 flex items-center justify-between hover:border-amber-500/50 hover:bg-gray-900/40 transition group cursor-pointer">
            <div>
                <p class="text-xs text-gray-400 uppercase font-semibold group-hover:text-amber-400 transition">Pending Approvals / ஒப்புதல்கள்</p>
                <h4 class="text-3xl font-bold text-gray-100 mt-1">{{ $pendingPostsCount }}</h4>
            </div>
            <div class="p-3 bg-amber-500/10 rounded-lg text-amber-400 group-hover:bg-amber-500/20 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>
    </div>

    <!-- Suspicious Security Alerts Panel -->
    @if($suspiciousIPs->isNotEmpty() || $suspiciousUsers->isNotEmpty())
        <div class="p-5 bg-red-950/25 border border-red-800/40 rounded-xl space-y-4 shadow-lg animate-pulse">
            <h5 class="text-red-400 font-bold text-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Security Alerts / சந்தேகத்திற்குிடமான செயல்பாடுகள்
            </h5>
            
            <div class="grid gap-4 sm:grid-cols-2 text-xs">
                @if($suspiciousIPs->isNotEmpty())
                    <div class="space-y-2">
                        <p class="font-semibold text-red-300">Failed attempts by IP (Last 24h):</p>
                        <ul class="list-disc pl-5 text-gray-300 space-y-1">
                            @foreach($suspiciousIPs as $ip)
                                <li>IP <strong class="text-white">{{ $ip->ip_address }}</strong>: {{ $ip->count }} failed attempts</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($suspiciousUsers->isNotEmpty())
                    <div class="space-y-2">
                        <p class="font-semibold text-red-300">Rapid IP Switching by Users (Last 24h):</p>
                        <ul class="list-disc pl-5 text-gray-300 space-y-1">
                            @foreach($suspiciousUsers as $log)
                                <li>User <strong class="text-white">{{ $log->user->name }}</strong>: logged in from {{ $log->ip_count }} distinct IPs</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Quick Navigation Panel -->
    <div class="flex gap-4 flex-wrap border-b border-gray-800 pb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2.5 bg-burnt-orange hover:bg-orange-650 rounded-lg text-xs font-bold text-white uppercase tracking-wider transition shadow-md">
            Manage Users & Role Assignment
        </a>
        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-850 hover:bg-gray-805 border border-gray-700 rounded-lg text-xs font-bold text-gray-350 uppercase tracking-wider transition">
            Content Moderation List
        </a>
    </div>

    <!-- Login Audit Trail -->
    <div>
        <h4 class="text-lg font-semibold text-gray-200 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Security Login Audit Trail / பாதுகாப்பு தணிக்கை பதிவு
        </h4>

        <div class="bg-gray-950/30 rounded-xl border border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800 text-left">
                    <thead>
                        <tr class="bg-gray-950/80 text-gray-400 text-xs uppercase font-semibold">
                            <th class="px-6 py-4">User / Email</th>
                            <th class="px-6 py-4">IP Address</th>
                            <th class="px-6 py-4">Device (User Agent)</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm">
                        @foreach($loginLogs as $log)
                            <tr class="hover:bg-gray-900/30 transition">
                                <td class="px-6 py-4">
                                    @if($log->user)
                                        <div class="font-semibold text-gray-200">{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                    @else
                                        <div class="font-semibold text-red-400">Failed Attempt</div>
                                        <div class="text-xs text-gray-500">{{ $log->email ?? 'N/A' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-300">{{ $log->ip_address }}</td>
                                <td class="px-6 py-4 text-xs text-gray-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->is_successful)
                                        <span class="px-2 py-0.5 bg-green-950/40 text-green-400 border border-green-800 rounded text-xs font-medium">Success</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-950/40 text-red-400 border border-red-800 rounded text-xs font-medium">Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">{{ $log->logged_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination links -->
            <div class="p-4 bg-gray-950/40 border-t border-gray-800">
                {{ $loginLogs->links() }}
            </div>
        </div>
    </div>
</div>
