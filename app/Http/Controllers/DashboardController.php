<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isEditor()) {
            return $this->editorDashboard();
        }

        if ($user->isAuthor()) {
            return $this->authorDashboard();
        }

        // Default: Visitor
        return view('dashboard', ['role' => 'visitor']);
    }

    protected function adminDashboard()
    {
        $usersCount = User::count();
        $postsCount = Post::count();
        $pendingPostsCount = Post::where('status', 'submitted')->count();
        
        $users = User::with('authorProfile')->orderBy('created_at', 'desc')->get();
        $authorsCategory = \App\Models\Category::where('slug', 'pathivargal')->first();
        $authors = $authorsCategory ? $authorsCategory->subcategories()->orderBy('name')->get() : collect();

        // Paginated login logs
        $loginLogs = LoginActivity::with('user')->orderBy('logged_at', 'desc')->paginate(15);

        // Suspicious activity detection
        // 1. Multiple failed attempts from same IP in last 24h
        $suspiciousIPs = LoginActivity::where('is_successful', false)
            ->where('logged_at', '>=', now()->subDay())
            ->groupBy('ip_address')
            ->selectRaw('ip_address, count(*) as count')
            ->havingRaw('count(*) > ?', [5])
            ->get();

        // 2. Users logging in from multiple IPs in last 24h
        $suspiciousUsers = LoginActivity::where('is_successful', true)
            ->where('logged_at', '>=', now()->subDay())
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->selectRaw('user_id, count(distinct ip_address) as ip_count')
            ->havingRaw('count(distinct ip_address) > ?', [1])
            ->with('user')
            ->get();

        return view('dashboard', [
            'role' => 'admin',
            'usersCount' => $usersCount,
            'postsCount' => $postsCount,
            'pendingPostsCount' => $pendingPostsCount,
            'users' => $users,
            'authors' => $authors,
            'loginLogs' => $loginLogs,
            'suspiciousIPs' => $suspiciousIPs,
            'suspiciousUsers' => $suspiciousUsers,
        ]);
    }

    protected function editorDashboard()
    {
        // Queue: submitted, under_review posts
        $reviewQueue = Post::whereIn('status', ['submitted', 'under_review'])
            ->with(['author', 'authorSubcategory'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dashboard', [
            'role' => 'editor',
            'reviewQueue' => $reviewQueue,
        ]);
    }

    protected function authorDashboard()
    {
        $user = auth()->user();

        // Author drafts and other posts
        $drafts = Post::where('author_id', $user->id)
            ->where('status', 'draft')
            ->orderBy('updated_at', 'desc')
            ->get();

        $submitted = Post::where('author_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review', 'approved', 'published', 'rejected'])
            ->with(['feedback' => function($q) {
                $q->with('user');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard', [
            'role' => 'author',
            'drafts' => $drafts,
            'submitted' => $submitted,
        ]);
    }
}
