<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kelola Program Studi</h1>
            <p class="mt-1 text-sm text-slate-600">Daftar program studi di bawah naungan {{ auth()->user()->name }}.</p>
        </div>
    </x-slot>

    <div class="mb-6 flex justify-end">
        <a href="{{ route('perti.prodis.create') }}" class="ui-btn-primary">+ Buat akun prodi</a>
    </div>



    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama Program Studi</th>
                    <th>Email Login</th>
                    <th>Kode Prodi</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prodis as $p)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $p->name }}</td>
                        <td class="text-slate-600">{{ $p->email }}</td>
                        <td class="text-slate-500 text-sm">{{ $p->kode_prodi ?? '-' }}</td>
                        <td class="text-right text-sm font-semibold">
                            <a href="{{ route('perti.prodis.edit', $p->id) }}" class="text-violet-600 hover:text-violet-500">Edit</a>
                            <span class="mx-2 text-slate-300">|</span>
                            <form action="{{ route('perti.prodis.destroy', $p->id) }}" method="post" class="inline"
                                onsubmit="return confirm('Hapus akun program studi ini? Semua dokumen unggahan mereka juga akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-slate-500">
                            Belum ada program studi yang dibuat. Klik tombol <strong>+ Buat akun prodi</strong> untuk menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($prodis->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">{{ $prodis->links() }}</div>
        @endif
    </div>
</x-app-layout>
