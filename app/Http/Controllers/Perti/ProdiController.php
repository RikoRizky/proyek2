<?php

namespace App\Http\Controllers\Perti;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Perti as PertiModel;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Perti mengelola akun Program Studi di bawah naungannya.
 */
class ProdiController extends Controller
{
    /**
     * Ambil profil Perti dari user yang sedang login.
     */
    private function getPertiProfile(): PertiModel
    {
        return auth()->user()->pertiProfile()
            ?? abort(403, 'Profil perti tidak ditemukan.');
    }

    public function index(): View
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403, 'Profil perti tidak ditemukan.');

        $prodis = Prodi::query()
            ->where('perti_id', $pertiProfile->id)
            ->with('user')
            ->orderBy('id')
            ->paginate(20);

        return view('perti.prodis.index', compact('prodis'));
    }

    public function create(): View|\Illuminate\Http\RedirectResponse
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $package = auth()->user()->effective_package;
        $prodiCount = Prodi::query()->where('perti_id', $pertiProfile->id)->count();
        $limit = $package === 'Starter' ? 3 : ($package === 'Pro' ? 10 : -1);

        if ($limit !== -1 && $prodiCount >= $limit) {
            return redirect()->route('perti.prodis.index')
                ->with('error', "Batas pembuatan akun Program Studi (maksimal {$limit}) tercapai untuk paket {$package} Anda.");
        }

        return view('perti.prodis.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $package = auth()->user()->effective_package;
        $prodiCount = Prodi::query()->where('perti_id', $pertiProfile->id)->count();
        $limit = $package === 'Starter' ? 3 : ($package === 'Pro' ? 10 : -1);

        if ($limit !== -1 && $prodiCount >= $limit) {
            return redirect()->route('perti.prodis.index')
                ->with('error', "Batas pembuatan akun Program Studi (maksimal {$limit}) tercapai untuk paket {$package} Anda.");
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::defaults()],
            'kode_prodi' => ['nullable', 'string', 'max:32'],
        ]);

        // Buat akun User untuk prodi
        $user = User::query()->create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'role'              => UserRole::Prodi,
            'email_verified_at' => now(),
        ]);

        // Buat profil Prodi dengan perti_id dari Perti yang login
        Prodi::query()->create([
            'user_id'    => $user->id,
            'perti_id'   => $pertiProfile->id,
            'kode_prodi' => $validated['kode_prodi'] ?? null,
        ]);

        return redirect()->route('perti.prodis.index')->with('status', 'Akun program studi berhasil dibuat.');
    }

    public function edit(string $id): View
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $prodi = Prodi::query()
            ->where('id', $id)
            ->where('perti_id', $pertiProfile->id)
            ->with('user')
            ->firstOrFail();

        return view('perti.prodis.edit', compact('prodi'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $prodi = Prodi::query()
            ->where('id', $id)
            ->where('perti_id', $pertiProfile->id)
            ->with('user')
            ->firstOrFail();

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $prodi->user_id],
            'password'   => ['nullable', 'confirmed', Password::defaults()],
            'kode_prodi' => ['nullable', 'string', 'max:32'],
        ]);

        $prodi->user->name  = $validated['name'];
        $prodi->user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $prodi->user->password = $validated['password'];
        }
        $prodi->user->save();

        $prodi->kode_prodi = $validated['kode_prodi'] ?? null;
        $prodi->save();

        return redirect()->route('perti.prodis.index')->with('status', 'Data program studi berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $pertiProfile = auth()->user()->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $prodi = Prodi::query()
            ->where('id', $id)
            ->where('perti_id', $pertiProfile->id)
            ->firstOrFail();

        // Hapus user → cascade hapus prodi record dan submissions
        $prodi->user()->delete();

        return redirect()->route('perti.prodis.index')->with('status', 'Akun program studi berhasil dihapus.');
    }
}
