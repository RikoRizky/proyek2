<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Manajemen Akun</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Perguruan Tinggi</h1>
                <p class="mt-1 text-sm text-slate-600">Daftar akun Perguruan Tinggi yang terdaftar dalam sistem.</p>
            </div>
            <a href="{{ route('admin.pertis.create') }}" class="ui-btn-primary shrink-0">+ Tambah Perti</a>
        </div>
    </x-slot>



    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama Institusi</th>
                    <th>Email Login</th>
                    <th>Kode PT</th>
                    <th>Alamat</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pertis as $u)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $u->name }}</td>
                        <td class="text-slate-600">{{ $u->email }}</td>
                        <td class="text-slate-500 text-sm">{{ $u->pertiProfile?->kode_pt ?? '-' }}</td>
                        <td class="text-slate-500 text-sm max-w-xs truncate">{{ $u->pertiProfile?->alamat ?? '-' }}</td>
                        <td class="text-right text-sm font-semibold">
                            <a href="{{ route('admin.pertis.edit', $u) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                            <span class="mx-2 text-slate-300">|</span>
                            <form action="{{ route('admin.pertis.destroy', $u) }}" method="post" class="inline"
                                onsubmit="return confirm('Hapus akun Perguruan Tinggi ini? Semua program studi di bawahnya juga akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-slate-500">
                            Belum ada akun Perguruan Tinggi. Klik tombol <strong>+ Tambah Perti</strong> untuk menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($pertis->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">{{ $pertis->links() }}</div>
        @endif
    </div>
</x-app-layout>
