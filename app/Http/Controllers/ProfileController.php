<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user()->load('profile.attachments');

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status'          => session('status'),
            'profile'         => $user->profile,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $profileData = $request->only([
            'first_name', 'last_name', 'phone_number', 'address',
            'gender', 'birth_date',
            'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone',
            'drivers_license_number', 'drivers_license_expiry',
        ]);

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        $this->storeProfileFiles($request, $profile, 'valid_id', 'valid_id');
        $this->storeProfileFiles($request, $profile, 'drivers_license_file', 'drivers_license');

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function deleteAttachment(Request $request, Attachment $attachment): RedirectResponse
    {
        $user = $request->user();

        if ($attachment->attachable_type !== 'App\Models\Profile' || $attachment->attachable_id !== $user->profile?->id) {
            abort(403);
        }

        Storage::disk('public')->delete(str_replace(asset('/storage/'), '', $attachment->file_path));
        $attachment->delete();

        return back()->with('status', 'profile-updated');
    }

    private function storeProfileFiles(ProfileUpdateRequest $request, $profile, string $inputKey, string $category): void
    {
        if (!$request->hasFile($inputKey)) {
            return;
        }

        foreach ($request->file($inputKey) as $file) {
            $fileName = strtoupper($category) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile-documents', $fileName, 'public');

            $attachment = new Attachment([
                'file_name'      => $fileName,
                'file_path'      => asset('/storage/' . $path),
                'file_type'      => $file->getMimeType(),
                'file_size'      => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension(),
                'category'       => $category,
            ]);

            $profile->attachments()->save($attachment);
        }
    }
}
