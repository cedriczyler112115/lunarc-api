<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'role' => ['required', 'in:guest,car_owner'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'extension_name' => ['nullable', 'string', 'max:50'],
            'birthday' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];

        if ($request->input('role') === 'car_owner') {
            $rules['owner_description'] = ['required', 'string', 'min:10'];
        } else {
            $rules['owner_description'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $fullNameParts = array_filter([
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
            $validated['extension_name'] ?? null,
        ]);
        $validated['name'] = implode(' ', $fullNameParts);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = 'storage/'.$path;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_approved'] = false;
        $validated['is_admin'] = false;

        $user = User::create($validated);

        event(new Registered($user));

        return redirect(route('login'))
            ->with('status', 'Registration submitted successfully! Your account is pending Admin approval. You will be able to log in once an Administrator approves your account.');
    }
}
