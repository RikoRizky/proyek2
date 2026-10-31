<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Perti;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * CRUD akun Program Studi (role=prodi) oleh Admin.
 */
class ProdiController extends Controller
{
    public function create(): View
    {
        $pertis = User::query()
            ->where('role', UserRole::Perti)
            ->with('pertiProfile')
            ->orderBy('name')
            ->get();

        return view('admin.prodis.create', compact('pertis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'perti_id'   => ['required', Rule::exists('pertis', 'id')],
            'kode_prodi' => ['nullable', 'string', 'max:32'],
        ]);

        $user = User::query()->create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'role'              => UserRole::Prodi,
            'email_verified_at' => now(),
        ]);

        Prodi::query()->create([
            'user_id'    => $user->id,
            'perti_id'   => $validated['perti_id'],
            'kode_prodi' => $validated['kode_prodi'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Akun program studi berhasil dibuat.');
    }

    public function edit(User $prodi): View
    {
        abort_unless($prodi->role === UserRole::Prodi, 404);
        $prodi->load('prodiProfile');

        $pertis = User::query()
            ->where('role', UserRole::Perti)
            ->with('pertiProfile')
            ->orderBy('name')
            ->get();

        return view('admin.prodis.edit', compact('prodi', 'pertis'));
    }

    public function update(Request $request, User $prodi): RedirectResponse
    {
        abort_unless($prodi->role === UserRole::Prodi, 404);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($prodi->id)],
            'password'   => ['nullable', 'confirmed', Password::defaults()],
            'perti_id'   => ['required', Rule::exists('pertis', 'id')],
            'kode_prodi' => ['nullable', 'string', 'max:32'],
        ]);

        $prodi->name  = $validated['name'];
        $prodi->email = $validated['email'];
        if (!empty($validated['password'])) {
            $prodi->password = $validated['password'];
        }
        $prodi->save();

        $prodi->prodiProfile()->updateOrCreate(
            ['user_id' => $prodi->id],
            [
                'perti_id'   => $validated['perti_id'],
                'kode_prodi' => $validated['kode_prodi'] ?? null,
            ]
        );

        return redirect()->route('admin.users.index')->with('status', 'Data program studi berhasil diperbarui.');
    }

    public function destroy(User $prodi): RedirectResponse
    {
        abort_unless($prodi->role === UserRole::Prodi, 404);
        $prodi->delete();

        return redirect()->route('admin.users.index')->with('status', 'Akun program studi berhasil dihapus.');
    }
}
