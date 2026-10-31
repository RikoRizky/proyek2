<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();

            // Create directory if not exists
            if (!file_exists(public_path('uploads/profile_photos'))) {
                mkdir(public_path('uploads/profile_photos'), 0755, true);
            }

            // Delete old profile photo if exists
            if ($user->profile_photo_path && file_exists(public_path('uploads/profile_photos/' . $user->profile_photo_path))) {
                @unlink(public_path('uploads/profile_photos/' . $user->profile_photo_path));
            }

            // Move new file to public/uploads/profile_photos
            $file->move(public_path('uploads/profile_photos'), $filename);

            // Save filename in database
            $user->profile_photo_path = $filename;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort(403, 'Penghapusan akun hanya dapat dilakukan oleh administrator melalui panel admin.');
    }
}
