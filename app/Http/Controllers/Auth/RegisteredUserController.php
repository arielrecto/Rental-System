<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                           => 'required|string|max:255',
            'email'                          => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password'                       => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name'                     => 'required|string|max:255',
            'last_name'                      => 'required|string|max:255',
            'phone_number'                   => 'required|string|max:20',
            'address'                        => 'required|string',
            'gender'                         => 'required|in:male,female',
            'birth_date'                     => 'required|date',
            'emergency_contact_name'         => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:100',
            'emergency_contact_phone'        => 'required|string|max:20',
            'drivers_license_number'         => 'nullable|string|max:50',
            'drivers_license_expiry'         => 'nullable|date',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->profile()->create([
            'first_name'                     => $request->first_name,
            'last_name'                      => $request->last_name,
            'phone_number'                   => $request->phone_number,
            'address'                        => $request->address,
            'gender'                         => $request->gender,
            'birth_date'                     => $request->birth_date,
            'emergency_contact_name'         => $request->emergency_contact_name,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'emergency_contact_phone'        => $request->emergency_contact_phone,
            'drivers_license_number'         => $request->drivers_license_number,
            'drivers_license_expiry'         => $request->drivers_license_expiry,
        ]);

        $customer = Role::where('name', 'customer')->first();
        $user->assignRole($customer);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home'));
    }
}
