<x-viewer-layout>
    <x-slot name="header">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-800">
                    Tabel LKPS
                </span>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">
                    {{ $routePrefix === 'unit' ? 'Pratinjau & Editor Data Tabel' : 'Pratinjau Data Lengkap' }}
                </span>
            </div>
            <h1 class="mt-0.5 truncate text-base font-bold text-slate-900 sm:text-lg">
                {{ $tableCode }}: {{ $requirement?->title ?? $submission?->table_name ?? ('Tabel ' . $tableCode) }}
            </h1>
            <p class="mt-0.5 truncate text-xs text-slate-500">
                {{ $prodi?->name ?? 'Program Studi' }} · {{ $requirement?->module?->name ?? 'Instrumen LAM INFOKOM' }}
            </p>
        </div>
    </x-slot>

    @php
        $schema = \App\Services\LkpsExcelService::getTableSchema($tableCode);
        $summary = $submission?->summary_data ?? [];
        $headerStructure = $summary['header_structure'] ?? ($schema['header_rows'] ?? null);
        $hasMultiHeader = !empty($headerStructure) && is_array($headerStructure) && count($headerStructure) >= 2;
        $note = $summary['note'] ?? ($schema['note'] ?? null);
        $hasTotal = $summary['has_total'] ?? ($schema['has_total'] ?? false);
        $totalLabelCol = $summary['total_label_col'] ?? ($schema['total_label_col'] ?? 1);
        $sumCols = $summary['sum_cols'] ?? ($schema['sum_cols'] ?? []);

        $rows = $submission?->table_rows ?? [];
        $headers = $submission?->dynamic_headers ?? [];
        if (empty($headers)) {
            if (!empty($schema['leaf_headers'])) {
                $headers = $schema['leaf_headers'];
            } elseif (!empty($rows)) {
                $first = $rows[0];
                if (isset($first['cells']) && is_array($first['cells'])) {
                    $headers = array_map(fn($i) => 'Kolom ' . ($i + 1), array_keys($first['cells']));
                } else {
                    $headers = array_keys($first);
                }
            }
        }
        if (empty($rows) && !empty($schema['default_rows'])) {
            $rows = $schema['default_rows'];
        }
        $rowCount = count($rows);
        $rowsJson = json_encode($rows);
        $headersJson = json_encode($headers);
        $rowMerges = $summary['row_merges'] ?? [];
        $summaryWithStructure = array_merge($summary, [
            'header_structure' => $headerStructure,
            'row_merges' => $rowMerges,
            'has_total' => $hasTotal,
            'total_label_col' => $totalLabelCol,
            'sum_cols' => $sumCols,
            'note' => $note,
        ]);
        $summaryJson = json_encode($summaryWithStructure);
        $isApproved = $submission?->status === 'approved';
        $isRevision = $submission?->status === 'revision';
        $isSubmitted = in_array($submission?->status, ['submitted', 'uploaded']);
        $initialMode = request('mode', ($submission && count($submission->table_rows ?? []) > 0 ? 'preview' : 'edit'));
    @endphp

    {{-- Success Toast Notification --}}
    @if (session('status'))
        <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-semibold text-emerald-900 flex items-center gap-2 shadow-xs">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Catatan Revisi Banner (Jika ada) --}}
    @if ($submission && $submission->validation_notes)
        <div class="shrink-0 border-b {{ $isRevision ? 'border-rose-200 bg-rose-50/90 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }} px-4 py-3 sm:px-6">
            <div class="flex items-start gap-2.5">
                <svg class="h-4 w-4 shrink-0 {{ $isRevision ? 'text-rose-600' : 'text-slate-500' }} mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                </svg>
                <div class="min-w-0 flex-1 text-xs">
                    <span class="font-bold">Catatan Review Perti:</span>
                    <span class="ml-1">{{ $submission->validation_notes }}</span>
                    @if ($submission->validated_at)
                        <span class="text-slate-400 ml-1">({{ $submission->validated_at->diffForHumans() }})</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Import Modal & Component Definition Include (Loaded First) --}}
    @include('unit.submissions._lkps-modals')

    {{-- Main Container with Alpine View Mode Controller --}}
    <div x-data="{ viewMode: '{{ $initialMode }}', searchQuery: '' }" class="flex-1 flex flex-col min-h-0">

        {{-- ──────────────────────────────────────────────────────────── --}}
        {{-- TOP ACTION BAR & MODE TOGGLE                                 --}}
        {{-- ──────────────────────────────────────────────────────────── --}}
        <div class="shrink-0 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:px-6 py-2.5 select-none shadow-xs">
            <div class="flex items-center gap-3">
                <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-violet-600 hover:text-violet-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </a>
                <span class="text-slate-300">|</span>
                @if ($submission && $rowCount > 0)
                    <span class="ui-badge {{ $submission->statusBadgeClass() }}">{{ $submission->statusLabel() }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                        {{ $rowCount }} baris data
                    </span>
                    @if (isset($summary['total_sks']))
                        <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                            Total SKS: {{ $summary['total_sks'] }}
                        </span>
                    @endif
                @else
                    <span class="ui-badge bg-slate-100 text-slate-500 ring-slate-500/15">Draft Baru</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                {{-- Mode Switcher for Prodi (Unit Role) --}}
                @if ($routePrefix === 'unit')
                    <div class="inline-flex rounded-xl bg-slate-100 p-0.5 text-xs font-bold text-slate-600">
                        <button type="button" @click="viewMode = 'preview'"
                            :class="viewMode === 'preview' ? 'bg-white text-violet-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <span>Pratinjau</span>
                        </button>
                        <button type="button" @click="viewMode = 'edit'"
                            :class="viewMode === 'edit' ? 'bg-white text-violet-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            <span>Mode Edit</span>
                        </button>
                    </div>

                    <button type="button" onclick="openLkpsImportModal('{{ $tableCode }}', '{{ addslashes($requirement?->title ?? ('Tabel ' . $tableCode)) }}')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-xs hover:bg-emerald-50 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                        </svg>
                        <span>Import Excel</span>
                    </button>
                @endif

                {{-- Unduh Excel --}}
                @if ($rowCount > 0 || $submission)
                    <a href="{{ $routePrefix === 'unit' ? route('unit.lkps.export', $tableCode) : route('perti.prodis.lkps.export', [$prodi->id, $tableCode]) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition">
                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Unduh Excel</span>
                    </a>
                @endif

                {{-- Aksi Validasi Perti --}}
                @if ($routePrefix === 'perti' && $submission)
                    <div class="flex items-center gap-1.5">
                        <form method="POST" action="{{ route('perti.prodis.lkps.validate', [$prodi->id, $tableCode]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" onclick="return confirm('Setujui tabel {{ $tableCode }} ini?')"
                                class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <span>Setujui</span>
                            </button>
                        </form>
                        <button type="button" onclick="openLkpsViewerRevisionModal()"
                            class="inline-flex items-center gap-1 rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            <span>Perlu Revisi</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────────── --}}
        {{-- SECTION 1: MODE PRATINJAU (Clean View & Quick Search)        --}}
        {{-- ──────────────────────────────────────────────────────────── --}}
        <div x-show="viewMode === 'preview'" class="flex-1 flex flex-col min-h-0 overflow-hidden">
            @if ($rowCount === 0)
                <div class="flex-1 flex items-center justify-center p-8 bg-slate-50">
                    <div class="text-center max-w-md">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 mb-4 shadow-sm">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Tabel {{ $tableCode }} Belum Berisi Data</h3>
                        <p class="mt-1 text-xs text-slate-500">Mulai isi data tabel langsung di web atau import sekali klik dari berkas Excel LAM INFOKOM.</p>
                        @if ($routePrefix === 'unit')
                            <div class="mt-5 flex items-center justify-center gap-2">
                                <button type="button" @click="viewMode = 'edit'" class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                    <span>Buka Mode Edit Web</span>
                                </button>
                                <button type="button" onclick="openLkpsImportModal('{{ $tableCode }}', '{{ addslashes($requirement?->title ?? ('Tabel ' . $tableCode)) }}')"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-white px-4 py-2 text-xs font-bold text-emerald-700 shadow-xs hover:bg-emerald-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                    <span>Import File Excel</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Quick Filter Toolbar --}}
                <div class="shrink-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                    <div class="relative w-72 max-w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari dalam tabel..."
                            class="w-full rounded-xl border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-xs text-slate-800 focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 shadow-2xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span>Menampilkan <strong>{{ $rowCount }}</strong> baris</span>
                        <span>·</span>
                        <span><strong>{{ count($headers) }}</strong> kolom</span>
                        @if ($routePrefix === 'unit')
                            <button type="button" @click="viewMode = 'edit'" class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700 hover:bg-violet-100 transition">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                <span>Edit Data Tabel</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Table Canvas --}}
                <div class="flex-1 overflow-auto bg-white p-4 sm:p-6">
                    <div class="rounded-xl border border-slate-200 overflow-x-auto shadow-xs">
                        <table class="w-full text-left text-xs divide-y divide-slate-200" style="min-width: max-content;">

                            @if ($hasMultiHeader)
                                {{-- 1. MULTI-LEVEL MERGED HEADERS (Rowspan & Colspan) --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    @foreach ($headerStructure as $hRowIdx => $hRow)
                                        <tr class="divide-x divide-slate-200">
                                            @foreach ($hRow as $hCol)
                                                <th rowspan="{{ $hCol['rowspan'] ?? 1 }}"
                                                    colspan="{{ $hCol['colspan'] ?? 1 }}"
                                                    style="{{ isset($hCol['width']) ? 'min-width: ' . $hCol['width'] . ';' : 'min-width: 100px;' }}"
                                                    class="py-2.5 px-3 text-center border-b border-slate-200 font-bold {{ ($hCol['colspan'] ?? 1) > 1 ? 'bg-violet-100/70 text-violet-900 border-b-2 border-violet-300' : 'bg-slate-50 text-slate-800' }}">
                                                    {{ $hCol['label'] }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </thead>
                            @elseif ($tableCode === '2.B.1')
                                {{-- 2. TABEL 2.B.1 MATRIKS KHUSUS --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    <tr>
                                        <th class="py-3 px-3.5 w-12 text-center bg-slate-100/90 text-slate-500">No</th>
                                        <th class="py-3 px-4 min-w-[240px]">Mata Kuliah</th>
                                        <th class="py-3 px-3 w-20 text-center">SKS</th>
                                        <th class="py-3 px-3 w-24 text-center">Semester</th>
                                        @foreach ($headers as $pl)
                                            <th class="py-3 px-3 text-center min-w-[90px] bg-violet-50/60 border-l border-violet-100 text-violet-900">{{ $pl }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            @elseif ($tableCode === '2.B.2')
                                {{-- 3. TABEL 2.B.2 MATRIKS KHUSUS --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    <tr>
                                        <th class="py-3 px-3.5 w-12 text-center bg-slate-100/90 text-slate-500">No</th>
                                        <th class="py-3 px-4 min-w-[280px]">Capaian Pembelajaran Lulusan (CPL)</th>
                                        @foreach ($headers as $pl)
                                            <th class="py-3 px-3 text-center min-w-[90px] bg-violet-50/60 border-l border-violet-100 text-violet-900">{{ $pl }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            @else
                                {{-- 4. TABEL STANDAR / 1 TIER --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    <tr>
                                        @if (!in_array(strtolower($headers[0] ?? ''), ['no', 'no.']))
                                            <th class="py-3 px-3.5 w-12 text-center bg-slate-100/90 text-slate-500">No</th>
                                        @endif
                                        @foreach ($headers as $h)
                                            <th class="py-3 px-4 font-bold text-slate-800 border-r border-slate-200/60 last:border-r-0 min-w-[170px]">{{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            @endif

                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($rows as $idx => $r)
                                    <tr class="hover:bg-violet-50/30 transition"
                                        x-show="searchQuery === '' || JSON.stringify({{ json_encode($r) }}).toLowerCase().includes(searchQuery.toLowerCase())">

                                        @if ($tableCode === '2.B.1')
                                            <td class="py-2.5 px-3.5 text-center font-bold text-slate-400 bg-slate-50/40">{{ $idx + 1 }}</td>
                                            <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $r['mata_kuliah'] ?? '-' }}</td>
                                            <td class="py-2.5 px-3 text-center font-medium">{{ $r['sks'] ?? '-' }}</td>
                                            <td class="py-2.5 px-3 text-center text-slate-600">Semester {{ $r['semester'] ?? '-' }}</td>
                                            @foreach ($headers as $pl)
                                                @php $checked = isset($r['profil_lulusan'][$pl]) && ($r['profil_lulusan'][$pl] == 1 || $r['profil_lulusan'][$pl] === true); @endphp
                                                <td class="py-2.5 px-3 text-center border-l border-violet-100 bg-violet-50/10">
                                                    @if ($checked)
                                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">✓</span>
                                                    @else
                                                        <span class="text-slate-300">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @elseif ($tableCode === '2.B.2')
                                            <td class="py-2.5 px-3.5 text-center font-bold text-slate-400 bg-slate-50/40">{{ $idx + 1 }}</td>
                                            <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $r['cpl'] ?? '-' }}</td>
                                            @foreach ($headers as $pl)
                                                @php $checked = isset($r['profil_lulusan'][$pl]) && ($r['profil_lulusan'][$pl] == 1 || $r['profil_lulusan'][$pl] === true); @endphp
                                                <td class="py-2.5 px-3 text-center border-l border-violet-100 bg-violet-50/10">
                                                    @if ($checked)
                                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">✓</span>
                                                    @else
                                                        <span class="text-slate-300">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @else
                                            {{-- Rows dengan sel atau array biasa --}}
                                            @php
                                                $cells = $r['cells'] ?? (is_array($r) ? array_values($r) : []);
                                                $isTotalRow = !empty($r['is_total']) || (isset($cells[1]) && strtolower(trim($cells[1])) === 'total');
                                                $hasNoInHeaders = in_array(strtolower($headers[0] ?? ''), ['no', 'no.']);
                                            @endphp

                                            @if (!$hasNoInHeaders)
                                                <td class="py-2.5 px-3.5 text-center font-bold text-slate-400 bg-slate-50/40">{{ $idx + 1 }}</td>
                                            @endif

                                            @foreach ($headers as $cIdx => $h)
                                                @php
                                                    $activeRowMerge = null;
                                                    $isRowHidden = false;
                                                    if (!empty($rowMerges) && is_array($rowMerges)) {
                                                        foreach ($rowMerges as $rm) {
                                                            if ((int)$rm['colIdx'] === $cIdx) {
                                                                if ($idx === (int)$rm['startRow']) {
                                                                    $activeRowMerge = $rm;
                                                                    break;
                                                                } elseif ($idx > (int)$rm['startRow'] && $idx <= (int)$rm['endRow']) {
                                                                    $isRowHidden = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @if (!$isRowHidden)
                                                    <td @if($activeRowMerge) rowspan="{{ $activeRowMerge['rowspan'] }}" @endif
                                                        class="py-2.5 px-3 border-r border-slate-100 {{ $isTotalRow ? 'font-bold bg-slate-50 text-slate-900' : 'text-slate-700' }} {{ $cIdx === 0 && $hasNoInHeaders ? 'text-center font-bold text-slate-400 bg-slate-50/40' : '' }} {{ $activeRowMerge ? 'bg-violet-50/30 align-middle font-medium' : '' }}">
                                                        {{ $activeRowMerge['value'] ?? ($cells[$cIdx] ?? ($r[$h] ?? '-')) }}
                                                    </td>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- Footer Table (Total & Summary) --}}
                            @if ($hasTotal && !empty($sumCols))
                                <tfoot class="border-t-2 border-slate-200 bg-slate-50/90 font-semibold text-slate-800">
                                    <tr>
                                        @if (!in_array(strtolower($headers[0] ?? ''), ['no', 'no.']))
                                            <td class="py-2.5 px-3 text-center">∑</td>
                                        @endif
                                        @foreach ($headers as $cIdx => $h)
                                            <td class="py-2.5 px-3 text-center border-r border-slate-200">
                                                @if ($cIdx === $totalLabelCol)
                                                    <span class="font-black text-slate-900 text-xs uppercase tracking-wider">Total</span>
                                                @elseif (in_array($cIdx, $sumCols))
                                                    @php
                                                        $colSum = 0;
                                                        foreach ($rows as $item) {
                                                            $val = (float)($item['cells'][$cIdx] ?? ($item[$h] ?? 0));
                                                            $colSum += $val;
                                                        }
                                                    @endphp
                                                    <span class="font-bold text-violet-700 text-xs">{{ $colSum }}</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Note Box (Keterangan) --}}
                    @if ($note)
                        <div class="mt-4 rounded-xl border border-amber-200/80 bg-amber-50/60 p-3.5 text-xs text-amber-900 flex items-start gap-2 shadow-2xs">
                            <span class="font-bold shrink-0 text-amber-800">Keterangan :</span>
                            <span class="italic text-amber-900">{{ $note }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- ──────────────────────────────────────────────────────────── --}}
        {{-- SECTION 2: MODE EDIT (Halaman Penuh, Horizontal Scrollable)  --}}
        {{-- ──────────────────────────────────────────────────────────── --}}
        @if ($routePrefix === 'unit')
            <div x-show="viewMode === 'edit'" class="flex-1 flex flex-col min-h-0 overflow-hidden"
                 x-data="lkpsTableEditor('{{ $tableCode }}', {{ $headersJson }}, {{ $rowsJson }}, {{ $summaryJson }})">

                {{-- Toolbar Atas Editor --}}
                <div class="shrink-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center flex-wrap gap-2 text-xs text-slate-500">
                        <span class="font-medium text-slate-700">💡 Tip:</span>
                        <span>Paste langsung dari Excel (<kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Cmd+V</kbd> / <kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Ctrl+V</kbd>). <kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Enter</kbd> baris baru.</span>
                        <span class="text-slate-300">|</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><span x-text="rows.length"></span> baris</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><span x-text="headers.length"></span> kolom</span>
                        <span x-show="headerStructure" class="rounded-full bg-violet-100 px-2 py-0.5 font-bold text-violet-800">Kolom Gabungan Aktif</span>
                        <span x-show="rowMerges && rowMerges.length > 0" class="rounded-full bg-indigo-100 px-2 py-0.5 font-bold text-indigo-800">
                            <span x-text="rowMerges.length"></span> Baris Digabung
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="resetToDefaultColumns()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition" title="Kembalikan nama kolom dan baris ke template standar LAM INFOKOM">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            Format Standar
                        </button>

                        {{-- Tombol Gabungkan (Kolom / Baris) --}}
                        <button type="button" @click="openMergeModal('columns')" class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50/90 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition shadow-xs" title="Gabungkan beberapa kolom atau baris layaknya Excel (bisa lebih dari 2)">
                            <span class="text-sm">🔲</span>
                            <span>Gabungkan (Kolom / Baris)</span>
                        </button>

                        {{-- Tombol Pisahkan / Kelola Gabungan jika sedang ada gabungan kolom atau baris --}}
                        <button x-show="headerStructure || (rowMerges && rowMerges.length > 0)" type="button" @click="openMergeModal('active')" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition" title="Lihat daftar gabungan kolom & baris, atau pisahkan kembali">
                            <span>↺ Kelola Gabungan</span>
                        </button>

                        @if ($tableCode === '2.B.1' || $tableCode === '2.B.2')
                            <button type="button" @click="addPlColumn()" class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50/80 px-2.5 py-1.5 text-xs font-bold text-violet-700 hover:bg-violet-100 transition shadow-xs">
                                <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                + Tambah Kolom PL
                            </button>
                        @else
                            <button type="button" @click="addColumn()" class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50/80 px-3 py-1.5 text-xs font-bold text-violet-700 hover:bg-violet-100 transition shadow-xs">
                                <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                + Tambah Kolom
                            </button>
                        @endif

                        <button type="button" @click="tableCode === '2.B.1' ? addRow2B1() : (tableCode === '2.B.2' ? addRow2B2() : addRow())" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-violet-700 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            + Tambah Baris
                        </button>

                        <button type="button" @click="addTotalRow()" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs" title="Tambahkan baris Total yang menjumlahkan kolom angka secara otomatis">
                            <span class="font-bold text-violet-600 text-sm leading-none">∑</span>
                            <span>+ Baris Total</span>
                        </button>

                        <button type="button" @click="saveTableData()" :disabled="saving"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-1.5 text-xs font-bold text-white shadow-xs hover:from-emerald-700 hover:to-teal-700 transition disabled:opacity-50">
                            <svg x-show="!saving" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <svg x-show="saving" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan Data'">Simpan Data</span>
                        </button>
                    </div>
                </div>

                {{-- Canvas Tabel Luas & Dapat Digeser-geser --}}
                <div class="flex-1 overflow-auto bg-slate-100 p-4 sm:p-6">
                    <div class="rounded-xl border border-slate-200 bg-white overflow-x-auto shadow-sm">
                        <table class="w-full text-left text-xs divide-y divide-slate-200" style="min-width: max-content;">

                            @if ($tableCode === '2.B.1')
                                {{-- TABEL 2.B.1 KHUSUS --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    <tr>
                                        <th class="py-3 px-3 w-12 text-center bg-slate-100">No</th>
                                        <th class="py-3 px-4 min-w-[260px]">Mata Kuliah</th>
                                        <th class="py-3 px-3 w-20 text-center">SKS</th>
                                        <th class="py-3 px-3 w-24 text-center">Semester</th>
                                        <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                            <th class="py-2.5 px-2 text-center min-w-[110px] bg-violet-50/40 border-l border-violet-100">
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="text" x-model="headers[plIdx]"
                                                        class="w-full text-center bg-transparent border border-transparent hover:border-slate-300 focus:border-violet-500 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                                        title="Klik untuk mengubah nama Profil Lulusan">
                                                    <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1" title="Hapus kolom PL">×</button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-3 px-3 w-12 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(row, idx) in rows" :key="idx">
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-3 text-center font-bold text-slate-400 bg-slate-50/30" x-text="idx + 1"></td>
                                            <td class="py-2 px-3 min-w-[260px]">
                                                <input type="text" x-model="row.mata_kuliah" placeholder="Nama mata kuliah..."
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <td class="py-2 px-3 w-20">
                                                <input type="number" min="0" max="10" x-model.number="row.sks" @input="calcSummary2B1()"
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <td class="py-2 px-3 w-24">
                                                <input type="number" min="1" max="8" x-model.number="row.semester"
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                                <td class="py-2 px-2 text-center bg-violet-50/15 border-l border-violet-100/60 min-w-[110px]">
                                                    <input type="checkbox"
                                                        :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                                        @change="togglePlCheck(row, pl, $event.target.checked)"
                                                        class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                                </td>
                                            </template>
                                            <td class="py-2 px-3 text-center w-12">
                                                <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>

                            @elseif ($tableCode === '2.B.2')
                                {{-- TABEL 2.B.2 KHUSUS --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    <tr>
                                        <th class="py-3 px-4 min-w-[320px]">Capaian Pembelajaran Lulusan (CPL)</th>
                                        <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                            <th class="py-2.5 px-2 text-center min-w-[110px] bg-violet-50/40 border-l border-violet-100">
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="text" x-model="headers[plIdx]"
                                                        class="w-full text-center bg-transparent border border-transparent hover:border-slate-300 focus:border-violet-500 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                                        title="Klik untuk mengubah nama Profil Lulusan">
                                                    <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1">×</button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-3 px-3 w-12 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(row, idx) in rows" :key="idx">
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-4 min-w-[320px]">
                                                <input type="text" x-model="row.cpl" placeholder="Contoh: CPL 01 - Mampu merancang sistem..."
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                                <td class="py-2 px-2 text-center bg-violet-50/15 border-l border-violet-100/60 min-w-[110px]">
                                                    <input type="checkbox"
                                                        :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                                        @change="togglePlCheck(row, pl, $event.target.checked)"
                                                        class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                                </td>
                                            </template>
                                            <td class="py-2 px-3 text-center w-12">
                                                <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>

                            @else
                                {{-- TABEL UMUM / STANDAR / MULTI-LEVEL MERGED HEADERS --}}
                                <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                                    {{-- Row 1 Multi-Tier (Jika Kolom Tergabung / 2 Tingkat) --}}
                                    <tr x-show="headerStructure && headerStructure.length >= 2" class="divide-x divide-slate-200">
                                        <template x-for="(col, cIdx) in (headerStructure ? headerStructure[0] : [])" :key="'h0-' + cIdx">
                                            <th :rowspan="col.rowspan || 1"
                                                :colspan="col.colspan || 1"
                                                :style="col.width ? 'min-width: ' + col.width : 'min-width: 120px;'"
                                                class="py-2.5 px-3 text-center border-b border-slate-200"
                                                :class="(col.colspan || 1) > 1 ? 'bg-violet-100 text-violet-900 border-b-2 border-violet-400 font-bold' : 'bg-slate-50 text-slate-700 font-bold'">
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="text" x-model="col.label"
                                                        class="w-full text-center border-0 bg-transparent text-xs font-bold p-0 focus:ring-0 focus:bg-white rounded transition"
                                                        :class="(col.colspan || 1) > 1 ? 'text-violet-900' : 'text-slate-800'"
                                                        title="Klik untuk mengubah nama header ini">
                                                </div>
                                            </th>
                                        </template>
                                        <th rowspan="2" class="py-3 px-3 w-12 text-center text-slate-400 font-semibold bg-slate-50 border-b border-slate-200">Hapus</th>
                                    </tr>

                                    {{-- Row 2 Sub-Kolom Multi-Tier --}}
                                    <tr x-show="headerStructure && headerStructure.length >= 2" class="divide-x divide-slate-200 bg-slate-50/90">
                                        <template x-for="(subCol, sIdx) in (headerStructure && headerStructure[1] ? headerStructure[1] : [])" :key="'h1-' + sIdx">
                                            <th :style="subCol.width ? 'min-width: ' + subCol.width : 'min-width: 85px;'"
                                                class="py-2 px-2 text-center border-b border-slate-200 text-slate-700 font-bold text-xs">
                                                <input type="text" x-model="subCol.label"
                                                    @input="if (headers[subCol.colIdx] !== undefined) headers[subCol.colIdx] = subCol.label"
                                                    class="w-full text-center border-0 bg-transparent text-xs font-bold text-slate-800 p-0 focus:ring-0 focus:bg-white rounded transition"
                                                    title="Klik untuk mengubah nama sub-kolom ini">
                                            </th>
                                        </template>
                                    </tr>

                                    {{-- Single-tier Header (Tabel Standar 1 Baris Header) --}}
                                    <tr x-show="!headerStructure || headerStructure.length < 2">
                                        <template x-for="(col, cIdx) in headers" :key="'col-' + cIdx">
                                            <th class="py-2.5 px-3 min-w-[170px] border-r border-slate-200/80 bg-slate-50">
                                                <div class="flex items-center justify-between gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 focus-within:border-violet-500 focus-within:ring-1 focus-within:ring-violet-500 shadow-2xs">
                                                    <input type="text" x-model="headers[cIdx]"
                                                        class="w-full border-0 text-xs font-bold text-slate-800 p-0 focus:ring-0 focus:outline-none"
                                                        title="Klik untuk mengubah nama judul kolom ini">
                                                    <button type="button" @click="removeColumn(cIdx)"
                                                        class="text-slate-300 hover:text-rose-600 p-0.5 rounded transition shrink-0"
                                                        title="Hapus kolom ini">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-2 px-2 w-10 text-center bg-slate-50">
                                            <button type="button" @click="addColumn()" class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-100 text-violet-700 hover:bg-violet-200 transition font-bold" title="Tambah kolom baru">+</button>
                                        </th>
                                        <th class="py-3 px-3 w-12 text-center text-slate-400 font-semibold bg-slate-50">Hapus</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(row, rIdx) in rows" :key="rIdx">
                                        <tr class="hover:bg-slate-50/70 transition" :class="row.is_total ? 'bg-slate-50/90 font-bold' : ''">
                                            <template x-for="(col, cIdx) in headers" :key="'cell-' + cIdx">
                                                <td x-show="!isCellRowHidden(rIdx, cIdx)"
                                                    class="py-2 px-2.5 border-r border-slate-100"
                                                    :rowspan="getCellRowMerge(rIdx, cIdx) ? getCellRowMerge(rIdx, cIdx).rowspan : 1"
                                                    :class="getCellRowMerge(rIdx, cIdx) ? 'bg-violet-50/50 align-middle ring-1 ring-inset ring-violet-200' : ''"
                                                    :style="(headerStructure && headerStructure[1] && headerStructure[1].find(s => s.colIdx === cIdx)) ? 'min-width: ' + (headerStructure[1].find(s => s.colIdx === cIdx).width || '85px') : (cIdx === 0 && (headers[0] === 'No' || headers[0] === 'No.') ? 'min-width: 50px; width: 50px;' : 'min-width: 170px;')">
                                                    <div class="relative">
                                                        <input type="text"
                                                            :id="'cell-' + rIdx + '-' + cIdx"
                                                            :value="row.cells ? row.cells[cIdx] : ''"
                                                            @input="setCellVal(row, cIdx, $event.target.value)"
                                                            @paste="handleExcelPaste($event, rIdx, cIdx)"
                                                            @keydown="handleKeyNavigation($event, rIdx, cIdx)"
                                                            :placeholder="row.is_total && cIdx === totalLabelCol ? 'Total' : 'Isi ' + col + '...'"
                                                            class="w-full rounded-lg border-slate-200 text-xs py-2 px-3 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 bg-white shadow-2xs font-sans"
                                                            :class="row.is_total ? 'font-bold text-slate-900 bg-slate-100/50' : (cIdx === 0 && (headers[0] === 'No' || headers[0] === 'No.') ? 'text-center font-bold text-slate-500' : '')">
                                                        <span x-show="getCellRowMerge(rIdx, cIdx)"
                                                            class="absolute -top-2 right-1 inline-flex items-center rounded bg-violet-200/90 px-1 py-0.5 text-[9px] font-bold text-violet-800 shadow-2xs pointer-events-none">
                                                            Gabung <span x-text="getCellRowMerge(rIdx, cIdx) ? getCellRowMerge(rIdx, cIdx).rowspan : ''"></span> baris
                                                        </span>
                                                    </div>
                                                </td>
                                            </template>
                                            <td x-show="!headerStructure || headerStructure.length < 2" class="py-2 px-2 text-center text-slate-300">·</td>
                                            <td class="py-2 px-3 text-center w-12">
                                                <button type="button" @click="removeRow(rIdx)" class="text-rose-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Hapus baris ini">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="rows.length === 0">
                                        <td :colspan="headers.length + 3" class="text-center py-12 text-slate-400">
                                            <p class="font-medium">Belum ada baris data pada tabel ini.</p>
                                            <p class="text-xs mt-1">Klik tombol <span class="font-bold text-violet-600">+ Tambah Baris</span> di atas untuk mulai mengisi, atau tempel (Paste) langsung dari Excel.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            @endif

                            {{-- Footer Live Sum / Total Row --}}
                            <tfoot class="border-t-2 border-slate-200 bg-slate-50/90 font-semibold text-slate-700">
                                <tr x-show="hasTotal">
                                    <template x-for="(col, cIdx) in headers" :key="'foot-' + cIdx">
                                        <td class="py-2.5 px-3 text-center border-r border-slate-200">
                                            <span x-show="cIdx === totalLabelCol" class="font-black text-slate-800 text-xs uppercase tracking-wider">Total</span>
                                            <span x-show="sumCols.includes(cIdx)" class="font-bold text-violet-700 text-xs" x-text="calcColumnSum(cIdx)"></span>
                                            <span x-show="cIdx !== totalLabelCol && !sumCols.includes(cIdx)" class="text-slate-300">-</span>
                                        </td>
                                    </template>
                                    <td x-show="!headerStructure || headerStructure.length < 2"></td>
                                    <td class="w-12"></td>
                                </tr>
                                <tr>
                                    <td :colspan="headers.length + 3" class="py-2 px-4 bg-slate-100/70">
                                        <div class="flex items-center justify-between text-xs text-slate-600">
                                            <span>Total: <strong x-text="rows.length"></strong> baris data terisi</span>
                                            <span><strong x-text="headers.length"></strong> kolom aktif (dapat digeser ke kanan)</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Editable Note / Keterangan Box --}}
                    <div x-show="note || (summary && summary.note)" class="mt-4 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5 text-xs text-amber-900 shadow-2xs">
                        <div class="flex items-center justify-between mb-1.5 font-bold">
                            <span>Keterangan / Catatan Kaki Tabel:</span>
                            <span class="text-[11px] font-normal text-amber-700">Akan tercetak di bawah tabel dan diexport ke berkas Excel</span>
                        </div>
                        <input type="text" x-model="note" placeholder="Contoh: *) Pustakawan adalah staf perpustakaan yang memiliki ijazah atau sertifikat kompetensi..."
                            class="w-full rounded-lg border-amber-200 bg-white text-xs py-2 px-3 text-slate-800 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 shadow-2xs">
                    </div>
                </div>

                {{-- Modal Gabungkan Kolom atau Baris (Colspan & Rowspan) --}}
                <div x-show="showMergeModal" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex min-h-screen items-center justify-center p-4">
                    <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                        {{-- Modal Header & Tabs --}}
                        <div class="border-b border-slate-100 bg-slate-50/70 p-4 sm:p-5">
                            <div class="flex items-center justify-between pb-3">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 font-bold">🔲</span>
                                    <span>Fitur Gabungkan Kolom / Baris (Excel Style)</span>
                                </h3>
                                <button type="button" @click="closeMergeModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
                            </div>

                            {{-- Navigation Tabs --}}
                            <div class="flex items-center gap-1.5 pt-1">
                                <button type="button" @click="mergeTab = 'columns'"
                                    :class="mergeTab === 'columns' ? 'bg-white text-violet-700 shadow-xs border-violet-200' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200/70'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-bold transition">
                                    <span>🔲 Gabung Kolom (Colspan)</span>
                                </button>
                                <button type="button" @click="mergeTab = 'rows'"
                                    :class="mergeTab === 'rows' ? 'bg-white text-violet-700 shadow-xs border-violet-200' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200/70'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-bold transition">
                                    <span>🧱 Gabung Baris (Rowspan)</span>
                                </button>
                                <button type="button" @click="mergeTab = 'active'"
                                    :class="mergeTab === 'active' ? 'bg-white text-violet-700 shadow-xs border-violet-200' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200/70'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-bold transition">
                                    <span>📋 Kelola Gabungan Aktif</span>
                                    <span x-show="headerStructure || (rowMerges && rowMerges.length > 0)"
                                        class="h-2 w-2 rounded-full bg-violet-600"></span>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 1: GABUNG KOLOM (COLSPAN) --}}
                        <div x-show="mergeTab === 'columns'" class="p-4 sm:p-6 space-y-4 text-xs">
                            <div class="rounded-xl bg-violet-50/70 border border-violet-100 p-3 text-slate-700 leading-relaxed">
                                💡 <strong>Petunjuk:</strong> Centang kolom-kolom yang ingin digabungkan di bawah 1 judul grup bertingkat (<strong>bisa pilih lebih dari 2 kolom</strong>, misalnya 8 kolom S3 s/d SMA pada Tabel 1.A.5).
                            </div>

                            <div>
                                <div class="flex items-center justify-between pb-1.5">
                                    <span class="font-bold text-slate-800">1. Centang Kolom yang Ingin Digabungkan:</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="selectAllColumnsToMerge()" class="text-violet-600 hover:underline font-bold">Pilih Semua</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button" @click="clearColumnsToMerge()" class="text-slate-500 hover:underline">Bersihkan</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-52 overflow-y-auto p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <template x-for="(colName, cIdx) in headers" :key="'col-select-' + cIdx">
                                        <label :class="selectedColumnsToMerge.includes(cIdx) ? 'border-violet-500 bg-violet-100/80 text-violet-900 ring-2 ring-violet-400 font-bold' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium'"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition select-none text-xs shadow-2xs">
                                            <input type="checkbox" :checked="selectedColumnsToMerge.includes(cIdx)" @change="toggleColumnToMerge(cIdx)"
                                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500 h-4 w-4 shrink-0">
                                            <span class="truncate" x-text="(cIdx + 1) + '. ' + colName"></span>
                                        </label>
                                    </template>
                                </div>

                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2.5 py-0.5 font-bold text-violet-800">
                                        <strong x-text="selectedColumnsToMerge.length"></strong> kolom dipilih (Bisa lebih dari 2)
                                    </span>
                                    <span class="text-[11px] text-slate-500" x-show="selectedColumnsToMerge.length >= 2">
                                        Rentang: Kolom <strong x-text="Math.min(...selectedColumnsToMerge) + 1"></strong> s/d <strong x-text="Math.max(...selectedColumnsToMerge) + 1"></strong>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">2. Judul Header Induk (Merged Header Title):</label>
                                <input type="text" x-model="mergeGroupTitle" placeholder="Contoh: Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir"
                                    class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 shadow-2xs">
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="closeMergeModal()" class="rounded-xl px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                                <button type="button" @click="applyMultiColumnMerge()" class="rounded-xl bg-violet-600 px-5 py-2 font-bold text-white hover:bg-violet-700 transition shadow-xs">
                                    ✓ Terapkan Gabung Kolom
                                </button>
                            </div>
                        </div>

                        {{-- TAB 2: GABUNG BARIS (ROWSPAN) --}}
                        <div x-show="mergeTab === 'rows'" class="p-4 sm:p-6 space-y-4 text-xs">
                            <div class="rounded-xl bg-violet-50/70 border border-violet-100 p-3 text-slate-700 leading-relaxed">
                                💡 <strong>Petunjuk:</strong> Pilih kolom dan centang baris-baris yang ingin disatukan selnya secara vertikal (<strong>bisa pilih lebih dari 2 baris</strong>, misalnya Baris 1, 2, 3 untuk unit kerja atau kategori yang sama).
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">1. Pilih Kolom Target Penggabungan Baris:</label>
                                <select x-model.number="rowMergeColIdx" class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 shadow-2xs">
                                    <template x-for="(h, idx) in headers" :key="'rmc-' + idx">
                                        <option :value="idx" x-text="'Kolom ' + (idx + 1) + ': ' + h"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between pb-1.5">
                                    <span class="font-bold text-slate-800">2. Centang Baris yang Ingin Digabungkan Vertikal:</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="selectAllRowsToMerge()" class="text-violet-600 hover:underline font-bold">Pilih Semua</button>
                                        <span class="text-slate-300">|</span>
                                        <button type="button" @click="clearRowsToMerge()" class="text-slate-500 hover:underline">Bersihkan</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <template x-for="(row, rIdx) in rows" :key="'row-select-' + rIdx">
                                        <label :class="selectedRowsToMerge.includes(rIdx) ? 'border-violet-500 bg-violet-100/80 text-violet-900 ring-2 ring-violet-400 font-bold' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium'"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition select-none text-xs shadow-2xs">
                                            <input type="checkbox" :checked="selectedRowsToMerge.includes(rIdx)" @change="toggleRowToMerge(rIdx)"
                                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500 h-4 w-4 shrink-0">
                                            <span class="truncate">
                                                Baris <strong x-text="rIdx + 1"></strong>:
                                                <span class="text-slate-500 font-normal" x-text="row.cells ? (row.cells[rowMergeColIdx] || '(kosong)') : ''"></span>
                                            </span>
                                        </label>
                                    </template>
                                </div>

                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2.5 py-0.5 font-bold text-violet-800">
                                        <strong x-text="selectedRowsToMerge.length"></strong> baris dipilih (Bisa lebih dari 2)
                                    </span>
                                    <span class="text-[11px] text-slate-500" x-show="selectedRowsToMerge.length >= 2">
                                        Rentang: Baris <strong x-text="Math.min(...selectedRowsToMerge) + 1"></strong> s/d <strong x-text="Math.max(...selectedRowsToMerge) + 1"></strong>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">3. Teks / Isi Sel Gabungan:</label>
                                <input type="text" x-model="rowMergeValue" placeholder="Teks yang akan ditampilkan pada sel vertikal gabungan..."
                                    class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 shadow-2xs">
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="closeMergeModal()" class="rounded-xl px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                                <button type="button" @click="applyMultiRowMerge()" class="rounded-xl bg-violet-600 px-5 py-2 font-bold text-white hover:bg-violet-700 transition shadow-xs">
                                    ✓ Terapkan Gabung Baris
                                </button>
                            </div>
                        </div>

                        {{-- TAB 3: KELOLA GABUNGAN AKTIF --}}
                        <div x-show="mergeTab === 'active'" class="p-4 sm:p-6 space-y-5 text-xs">
                            {{-- Status Gabungan Kolom --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-bold text-slate-800">Gabungan Kolom (Header Colspan)</h4>
                                    <button x-show="columnGroups && columnGroups.length > 0" type="button" @click="unmergeColumns()" class="text-rose-600 hover:underline font-bold text-[11px]">
                                        ↺ Pisahkan Semua Kolom
                                    </button>
                                </div>
                                <div x-show="columnGroups && columnGroups.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="(grp, gIdx) in columnGroups" :key="'grp-' + gIdx">
                                        <div class="flex items-center justify-between rounded-xl border border-violet-200 bg-violet-50/50 p-2.5 shadow-2xs">
                                            <div>
                                                <span class="font-bold text-violet-900" x-text="grp.title"></span>
                                                <p class="text-slate-500 text-[11px] mt-0.5">
                                                    Kolom <strong x-text="grp.startCol + 1"></strong> s/d <strong x-text="grp.endCol + 1"></strong>
                                                    (<span x-text="grp.endCol - grp.startCol + 1"></span> kolom digabung)
                                                </p>
                                            </div>
                                            <button type="button" @click="removeColumnGroup(gIdx)" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs">
                                                Pisahkan
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="!columnGroups || columnGroups.length === 0">
                                    <p class="text-slate-400 italic">Tidak ada gabungan kolom header yang aktif (tabel 1 tingkat standar).</p>
                                </div>
                            </div>

                            {{-- Status Gabungan Baris --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-bold text-slate-800">Gabungan Baris Vertikal (Rowspan Sel)</h4>
                                    <button x-show="rowMerges && rowMerges.length > 0" type="button" @click="clearAllRowMerges()" class="text-rose-600 hover:underline font-bold text-[11px]">
                                        Hapus Semua Gabungan Baris
                                    </button>
                                </div>
                                <div x-show="rowMerges && rowMerges.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="(rm, rmIdx) in rowMerges" :key="'active-rm-' + rmIdx">
                                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-2.5 shadow-2xs">
                                            <div>
                                                <span class="font-bold text-slate-800" x-text="rm.colName"></span>:
                                                <span class="text-slate-600">Baris <strong x-text="rm.startRow + 1"></strong> s/d <strong x-text="rm.endRow + 1"></strong></span>
                                                <span class="ml-1 text-[11px] text-violet-700 font-semibold" x-text="'(Isi: ' + (rm.value || '-') + ')'"></span>
                                            </div>
                                            <button type="button" @click="removeRowMerge(rmIdx)" class="rounded-lg border border-slate-200 bg-white px-2 py-1 font-semibold text-rose-600 hover:bg-rose-50 transition shadow-2xs">
                                                Hapus
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="!rowMerges || rowMerges.length === 0">
                                    <p class="text-slate-400 italic">Belum ada baris yang digabungkan secara vertikal.</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-2 border-t border-slate-100">
                                <button type="button" @click="closeMergeModal()" class="rounded-xl bg-slate-800 px-5 py-2 font-bold text-white hover:bg-slate-900 transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bar Aksi Bawah Sticky --}}
                <div class="shrink-0 bg-white border-t border-slate-200 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                    <div>
                        <p class="text-xs font-semibold text-emerald-700" x-text="saveStatusMessage"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="hasTotal">
                            <button type="button" @click="addTotalRow()"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition">
                                <span>∑ Tambah Baris Total</span>
                            </button>
                        </template>
                        <button type="button" @click="tableCode === '2.B.1' ? addRow2B1() : (tableCode === '2.B.2' ? addRow2B2() : addRow())"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition">
                            <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>+ Tambah Baris Baru</span>
                        </button>
                        <button type="button" @click="saveTableData()" :disabled="saving"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-md shadow-violet-500/20 hover:from-violet-700 hover:to-indigo-700 transition disabled:opacity-50">
                            <svg x-show="!saving" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <svg x-show="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan & Ajukan Data Tabel'">Simpan &amp; Ajukan Data Tabel</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Perlu Revisi untuk LKPS Viewer (Role Perti) --}}
    @if ($routePrefix === 'perti' && $submission)
        <div id="lkpsViewerRevisionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </span>
                            Catatan Revisi Tabel {{ $tableCode }}
                        </h3>
                        <button type="button" onclick="closeLkpsViewerRevisionModal()" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('perti.prodis.lkps.validate', [$prodi->id, $tableCode]) }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="revision">

                        <div>
                            <label for="viewer_validation_notes" class="block text-xs font-bold text-slate-700">
                                Poin-poin Perbaikan / Koreksi Data:
                            </label>
                            <textarea id="viewer_validation_notes" name="notes" rows="4" required
                                placeholder="Jelaskan kolom atau baris data mana yang perlu diperbaiki oleh Program Studi..."
                                class="mt-1 w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 focus:border-rose-500 focus:ring-1 focus:ring-rose-500">{{ $submission->validation_notes }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" onclick="closeLkpsViewerRevisionModal()"
                                class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition">
                                Kirim Catatan Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Script Modal Revisi (Perti) --}}
    <script>
        function openLkpsViewerRevisionModal() {
            document.getElementById('lkpsViewerRevisionModal')?.classList.remove('hidden');
        }
        function closeLkpsViewerRevisionModal() {
            document.getElementById('lkpsViewerRevisionModal')?.classList.add('hidden');
        }
    </script>
</x-viewer-layout>
