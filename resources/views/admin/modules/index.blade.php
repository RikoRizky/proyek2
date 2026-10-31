<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Manajemen Kriteria & Modul</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kriteria &amp; Instrumen Akreditasi</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola modul kriteria akreditasi beserta butir dokumen evaluasi diri (LED) dan tabel kinerja (LKPS).</p>
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

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <p class="text-xs text-slate-500 font-medium">
            Total <strong>{{ $modules->total() }}</strong> kriteria terdaftar dalam instrumen.
        </p>
        <a href="{{ route('admin.modules.create') }}" class="ui-btn-primary inline-flex items-center gap-2 text-xs font-bold">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            + Tambah Kriteria Baru
        </a>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th class="w-16 text-center">Urutan</th>
                    <th>Nama Kriteria / Modul</th>
                    <th>Komposisi Isian</th>
                    <th>Total Butir</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($modules as $module)
                    @php
                        $docCount = $module->requirements->filter(fn($r) => !$r->isTable())->count();
                        $tableCount = $module->requirements->filter(fn($r) => $r->isTable())->count();
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="text-center font-bold text-slate-400">
                            {{ $module->sort_order ?? '-' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.modules.show', $module) }}" class="font-bold text-slate-900 hover:text-violet-600 transition block">
                                {{ $module->name }}
                            </a>
                            @if ($module->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $module->description }}</p>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 rounded-md bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-800 ring-1 ring-violet-500/20" title="Butir Dokumen LED (PPEPP)">
                                    📄 {{ $docCount }} Dokumen LED
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-800 ring-1 ring-emerald-500/20" title="Tabel Kinerja LKPS">
                                    📊 {{ $tableCount }} Tabel LKPS
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">
                                {{ $module->requirements_count }} butir
                            </span>
                        </td>
                        <td class="text-right space-x-2">
                            <a href="{{ route('admin.modules.show', $module) }}" class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700 hover:bg-violet-100 transition shadow-xs">
                                Kelola Isi &amp; Tabel →
                            </a>
                            <a href="{{ route('admin.modules.edit', $module) }}" class="font-semibold text-xs text-slate-600 hover:text-slate-900">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            Belum ada kriteria yang dibuat. Klik tombol <span class="font-bold text-violet-600">+ Tambah Kriteria Baru</span> di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">{{ $modules->links() }}</div>
    </div>
</x-app-layout>
