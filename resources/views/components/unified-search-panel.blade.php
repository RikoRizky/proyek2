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

<style>
    @media (min-width: 640px) {
        .search-card-head {
            align-items: flex-start;
        }
    }
</style>

<div
    x-data="unifiedSearchApp({
        items: @js($items),
        initialQuery: @js($initialQuery),
        isPerti: @js($isPerti),
    })"
    class="space-y-5"
>
    {{-- Panel Pencarian & Filter Utama --}}
    <div class="ui-card p-5 space-y-4">
        {{-- Input Search Bar Utama (Full Width, Bersih) --}}
        <div class="relative">
            <span style="left: 0.875rem;" class="pointer-events-none absolute inset-y-0 flex items-center text-slate-400">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <input
                id="unified-search-input"
                type="text"
                x-model="searchQuery"
                placeholder="Ketik kriteria, nama berkas, data mahasiswa, data nilai, SK, atau kode tabel..."
                style="padding-left: 2.5rem; padding-right: 2.25rem;"
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/20"
                autofocus
            />
            <button
                type="button"
                x-show="searchQuery.length > 0"
                @click="searchQuery = ''"
                x-cloak
                style="right: 0.75rem;"
                class="absolute inset-y-0 flex items-center text-slate-400 hover:text-slate-600"
                title="Bersihkan pencarian"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Baris Filter Dropdown --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 {{ $isPerti ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }}">
            @if ($isPerti)
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Program Studi</label>
                    <select
                        name="prodi_id"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-semibold text-slate-800 focus:border-violet-500 focus:ring-violet-500/20"
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

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tipe Dokumen</label>
                <select x-model="selectedType" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                    <option value="all">Semua (LED, LKPS &amp; Arsip)</option>
                    <option value="led">Dokumen LED (PPEPP)</option>
                    <option value="lkps">Tabel Kinerja LKPS</option>
                    <option value="archive">Arsip &amp; Dokumen Tambahan</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kriteria / Modul</label>
                <select x-model="selectedModule" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                    <option value="all">Semua Kriteria</option>
                    @foreach ($moduleOptions as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kategori Arsip</label>
                <select x-model="selectedArchiveCategory" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                    <option value="all">Semua Kategori Arsip</option>
                    @foreach ($archiveCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    {{ $isPerti ? 'Status Validasi' : 'Status Unggahan' }}
                </label>
                <select x-model="selectedStatus" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
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

    {{-- Ringkasan Hasil Tenang --}}
    <div class="flex flex-wrap items-center justify-between gap-2 px-1 text-xs text-slate-500">
        <div>
            Menampilkan <span class="font-bold text-slate-800" x-text="filteredItems.length"></span>
            dari <span class="font-bold text-slate-800" x-text="items.length"></span> data
            <template x-if="hasActiveFilters">
                <button type="button" @click="resetFilters()" class="ml-3 font-semibold text-violet-600 hover:underline">
                    Reset filter
                </button>
            </template>
        </div>
    </div>

    {{-- Daftar Hasil Kartu (Selaras dengan Halaman Arsip) --}}
    <div class="space-y-4">
        <template x-for="item in filteredItems" :key="item.unique_key">
            <article class="ui-card overflow-hidden transition hover:border-violet-300 hover:shadow-md">
                {{-- Bagian Atas Kartu: Metadata, Status, Judul & Aksi Utama --}}
                <div class="p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            @if ($isPerti)
                                <template x-if="item.prodi_name">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-700"
                                          x-text="item.prodi_name"></span>
                                </template>
                            @endif

                            <template x-if="item.is_archive">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-800 ring-1 ring-sky-200"
                                      x-text="item.category"></span>
                            </template>
                            <template x-if="!item.is_archive && item.is_table">
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200"
                                      x-text="'Tabel ' + item.table_code"></span>
                            </template>
                            <template x-if="!item.is_archive && !item.is_table">
                                <span class="inline-flex items-center rounded-full bg-violet-50 px-2.5 py-0.5 text-[11px] font-bold text-violet-700">
                                    Dokumen LED
                                </span>
                            </template>

                            <template x-if="item.is_archive">
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-300">·</span>
                                    <span>Arsip Tambahan</span>
                                    <template x-if="item.academic_year">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="text-slate-300">·</span>
                                            <span class="font-semibold text-slate-700" x-text="item.academic_year"></span>
                                        </span>
                                    </template>
                                    <template x-if="item.table_code">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="text-slate-300">·</span>
                                            <span class="font-semibold text-emerald-700" x-text="'Tabel ' + item.table_code"></span>
                                        </span>
                                    </template>
                                </span>
                            </template>

                            <template x-if="!item.is_archive && item.module_name">
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-300">·</span>
                                    <span class="truncate max-w-xs" x-text="item.module_name"></span>
                                </span>
                            </template>

                            <template x-if="item.is_table && item.row_count > 0">
                                <span class="inline-flex items-center gap-2">
                                    <span class="text-slate-300">·</span>
                                    <span class="font-semibold text-indigo-700" x-text="item.row_count + ' baris terisi'"></span>
                                </span>
                            </template>
                        </div>

                        {{-- Status Badge --}}
                        <div class="shrink-0">
                            <template x-if="item.status === 'approved'">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                                    Sesuai
                                </span>
                            </template>
                            <template x-if="item.status === 'revision'">
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-200">
                                    Perlu Revisi
                                </span>
                            </template>
                            <template x-if="item.status === 'uploaded'">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-800 ring-1 ring-amber-200">
                                    Menunggu Validasi
                                </span>
                            </template>
                            <template x-if="item.status === 'archive'">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-bold text-sky-700 ring-1 ring-sky-200">
                                    Tersedia
                                </span>
                            </template>
                            <template x-if="item.status === 'unsubmitted'">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600"
                                      x-text="item.status_label"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between search-card-head">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-slate-900" x-text="item.title"></h3>
                            <template x-if="item.description">
                                <p class="mt-1 text-xs leading-relaxed text-slate-600" x-text="item.description"></p>
                            </template>
                        </div>

                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <template x-if="item.archive_index_url">
                                <a :href="item.archive_index_url"
                                   class="ui-btn-secondary py-1.5 px-3 text-xs">
                                    Kelola di Arsip
                                </a>
                            </template>

                            <template x-if="item.export_url">
                                <a :href="item.export_url"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition">
                                    Unduh Excel
                                </a>
                            </template>

                            <template x-if="!item.is_archive || !item.files || item.files.length === 0">
                                <a :href="item.action_url"
                                   class="ui-btn-primary py-1.5 px-3.5 text-xs">
                                    <span x-text="item.action_label"></span>
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- Catatan Revisi / Validasi --}}
                    <template x-if="item.has_notes">
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs text-rose-900">
                            <p class="font-bold text-rose-800">
                                {{ $isPerti ? 'Catatan Validasi:' : 'Catatan Perbaikan:' }}
                            </p>
                            <p class="mt-1 text-slate-800" style="white-space: pre-line;" x-text="item.validation_notes"></p>
                        </div>
                    </template>
                </div>

                {{-- Bagian Bawah Kartu: Daftar Berkas & Tautan Lebar Penuh --}}
                <template x-if="(item.files && item.files.length > 0) || (item.drive_links && item.drive_links.length > 0)">
                    <div class="border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 space-y-2.5">
                        <template x-if="item.files && item.files.length > 0">
                            <div class="space-y-2">
                                <template x-for="file in item.files" :key="item.unique_key + '-f-' + file.index">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-slate-200 bg-white px-3.5 py-2.5">
                                        <div class="min-w-0 flex items-center gap-2.5 flex-1">
                                            <span class="shrink-0 rounded-md px-2 py-0.5 text-[10px] font-extrabold uppercase"
                                                  :class="{
                                                      'bg-rose-100 text-rose-700': file.preview_kind === 'pdf',
                                                      'bg-emerald-100 text-emerald-700': file.preview_kind === 'spreadsheet',
                                                      'bg-sky-50 text-sky-700 ring-1 ring-sky-200': file.preview_kind === 'image',
                                                      'bg-amber-100 text-amber-800': file.preview_kind === 'download_only'
                                                  }"
                                                  x-text="file.ext"></span>
                                            <span class="truncate text-xs font-semibold text-slate-800" x-text="file.filename"></span>
                                            <span class="shrink-0 text-xs text-slate-400" x-text="file.size_label"></span>
                                        </div>
                                        <div class="flex shrink-0 items-center gap-2">
                                            <a :href="file.view_url"
                                               class="ui-btn-primary py-1.5 px-3 text-xs"
                                               title="Buka & periksa dokumen langsung di aplikasi">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                </svg>
                                                Lihat
                                            </a>
                                            <a :href="file.download_url"
                                               class="ui-btn-secondary py-1.5 px-3 text-xs"
                                               title="Unduh berkas">
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                                </svg>
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="item.drive_links && item.drive_links.length > 0">
                            <div class="flex flex-wrap items-center gap-2">
                                <template x-for="(dl, dIdx) in item.drive_links" :key="item.unique_key + '-dl-' + dIdx">
                                    <a :href="dl.url" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-violet-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-violet-50">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                        </svg>
                                        <span x-text="dl.name"></span>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </article>
        </template>

        {{-- Empty State --}}
        <div x-show="filteredItems.length === 0" x-cloak class="ui-card p-10 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Tidak ada data yang cocok</h3>
            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                Tidak ditemukan dokumen, tabel, atau arsip yang cocok dengan kata kunci atau filter yang dipilih.
            </p>
            <div class="mt-4 flex justify-center gap-2">
                <button type="button" @click="resetFilters()" class="ui-btn-primary py-2 px-4 text-xs">
                    Reset Pencarian &amp; Filter
                </button>
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
