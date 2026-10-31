<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Modul</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $module->name }}</h1>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.modules.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-600 hover:text-violet-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke modul
        </a>
        <div class="flex flex-wrap gap-2">
            <button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'add-requirement')"
                class="ui-btn-primary text-sm"
            >
                + Tambah Persyaratan
            </button>
            <a href="{{ route('admin.modules.edit', $module) }}" class="ui-btn-secondary text-sm">Edit modul</a>
        </div>
    </div>



    @if ($module->description)
        <p class="mb-6 text-slate-600">{{ $module->description }}</p>
    @endif

    <div class="ui-card overflow-hidden">
        <div class="ui-section-header">
            <h2 class="text-lg font-bold text-slate-900">Daftar persyaratan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th class="w-16">No Urut</th>
                        <th>Judul Syarat</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($module->requirements as $req)
                        <tr class="transition hover:bg-violet-50/25">
                            <td class="font-bold text-slate-400 text-center">{{ $req->sort_order ?? '-' }}</td>
                            <td class="font-semibold text-slate-900">{{ $req->title }}</td>
                            <td class="text-slate-600 text-sm max-w-md truncate">{{ $req->description ?? '-' }}</td>
                            <td class="text-right space-x-2 text-sm font-semibold">
                                <button
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'edit-requirement-{{ $req->id }}')"
                                    class="text-violet-600 hover:text-violet-500 inline-block"
                                >
                                    Edit
                                </button>
                                <span class="text-slate-300">|</span>
                                <form action="{{ route('admin.modules.requirements.destroy', [$module, $req]) }}" method="post" class="inline" onsubmit="return confirm('Hapus persyaratan ini? Semua berkas yang diunggah oleh Prodi untuk syarat ini juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="ui-empty text-sm py-8 text-center text-slate-500">Belum ada persyaratan. Klik "+ Tambah Persyaratan" untuk menambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <form method="post" action="{{ route('admin.modules.destroy', $module) }}" class="mt-8" onsubmit="return confirm('Hapus modul ini beserta persyaratannya?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">Hapus modul</button>
    </form>

    {{-- ── MODAL: TAMBAH PERSYARATAN ── --}}
    <x-modal name="add-requirement" :show="$errors->any() && !session('edit_requirement_id')" focusable>
        <form method="post" action="{{ route('admin.modules.requirements.store', $module) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-slate-900 mb-4">Tambah Persyaratan Baru</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Judul Syarat</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="ui-input mt-2" placeholder="Contoh: Rencana Strategis (Renstra)...">
                    @error('title')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                    <textarea name="description" class="ui-input mt-2 h-24" placeholder="Deskripsi atau kriteria detail berkas...">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Urutan Tampilan <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', ($module->requirements->max('sort_order') ?? 0) + 1) }}" class="ui-input mt-2" placeholder="Contoh: 1">
                    @error('sort_order')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="ui-btn-secondary">Batal</button>
                <button type="submit" class="ui-btn-primary">Tambah</button>
            </div>
        </form>
    </x-modal>

    {{-- ── MODAL: EDIT PERSYARATAN ── --}}
    @foreach ($module->requirements as $req)
        <x-modal name="edit-requirement-{{ $req->id }}" :show="$errors->any() && session('edit_requirement_id') == $req->id" focusable>
            <form method="post" action="{{ route('admin.modules.requirements.update', [$module, $req]) }}" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Persyaratan</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Judul Syarat</label>
                        <input type="text" name="title" value="{{ old('title', $req->title) }}" required class="ui-input mt-2">
                        @error('title')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                        <textarea name="description" class="ui-input mt-2 h-24">{{ old('description', $req->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Urutan Tampilan <span class="font-normal text-slate-400">(opsional)</span></label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $req->sort_order) }}" class="ui-input mt-2">
                        @error('sort_order')<p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="ui-btn-secondary">Batal</button>
                    <button type="submit" class="ui-btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>