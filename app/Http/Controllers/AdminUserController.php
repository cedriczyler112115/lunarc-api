<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $pendingCount = User::where('is_approved', false)->count();
        $approvedCount = User::where('is_approved', true)->count();

        return view('admin.users.index', compact('users', 'pendingCount', 'approvedCount'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_approved' => true,
        ]);

        return back()->with('success', "User account for {$user->name} ({$user->email}) has been approved!");
    }

    public function toggleAdmin(User $user)
    {
        // Prevent demoting last admin or self if needed
        if ($user->id === auth()->id() && $user->is_admin) {
            return back()->withErrors(['user' => 'You cannot remove your own admin privileges.']);
        }

        $newRole = !$user->is_admin;
        $user->update([
            'is_admin' => $newRole,
            'is_approved' => true, // Admin users are automatically approved
        ]);

        $statusText = $newRole ? 'granted Admin privileges' : 'changed to standard user';
        return back()->with('success', "User {$user->name} has been {$statusText}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User registration for {$name} was rejected and removed.");
    }
}
