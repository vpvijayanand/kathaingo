<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Get all users who are not admins
        $users = \App\Models\User::where('is_admin', false)->orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
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
}
