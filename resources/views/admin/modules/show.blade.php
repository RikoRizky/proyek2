<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Detail Kriteria</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $module->name }}</h1>
            @if ($module->description)
                <p class="mt-1 text-sm text-slate-600 max-w-3xl">{{ $module->description }}</p>
            @endif
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm font-semibold text-emerald-900 flex items-center gap-2">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @php
        $docReqs = $module->requirements->filter(fn($r) => !$r->isTable());
        $tableReqs = $module->requirements->filter(fn($r) => $r->isTable());
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.modules.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-600 hover:text-violet-500 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke daftar kriteria
        </a>
        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'add-requirement')"
                class="ui-btn-primary inline-flex items-center gap-1.5 text-xs font-bold"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                + Tambah Butir / Tabel Baru
            </button>
            <a href="{{ route('admin.modules.edit', $module) }}" class="ui-btn-secondary text-xs">Edit Kriteria</a>
        </div>
    </div>

    {{-- Filter Tabs & Table Section --}}
    <div x-data="{ tabFilter: 'all' }" class="space-y-4">
        {{-- Filter Buttons --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-3">
            <button type="button" @click="tabFilter = 'all'"
                :class="tabFilter === 'all' ? 'border-violet-600 bg-violet-50/80 text-violet-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="inline-flex items-center gap-2 rounded-xl border px-3.5 py-2 text-xs transition">
                <span>Semua Butir</span>
                <span class="rounded-full bg-slate-200/70 px-2 py-0.5 text-[10px] font-bold text-slate-800">{{ $module->requirements->count() }}</span>
            </button>

            <button type="button" @click="tabFilter = 'doc'"
                :class="tabFilter === 'doc' ? 'border-violet-600 bg-violet-50/80 text-violet-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="inline-flex items-center gap-2 rounded-xl border px-3.5 py-2 text-xs transition">
                <span>📄 Dokumen LED</span>
                <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold text-violet-800">{{ $docReqs->count() }}</span>
            </button>

            <button type="button" @click="tabFilter = 'table'"
                :class="tabFilter === 'table' ? 'border-emerald-600 bg-emerald-50/80 text-emerald-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="inline-flex items-center gap-2 rounded-xl border px-3.5 py-2 text-xs transition">
                <span>📊 Tabel Kinerja LKPS</span>
                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">{{ $tableReqs->count() }}</span>
            </button>
        </div>

        {{-- Table Container --}}
        <div class="ui-card overflow-hidden">
            <div class="ui-section-header flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Butir &amp; Persyaratan Kriteria</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Semua butir ini akan otomatis muncul pada portal Program Studi dan Perguruan Tinggi.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">No Urut</th>
                            <th class="min-w-[240px]">Judul Butir / Syarat</th>
                            <th class="w-44">Tipe Pengisian</th>
                            <th>Deskripsi / Petunjuk</th>
                            <th class="text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($module->requirements as $req)
                            @php
                                $isTable = $req->isTable();
                                $itemType = $isTable ? 'table' : 'doc';
                            @endphp
                            <tr class="transition hover:bg-violet-50/25"
                                x-show="tabFilter === 'all' || tabFilter === '{{ $itemType }}'">
                                <td class="font-bold text-slate-400 text-center">{{ $req->sort_order ?? '-' }}</td>
                                <td>
                                    <p class="font-bold text-slate-900 text-sm">{{ $req->title }}</p>
                                    @if ($isTable && $req->table_code)
                                        <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">Kode Sheet LKPS: {{ $req->table_code }}</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($isTable)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-emerald-500/20">
                                            📊 Tabel {{ $req->table_code ?: 'LKPS' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-800 ring-1 ring-violet-500/20">
                                            📄 Dokumen LED
                                        </span>
                                    @endif
                                </td>
                                <td class="text-slate-600 text-xs leading-relaxed max-w-md">
                                    {{ $req->description ?? '-' }}
                                </td>
                                <td class="text-right space-x-2 text-xs font-semibold">
                                    <button
                                        type="button"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'edit-requirement-{{ $req->id }}')"
                                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700 hover:bg-violet-50 hover:text-violet-700 transition"
                                    >
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.modules.requirements.destroy', [$module, $req]) }}" method="post" class="inline" onsubmit="return confirm('Hapus butir &quot;{{ addslashes($req->title) }}&quot;? Semua data isian dan berkas prodi terkait butir ini akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 p-1" title="Hapus butir ini">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="ui-empty text-sm py-12 text-center text-slate-500">
                                    Belum ada butir persyaratan. Klik tombol <span class="font-bold text-violet-600">+ Tambah Butir / Tabel Baru</span> di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Danger Zone: Hapus Modul --}}
    <div class="mt-8 pt-4 border-t border-slate-200/80 flex items-center justify-between">
        <p class="text-xs text-slate-400">Menghapus kriteria ini akan menghapus semua butir di dalamnya.</p>
        <form method="post" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('PERINGATAN: Hapus kriteria {{ addslashes($module->name) }} beserta semua butir persyaratannya?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                Hapus Kriteria Ini
            </button>
        </form>
    </div>

    {{-- ── MODAL: TAMBAH BUTIR PERSYARATAN / TABEL ── --}}
    <x-modal name="add-requirement" :show="(isset($errors) && $errors->any()) && !session('edit_requirement_id')" focusable>
        <form method="post" action="{{ route('admin.modules.requirements.store', $module) }}" class="p-6">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-violet-600">Form Input Admin</span>
                    <h2 class="text-base font-bold text-slate-900 mt-0.5">Tambah Butir Persyaratan / Tabel Baru</h2>
                </div>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">×</button>
            </div>

            @include('admin.requirements._form', ['requirement' => null])

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')" class="ui-btn-secondary text-xs">Batal</button>
                <button type="submit" class="ui-btn-primary text-xs font-bold">Simpan &amp; Tambahkan</button>
            </div>
        </form>
    </x-modal>

    {{-- ── MODAL: EDIT BUTIR PERSYARATAN / TABEL ── --}}
    @foreach ($module->requirements as $req)
        <x-modal name="edit-requirement-{{ $req->id }}" :show="(isset($errors) && $errors->any()) && session('edit_requirement_id') == $req->id" focusable>
            <form method="post" action="{{ route('admin.modules.requirements.update', [$module, $req]) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-violet-600">Edit Butir</span>
                        <h2 class="text-base font-bold text-slate-900 mt-0.5">Edit Butir: {{ $req->title }}</h2>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">×</button>
                </div>

                @include('admin.requirements._form', ['requirement' => $req])

                <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" x-on:click="$dispatch('close')" class="ui-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="ui-btn-primary text-xs font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
