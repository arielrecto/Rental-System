<?php

namespace App\Http\Controllers\Internal;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::role('internal')
            ->with('profile')
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('internal');

        Profile::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('internal.users.index')
            ->with('success', 'User created successfully');
    }

    public function show(string $id)
    {
        $user = User::with('profile')->findOrFail($id);

        return Inertia::render('Internal/UserManagement/UserManagementShow', [
            'user' => $user
        ]);
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
     

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'pin' => $request->pin
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }


        if($user->profile){
            $user->profile->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        return back()
            ->with('success', 'User updated successfully');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->profile()->delete();
        $user->delete();

        return redirect()->route('internal.users.index')
            ->with('success', 'User deleted successfully');
    }
}
