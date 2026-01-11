<?php

namespace App\Http\Controllers\Internal;

use App\Models\User;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with(['profile', 'roles'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Internal/UserManagement/UserManagementIndex', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return Inertia::render('Internal/UserManagement/UserManagementCreate');
    }

    public function show( $id){
        $user = User::with(['profile', 'roles'])->find($id);


        return Inertia::render('Internal/UserManagement/UserManagementShow', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'pin' => 'required|numeric|digits_between:4,6|confirmed',
            // Profile fields
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today'
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'pin' => Hash::make($validated['pin'])
        ]);

        // Create profile if any profile data is provided
        if ($request->filled(['first_name', 'last_name', 'phone_number', 'address', 'gender', 'birth_date'])) {
            Profile::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null
            ]);
        }

        return redirect()->route('internal.user-management.index')
            ->with('success', 'User created successfully');
    }

    public function edit(string $id)
    {
        $user = User::with('profile')->findOrFail($id);

        return Inertia::render('Internal/UserManagement/UserManagementEdit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'pin' => 'nullable|numeric|digits_between:4,6|confirmed',
            // Profile fields
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today'
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update PIN if provided
        if ($request->filled('pin')) {
            $user->update(['pin' => Hash::make($validated['pin'])]);
        }

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null
            ]
        );

        return redirect()->route('internal.user-management.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('internal.user-management.index')
            ->with('success', 'User deleted successfully');
    }
}
