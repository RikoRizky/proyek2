<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Manajemen Akun</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Daftar Pengguna</h1>
                <p class="mt-1 text-sm text-slate-600">Kelola semua akun: Administrator, Perguruan Tinggi, dan Program Studi.</p>
            </div>
        </div>
    </x-slot>

    @error('delete')
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
    @enderror

    {{-- ── SEARCH BAR ── --}}
    <div class="mb-8">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau email akun (Admin, Perti, Prodi)..."
                    class="w-full rounded-xl border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-slate-200 focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400"
                >
            </div>
            <button type="submit" class="ui-btn-primary px-5 py-2.5 text-sm">Cari</button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" class="ui-btn-secondary px-5 py-2.5 text-sm">Reset</a>
            @endif
        </form>
        @if($search)
            <p class="mt-2 text-xs text-slate-500">
                Menampilkan hasil pencarian untuk: <span class="font-semibold text-violet-700">"{{ $search }}"</span>
            </p>
        @endif
    </div>

    {{-- ── SECTION 1: ADMINISTRATOR ── --}}
    <div class="mb-10">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Administrator</h2>
                <p class="text-xs text-slate-500">Akun dengan akses penuh ke seluruh sistem.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="ui-btn-primary shrink-0">+ Tambah Admin</a>
        </div>
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $u)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $u->name }}</td>
                            <td class="text-slate-600">{{ $u->email }}</td>
                            <td class="text-right text-sm font-semibold">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                                @if ($u->id !== auth()->id())
                                    <span class="mx-2 text-slate-300">|</span>
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="post" class="inline" onsubmit="return confirm('Hapus akun administrator ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-sm text-slate-500">
                                @if($search) Tidak ada admin yang cocok dengan "{{ $search }}". @else Tidak ada data. @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── SECTION 2: PERGURUAN TINGGI ── --}}
    <div class="mb-10">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Perguruan Tinggi</h2>
                <p class="text-xs text-slate-500">Akun institusi; setiap perti dapat membuat akun program studi sendiri.</p>
            </div>
            <a href="{{ route('admin.pertis.create') }}" class="ui-btn-primary shrink-0">+ Tambah Perti</a>
        </div>
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Institusi</th>
                        <th>Email</th>
                        <th>Paket</th>
                        <th>Kode PT</th>
                        <th>Jml. Prodi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pertis as $u)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $u->name }}</td>
                            <td class="text-slate-600">{{ $u->email }}</td>
                            <td class="text-slate-500 text-sm">
                                @if($u->active_package === 'Enterprise')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-violet-100 text-violet-700 border border-violet-200">Enterprise</span>
                                @elseif($u->active_package === 'Pro')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">Pro</span>
                                @elseif($u->active_package === 'Starter')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">Starter</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-50 text-slate-400 border border-slate-100">Free</span>
                                @endif
                            </td>
                            <td class="text-slate-500 text-sm">{{ $u->pertiProfile?->kode_pt ?? '-' }}</td>
                            <td class="text-slate-700 font-medium">{{ $u->pertiProfile?->prodis->count() ?? 0 }}</td>
                            <td class="text-right text-sm font-semibold">
                                <a href="{{ route('admin.pertis.edit', $u) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                                <span class="mx-2 text-slate-300">|</span>
                                <form action="{{ route('admin.pertis.destroy', $u) }}" method="post" class="inline" onsubmit="return confirm('Hapus akun perti ini? Semua program studi di bawahnya juga akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-sm text-slate-500">
                                @if($search) Tidak ada perguruan tinggi yang cocok dengan "{{ $search }}". @else Belum ada akun Perguruan Tinggi. Buat dengan tombol di atas. @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($pertis->hasPages())
                <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        Halaman <span class="font-semibold text-slate-700">{{ $pertis->currentPage() }}</span>
                        dari <span class="font-semibold text-slate-700">{{ $pertis->lastPage() }}</span>
                        &mdash; <span class="font-semibold text-slate-700">{{ $pertis->total() }}</span> total perti
                    </p>
                    <div>{{ $pertis->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── SECTION 3: PROGRAM STUDI ── --}}
    <div>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Program Studi</h2>
                <p class="text-xs text-slate-500">Akun program studi dibuat oleh universitas atau oleh administrator.</p>
            </div>
            <a href="{{ route('admin.prodis.create') }}" class="ui-btn-primary shrink-0">+ Tambah Prodi</a>
        </div>
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Program Studi</th>
                        <th>Email</th>
                        <th>Kode Prodi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentPerti = null; @endphp
                    @forelse ($prodis as $u)
                        @php $pertiName = $u->prodiProfile?->perti?->user?->name ?? 'Tanpa Institusi / Independen'; @endphp
                        @if($currentPerti !== $pertiName)
                            <tr class="bg-slate-50/80 border-y border-slate-100">
                                <td colspan="4" class="px-4 py-2.5 text-xs font-black text-slate-600 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $pertiName }}
                                    </div>
                                </td>
                            </tr>
                            @php $currentPerti = $pertiName; @endphp
                        @endif
                        <tr class="hover:bg-slate-50/50">
                            <td class="font-semibold text-slate-900 pl-6">{{ $u->name }}</td>
                            <td class="text-slate-600">{{ $u->email }}</td>
                            <td class="text-slate-500 text-sm">{{ $u->prodiProfile?->kode_prodi ?? '-' }}</td>
                            <td class="text-right text-sm font-semibold">
                                <a href="{{ route('admin.prodis.edit', $u) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                                <span class="mx-2 text-slate-300">|</span>
                                <form action="{{ route('admin.prodis.destroy', $u) }}" method="post" class="inline" onsubmit="return confirm('Hapus akun program studi ini? Semua dokumen unggahan mereka juga akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-sm text-slate-500">
                                @if($search) Tidak ada program studi yang cocok dengan "{{ $search }}". @else Belum ada akun Program Studi. Perti yang login dapat membuat akun prodi. @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($prodis->hasPages())
                <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        Halaman <span class="font-semibold text-slate-700">{{ $prodis->currentPage() }}</span>
                        dari <span class="font-semibold text-slate-700">{{ $prodis->lastPage() }}</span>
                        &mdash; <span class="font-semibold text-slate-700">{{ $prodis->total() }}</span> total prodi
                    </p>
                    <div>{{ $prodis->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
