<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        // Get all users except current admin
        $users = \App\Models\User::with('authorProfile')->where('id', '!=', auth()->id())->orderBy('created_at', 'desc')->get();
        
        $authorsCategory = \App\Models\Category::where('slug', 'pathivargal')->first();
        $authors = $authorsCategory ? $authorsCategory->subcategories()->orderBy('name')->get() : collect();

        return view('admin.users.index', compact('users', 'authors'));
    }

    public function approve(\App\Models\User $user)
    {
        $user->is_approved = true;
        $user->save();

        // Send mail trigger here (simulated for now)
        // \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\UserApproved($user));

        return redirect()->back()->with('status', 'User approved successfully!');
    }

    public function delete(\App\Models\User $user)
    {
        $user->delete();
        return redirect()->back()->with('status', 'User deleted successfully!');
    }

    /**
     * Link a user to an author profile.
     */
    public function linkAuthor(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'author_subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        // Clear existing association for this user
        \App\Models\Subcategory::where('user_id', $user->id)->update(['user_id' => null]);

        // Link new subcategory if provided
        if ($request->filled('author_subcategory_id')) {
            $subcategory = \App\Models\Subcategory::find($request->author_subcategory_id);
            if ($subcategory) {
                $subcategory->user_id = $user->id;

                // Propagate user details if author fields are blank
                if (!$subcategory->email) {
                    $subcategory->email = $user->email;
                }
                if (!$subcategory->phone) {
                    $subcategory->phone = $user->mobile;
                }

                $subcategory->save();
            }
        }

        return redirect()->back()->with('status', 'Author profile linked successfully!');
    }

    /**
     * Update a user's role.
     */
    public function updateRole(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|string|in:visitor,author,editor,admin,seo_manager',
        ]);

        $user->role = $request->role;
        $user->is_admin = ($request->role === 'admin');
        $user->save();

        return redirect()->back()->with('status', 'User role updated successfully!');
    }

    /**
     * Display the Writer Engine Verification Dashboard.
     */
    public function writersVerification()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        // Fetch writers only with their statistics and authored posts (eager loaded content categories)
        $writers = \App\Models\Subcategory::writersOnly()
            ->withWriterStats()
            ->with(['authoredPosts' => function ($query) {
                $query->where('status', 'published')->with('category');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.writers.verification', compact('writers'));
    }

    /**
     * Update the writer verification details.
     */
    public function updateWriterVerification(Request $request, \App\Models\Subcategory $subcategory)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'trust_level' => 'required|integer|min:1',
            'is_featured' => 'required|boolean',
            'tagline'     => 'nullable|string|max:255',
            'tagline_en'  => 'nullable|string|max:255',
        ]);

        $subcategory->update([
            'trust_level' => $request->trust_level,
            'is_featured' => $request->is_featured,
            'tagline'     => $request->tagline,
            'tagline_en'  => $request->tagline_en,
        ]);

        return redirect()->back()->with('status', "Writer profile for '{$subcategory->name}' updated successfully!");
    }
}
