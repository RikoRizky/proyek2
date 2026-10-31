<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Perti;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * CRUD akun Perguruan Tinggi (role=perti) + profil tabel pertis.
 * Hanya bisa diakses oleh Admin.
 */
class PertiController extends Controller
{
    public function index(): View
    {
        $pertis = User::query()
            ->where('role', UserRole::Perti)
            ->with('pertiProfile')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.pertis.index', compact('pertis'));
    }

    public function create(): View
    {
        return view('admin.pertis.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Password::defaults()],
            'kode_pt'               => ['nullable', 'string', 'max:32'],
            'alamat'                => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::query()->create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'role'              => UserRole::Perti,
            'email_verified_at' => now(),
        ]);

        $user->pertiProfile()->create([
            'kode_pt' => $validated['kode_pt'] ?? null,
            'alamat'  => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Akun Perguruan Tinggi berhasil dibuat.');
    }

    public function edit(User $perti): View
    {
        abort_unless($perti->role === UserRole::Perti, 404);

        $perti->load('pertiProfile');

        return view('admin.pertis.edit', compact('perti'));
    }

    public function update(Request $request, User $perti): RedirectResponse
    {
        abort_unless($perti->role === UserRole::Perti, 404);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($perti->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'kode_pt'  => ['nullable', 'string', 'max:32'],
            'alamat'   => ['nullable', 'string', 'max:500'],
        ]);

        $perti->name  = $validated['name'];
        $perti->email = $validated['email'];
        if (!empty($validated['password'])) {
            $perti->password = $validated['password'];
        }
        $perti->save();

        $perti->pertiProfile()->updateOrCreate(
            ['user_id' => $perti->id],
            [
                'kode_pt' => $validated['kode_pt'] ?? null,
                'alamat'  => $validated['alamat'] ?? null,
            ]
        );

        return redirect()->route('admin.users.index')->with('status', 'Data Perguruan Tinggi berhasil diperbarui.');
    }

    public function destroy(User $perti): RedirectResponse
    {
        abort_unless($perti->role === UserRole::Perti, 404);

        // Hapus user → cascade ke pertis → cascade ke prodis
        $perti->delete();

        return redirect()->route('admin.users.index')->with('status', 'Akun Perguruan Tinggi dihapus.');
    }
}
