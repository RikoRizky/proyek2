<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">
                {{ $isPerti ? 'Perguruan Tinggi · Arsip & Data Pendukung' : 'Unit Kerja · Program Studi' }}
            </p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Arsip &amp; Dokumen Tambahan
            </h1>
            <p class="mt-1 text-sm text-slate-600">
                Kelola kumpulan data mahasiswa, data nilai, SK legalitas, RPS, serta dokumen tambahan lain tanpa mengganggu perhitungan 65 butir wajib.
            </p>
        </div>
    </x-slot>

    <div
        x-data="archivesPageApp({
            documents: @js($documents),
            categories: @js($categories),
            presets: @js($presets),
            storeUrl: @js(route($routePrefix . '.archives.store')),
            initialQuery: @js($initialQuery),
            initialCategory: @js($initialCategory),
            isPerti: @js($isPerti),
        })"
        x-init="init()"
        @keydown.window.escape="closeModals()"
        class="space-y-6"
    >
        {{-- Top Action & Info Strip --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 font-semibold text-violet-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                    <span x-text="documents.length + ' arsip tersimpan'"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-600">
                    Format didukung: {{ \App\Support\AccreditationUpload::archiveAllowedMimesLabel() }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route($routePrefix . '.search') }}"
                   class="ui-btn-secondary text-xs py-2 px-3.5">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    Cari Semua Dokumen &amp; LKPS
                </a>
                <button type="button"
                        @click="openCreateModal()"
                        class="ui-btn-primary text-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Tambah Dokumen Tambahan
                </button>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-800 shadow-sm" role="alert">
                <p class="font-bold text-rose-900">Mohon periksa isian formulir:</p>
                <ul class="mt-1 list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Copy Link Toast --}}
        <div x-show="toastMessage"
             x-cloak
             x-transition
             style="bottom: 1.25rem; right: 1.25rem;"
             class="fixed z-50 flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-xl">
            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        {{-- Search & Filter Panel --}}
        <div class="ui-card p-5 space-y-4">
            <div class="relative">
                <span style="left: 0.875rem;" class="pointer-events-none absolute inset-y-0 flex items-center text-violet-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </span>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Cari arsip, misalnya: kumpulan data mahasiswa, data nilai, SK pendirian, transkrip, .xlsx..."
                       style="padding-left: 3rem; padding-right: 2.5rem;"
                       class="w-full rounded-xl border border-slate-200 bg-white py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                <button type="button"
                        x-show="searchQuery.length > 0"
                        @click="searchQuery = ''"
                        x-cloak
                        style="right: 0.75rem;"
                        class="absolute inset-y-0 flex items-center text-slate-400 hover:text-slate-600"
                        title="Bersihkan pencarian">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Filter Dropdowns --}}
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kategori</label>
                    <select x-model="selectedCategory"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                        <option value="all">Semua Kategori</option>
                        <template x-for="cat in categories" :key="cat">
                            <option :value="cat" x-text="cat"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahun Akademik / TS</label>
                    <select x-model="selectedYear"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                        <option value="all">Semua Tahun Akademik</option>
                        <template x-for="yr in availableYears" :key="yr">
                            <option :value="yr" x-text="yr"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                        {{ $isPerti ? 'Unit / Program Studi' : 'Sumber Dokumen' }}
                    </label>
                    <select x-model="selectedScope"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-violet-500 focus:ring-violet-500/20">
                        <option value="all">{{ $isPerti ? 'Semua Unit (PT & Prodi)' : 'Semua Sumber' }}</option>
                        <option value="shared">Dokumen PT / UPPS (Semua Prodi)</option>
                        @if ($isPerti)
                            @foreach ($prodis as $prodiOpt)
                                <option value="{{ $prodiOpt['id'] }}">{{ $prodiOpt['name'] }}</option>
                            @endforeach
                        @else
                            <option value="prodi_only">Khusus Program Studi Ini</option>
                        @endif
                    </select>
                </div>
            </div>

            {{-- Quick Category Chips --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <button type="button"
                        @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-violet-600 text-white border-violet-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-50'"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition">
                    Semua
                </button>
                <template x-for="cat in categories" :key="'chip-' + cat">
                    <button type="button"
                            @click="selectedCategory = (selectedCategory === cat ? 'all' : cat)"
                            :class="selectedCategory === cat ? 'bg-violet-600 text-white border-violet-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-50'"
                            class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                            x-text="cat"></button>
                </template>
            </div>
        </div>

        {{-- Summary Counter --}}
        <div class="flex flex-wrap items-center justify-between gap-2 px-1 text-xs text-slate-500">
            <div>
                Menampilkan <span class="font-bold text-slate-800" x-text="filteredDocuments.length"></span>
                dari <span class="font-bold text-slate-800" x-text="documents.length"></span> dokumen arsip
                <button type="button"
                        x-show="hasActiveFilters"
                        @click="resetFilters()"
                        x-cloak
                        class="ml-3 font-semibold text-violet-600 hover:underline">
                    Reset filter
                </button>
            </div>
        </div>

        {{-- Empty State --}}
        <div x-show="filteredDocuments.length === 0" x-cloak class="ui-card p-10 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-900" x-text="documents.length === 0 ? 'Belum ada dokumen tambahan' : 'Tidak ada dokumen yang cocok dengan filter'"></h3>
            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                <span x-show="documents.length === 0">Mulai unggah data mahasiswa, data nilai, SK pendirian/akreditasi, RPS, atau dokumen pendukung lain di sini.</span>
                <span x-show="documents.length > 0">Coba ubah kata kunci pencarian atau pilih kategori lain.</span>
            </p>
            <div class="mt-4 flex justify-center gap-2">
                <button type="button" x-show="hasActiveFilters" @click="resetFilters()" class="ui-btn-secondary text-xs py-2 px-3">
                    Reset Filter
                </button>
                <button type="button" @click="openCreateModal()" class="ui-btn-primary text-xs py-2 px-4">
                    + Tambah Dokumen
                </button>
            </div>
        </div>

        {{-- Document List --}}
        <div class="space-y-4">
            <template x-for="doc in filteredDocuments" :key="doc.id">
                <article class="ui-card p-5 transition hover:border-violet-300 hover:shadow-md">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1 space-y-2.5">
                            {{-- Top Badges --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-[11px] font-bold text-violet-800"
                                      x-text="doc.category"></span>

                                <template x-if="doc.academic_year">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-700"
                                          x-text="doc.academic_year"></span>
                                </template>

                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                      :class="doc.is_pt_shared ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : 'bg-sky-50 text-sky-800 ring-1 ring-sky-200'"
                                      x-text="doc.scope_label"></span>

                                <template x-if="doc.table_code">
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700"
                                          x-text="'Tabel ' + doc.table_code"></span>
                                </template>

                                <template x-if="doc.requirement_label">
                                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-[11px] font-medium text-amber-800 truncate max-w-xs"
                                          x-text="doc.requirement_label"></span>
                                </template>
                            </div>

                            {{-- Title & Description --}}
                            <div>
                                <h2 class="text-base font-bold text-slate-900" x-text="doc.title"></h2>
                                <p x-show="doc.description" class="mt-1 text-xs leading-relaxed text-slate-600" x-text="doc.description"></p>
                                <p class="mt-1.5 text-[11px] text-slate-400">
                                    Diunggah oleh <span class="font-semibold text-slate-600" x-text="doc.uploader_name"></span>
                                    · Diperbarui <span x-text="doc.updated_label"></span>
                                </p>
                            </div>

                            {{-- Attached Physical Files with Instant Cek & Unduh --}}
                            <template x-if="doc.files.length > 0">
                                <div class="pt-1 space-y-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        Berkas Tersimpan (<span x-text="doc.files.length"></span>)
                                    </p>
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <template x-for="file in doc.files" :key="doc.id + '-f-' + file.index">
                                            <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-2">
                                                <div class="min-w-0 flex items-center gap-2 flex-1">
                                                    <span class="shrink-0 rounded-md px-2 py-0.5 text-[10px] font-extrabold uppercase"
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
                                                       class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-2.5 py-1 text-[11px] font-bold text-white transition hover:bg-violet-500"
                                                       title="Cek / Pratinjau di aplikasi">
                                                        Cek
                                                    </a>
                                                    <a :href="file.download_url"
                                                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50"
                                                       title="Unduh berkas">
                                                        Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            {{-- Google Drive Links --}}
                            <template x-if="doc.google_drive_links.length > 0">
                                <div class="pt-1 flex flex-wrap items-center gap-2">
                                    <template x-for="(link, lIdx) in doc.google_drive_links" :key="doc.id + '-l-' + lIdx">
                                        <a :href="link.url"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-violet-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-violet-50">
                                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                            </svg>
                                            <span x-text="link.name"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Right Actions Column --}}
                        <div class="flex shrink-0 flex-wrap items-center gap-2 lg:flex-col lg:items-end">
                            <div class="flex flex-wrap items-center gap-2">
                                <a :href="doc.primary_view_url"
                                   class="ui-btn-primary py-2 px-3.5 text-xs">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    Buka Viewer
                                </a>

                                <button type="button"
                                        @click="copyArchiveLink(doc.view_url)"
                                        class="ui-btn-secondary py-2 px-3 text-xs"
                                        title="Salin tautan arsip untuk ditempel ke kolom Link Bukti LKPS">
                                    <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                                    </svg>
                                    Salin Link Bukti
                                </button>
                            </div>

                            <template x-if="doc.can_edit">
                                <div class="flex items-center gap-2 pt-1">
                                    <button type="button"
                                            @click="openEditModal(doc)"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        Edit
                                    </button>
                                    <button type="button"
                                            @click="openDeleteModal(doc)"
                                            class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        Hapus
                                    </button>
                                </div>
                            </template>

                            <template x-if="!doc.can_edit">
                                <span class="text-[11px] font-medium text-slate-400">
                                    Dibagikan oleh Perguruan Tinggi
                                </span>
                            </template>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        {{-- Create / Edit Modal --}}
        <div x-show="formModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm">
            <div @click.outside="formModalOpen = false"
                 style="margin-top: 2rem; margin-bottom: 2rem;"
                 class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-6 py-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-violet-600"
                           x-text="isEditing ? 'PERBARUI DOKUMEN ARSIP' : 'TAMBAH DOKUMEN TAMBAHAN'"></p>
                        <h3 class="mt-0.5 text-lg font-bold text-slate-900"
                            x-text="isEditing ? 'Edit Dokumen Arsip' : 'Unggah / Tambahkan Dokumen Pendukung'"></h3>
                    </div>
                    <button type="button" @click="formModalOpen = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST" enctype="multipart/form-data" class="max-h-[70vh] overflow-y-auto p-6 space-y-5">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    {{-- Quick Templates in Create Mode --}}
                    <div x-show="!isEditing" class="rounded-xl border border-violet-200/80 bg-violet-50/50 p-3.5 space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-violet-700">Template Cepat LAM INFOKOM (Klik untuk isi otomatis)</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="(preset, pIdx) in presets" :key="pIdx">
                                <button type="button"
                                        @click="applyPreset(preset)"
                                        class="rounded-lg border border-violet-200 bg-white px-2.5 py-1 text-xs font-semibold text-violet-700 transition hover:bg-violet-700 hover:text-white"
                                        x-text="preset.label"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Judul Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               x-model="form.title"
                               required
                               placeholder="Contoh: Kumpulan Data Mahasiswa Aktif TA 2025/2026"
                               class="ui-input text-sm">
                    </div>

                    {{-- Category + Academic Year --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select name="category"
                                    x-model="form.category"
                                    required
                                    class="ui-input text-sm">
                                <template x-for="cat in categories" :key="'opt-' + cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                                <option value="__custom__">+ Buat Kategori Baru (Kustom)...</option>
                            </select>

                            <div x-show="form.category === '__custom__'" x-cloak class="mt-2">
                                <input type="text"
                                       name="custom_category"
                                       x-model="form.custom_category"
                                       :required="form.category === '__custom__'"
                                       placeholder="Ketik nama kategori baru..."
                                       class="ui-input text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Tahun Akademik / Periode TS
                            </label>
                            <input type="text"
                                   name="academic_year"
                                   x-model="form.academic_year"
                                   list="archive-academic-years"
                                   placeholder="Contoh: TS, TS-1, 2025/2026"
                                   class="ui-input text-sm">
                            <datalist id="archive-academic-years">
                                @foreach ($academicYears as $yr)
                                    <option value="{{ $yr }}"></option>
                                @endforeach
                                <option value="2025/2026"></option>
                                <option value="2024/2025"></option>
                                <option value="2023/2024"></option>
                            </datalist>
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @foreach ($academicYears as $yr)
                                    <button type="button"
                                            @click="form.academic_year = @js($yr)"
                                            :class="form.academic_year === @js($yr) ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                            class="rounded-md px-2 py-0.5 text-[11px] font-semibold transition">
                                        {{ $yr }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Perti Scope Selector --}}
                    @if ($isPerti)
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Cakupan / Unit Pemilik
                            </label>
                            <select name="prodi_id" x-model="form.prodi_id" class="ui-input text-sm">
                                <option value="shared">Perguruan Tinggi / UPPS (Dibagikan ke Semua Prodi)</option>
                                @foreach ($prodis as $prodiOpt)
                                    <option value="{{ $prodiOpt['id'] }}">Khusus Prodi: {{ $prodiOpt['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Optional Linkage to LKPS Table or LED Requirement --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 rounded-xl border border-slate-200/80 bg-slate-50/80 p-3.5">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                Kaitkan ke Tabel LKPS (Opsional)
                            </label>
                            <select name="table_code" x-model="form.table_code" class="ui-input text-xs">
                                <option value="">-- Tidak dikaitkan ke tabel LKPS --</option>
                                @foreach ($tableOptions as $tbl)
                                    <option value="{{ $tbl['table_code'] }}">{{ $tbl['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                Kaitkan ke Butir Kriteria / LED (Opsional)
                            </label>
                            <select name="requirement_id" x-model="form.requirement_id" class="ui-input text-xs">
                                <option value="">-- Tidak dikaitkan ke butir --</option>
                                @foreach ($requirementOptions as $reqOpt)
                                    <option value="{{ $reqOpt['id'] }}">{{ $reqOpt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Catatan / Keterangan (Opsional)
                        </label>
                        <textarea name="description"
                                  x-model="form.description"
                                  rows="2"
                                  placeholder="Jelaskan isi dokumen agar mudah dicari kembali oleh prodi atau universitas..."
                                  class="ui-input text-sm"></textarea>
                    </div>

                    {{-- Existing Files (Edit Mode) --}}
                    <div x-show="isEditing && form.existingFiles.length > 0" x-cloak class="space-y-2 rounded-xl border border-slate-200 bg-slate-50/80 p-3.5">
                        <p class="text-xs font-bold text-slate-700">Berkas Saat Ini (hapus centang jika ingin menghapus berkas)</p>
                        <div class="space-y-1.5">
                            <template x-for="(ef, efIdx) in form.existingFiles" :key="'keep-' + efIdx">
                                <label class="flex items-center justify-between gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs cursor-pointer">
                                    <span class="flex items-center gap-2 min-w-0">
                                        <input type="checkbox"
                                               name="keep_files[]"
                                               :value="ef.file_path"
                                               checked
                                               class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        <span class="truncate font-medium text-slate-800" x-text="ef.filename"></span>
                                    </span>
                                    <span class="shrink-0 text-[11px] text-slate-400" x-text="ef.size_label"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- Upload Physical Files --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                            Unggah Berkas Fisik (Bisa Pilih Lebih Dari 1 File)
                        </label>

                        @if ($isStarterPackage)
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                                Akun berpaket <strong>Starter</strong> hanya mendukung tautan Google Drive di bawah. Upgrade paket untuk mengunggah berkas langsung ke server.
                            </div>
                        @else
                            <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/60 p-4 text-center hover:border-violet-400 transition">
                                <input type="file"
                                       name="documents[]"
                                       multiple
                                       accept="{{ \App\Support\AccreditationUpload::archiveAcceptAttribute() }}"
                                       @change="onFileSelect($event)"
                                       class="block w-full text-xs text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-violet-700 cursor-pointer">
                                <p class="mt-2 text-[11px] text-slate-500">
                                    Format: {{ \App\Support\AccreditationUpload::archiveAllowedMimesLabel() }} · Maks. {{ \App\Support\AccreditationUpload::maxUploadMb() }} MB/berkas.
                                </p>
                                <div x-show="selectedFileNames.length > 0" x-cloak class="mt-2 flex flex-wrap justify-center gap-1.5">
                                    <template x-for="(fname, fnIdx) in selectedFileNames" :key="fnIdx">
                                        <span class="inline-flex rounded-md bg-violet-100 px-2 py-0.5 text-[11px] font-semibold text-violet-800" x-text="fname"></span>
                                    </template>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Google Drive Links --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Tautan Google Drive / Eksternal (Opsional)
                            </label>
                            <button type="button"
                                    @click="addDriveLinkRow()"
                                    class="text-xs font-bold text-violet-600 hover:text-violet-500">
                                + Tambah Tautan
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(link, idx) in form.google_drive_links" :key="'dl-' + idx">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <input type="text"
                                           :name="'google_drive_links[' + idx + '][name]'"
                                           x-model="link.name"
                                           placeholder="Label link (mis. Folder Data Nilai 2025)"
                                           style="margin-top: 0;"
                                           class="ui-input text-xs sm:flex-1">
                                    <input type="text"
                                           :name="'google_drive_links[' + idx + '][url]'"
                                           x-model="link.url"
                                           placeholder="https://drive.google.com/..."
                                           style="margin-top: 0;"
                                           class="ui-input text-xs sm:flex-1">
                                    <button type="button"
                                            @click="removeDriveLinkRow(idx)"
                                            class="shrink-0 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-100">
                                        Hapus
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4">
                        <button type="button" @click="formModalOpen = false" class="ui-btn-secondary py-2 px-4 text-xs">
                            Batal
                        </button>
                        <button type="submit" class="ui-btn-primary py-2 px-5 text-xs">
                            <span x-text="isEditing ? 'Simpan Perubahan' : 'Simpan Dokumen Tambahan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="deleteModalOpen"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
            <div @click.outside="deleteModalOpen = false"
                 class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-slate-200 space-y-4">
                <h3 class="text-base font-bold text-slate-900">Hapus Dokumen Tambahan?</h3>
                <p class="text-xs leading-relaxed text-slate-600">
                    Dokumen <span class="font-bold text-slate-800" x-text="deleteTarget?.title"></span> beserta seluruh berkas fisiknya akan dihapus dari arsip. Tindakan ini tidak memengaruhi kelengkapan 65 butir wajib.
                </p>
                <form :action="deleteTarget?.destroy_url" method="POST" class="flex justify-end gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="ui-btn-secondary py-2 px-4 text-xs">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-rose-600 py-2 px-4 text-xs font-semibold text-white shadow-sm transition hover:opacity-90">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function archivesPageApp(config) {
            return {
                documents: config.documents || [],
                categories: config.categories || [],
                presets: config.presets || [],
                storeUrl: config.storeUrl,
                isPerti: Boolean(config.isPerti),

                searchQuery: config.initialQuery || '',
                selectedCategory: config.initialCategory || 'all',
                selectedYear: 'all',
                selectedScope: 'all',

                formModalOpen: false,
                deleteModalOpen: false,
                isEditing: false,
                formAction: config.storeUrl,
                deleteTarget: null,
                selectedFileNames: [],
                toastMessage: '',

                form: {
                    title: '',
                    category: (config.categories && config.categories[0]) || 'Dokumen Mahasiswa',
                    custom_category: '',
                    academic_year: 'TS',
                    prodi_id: 'shared',
                    table_code: '',
                    requirement_id: '',
                    description: '',
                    existingFiles: [],
                    google_drive_links: [],
                },

                init() {
                    if (this.selectedCategory && !this.categories.includes(this.selectedCategory)) {
                        this.selectedCategory = 'all';
                    }
                },

                get availableYears() {
                    const years = new Set(['TS', 'TS-1', 'TS-2', 'TS-3', 'Umum']);
                    this.documents.forEach(d => {
                        if (d.academic_year) years.add(d.academic_year);
                    });
                    return Array.from(years);
                },

                get hasActiveFilters() {
                    return this.searchQuery.trim() !== ''
                        || this.selectedCategory !== 'all'
                        || this.selectedYear !== 'all'
                        || this.selectedScope !== 'all';
                },

                get filteredDocuments() {
                    const q = this.searchQuery.trim().toLowerCase();

                    return this.documents.filter(doc => {
                        if (this.selectedCategory !== 'all' && doc.category !== this.selectedCategory) {
                            return false;
                        }
                        if (this.selectedYear !== 'all' && doc.academic_year !== this.selectedYear) {
                            return false;
                        }
                        if (this.selectedScope !== 'all') {
                            if (this.selectedScope === 'shared' && !doc.is_pt_shared) {
                                return false;
                            } else if (this.selectedScope === 'prodi_only' && doc.is_pt_shared) {
                                return false;
                            } else if (!['shared', 'prodi_only'].includes(this.selectedScope) && doc.prodi_id !== this.selectedScope) {
                                return false;
                            }
                        }
                        if (q !== '') {
                            const tokens = q.split(/\s+/).filter(Boolean);
                            for (const t of tokens) {
                                if (!doc.search_text.includes(t)) {
                                    return false;
                                }
                            }
                        }
                        return true;
                    });
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.selectedCategory = 'all';
                    this.selectedYear = 'all';
                    this.selectedScope = 'all';
                },

                closeModals() {
                    this.formModalOpen = false;
                    this.deleteModalOpen = false;
                },

                openCreateModal() {
                    this.isEditing = false;
                    this.formAction = this.storeUrl;
                    this.selectedFileNames = [];
                    this.form = {
                        title: '',
                        category: this.categories[0] || 'Dokumen Mahasiswa',
                        custom_category: '',
                        academic_year: 'TS',
                        prodi_id: 'shared',
                        table_code: '',
                        requirement_id: '',
                        description: '',
                        existingFiles: [],
                        google_drive_links: [{ name: '', url: '' }],
                    };
                    this.formModalOpen = true;
                },

                openEditModal(doc) {
                    this.isEditing = true;
                    this.formAction = doc.update_url;
                    this.selectedFileNames = [];
                    const knownCategory = this.categories.includes(doc.category);
                    this.form = {
                        title: doc.title || '',
                        category: knownCategory ? doc.category : '__custom__',
                        custom_category: knownCategory ? '' : (doc.category || ''),
                        academic_year: doc.academic_year || '',
                        prodi_id: doc.prodi_id || 'shared',
                        table_code: doc.table_code || '',
                        requirement_id: doc.requirement_id || '',
                        description: doc.description || '',
                        existingFiles: Array.isArray(doc.files) ? doc.files : [],
                        google_drive_links: Array.isArray(doc.google_drive_links) && doc.google_drive_links.length > 0
                            ? doc.google_drive_links.map(l => ({ name: l.name || '', url: l.url || '' }))
                            : [{ name: '', url: '' }],
                    };
                    this.formModalOpen = true;
                },

                openDeleteModal(doc) {
                    this.deleteTarget = doc;
                    this.deleteModalOpen = true;
                },

                applyPreset(preset) {
                    this.form.title = preset.title || '';
                    this.form.category = preset.category || 'Dokumen Lainnya';
                    this.form.custom_category = '';
                    this.form.academic_year = preset.academic_year || 'TS';
                    this.form.table_code = preset.table_code || '';
                    this.form.description = preset.description || '';
                },

                onFileSelect(event) {
                    const files = Array.from(event.target.files || []);
                    this.selectedFileNames = files.map(f => f.name);
                },

                addDriveLinkRow() {
                    this.form.google_drive_links.push({ name: '', url: '' });
                },

                removeDriveLinkRow(idx) {
                    this.form.google_drive_links.splice(idx, 1);
                },

                async copyArchiveLink(url) {
                    try {
                        await navigator.clipboard.writeText(url);
                        this.toastMessage = 'Tautan arsip berhasil disalin! Bisa langsung ditempel di Link Bukti LKPS.';
                        setTimeout(() => { this.toastMessage = ''; }, 3000);
                    } catch (_) {
                        window.prompt('Salin tautan arsip berikut:', url);
                    }
                },
            };
        }
    </script>
</x-app-layout>
