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
 * Halaman utama manajemen akun di panel Admin.
 * Menampilkan 3 tabel: Admin, Perti, Prodi.
 * CRUD akun Admin ada di sini; CRUD Perti ada di PertiController.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));

        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->when($search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->get();

        $pertis = User::query()
            ->where('role', UserRole::Perti)
            ->with('pertiProfile')
            ->when($search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate(10, ['*'], 'perti_page')
            ->withQueryString();

        $prodis = User::query()
            ->select('users.*')
            ->where('users.role', UserRole::Prodi)
            ->with(['prodiProfile.perti.user'])
            ->leftJoin('prodis', 'users.id', '=', 'prodis.user_id')
            ->leftJoin('pertis', 'prodis.perti_id', '=', 'pertis.id')
            ->leftJoin('users as perti_users', 'pertis.user_id', '=', 'perti_users.id')
            ->when($search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('users.name', 'like', "%{$search}%")
                   ->orWhere('users.email', 'like', "%{$search}%")
            ))
            ->orderBy('perti_users.name')
            ->orderBy('users.name')
            ->paginate(20, ['users.*'], 'prodi_page')
            ->withQueryString();

        return view('admin.users.index', compact('admins', 'pertis', 'prodis', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::query()->create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => $validated['password'],
            'role'              => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Akun administrator berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->role === UserRole::Admin, 404);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Admin, 404);

        // Cegah hapus admin terakhir via role change (tidak berlaku di sini karena edit hanya admin)
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Data administrator diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->withErrors(['delete' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        if ($user->role === UserRole::Admin && User::query()->where('role', UserRole::Admin)->count() === 1) {
            return redirect()->route('admin.users.index')->withErrors(['delete' => 'Tidak dapat menghapus administrator terakhir.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Akun dihapus.');
    }
}
