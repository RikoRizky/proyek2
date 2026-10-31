<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        if ($request->has('registration_token')) {
            session(['registration_token' => $request->registration_token]);
        }
        
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login menggunakan Google. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        // Cek apakah ini mode registrasi dari checkout
        $registrationToken = session('registration_token');
        session()->forget('registration_token'); // hapus dari sesi setelah dibaca

        if ($registrationToken) {
            // Mode Registrasi Perti
            $transaction = Transaction::where('registration_token', $registrationToken)
                ->where('status', 'success')
                ->where('is_registered', false)
                ->first();

            if (!$transaction) {
                return redirect()->route('login')->with('error', 'Sesi pendaftaran tidak valid atau sudah kadaluarsa.');
            }

            if ($user) {
                // Email sudah terdaftar, cukup update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                // Jika mereka menggunakan email yg sama untuk daftar paket baru?
                // Biasanya tidak bisa register dengan email yang sudah ada, karena validasi email di PertiRegistrationController mencegah ini ('unique:users'). 
                // Jika ingin lanjut pendaftaran, atau mengarahkan agar login.
                return redirect()->route('login')->with('error', 'Email ini sudah terdaftar. Silakan login langsung lalu lakukan perpanjangan dari dalam dashboard.');
            }

            // Buat User Baru
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => null, // Password kosong karena via Google
                'google_id' => $googleUser->getId(),
                'role' => \App\Enums\UserRole::Perti,
                'email_verified_at' => now(),
                'active_package' => $transaction->package_name,
                'package_valid_until' => now()->addYear(), // Atau sesuai duration_years jika ada
            ]);

            $user->pertiProfile()->create([
                // field ini akan kosong, mereka bisa melengkapinya nanti di profile
                'kode_pt' => null,
                'alamat' => null,
            ]);

            $transaction->update(['is_registered' => true]);

            Auth::login($user);
            return redirect()->route('dashboard')->with('status', 'Pendaftaran Akun Perguruan Tinggi via Google Berhasil!');
        }

        // Mode Login Biasa
        if ($user) {
            // Update google_id jika belum ada (misal sebelumnya daftar manual, lalu sekarang login google)
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        // Email tidak ada di database & tidak ada token registrasi
        return redirect()->route('login')->with('error', 'Email Anda belum terdaftar. Silakan lakukan pendaftaran atau berlangganan terlebih dahulu.');
    }
}
