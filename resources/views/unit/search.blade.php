<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Unit Kerja · Program Studi</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pencarian Dokumen &amp; Tabel LKPS</h1>
                <p class="mt-1 text-sm text-slate-600">Temukan butir kriteria, dokumen LED (PPEPP), dan data tabel kinerja LKPS secara instan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="unitSearchApp(@js($items), '{{ addslashes($initialQuery) }}')" class="space-y-6">

        {{-- Panel Filter & Pencarian Utama --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm">
            {{-- Input Search Bar Utama --}}
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-5 w-5 text-violet-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Ketik kriteria, nama dokumen, atau kode tabel (contoh: 'Kriteria 1', '[PELAKSANAAN] Penerapan Tata Kelola', '1.A.1')..."
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

            {{-- Saran Pencarian Cepat (Quick Chips) --}}
            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs">
                <span class="font-semibold text-slate-500 mr-1">Contoh cari cepat:</span>
                <button type="button" @click="setQuery('Kriteria 1')"
                    :class="searchQuery.toLowerCase().includes('kriteria 1') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    Kriteria 1
                </button>
                <button type="button" @click="setQuery('Kriteria 2')"
                    :class="searchQuery.toLowerCase().includes('kriteria 2') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    Kriteria 2
                </button>
                <button type="button" @click="setQuery('Kriteria 3')"
                    :class="searchQuery.toLowerCase().includes('kriteria 3') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    Kriteria 3
                </button>
                <button type="button" @click="setQuery('[PELAKSANAAN] Penerapan Tata Kelola & Kegiatan SPMI')"
                    :class="searchQuery.includes('[PELAKSANAAN]') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    [PELAKSANAAN] Penerapan Tata Kelola
                </button>
                <button type="button" @click="setQuery('Tabel 1.A.1')"
                    :class="searchQuery.toLowerCase().includes('1.a.1') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    Tabel 1.A.1
                </button>
                <button type="button" @click="setQuery('[PENETAPAN]')"
                    :class="searchQuery.includes('[PENETAPAN]') ? 'bg-violet-600 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-700 hover:bg-violet-100 hover:text-violet-700 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    [PENETAPAN]
                </button>
                <button type="button" @click="toggleStatus('revision')"
                    :class="selectedStatus === 'revision' ? 'bg-rose-600 text-white shadow-xs font-bold ring-2 ring-rose-400' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 font-medium'"
                    class="rounded-lg px-2.5 py-1 transition">
                    ⚠️ Perlu Revisi
                </button>
            </div>

            {{-- Baris Filter Tabs: Tipe, Kriteria, dan Status --}}
            <div class="mt-5 grid grid-cols-1 gap-3 pt-4 border-t border-slate-100 sm:grid-cols-3">
                {{-- Filter Tipe --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tipe Persyaratan</label>
                    <select x-model="selectedType" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                        <option value="all">Semua Tipe (Dokumen &amp; Tabel)</option>
                        <option value="led">📄 Dokumen LED (PPEPP)</option>
                        <option value="lkps">📊 Tabel Kinerja LKPS</option>
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

                {{-- Filter Status --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Unggahan</label>
                    <select x-model="selectedStatus" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                        <option value="all">Semua Status</option>
                        <option value="approved">✅ Sesuai (Disetujui)</option>
                        <option value="revision">⚠️ Perlu Revisi</option>
                        <option value="uploaded">⏳ Menunggu Validasi</option>
                        <option value="unsubmitted">⚪ Belum Diunggah / Belum Diisi</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Ringkasan Hasil Pencarian --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-1 text-xs text-slate-600">
            <div>
                <span>Ditemukan </span>
                <span class="font-bold text-slate-900" x-text="filteredItems.length"></span>
                <span> butir dari total </span>
                <span class="font-bold text-slate-900" x-text="items.length"></span>
                <span> persyaratan</span>
                <template x-if="searchQuery || selectedModule !== 'all' || selectedType !== 'all' || selectedStatus !== 'all'">
                    <button type="button" @click="resetFilters()" class="ml-2 font-semibold text-violet-600 hover:text-violet-800 underline">
                        Reset filter
                    </button>
                </template>
            </div>
            <div class="flex items-center gap-2 font-medium">
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-800 ring-1 ring-emerald-500/20">
                    <span class="font-bold" x-text="items.filter(i => i.status === 'approved').length"></span> Sesuai
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-800 ring-1 ring-rose-500/20">
                    <span class="font-bold" x-text="items.filter(i => i.status === 'revision').length"></span> Perlu Revisi
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-800 ring-1 ring-amber-500/20">
                    <span class="font-bold" x-text="items.filter(i => ['uploaded', 'submitted'].includes(i.status)).length"></span> Menunggu
                </span>
            </div>
        </div>

        {{-- Daftar Hasil Kartu Persyaratan --}}
        <div class="space-y-3">
            <template x-for="item in filteredItems" :key="item.id">
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs transition hover:border-violet-300 hover:shadow-md">
                    {{-- Garis Aksen Kiri berdasarkan tipe / status --}}
                    <div
                        class="absolute inset-y-0 left-0 w-1.5"
                        :class="{
                            'bg-emerald-500': item.status === 'approved',
                            'bg-rose-500': item.status === 'revision',
                            'bg-amber-500': item.status === 'uploaded',
                            'bg-violet-400': item.status === 'unsubmitted' && item.type_key === 'led',
                            'bg-indigo-400': item.status === 'unsubmitted' && item.type_key === 'lkps'
                        }"
                    ></div>

                    <div class="pl-2">
                        {{-- Baris Header Kartu: Kriteria, Tipe, dan Status Badge --}}
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                {{-- Badge Kriteria --}}
                                <span class="inline-flex items-center rounded-lg bg-violet-100 px-2.5 py-0.5 text-[11px] font-bold text-violet-800" x-text="item.module_short"></span>

                                {{-- Badge Tipe --}}
                                <template x-if="item.is_table">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"/>
                                        </svg>
                                        <span x-text="'Tabel ' + item.table_code"></span>
                                    </span>
                                </template>
                                <template x-if="!item.is_table">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">
                                        <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                        <span>Dokumen LED</span>
                                    </span>
                                </template>

                                <span class="text-xs text-slate-400 font-medium truncate max-w-xs" x-text="item.module_name"></span>
                            </div>

                            {{-- Status Badge --}}
                            <div>
                                <template x-if="item.status === 'approved'">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-500/20">
                                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        <span>Sesuai</span>
                                    </span>
                                </template>
                                <template x-if="item.status === 'revision'">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-500/20">
                                        <svg class="h-3.5 w-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Z"/></svg>
                                        <span>Perlu Revisi</span>
                                    </span>
                                </template>
                                <template x-if="item.status === 'uploaded'">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800 ring-1 ring-amber-500/20">
                                        <svg class="h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        <span>Menunggu Validasi</span>
                                    </span>
                                </template>
                                <template x-if="item.status === 'unsubmitted'">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        <span x-text="item.status_label"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Judul Persyaratan & Deskripsi --}}
                        <div class="mt-2.5">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-violet-900 transition" x-text="item.title"></h3>
                            <template x-if="item.description">
                                <p class="mt-1 text-xs text-slate-600 leading-relaxed" x-text="item.description"></p>
                            </template>
                        </div>

                        {{-- Detail Tambahan (Jumlah berkas / baris LKPS) --}}
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <template x-if="!item.is_table && item.file_count > 0">
                                <span class="inline-flex items-center gap-1 font-semibold text-violet-700 bg-violet-50 px-2 py-0.5 rounded-md">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.37l-8.485 8.486a1.5 1.5 0 01-2.122-2.122l7.778-7.778"/></svg>
                                    <span x-text="item.file_count + ' berkas terunggah'"></span>
                                </span>
                            </template>

                            <template x-if="item.is_table && item.row_count > 0">
                                <span class="inline-flex items-center gap-1 font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                                    <span x-text="item.row_count + ' baris data terisi'"></span>
                                </span>
                            </template>
                        </div>

                        {{-- Catatan Revisi jika ada --}}
                        <template x-if="item.has_notes">
                            <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50/80 p-3 text-xs text-rose-900">
                                <p class="font-bold flex items-center gap-1.5 text-rose-800">
                                    <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                                    </svg>
                                    <span>Catatan Perbaikan / Validasi:</span>
                                </p>
                                <p class="mt-1 text-slate-800 whitespace-pre-line" x-text="item.validation_notes"></p>
                            </div>
                        </template>

                        {{-- Tombol Aksi Langsung ke Halaman Modul / Tabel --}}
                        <div class="mt-4 flex items-center justify-end border-t border-slate-100 pt-3">
                            <a
                                :href="item.action_url"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-violet-700 transition"
                            >
                                <span x-text="item.action_label"></span>
                                <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State ketika tidak ada yang cocok --}}
            <div x-show="filteredItems.length === 0" class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-sm" style="display: none;">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-bold text-slate-900">Tidak ada persyaratan yang cocok</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                    Tidak ditemukan data yang cocok dengan kata kunci <span class="font-bold text-violet-700" x-text="'&quot;' + searchQuery + '&quot;'"></span> atau filter yang sedang aktif.
                </p>
                <div class="mt-5">
                    <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white hover:bg-violet-700 transition shadow-xs">
                        Reset Semua Pencarian &amp; Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function unitSearchApp(itemsData, initialQ) {
            const romanMap = { 'i': 1, 'ii': 2, 'iii': 3, 'iv': 4, 'v': 5, 'vi': 6, 'vii': 7 };

            return {
                items: itemsData,
                searchQuery: initialQ || '',
                selectedType: 'all',
                selectedModule: 'all',
                selectedStatus: 'all',

                normalize(str) {
                    return (str || '')
                        .toLowerCase()
                        .replace(/kiteria/g, 'kriteria')
                        .trim();
                },

                get filteredItems() {
                    const rawQ = this.normalize(this.searchQuery);

                    // Check if query contains a specific kriteria / modul number: e.g. "kriteria 2", "kiteria 2", "kriteria ii", "modul 2"
                    let targetKriteriaNum = null;
                    const kriteriaMatch = rawQ.match(/\b(?:kriteria|modul)\s*(\d+|vii|vi|iv|v|iii|ii|i)(?!\.\d+)\b/i);
                    if (kriteriaMatch) {
                        const matchedVal = kriteriaMatch[1].toLowerCase();
                        targetKriteriaNum = romanMap[matchedVal] || parseInt(matchedVal, 10);
                    }

                    // Remove the kriteria pattern from search words so remaining words can be searched
                    const cleanedQ = rawQ.replace(/\b(?:kriteria|modul)\s*(\d+|vii|vi|iv|v|iii|ii|i)(?!\.\d+)\b/gi, ' ').trim();
                    const queryWords = cleanedQ ? cleanedQ.split(/\s+/).filter(Boolean) : [];

                    return this.items.filter(item => {
                        // Filter Type
                        if (this.selectedType !== 'all' && item.type_key !== this.selectedType) {
                            return false;
                        }

                        // Filter Module (only applies if query does not already specify an explicit kriteria)
                        if (targetKriteriaNum === null && this.selectedModule !== 'all' && String(item.module_id) !== String(this.selectedModule)) {
                            return false;
                        }

                        // Filter Status
                        if (this.selectedStatus !== 'all') {
                            if (this.selectedStatus === 'approved' && item.status !== 'approved') return false;
                            if (this.selectedStatus === 'revision' && item.status !== 'revision') return false;
                            if (this.selectedStatus === 'uploaded' && !['uploaded', 'submitted'].includes(item.status)) return false;
                            if (this.selectedStatus === 'unsubmitted' && item.status !== 'unsubmitted') return false;
                        }

                        // Explicit Kriteria matching: if user searched "Kriteria 2", ONLY items belonging to Kriteria 2 must match
                        if (targetKriteriaNum !== null) {
                            const itemOrder = Number(item.module_order);
                            const matchesOrder = itemOrder === targetKriteriaNum;
                            const matchesShort = (item.module_short || '').toLowerCase().includes('kriteria ' + targetKriteriaNum);
                            const matchesName = (item.module_name || '').toLowerCase().includes('kriteria ' + targetKriteriaNum);
                            if (!matchesOrder && !matchesShort && !matchesName) {
                                return false;
                            }
                        }

                        // Keyword Query Filter for remaining words (all words must match)
                        if (queryWords.length > 0) {
                            const haystack = this.normalize(item.search_text);
                            const matches = queryWords.every(word => haystack.includes(word));
                            if (!matches) return false;
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

                toggleStatus(st) {
                    this.selectedStatus = this.selectedStatus === st ? 'all' : st;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.selectedType = 'all';
                    this.selectedModule = 'all';
                    this.selectedStatus = 'all';
                }
            };
        }
    </script>
</x-app-layout>
