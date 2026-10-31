<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PertiRegistrationController extends Controller
{
    public function showForm($token)
    {
        $transaction = \App\Models\Transaction::where('registration_token', $token)
            ->where('status', 'success')
            ->where('is_registered', false)
            ->firstOrFail();

        return view('checkout.register_perti', compact('transaction'));
    }

    public function process(Request $request, $token)
    {
        $transaction = \App\Models\Transaction::where('registration_token', $token)
            ->where('status', 'success')
            ->where('is_registered', false)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'kode_pt' => 'nullable|string|max:32',
            'alamat' => 'nullable|string|max:500',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Auto-hashed by User model
            'role' => \App\Enums\UserRole::Perti,
            'email_verified_at' => now(),
            'active_package' => $transaction->package_name,
            'package_valid_until' => now()->addYear(),
        ]);

        $user->pertiProfile()->create([
            'kode_pt' => $validated['kode_pt'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        $transaction->update(['is_registered' => true]);

        // Auto login
        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Pendaftaran Akun Perguruan Tinggi Berhasil!');
    }
}
