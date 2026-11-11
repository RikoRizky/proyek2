@props([
    'isPerti' => false,
    'items' => collect(),
    'moduleOptions' => collect(),
    'archiveCategories' => [],
    'initialQuery' => '',
    'prodis' => collect(),
    'selectedProdiId' => 'all',
])

@php
    $routePrefix = $isPerti ? 'perti' : 'unit';
@endphp

<div
    x-data="unifiedSearchApp({
        items: @js($items),
        initialQuery: @js($initialQuery),
        isPerti: @js($isPerti),
    })"
    class="space-y-6"
>
    {{-- Panel Filter & Pencarian Utama --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm">
        {{-- Input Search Bar Utama --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-5 w-5 text-violet-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input
                    id="unified-search-input"
                    type="text"
                    x-model="searchQuery"
                    placeholder="Ketik kriteria, nama file, data mahasiswa, data nilai, SK, atau kode tabel (mis. 'Kriteria 1', 'mahasiswa', '1.A.1')..."
                    style="padding-left: 3.25rem !important; padding-right: 2.75rem !important;"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/60 py-3.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-violet-500/10 transition"
                    autofocus
                />
                <button
                    type="button"
                    x-show="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600"
                    title="Hapus pencarian"
                    style="display: none;"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <a href="{{ route($routePrefix . '.archives.index') }}"
               class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3.5 text-xs font-bold text-violet-700 transition hover:bg-violet-600 hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Arsip / Tambah Dokumen</span>
            </a>
        </div>

        {{-- Saran Pencarian Cepat (Quick Chips) --}}
        <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs">
            <span class="font-semibold text-slate-500 mr-1">Contoh cari cepat:</span>
            <button type="button" @click="setQuery('mahasiswa')"
                :class="searchQuery.toLowerCase().includes('mahasiswa') ? 'bg-sky-500 text-white shadow-sm font-bold' : 'bg-sky-50 text-sky-800 hover:bg-violet-100 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Data Mahasiswa
            </button>
            <button type="button" @click="setQuery('nilai')"
                :class="searchQuery.toLowerCase().includes('nilai') ? 'bg-sky-500 text-white shadow-sm font-bold' : 'bg-sky-50 text-sky-800 hover:bg-violet-100 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Data Nilai
            </button>
            <button type="button" @click="toggleType('archive')"
                :class="selectedType === 'archive' ? 'bg-violet-600 text-white shadow-sm font-bold' : 'bg-violet-50 text-violet-700 hover:bg-violet-100 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Arsip Tambahan
            </button>
            <button type="button" @click="setQuery('Kriteria 1')"
                :class="searchQuery.toLowerCase().includes('kriteria 1') ? 'bg-violet-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Kriteria 1
            </button>
            <button type="button" @click="setQuery('Kriteria 2')"
                :class="searchQuery.toLowerCase().includes('kriteria 2') ? 'bg-violet-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Kriteria 2
            </button>
            <button type="button" @click="setQuery('[PELAKSANAAN] Penerapan Tata Kelola & Kegiatan SPMI')"
                :class="searchQuery.includes('[PELAKSANAAN]') ? 'bg-violet-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                [PELAKSANAAN] Penerapan Tata Kelola
            </button>
            <button type="button" @click="setQuery('Tabel 1.A.1')"
                :class="searchQuery.toLowerCase().includes('1.a.1') ? 'bg-violet-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Tabel 1.A.1
            </button>
            <button type="button" @click="toggleStatus('revision')"
                :class="selectedStatus === 'revision' ? 'bg-rose-600 text-white shadow-sm font-bold ring-2 ring-rose-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 font-medium'"
                class="rounded-lg px-2.5 py-1 transition">
                Perlu Revisi
            </button>
            @if ($isPerti)
                <button type="button" @click="toggleStatus('uploaded')"
                    :class="selectedStatus === 'uploaded' ? 'bg-amber-500 text-white shadow-sm font-bold ring-2 ring-amber-200' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    Perlu Divalidasi
                </button>
            @endif
        </div>

        {{-- Baris Filter Tabs --}}
        <div class="mt-5 grid grid-cols-1 gap-3 pt-4 border-t border-slate-100 sm:grid-cols-2 {{ $isPerti ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }}">
            @if ($isPerti)
                {{-- Filter Program Studi --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Program Studi</label>
                    <select
                        name="prodi_id"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-semibold text-slate-800 focus:border-violet-500 focus:ring-1 focus:ring-violet-500"
                        onchange="window.location.href = '{{ route('perti.search') }}?prodi_id=' + this.value + (document.getElementById('unified-search-input')?.value ? '&q=' + encodeURIComponent(document.getElementById('unified-search-input').value) : '')"
                    >
                        <option value="all" {{ $selectedProdiId === 'all' ? 'selected' : '' }}>Semua Prodi &amp; PT</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ (string) $selectedProdiId === (string) $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Filter Tipe --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tipe Dokumen</label>
                <select x-model="selectedType" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                    <option value="all">Semua (LED, LKPS &amp; Arsip)</option>
                    <option value="led">Dokumen LED (PPEPP)</option>
                    <option value="lkps">Tabel Kinerja LKPS</option>
                    <option value="archive">Arsip &amp; Dokumen Tambahan</option>
                </select>
            </div>

            {{-- Filter Kriteria / Modul --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kriteria / Modul</label>
                <select x-model="selectedModule" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                    <option value="all">Semua Kriteria</option>
                    @foreach ($moduleOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori Arsip --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kategori Arsip</label>
                <select x-model="selectedArchiveCategory" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                    <option value="all">Semua Kategori Arsip</option>
                    @foreach ($archiveCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    {{ $isPerti ? 'Status Validasi' : 'Status Unggahan' }}
                </label>
                <select x-model="selectedStatus" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                    <option value="all">Semua Status</option>
                    @if ($isPerti)
                        <option value="uploaded">Menunggu Validasi</option>
                        <option value="approved">Sesuai (Disetujui)</option>
                    @else
                        <option value="approved">Sesuai (Disetujui)</option>
                        <option value="uploaded">Menunggu Validasi</option>
                    @endif
                    <option value="revision">Perlu Revisi</option>
                    <option value="archive">Dokumen Arsip Tersedia</option>
                    <option value="unsubmitted">Belum Diunggah / Belum Diisi</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Ringkasan Hasil Pencarian --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-1 text-xs text-slate-600">
        <div>
            <span>Ditemukan </span>
            <span class="font-bold text-slate-900" x-text="filteredItems.length"></span>
            <span> hasil dari total </span>
            <span class="font-bold text-slate-900" x-text="items.length"></span>
            <span> data</span>
            <template x-if="hasActiveFilters">
                <button type="button" @click="resetFilters()" class="ml-2 font-semibold text-violet-600 hover:text-violet-800 underline">
                    Reset filter
                </button>
            </template>
        </div>
        <div class="flex flex-wrap items-center gap-2 font-medium">
            <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-sky-800 ring-1 ring-sky-200">
                <span class="font-bold" x-text="items.filter(i => i.is_archive).length"></span> Arsip Tambahan
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-700 ring-1 ring-emerald-200">
                <span class="font-bold" x-text="items.filter(i => i.status === 'approved').length"></span> Sesuai
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-800 ring-1 ring-rose-200">
                <span class="font-bold" x-text="items.filter(i => i.status === 'revision').length"></span> Perlu Revisi
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-800 ring-1 ring-amber-200">
                <span class="font-bold" x-text="items.filter(i => ['uploaded', 'submitted'].includes(i.status)).length"></span> Menunggu
            </span>
        </div>
    </div>

    {{-- Daftar Hasil Kartu --}}
    <div class="space-y-3">
        <template x-for="item in filteredItems" :key="item.unique_key">
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:border-violet-400 hover:shadow-md">
                <div
                    class="absolute inset-y-0 left-0 w-1.5"
                    :class="{
                        'bg-emerald-500': item.status === 'approved',
                        'bg-rose-500': item.status === 'revision',
                        'bg-amber-500': item.status === 'uploaded',
                        'bg-sky-500': item.is_archive,
                        'bg-violet-400': item.status === 'unsubmitted' && item.type_key === 'led',
                        'bg-indigo-400': item.status === 'unsubmitted' && item.type_key === 'lkps'
                    }"
                ></div>

                <div class="pl-3">
                    {{-- Header Kartu --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($isPerti)
                                <template x-if="item.prodi_name">
                                    <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-0.5 text-[11px] font-bold shadow-sm"
                                          :class="item.prodi_id === 'shared' ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-white'">
                                        <span x-text="item.prodi_name"></span>
                                    </span>
                                </template>
                            @endif

                            {{-- Badge Kriteria / Kategori --}}
                            <template x-if="item.is_archive">
                                <span class="inline-flex items-center rounded-lg bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-800 ring-1 ring-sky-200"
                                      x-text="item.category"></span>
                            </template>
                            <template x-if="!item.is_archive">
                                <span class="inline-flex items-center rounded-lg bg-violet-100 px-2.5 py-0.5 text-[11px] font-bold text-violet-800"
                                      x-text="item.module_short"></span>
                            </template>

                            {{-- Badge Tipe / Tahun Akademik --}}
                            <template x-if="item.is_table">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200">
                                    <span x-text="'Tabel ' + item.table_code"></span>
                                </span>
                            </template>
                            <template x-if="!item.is_table && !item.is_archive">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">
                                    Dokumen LED
                                </span>
                            </template>
                            <template x-if="item.is_archive">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-2.5 py-0.5 text-[11px] font-bold text-violet-700 ring-1 ring-violet-200">
                                    Arsip Tambahan
                                </span>
                            </template>
                            <template x-if="item.is_archive && item.academic_year">
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700"
                                      x-text="item.academic_year"></span>
                            </template>
                            <template x-if="item.is_archive && item.table_code">
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200"
                                      x-text="'Tabel ' + item.table_code"></span>
                            </template>

                            <span class="text-xs text-slate-400 font-medium truncate max-w-xs" x-text="item.module_name"></span>
                        </div>

                        {{-- Status Badge --}}
                        <div>
                            <template x-if="item.status === 'approved'">
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                                    <span>Sesuai</span>
                                </span>
                            </template>
                            <template x-if="item.status === 'revision'">
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">
                                    <span>Perlu Revisi</span>
                                </span>
                            </template>
                            <template x-if="item.status === 'uploaded'">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 ring-1 ring-amber-200">
                                    <span>Menunggu Validasi</span>
                                </span>
                            </template>
                            <template x-if="item.status === 'archive'">
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">
                                    <span>Tersedia</span>
                                </span>
                            </template>
                            <template x-if="item.status === 'unsubmitted'">
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    <span x-text="item.status_label"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Judul & Deskripsi --}}
                    <div class="mt-2.5">
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-violet-900 transition" x-text="item.title"></h3>
                        <template x-if="item.description">
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed" x-text="item.description"></p>
                        </template>
                    </div>

                    {{-- Direct Files List (Cek & Unduh Langsung) --}}
                    <template x-if="item.files && item.files.length > 0">
                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <template x-for="file in item.files" :key="item.unique_key + '-f-' + file.index">
                                <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-2">
                                    <div class="min-w-0 flex items-center gap-2 flex-1">
                                        <span class="shrink-0 rounded-md px-1.5 py-0.5 text-[10px] font-extrabold uppercase"
                                              :class="{
                                                  'bg-rose-100 text-rose-700': file.preview_kind === 'pdf',
                                                  'bg-emerald-100 text-emerald-700': file.preview_kind === 'spreadsheet',
                                                  'bg-sky-50 text-sky-700 ring-1 ring-sky-200': file.preview_kind === 'image',
                                                  'bg-amber-100 text-amber-800': file.preview_kind === 'download_only'
                                              }"
                                              x-text="file.ext"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-semibold text-slate-800" x-text="file.filename"></p>
                                            <p class="text-[10px] text-slate-400" x-text="file.size_label"></p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <a :href="file.view_url"
                                           class="rounded-lg bg-violet-600 px-2.5 py-1 text-[11px] font-bold text-white transition hover:bg-violet-500">
                                            Cek
                                        </a>
                                        <a :href="file.download_url"
                                           class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-100">
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Drive Links --}}
                    <template x-if="item.drive_links && item.drive_links.length > 0">
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <template x-for="(dl, dIdx) in item.drive_links" :key="item.unique_key + '-dl-' + dIdx">
                                <a :href="dl.url" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-violet-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-violet-100">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                    <span x-text="dl.name"></span>
                                </a>
                            </template>
                        </div>
                    </template>

                    {{-- Detail Baris LKPS --}}
                    <template x-if="item.is_table && item.row_count > 0">
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                            <span class="inline-flex items-center gap-1 font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-md">
                                <span x-text="item.row_count + ' baris data terisi'"></span>
                            </span>
                        </div>
                    </template>

                    {{-- Catatan Revisi jika ada --}}
                    <template x-if="item.has_notes">
                        <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50/80 p-3 text-xs text-rose-900">
                            <p class="font-bold text-rose-800">
                                {{ $isPerti ? 'Catatan Validasi Terakhir:' : 'Catatan Perbaikan / Validasi:' }}
                            </p>
                            <p class="mt-1 text-slate-800 whitespace-pre-line" x-text="item.validation_notes"></p>
                        </div>
                    </template>

                    {{-- Footer Action Row --}}
                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-3">
                        <template x-if="item.archive_index_url">
                            <a :href="item.archive_index_url"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Kelola di Arsip
                            </a>
                        </template>

                        <template x-if="item.export_url">
                            <a :href="item.export_url"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition">
                                Unduh Excel
                            </a>
                        </template>

                        <a :href="item.action_url"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                            <span x-text="item.action_label"></span>
                            <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty State --}}
        <div x-show="filteredItems.length === 0" class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-sm" style="display: none;">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-bold text-slate-900">Tidak ada data yang cocok</h3>
            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                Tidak ditemukan dokumen, tabel, atau arsip yang cocok dengan kata kunci <span class="font-bold text-violet-700" x-text="'&quot;' + searchQuery + '&quot;'"></span> atau filter yang aktif.
            </p>
            <div class="mt-5 flex justify-center gap-2">
                <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white hover:bg-violet-700 transition shadow-sm">
                    Reset Semua Pencarian &amp; Filter
                </button>
                <a href="{{ route($routePrefix . '.archives.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Buka Halaman Arsip
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function unifiedSearchApp(config) {
        const romanMap = { 'i': 1, 'ii': 2, 'iii': 3, 'iv': 4, 'v': 5, 'vi': 6, 'vii': 7 };

        return {
            items: config.items || [],
            searchQuery: config.initialQuery || '',
            selectedType: 'all',
            selectedModule: 'all',
            selectedArchiveCategory: 'all',
            selectedStatus: 'all',

            normalize(str) {
                return (str || '')
                    .toLowerCase()
                    .replace(/kiteria/g, 'kriteria')
                    .trim();
            },

            get hasActiveFilters() {
                return Boolean(
                    this.searchQuery
                    || this.selectedType !== 'all'
                    || this.selectedModule !== 'all'
                    || this.selectedArchiveCategory !== 'all'
                    || this.selectedStatus !== 'all'
                );
            },

            get filteredItems() {
                const rawQ = this.normalize(this.searchQuery);

                let targetKriteriaNum = null;
                const kriteriaMatch = rawQ.match(/\b(?:kriteria|modul)\s*(\d+|vii|vi|iv|v|iii|ii|i)(?!\.\d+)\b/i);
                if (kriteriaMatch) {
                    const matchedVal = kriteriaMatch[1].toLowerCase();
                    targetKriteriaNum = romanMap[matchedVal] || parseInt(matchedVal, 10);
                }

                const cleanedQ = rawQ.replace(/\b(?:kriteria|modul)\s*(\d+|vii|vi|iv|v|iii|ii|i)(?!\.\d+)\b/gi, ' ').trim();
                const queryWords = cleanedQ ? cleanedQ.split(/\s+/).filter(Boolean) : [];

                return this.items.filter(item => {
                    if (this.selectedType !== 'all' && item.type_key !== this.selectedType) {
                        return false;
                    }

                    if (this.selectedArchiveCategory !== 'all') {
                        if (!item.is_archive || item.category !== this.selectedArchiveCategory) {
                            return false;
                        }
                    }

                    if (targetKriteriaNum === null && this.selectedModule !== 'all' && String(item.module_id) !== String(this.selectedModule)) {
                        return false;
                    }

                    if (this.selectedStatus !== 'all') {
                        if (this.selectedStatus === 'approved' && item.status !== 'approved') return false;
                        if (this.selectedStatus === 'revision' && item.status !== 'revision') return false;
                        if (this.selectedStatus === 'uploaded' && !['uploaded', 'submitted'].includes(item.status)) return false;
                        if (this.selectedStatus === 'unsubmitted' && item.status !== 'unsubmitted') return false;
                        if (this.selectedStatus === 'archive' && item.status !== 'archive') return false;
                    }

                    if (targetKriteriaNum !== null) {
                        const itemOrder = Number(item.module_order);
                        const matchesOrder = itemOrder === targetKriteriaNum;
                        const matchesShort = (item.module_short || '').toLowerCase().includes('kriteria ' + targetKriteriaNum);
                        const matchesName = (item.module_name || '').toLowerCase().includes('kriteria ' + targetKriteriaNum);
                        if (!matchesOrder && !matchesShort && !matchesName) {
                            return false;
                        }
                    }

                    if (queryWords.length > 0) {
                        const haystack = this.normalize(item.search_text);
                        if (!queryWords.every(word => haystack.includes(word))) {
                            return false;
                        }
                    }

                    return true;
                });
            },

            setQuery(term) {
                if (this.searchQuery === term) {
                    this.searchQuery = '';
                } else {
                    this.searchQuery = term;
                    if (/kriteria|modul/i.test(term)) {
                        this.selectedModule = 'all';
                    }
                }
            },

            toggleType(typeKey) {
                this.selectedType = this.selectedType === typeKey ? 'all' : typeKey;
            },

            toggleStatus(st) {
                this.selectedStatus = this.selectedStatus === st ? 'all' : st;
            },

            resetFilters() {
                this.searchQuery = '';
                this.selectedType = 'all';
                this.selectedModule = 'all';
                this.selectedArchiveCategory = 'all';
                this.selectedStatus = 'all';
            }
        };
    }
</script>
