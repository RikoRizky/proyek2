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

        // Pastikan struktur header mencakup semua kolom dinamis jika sebelumnya sudah ditambah kolom
        if ($hasMultiHeader && !empty($headers)) {
            $coveredCols = 0;
            foreach ($headerStructure[0] as $hCol) {
                $coveredCols += ($hCol['colspan'] ?? 1);
            }
            if (count($headers) > $coveredCols) {
                for ($c = $coveredCols; $c < count($headers); $c++) {
                    $headerStructure[0][] = [
                        'label' => $headers[$c] ?? ('Kolom ' . ($c + 1)),
                        'rowspan' => 2,
                        'colIdx' => $c,
                        'width' => '170px',
                    ];
                }
            }
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
    <div x-data="{ viewMode: '{{ $initialMode }}', searchQuery: '' }" class="flex-1 min-h-0 min-w-0 flex flex-col w-full max-w-full overflow-hidden">

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
                        <button type="button" onclick="openLkpsViewerValidateModal('{{ $submission->status === 'approved' ? 'approved' : 'revision' }}')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-violet-700 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            <span>Validasi Tabel</span>
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
            <div x-show="viewMode === 'edit'" class="flex-1 min-h-0 min-w-0 flex flex-col w-full max-w-full overflow-hidden"
                 x-data="lkpsTableEditor('{{ $tableCode }}', {{ $headersJson }}, {{ $rowsJson }}, {{ $summaryJson }})">

                {{-- Toolbar Atas Editor (Excel Style Toolbar) --}}
                <div class="shrink-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center flex-wrap gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                            Editor Excel
                        </span>
                        <span>Klik sel &amp; ketik. Gunakan <kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Panah</kbd> / <kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Tab</kbd> / <kbd class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 border border-slate-300">Enter</kbd> untuk navigasi.</span>
                        <span class="text-slate-300">|</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><span x-text="rows.length"></span> baris</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-bold text-slate-700"><span x-text="headers.length"></span> kolom</span>
                        <span x-show="headerStructure" class="rounded-full bg-violet-100 px-2 py-0.5 font-bold text-violet-800">Kolom Gabungan Aktif</span>
                        <span x-show="rowMerges && rowMerges.length > 0" class="rounded-full bg-indigo-100 px-2 py-0.5 font-bold text-indigo-800">
                            <span x-text="rowMerges.length"></span> Baris Digabung
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Tombol Asisten Tempel dari Excel (Paste) --}}
                        <button type="button" @click="openPasteModal()"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-xs"
                            title="Tempel data yang disalin (Copy) dari berkas Excel secara langsung">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                            </svg>
                            <span>Tempel dari Excel</span>
                        </button>

                        {{-- Tombol Tambah Baris Cepat (+1, +5, +10) --}}
                        <div class="inline-flex rounded-lg border border-slate-200 bg-white shadow-xs p-0.5">
                            <button type="button" @click="tableCode === '2.B.1' ? addRow2B1() : (tableCode === '2.B.2' ? addRow2B2() : addRow())"
                                class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-bold text-violet-700 hover:bg-violet-50 transition" title="Tambah 1 baris kosong">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                <span>+1 Baris</span>
                            </button>
                            <span class="text-slate-200 my-auto">|</span>
                            <button type="button" @click="addRowsBatch(5)" class="rounded-md px-2 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-50 transition" title="Tambah 5 baris">
                                +5
                            </button>
                            <span class="text-slate-200 my-auto">|</span>
                            <button type="button" @click="addRowsBatch(10)" class="rounded-md px-2 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-50 transition" title="Tambah 10 baris">
                                +10
                            </button>
                        </div>

                        {{-- Tombol Tambah Kolom Bebas --}}
                        <button type="button" @click="addColumn()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs" title="Tambah kolom baru ke tabel">
                            <span class="font-bold text-emerald-600 leading-none text-sm">+</span>
                            <span>Tambah Kolom</span>
                        </button>

                        {{-- Tombol Kembalikan Format Standar LAM INFOKOM --}}
                        <button type="button" @click="resetToDefaultColumns()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs" title="Pulihkan nama kolom ke format template resmi LAM INFOKOM (tidak mengunci kebebasan Anda)">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            <span>Format Standar</span>
                        </button>

                        {{-- Tombol Gabungkan Sel / Baris (Rowspan) --}}
                        <button type="button" @click="openMergeModal('rows')" class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50/90 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition shadow-xs" title="Gabungkan beberapa baris yang memiliki nilai sama secara vertikal">
                            <span class="text-xs">🧱</span>
                            <span>Gabung Baris</span>
                        </button>

                        {{-- Tombol Kelola Gabungan jika sedang ada gabungan kolom atau baris --}}
                        <button x-show="headerStructure || (rowMerges && rowMerges.length > 0)" type="button" @click="openMergeModal('active')" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition shadow-xs" title="Lihat daftar gabungan kolom & baris, atau pisahkan kembali">
                            <span>↺ Kelola Gabungan</span>
                        </button>

                        {{-- Bebas Toggle Baris Total (Excel Total Row, TIDAK DIPAKSA) --}}
                        <button type="button" @click="toggleTotalRow()"
                            :class="hasTotal ? 'bg-violet-100 text-violet-800 border-violet-300 font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 font-semibold'"
                            class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-1.5 text-xs transition shadow-xs"
                            title="Aktifkan atau matikan baris Total / Penjumlahan di bawah tabel">
                            <span class="font-bold leading-none" :class="hasTotal ? 'text-violet-700' : 'text-slate-400'">∑</span>
                            <span x-text="hasTotal ? 'Baris Total: Aktif' : 'Baris Total: Nonaktif'"></span>
                        </button>

                        {{-- Bersihkan Baris Kosong --}}
                        <button type="button" @click="removeEmptyRows()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition shadow-xs" title="Hapus baris yang seluruhnya kosong">
                            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            <span>Bersihkan Kosong</span>
                        </button>

                        {{-- Hapus Baris Total Tersasar (Hanya muncul jika tabel non-numerik menyimpan baris bertuliskan Total) --}}
                        <button x-show="!hasTotal && rows.some(r => r.is_total || (r.cells && r.cells.some(c => String(c).trim().toLowerCase() === 'total')))"
                            type="button" @click="removeTotalRows()"
                            class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition shadow-xs"
                            title="Hapus baris yang berisi tulisan Total dari tabel ini">
                            <span>🗑️ Hapus Baris Total</span>
                        </button>

                        {{-- Simpan Data --}}
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

                {{-- Formula Bar (Bilah Rumus & Koordinat Sel Aktif Excel + Tombol Navigasi Scroll) --}}
                <div class="shrink-0 bg-slate-50 border-b border-slate-300 px-4 sm:px-6 py-1.5 flex items-center gap-2 text-xs select-none">
                    <div class="flex items-center gap-1 px-2 py-0.5 bg-white border border-slate-300 rounded font-mono text-slate-800 font-bold shadow-2xs min-w-[70px] justify-center" title="Koordinat Sel Aktif">
                        <span class="text-emerald-700 text-[10px] uppercase font-bold">Sel</span>
                        <span x-text="getActiveCellCoord()"></span>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-slate-200/90 text-[11px] font-semibold text-slate-700 truncate max-w-[220px]" x-text="getActiveColName()" :title="getActiveColName()"></span>
                    <span class="font-serif italic font-bold text-slate-400 select-none text-xs px-1">fx</span>
                    <input type="text"
                        :value="activeCellVal"
                        @input="updateActiveCellValue($event.target.value)"
                        placeholder="Klik sel pada tabel di bawah untuk mulai mengedit atau melihat isinya..."
                        class="flex-1 rounded border border-slate-300 bg-white px-2.5 py-1 text-xs text-slate-800 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-2xs font-sans">
                    <span x-show="saveStatusMessage" x-text="saveStatusMessage" class="text-xs font-bold text-emerald-700 animate-pulse shrink-0"></span>

                    {{-- Tombol Navigasi Scroll Cepat (Geser Kiri / Kanan) --}}
                    <div class="flex items-center gap-1 shrink-0 border-l border-slate-300 pl-2">
                        <span class="text-[10px] text-slate-500 font-semibold hidden md:inline">Geser:</span>
                        <button type="button" @click="$refs.tableContainer.scrollBy({ left: -320, behavior: 'smooth' })"
                            class="h-6 px-2 flex items-center justify-center rounded bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 shadow-2xs font-bold text-xs"
                            title="Geser tabel ke kiri">◀ Kiri</button>
                        <button type="button" @click="$refs.tableContainer.scrollBy({ left: 320, behavior: 'smooth' })"
                            class="h-6 px-2 flex items-center justify-center rounded bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 shadow-2xs font-bold text-xs"
                            title="Geser tabel ke kanan">Kanan ▶</button>
                    </div>
                </div>

                {{-- CSS Scrollbar Kustom untuk Navigasi 4 Arah --}}
                <style>
                    .excel-viewport {
                        scrollbar-width: auto !important;
                        scrollbar-color: #64748b #e2e8f0 !important;
                        -webkit-overflow-scrolling: touch;
                        overflow-x: scroll !important;
                        overflow-y: auto !important;
                    }
                    .excel-viewport::-webkit-scrollbar {
                        width: 14px !important;
                        height: 14px !important;
                        display: block !important;
                    }
                    .excel-viewport::-webkit-scrollbar-track {
                        background: #f1f5f9 !important;
                        border-top: 1px solid #cbd5e1 !important;
                    }
                    .excel-viewport::-webkit-scrollbar-thumb {
                        background: #64748b !important;
                        border-radius: 7px !important;
                        border: 3px solid #f1f5f9 !important;
                    }
                    .excel-viewport::-webkit-scrollbar-thumb:hover {
                        background: #334155 !important;
                    }
                    .excel-viewport::-webkit-scrollbar-corner {
                        background: #e2e8f0 !important;
                    }
                </style>

                {{-- Canvas Tabel Luas & Spreadsheet Grid (Bidirectional Scroll: Horizontal & Vertikal) --}}
                <div x-ref="tableContainer" class="flex-1 min-h-0 min-w-0 w-full max-w-full overflow-x-scroll overflow-y-auto bg-slate-100 relative excel-viewport">
                    <table class="text-left text-xs border-collapse divide-none" style="min-width: 100%; width: max-content;">

                            @if ($tableCode === '2.B.1')
                                {{-- TABEL 2.B.1 KHUSUS --}}
                                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-10 border-b-2 border-slate-300 shadow-xs">
                                    <tr>
                                        <th class="py-2.5 px-3 w-12 text-center bg-slate-100 border-r border-b border-slate-300 font-mono text-[11px] text-slate-500">No</th>
                                        <th class="py-2.5 px-4 min-w-[260px] border-r border-b border-slate-300">Mata Kuliah</th>
                                        <th class="py-2.5 px-3 w-20 text-center border-r border-b border-slate-300">SKS</th>
                                        <th class="py-2.5 px-3 w-24 text-center border-r border-b border-slate-300">Semester</th>
                                        <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                            <th class="py-2 px-2 text-center min-w-[110px] bg-violet-50/60 border-r border-b border-slate-300">
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="text" x-model="headers[plIdx]"
                                                        class="w-full text-center bg-transparent border-0 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                                        title="Klik untuk mengubah nama Profil Lulusan">
                                                    <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1" title="Hapus kolom PL">×</button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-2.5 px-3 w-12 text-center border-b border-slate-300">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <template x-for="(row, idx) in rows" :key="idx">
                                        <tr class="hover:bg-slate-50 transition border-b border-slate-200">
                                            <td class="py-2 px-3 text-center font-mono text-[11px] font-bold text-slate-400 bg-slate-50 border-r border-slate-200" x-text="idx + 1"></td>
                                            <td class="py-1.5 px-2 min-w-[260px] border-r border-slate-200">
                                                <input type="text" x-model="row.mata_kuliah" placeholder="Nama mata kuliah..."
                                                    class="w-full rounded border-slate-200 text-xs py-1 px-2 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <td class="py-1.5 px-2 w-20 border-r border-slate-200">
                                                <input type="number" min="0" max="10" x-model.number="row.sks" @input="calcSummary2B1()"
                                                    class="w-full rounded border-slate-200 text-xs py-1 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <td class="py-1.5 px-2 w-24 border-r border-slate-200">
                                                <input type="number" min="1" max="8" x-model.number="row.semester"
                                                    class="w-full rounded border-slate-200 text-xs py-1 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                                <td class="py-1.5 px-2 text-center bg-violet-50/10 border-r border-slate-200 min-w-[110px]">
                                                    <input type="checkbox"
                                                        :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                                        @change="togglePlCheck(row, pl, $event.target.checked)"
                                                        class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                                </td>
                                            </template>
                                            <td class="py-1.5 px-2 text-center w-12">
                                                <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>

                            @elseif ($tableCode === '2.B.2')
                                {{-- TABEL 2.B.2 KHUSUS --}}
                                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-10 border-b-2 border-slate-300 shadow-xs">
                                    <tr>
                                        <th class="py-2.5 px-4 min-w-[320px] border-r border-b border-slate-300">Capaian Pembelajaran Lulusan (CPL)</th>
                                        <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                            <th class="py-2 px-2 text-center min-w-[110px] bg-violet-50/60 border-r border-b border-slate-300">
                                                <div class="flex items-center justify-center gap-1">
                                                    <input type="text" x-model="headers[plIdx]"
                                                        class="w-full text-center bg-transparent border-0 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                                        title="Klik untuk mengubah nama Profil Lulusan">
                                                    <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1">×</button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-2.5 px-3 w-12 text-center border-b border-slate-300">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <template x-for="(row, idx) in rows" :key="idx">
                                        <tr class="hover:bg-slate-50 transition border-b border-slate-200">
                                            <td class="py-1.5 px-3 min-w-[320px] border-r border-slate-200">
                                                <input type="text" x-model="row.cpl" placeholder="Contoh: CPL 01 - Mampu merancang sistem..."
                                                    class="w-full rounded border-slate-200 text-xs py-1 px-2 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                            </td>
                                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                                <td class="py-1.5 px-2 text-center bg-violet-50/10 border-r border-slate-200 min-w-[110px]">
                                                    <input type="checkbox"
                                                        :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                                        @change="togglePlCheck(row, pl, $event.target.checked)"
                                                        class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                                </td>
                                            </template>
                                            <td class="py-1.5 px-2 text-center w-12">
                                                <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>

                            @else
                                {{-- TABEL UMUM / STANDAR EXCEL SPREADSHEET GRID --}}
                                <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-20 border-b-2 border-slate-300 shadow-xs select-none">
                                    {{-- Multi-tier Tier 1 (Jika ada header gabungan bertingkat aktif) --}}
                                    <tr x-show="headerStructure && headerStructure.length >= 2">
                                        <th rowspan="2" class="py-2 px-2 w-10 text-center bg-slate-200 text-slate-500 font-mono text-[11px] border-b-2 border-r border-slate-300 sticky top-0 left-0 z-30 shadow-xs">#</th>
                                        <template x-for="(col, cIdx) in (headerStructure ? headerStructure[0] : [])" :key="'h0-' + cIdx">
                                            <th :rowspan="col.rowspan || 1"
                                                :colspan="col.colspan || 1"
                                                :style="col.width ? 'min-width: ' + col.width : 'min-width: 110px;'"
                                                class="py-2 px-2.5 text-center border-b-2 border-r border-slate-300 font-bold text-xs sticky top-0 z-20"
                                                :class="(col.colspan || 1) > 1 ? 'bg-violet-100 text-violet-900 border-b-2 border-violet-400' : 'bg-slate-100 text-slate-800'">
                                                <div class="flex items-center justify-between gap-1">
                                                    <input type="text" x-model="col.label"
                                                        @input="onHeaderTier0Input(col)"
                                                        class="w-full text-center border-0 bg-transparent text-xs font-bold p-0 focus:ring-0 focus:bg-white rounded transition"
                                                        :class="(col.colspan || 1) > 1 ? 'text-violet-900' : 'text-slate-800'"
                                                        title="Klik untuk mengubah nama header ini">
                                                    <template x-if="col.colIdx !== undefined && (!col.colspan || col.colspan === 1) && headers.length > 1">
                                                        <button type="button" @click="removeColumn(col.colIdx)"
                                                            class="text-slate-300 hover:text-rose-600 p-0.5 rounded transition shrink-0 opacity-60 hover:opacity-100 font-bold text-xs"
                                                            title="Hapus kolom ini">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </template>
                                                </div>
                                            </th>
                                        </template>
                                        <th rowspan="2" class="py-2 px-2 w-10 text-center bg-slate-100 border-b-2 border-slate-300 sticky top-0 z-20">
                                            <button type="button" @click="addColumn()" class="flex h-6 w-6 mx-auto items-center justify-center rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition font-bold text-xs" title="Tambah kolom baru">+</button>
                                        </th>
                                    </tr>

                                    {{-- Multi-tier Tier 2 (Sub-Kolom) --}}
                                    <tr x-show="headerStructure && headerStructure.length >= 2" class="bg-slate-100">
                                        <template x-for="(subCol, sIdx) in (headerStructure && headerStructure[1] ? headerStructure[1] : [])" :key="'h1-' + sIdx">
                                            <th :style="subCol.width ? 'min-width: ' + subCol.width : 'min-width: 85px;'"
                                                class="py-1.5 px-2 text-center border-b border-r border-slate-300 text-slate-700 font-bold text-xs sticky top-0 z-20"
                                                :class="activeCol === subCol.colIdx ? 'bg-emerald-100 text-emerald-900 border-b-2 border-emerald-600' : ''">
                                                <input type="text" x-model="subCol.label"
                                                    @input="if (headers[subCol.colIdx] !== undefined) headers[subCol.colIdx] = subCol.label"
                                                    class="w-full text-center border-0 bg-transparent text-xs font-bold text-slate-800 p-0 focus:ring-0 focus:bg-white rounded transition"
                                                    title="Klik untuk mengubah nama sub-kolom ini">
                                            </th>
                                        </template>
                                    </tr>

                                    {{-- Header 1 Baris Standar Excel (Column Letters A, B, C... & Names) --}}
                                    <tr x-show="!headerStructure || headerStructure.length < 2">
                                        {{-- Top-Left Corner Box (Sticky Top and Left!) --}}
                                        <th class="py-2 px-2 w-10 text-center bg-slate-200 text-slate-500 font-mono text-[11px] border-b-2 border-r border-slate-300 sticky top-0 left-0 z-30 shadow-xs">#</th>
                                        <template x-for="(col, cIdx) in headers" :key="'col-' + cIdx">
                                            <th class="py-1.5 px-2.5 border-b-2 border-r border-slate-300 transition sticky top-0 z-20"
                                                :class="activeCol === cIdx ? 'bg-emerald-100 text-emerald-900 border-b-2 border-emerald-600' : 'bg-slate-100 text-slate-700'"
                                                :style="(cIdx === 0 && (headers[0] === 'No' || headers[0] === 'No.')) ? 'min-width: 60px; width: 60px;' : 'min-width: 170px;'">
                                                <div class="flex items-center justify-between gap-1">
                                                    <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                                        <span class="text-[10px] font-mono font-bold shrink-0 px-1 py-0.5 rounded bg-slate-200/80 text-slate-600" x-text="getColLetter(cIdx)"></span>
                                                        <input type="text" x-model="headers[cIdx]"
                                                            class="w-full border-0 text-xs font-bold p-1 bg-transparent focus:ring-1 focus:ring-emerald-500 focus:bg-white rounded transition outline-none"
                                                            :class="activeCol === cIdx ? 'text-emerald-900 font-black' : 'text-slate-800'"
                                                            title="Klik untuk mengubah nama judul kolom ini">
                                                    </div>
                                                    <button type="button" @click="removeColumn(cIdx)"
                                                        class="text-slate-300 hover:text-rose-600 p-0.5 rounded transition shrink-0 opacity-60 hover:opacity-100 font-bold text-xs"
                                                        title="Hapus kolom ini">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-2 px-2 w-10 text-center bg-slate-100 border-b-2 border-slate-300 sticky top-0 z-20">
                                            <button type="button" @click="addColumn()" class="flex h-6 w-6 mx-auto items-center justify-center rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition font-bold text-xs" title="Tambah kolom baru">+</button>
                                        </th>
                                    </tr>
                                </thead>

                                {{-- Spreadsheet Grid Data Cells --}}
                                <tbody class="bg-white">
                                    <template x-for="(row, rIdx) in rows" :key="rIdx">
                                        <tr class="hover:bg-slate-50/60 transition group" :class="row.is_total ? 'bg-slate-100/70 font-bold' : ''">
                                            {{-- Sticky Row Number Column (Excel Style) --}}
                                            <td class="py-1 px-2 text-center font-mono text-[11px] select-none border-b border-r border-slate-300 sticky left-0 z-10 transition shadow-2xs"
                                                :class="activeRow === rIdx ? 'bg-emerald-100 text-emerald-900 font-bold' : 'bg-slate-100 text-slate-500'">
                                                <div class="flex items-center justify-center gap-0.5">
                                                    <span x-text="rIdx + 1"></span>
                                                    <button type="button" @click="removeRow(rIdx)"
                                                        class="hidden group-hover:inline text-rose-400 hover:text-rose-600 text-[10px] ml-0.5 font-bold"
                                                        title="Hapus baris ini">×</button>
                                                </div>
                                            </td>

                                            {{-- Data Cells --}}
                                            <template x-for="(col, cIdx) in headers" :key="'cell-' + cIdx">
                                                <td x-show="!isCellRowHidden(rIdx, cIdx)"
                                                    class="p-0 border-b border-r border-slate-200 relative transition-all bg-white"
                                                    :rowspan="getCellRowMerge(rIdx, cIdx) ? getCellRowMerge(rIdx, cIdx).rowspan : 1"
                                                    :class="[
                                                        (activeRow === rIdx && activeCol === cIdx) ? 'ring-2 ring-inset ring-emerald-600 bg-emerald-50/20 z-10' : '',
                                                        getCellRowMerge(rIdx, cIdx) ? 'bg-indigo-50/30' : ''
                                                    ]"
                                                    :style="getCellColWidth(cIdx)">
                                                    <div class="relative w-full h-full flex items-center group">
                                                        <input type="text"
                                                            :id="'cell-' + rIdx + '-' + cIdx"
                                                            :value="row.cells ? row.cells[cIdx] : ''"
                                                            @focus="onCellFocus(rIdx, cIdx)"
                                                            @input="setCellVal(row, cIdx, $event.target.value)"
                                                            @paste="handleExcelPaste($event, rIdx, cIdx)"
                                                            @keydown="handleKeyNavigation($event, rIdx, cIdx)"
                                                            class="w-full h-full py-2 px-2.5 text-xs text-slate-900 bg-transparent border-0 outline-none focus:outline-none focus:ring-0 font-sans"
                                                            :class="[
                                                                row.is_total ? 'font-bold text-slate-900' : '',
                                                                (cIdx === 0 && (headers[0] === 'No' || headers[0] === 'No.')) ? 'text-center font-bold text-slate-500' : ''
                                                            ]">
                                                        {{-- Tombol Undo/Pisahkan Baris (Unmerge) halus hanya saat di-hover, TANPA label teks yang mengganggu isi sel --}}
                                                        <button x-show="getCellRowMerge(rIdx, cIdx)"
                                                            type="button"
                                                            @click.stop="unmergeCell(rIdx, cIdx)"
                                                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 flex h-4 w-4 items-center justify-center rounded bg-slate-500/80 text-white hover:bg-rose-600 text-[10px] transition z-10"
                                                            title="Pisahkan baris gabungan (Unmerge)">↺</button>
                                                    </div>
                                                </td>
                                            </template>
                                            {{-- Row Action Cell --}}
                                            <td class="py-1 px-1.5 text-center border-b border-slate-200 w-10">
                                                <button type="button" @click="insertRow(rIdx, 'below')" class="text-slate-300 hover:text-emerald-600 p-1 rounded hover:bg-slate-100 transition" title="Sisipkan baris baru di bawah">+</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="rows.length === 0">
                                        <td :colspan="headers.length + 2" class="text-center py-12 text-slate-400 border-b border-slate-200">
                                            <p class="font-medium text-slate-600">Belum ada baris data pada tabel ini.</p>
                                            <p class="text-xs mt-1">Klik tombol <span class="font-bold text-emerald-700">+1 Baris</span> di toolbar atau gunakan tombol <span class="font-bold text-emerald-700">Tempel dari Excel</span>.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            @endif

                            {{-- Footer Live Sum / Total Row (HANYA MUNCUL JIKA USER MENGAKTIFKAN TOTAL) --}}
                            <tfoot class="border-t-2 border-slate-300 bg-slate-100 font-semibold text-slate-700" x-show="hasTotal">
                                <tr>
                                    <td class="py-2 px-2 text-center font-mono font-bold text-slate-500 border-r border-slate-300 bg-slate-200 sticky left-0 z-10">∑</td>
                                    <template x-for="(col, cIdx) in headers" :key="'foot-' + cIdx">
                                        <td class="py-2 px-2.5 text-center border-r border-slate-200">
                                            <span x-show="cIdx === totalLabelCol" class="font-black text-slate-900 text-xs uppercase tracking-wider">Total</span>
                                            <span x-show="sumCols.includes(cIdx)" class="font-bold text-emerald-800 text-xs font-mono" x-text="calcColumnSum(cIdx)"></span>
                                            <span x-show="cIdx !== totalLabelCol && !sumCols.includes(cIdx)" class="text-slate-300">-</span>
                                        </td>
                                    </template>
                                    <td class="w-10"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Bar Catatan Kaki Tabel (Terpisah di Bawah Grid, tidak mengganggu scroll) --}}
                    <div x-data="{ openNote: {{ !empty($note) ? 'true' : 'false' }} }" class="shrink-0 bg-amber-50 border-t border-amber-200 px-4 py-2 text-xs">
                        <div class="flex items-center justify-between">
                            <button type="button" @click="openNote = !openNote" class="inline-flex items-center gap-1.5 font-bold text-amber-900 hover:text-amber-700 transition">
                                <span class="text-sm">📝</span>
                                <span>Catatan Kaki Tabel LKPS (Opsional)</span>
                                <span class="text-[10px] text-amber-700 font-medium" x-text="openNote ? '(Sembunyikan)' : '(Buka untuk mengedit)'"></span>
                            </button>
                            <span class="text-[11px] text-amber-700 hidden sm:inline">Tercetak di bawah tabel saat diexport ke Excel &amp; PDF</span>
                        </div>
                        <div x-show="openNote" class="mt-1.5">
                            <input type="text" x-model="note" placeholder="Contoh: *) Data yang ditulis dalam tabel ini termasuk Unit Penjaminan Mutu..."
                                class="w-full rounded-lg border border-amber-200 bg-white text-xs py-1.5 px-3 text-slate-800 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 shadow-2xs">
                        </div>
                    </div>

                {{-- Modal Asisten Tempel dari Excel (Paste Assistant Modal) --}}
                <div x-show="showPasteModal" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex min-h-screen items-center justify-center p-4">
                    <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                        {{-- Header Modal --}}
                        <div class="border-b border-emerald-100 bg-emerald-50/80 p-4 sm:p-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-base shadow-xs">📋</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Tempel Data Langsung dari Excel</h3>
                                    <p class="text-xs text-slate-500">Salin (Copy) tabel di berkas Excel Anda (<kbd class="px-1 py-0.5 rounded bg-slate-200 font-mono text-[10px]">Cmd+C</kbd> / <kbd class="px-1 py-0.5 rounded bg-slate-200 font-mono text-[10px]">Ctrl+C</kbd>), lalu tempel di sini.</p>
                                </div>
                            </div>
                            <button type="button" @click="closePasteModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg p-1">✕</button>
                        </div>

                        <div class="p-4 sm:p-6 space-y-4 text-xs">
                            {{-- Area Tempel / Textarea --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5 font-bold text-slate-700">
                                    <label for="excel-paste-input">1. Tempel (Paste) Teks Tabel dari Excel:</label>
                                    <span class="text-[11px] text-slate-400 font-normal">Tekan Cmd+V atau Ctrl+V pada kotak di bawah</span>
                                </div>
                                <textarea id="excel-paste-input"
                                    x-model="pasteRawText"
                                    @input="onPasteInputChange()"
                                    rows="6"
                                    placeholder="Klik di sini lalu tekan Cmd+V atau Ctrl+V..."
                                    class="w-full rounded-xl border border-slate-300 p-3 font-mono text-xs text-slate-800 placeholder-slate-400 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-2xs"></textarea>
                            </div>

                            {{-- Pilihan Opsi Penerapan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div>
                                    <span class="block font-bold text-slate-800 mb-1.5">2. Mode Penerapan Data:</span>
                                    <div class="space-y-1.5">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="pasteMode" value="replace" class="text-emerald-600 focus:ring-emerald-500">
                                            <span><strong>Ganti seluruh isi tabel</strong> (Hapus data lama)</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" x-model="pasteMode" value="append" class="text-emerald-600 focus:ring-emerald-500">
                                            <span><strong>Tambahkan di bawah</strong> data yang sudah ada</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-800 mb-1.5">3. Baris Judul (Header):</span>
                                    <label class="flex items-start gap-2 cursor-pointer mt-1">
                                        <input type="checkbox" x-model="pasteHasHeaders" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mt-0.5">
                                        <span class="text-slate-600 leading-snug">Baris pertama berisi nama kolom / judul (jangan dimasukkan sebagai data baris).</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Live Preview Parsed Grid --}}
                            <div x-show="pastePreviewRows.length > 0" class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                    <span>Pratinjau Data Terdeteksi: <span class="text-emerald-700" x-text="(pasteHasHeaders ? pastePreviewRows.length - 1 : pastePreviewRows.length) + ' baris terdeteksi'"></span></span>
                                    <span class="text-slate-400 font-normal">Kolom tujuan: <span x-text="headers.length"></span> kolom</span>
                                </div>
                                <div class="max-h-48 overflow-auto rounded-xl border border-slate-200 bg-white shadow-2xs">
                                    <table class="w-full text-left text-[11px] divide-y divide-slate-200">
                                        <thead class="bg-slate-100 font-bold text-slate-600 sticky top-0">
                                            <tr>
                                                <th class="py-1.5 px-2 w-8 text-center bg-slate-200">#</th>
                                                <template x-for="(col, cIdx) in headers" :key="'preview-col-' + cIdx">
                                                    <th class="py-1.5 px-2 border-r border-slate-200 truncate max-w-[140px]" x-text="col"></th>
                                                </template>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="(pRow, rIdx) in (pasteHasHeaders ? pastePreviewRows.slice(1, 6) : pastePreviewRows.slice(0, 5))" :key="'preview-row-' + rIdx">
                                                <tr class="hover:bg-slate-50">
                                                    <td class="py-1 px-2 text-center text-slate-400 bg-slate-50 font-mono" x-text="rIdx + 1"></td>
                                                    <template x-for="(h, cIdx) in headers" :key="'pr-cell-' + cIdx">
                                                        <td class="py-1 px-2 border-r border-slate-100 text-slate-700 truncate max-w-[140px]" x-text="pRow[cIdx] || '-'"></td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <p x-show="pastePreviewRows.length > 5" class="text-[11px] text-slate-400 italic text-right">Menampilkan 5 baris pertama dari total <span x-text="pastePreviewRows.length"></span> baris.</p>
                            </div>

                            {{-- Footer Aksi Modal --}}
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="closePasteModal()" class="rounded-xl px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 transition">
                                    Batal
                                </button>
                                <button type="button" @click="applyPasteModal()" :disabled="pastePreviewRows.length === 0"
                                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2 font-bold text-white shadow-xs hover:from-emerald-700 hover:to-teal-700 transition disabled:opacity-50">
                                    ✓ Terapkan ke Tabel
                                </button>
                            </div>
                        </div>
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
                        {{-- Toggle Baris Total --}}
                        <button type="button" @click="toggleTotalRow()"
                            :class="hasTotal ? 'bg-violet-100 text-violet-800 border-violet-300 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 font-semibold'"
                            class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs shadow-xs transition"
                            title="Aktifkan atau matikan baris Total penjumlahan di bawah tabel">
                            <span class="font-bold text-violet-600">∑</span>
                            <span x-text="hasTotal ? 'Matikan Baris Total' : 'Aktifkan Baris Total'"></span>
                        </button>
                        {{-- Tambah Kolom Baru --}}
                        <button type="button" @click="addColumn()"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition"
                            title="Tambah kolom baru">
                            <span class="font-bold text-emerald-600 text-sm leading-none">+</span>
                            <span>Tambah Kolom</span>
                        </button>
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
        {{-- Modal Validasi Data Tabel LKPS (Disesuaikan persis tampilan Dokumen LED PPEPP) --}}
        <div id="lkpsViewerValidateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="lkpsValTitle">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeLkpsViewerValidateModal()"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-950/10">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-violet-50/80 to-white px-6 py-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-violet-700">VALIDASI DOKUMEN</p>
                        <h3 class="mt-1 text-base font-bold text-slate-900" id="lkpsValTitle">Tabel {{ $tableCode }}</h3>
                    </div>

                    <form method="POST" action="{{ route('perti.prodis.lkps.validate', [$prodi->id, $tableCode]) }}" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">STATUS VALIDASI</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition">
                                    <input type="radio" name="status" value="approved" id="lkps_status_approved" class="text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="block text-xs font-bold text-emerald-800">Sesuai</span>
                                        <span class="block text-[11px] text-slate-500">Dokumen sah &amp; sesuai</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition">
                                    <input type="radio" name="status" value="revision" id="lkps_status_revision" class="text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-rose-800">Perlu Revisi</span>
                                        <span class="block text-[11px] text-slate-500">Ada berkas keliru</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="viewer_validation_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">CATATAN VALIDASI (WAJIB JIKA REVISI)</label>
                            <textarea id="viewer_validation_notes" name="validation_notes" rows="3"
                                placeholder="Tulis catatan atau instruksi perbaikan untuk prodi..."
                                class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">{{ $submission->validation_notes }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="closeLkpsViewerValidateModal()"
                                class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="rounded-xl bg-violet-600 px-5 py-2 text-xs font-bold text-white hover:bg-violet-700 transition shadow-sm">
                                Simpan Validasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Script Modal Validasi LKPS (Perti) --}}
        <script>
            function openLkpsViewerValidateModal(status = 'approved') {
                if (status === 'approved') {
                    document.getElementById('lkps_status_approved').checked = true;
                } else {
                    document.getElementById('lkps_status_revision').checked = true;
                }
                document.getElementById('lkpsViewerValidateModal')?.classList.remove('hidden');
            }
            function closeLkpsViewerValidateModal() {
                document.getElementById('lkpsViewerValidateModal')?.classList.add('hidden');
            }
        </script>
    @endif
</x-viewer-layout>
