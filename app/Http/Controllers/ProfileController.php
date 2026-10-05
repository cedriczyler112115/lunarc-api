<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user(),
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $fullNameParts = array_filter([
            $validated['first_name'] ?? $user->first_name,
            $validated['middle_name'] ?? null,
            $validated['last_name'] ?? $user->last_name,
            $validated['extension_name'] ?? null,
        ]);
        $validated['name'] = implode(' ', $fullNameParts);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && str_contains($user->avatar_path, 'storage/avatars/')) {
                $relative = str_replace('storage/', '', $user->avatar_path);
                Storage::disk('public')->delete($relative);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = 'storage/'.$path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
