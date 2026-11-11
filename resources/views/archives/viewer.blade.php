<x-viewer-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-md bg-violet-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-violet-800">
                        {{ $document->category }}
                    </span>
                    @if ($document->academic_year)
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700">
                            {{ $document->academic_year }}
                        </span>
                    @endif
                    @if ($document->table_code)
                        <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                            Tabel {{ $document->table_code }}
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $document->isPtShared() ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : 'bg-sky-50 text-sky-800 ring-1 ring-sky-200' }}">
                        {{ $document->scopeLabel() }}
                    </span>
                </div>
                <h1 class="mt-1 truncate text-base font-bold text-slate-900 sm:text-lg">{{ $document->title }}</h1>
                <p class="mt-0.5 truncate text-xs text-slate-500">
                    Arsip &amp; Dokumen Tambahan · Diunggah oleh {{ $document->uploader?->name ?? 'Unit Akreditasi' }} · Diperbarui {{ $document->updated_at?->diffForHumans() }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $initialKind = $activeFile['preview_kind'] ?? 'none';
        $initialFilename = $activeFile['filename'] ?? $document->title;
        $initialExt = strtoupper($activeFile['ext'] ?? 'FILE');
        $initialSize = $activeFile['size_label'] ?? '';
    @endphp

    <style>
        .archive-viewer-split {
            display: flex;
            flex: 1 1 0%;
            min-height: 0;
            min-width: 0;
            flex-direction: column;
            overflow: hidden;
            background-color: #f1f5f9;
        }
        .archive-viewer-sidebar {
            width: 100%;
            max-height: 14rem;
            flex-shrink: 0;
            overflow-y: auto;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .archive-viewer-main {
            flex: 1 1 0%;
            min-width: 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: #e2e8f0;
            position: relative;
        }
        @media (min-width: 1024px) {
            .archive-viewer-split {
                flex-direction: row;
            }
            .archive-viewer-sidebar {
                width: 20rem;
                max-height: none;
                border-bottom: 0;
                border-right: 1px solid #e2e8f0;
            }
        }
    </style>

    {{-- Top Action Toolbar (Selalu Terlihat Penuh) --}}
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-2.5 select-none">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ $backUrl }}"
               class="inline-flex shrink-0 items-center gap-1.5 text-xs font-bold text-violet-600 hover:text-violet-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Arsip
            </a>
            @if ($activeFile)
                <span class="text-slate-300">|</span>
                <span id="archive-toolbar-ext" class="inline-flex shrink-0 items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-slate-700">
                    {{ $initialExt }}
                </span>
                <span id="archive-toolbar-filename" class="truncate text-xs font-bold text-slate-800 sm:text-sm">
                    {{ $initialFilename }}
                </span>
                <span id="archive-toolbar-size" class="hidden sm:inline shrink-0 text-xs text-slate-400">
                    {{ $initialSize }}
                </span>
            @endif
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2">
            @if ($activeFile)
                <button type="button"
                        id="archive-btn-copy"
                        onclick="copyArchiveViewerLink()"
                        class="ui-btn-secondary py-1.5 px-3 text-xs">
                    Salin Link
                </button>
                <a id="archive-btn-tab"
                   href="{{ $inlineUrl }}"
                   target="_blank"
                   rel="noopener"
                   style="{{ $initialKind === 'download_only' ? 'display: none;' : '' }}"
                   class="ui-btn-secondary py-1.5 px-3 text-xs">
                    Buka Tab Baru
                </a>
                <a id="archive-btn-download"
                   href="{{ $downloadUrl }}"
                   class="ui-btn-primary py-1.5 px-3.5 text-xs">
                    Unduh
                </a>
            @endif
        </div>
    </div>

    {{-- Main Split Content --}}
    <div class="archive-viewer-split">
        {{-- Left Sidebar: Metadata + File/Link Switcher --}}
        <aside class="archive-viewer-sidebar">
            <div class="p-4 space-y-4">
                @if ($document->description)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3 text-xs leading-relaxed text-slate-600">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Keterangan</p>
                        {{ $document->description }}
                    </div>
                @endif

                @if ($document->requirement)
                    <div class="rounded-xl border border-violet-200 bg-violet-50 p-3 text-xs text-violet-900">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-violet-600 mb-0.5">Terkait Persyaratan</p>
                        <p class="font-semibold">{{ $document->requirement->module?->shortLabel() }} · {{ $document->requirement->title }}</p>
                    </div>
                @endif

                {{-- Files List --}}
                @if (!empty($files))
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Berkas Tersimpan ({{ count($files) }})
                        </p>
                        <div class="space-y-2" id="archive-file-list">
                            @foreach ($files as $idx => $file)
                                @php $isActive = $idx === $activeFileIndex; @endphp
                                <button type="button"
                                        data-file-idx="{{ $idx }}"
                                        onclick="selectArchiveFile({{ $idx }})"
                                        class="archive-file-btn w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition {{ $isActive ? 'border-violet-500 bg-violet-50 text-violet-900 shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                                    <span class="mt-0.5 inline-flex shrink-0 items-center justify-center rounded-lg px-2 py-0.5 text-[10px] font-extrabold uppercase {{ match($file['preview_kind']) {
                                        'pdf' => 'bg-rose-100 text-rose-700',
                                        'spreadsheet' => 'bg-emerald-100 text-emerald-700',
                                        'image' => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
                                        default => 'bg-amber-100 text-amber-800',
                                    } }}">
                                        {{ strtoupper($file['ext']) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-semibold">{{ $file['filename'] }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">{{ $file['size_label'] }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Google Drive Links --}}
                @if (!empty($driveLinks))
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Tautan Google Drive ({{ count($driveLinks) }})
                        </p>
                        <div class="space-y-2">
                            @foreach ($driveLinks as $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center justify-between gap-2 rounded-xl border border-violet-200 bg-indigo-50 px-3 py-2.5 text-xs font-semibold text-indigo-700 transition hover:bg-violet-50">
                                    <span class="truncate">{{ $link['name'] }}</span>
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        {{-- Right Main Preview Canvas --}}
        <section class="archive-viewer-main">
            @if (!empty($files))
                {{-- 1. PDF Preview --}}
                <iframe
                    id="archive-pdf-frame"
                    src="{{ $initialKind === 'pdf' ? $inlineUrl : 'about:blank' }}"
                    title="Pratinjau Dokumen PDF"
                    style="display: {{ $initialKind === 'pdf' ? 'block' : 'none' }}; width: 100%; height: 100%; flex: 1 1 0%; border: 0; background-color: #ffffff;"
                ></iframe>

                {{-- 2. Image Preview --}}
                <div id="archive-image-container"
                     style="display: {{ $initialKind === 'image' ? 'flex' : 'none' }}; flex: 1 1 0%; width: 100%; height: 100%; align-items: center; justify-content: center; overflow: auto; padding: 1.5rem; background-color: #f1f5f9;">
                    <img id="archive-image-preview"
                         src="{{ $initialKind === 'image' ? $inlineUrl : '' }}"
                         alt="{{ $initialFilename }}"
                         style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #fff;" />
                </div>

                {{-- 3. Spreadsheet Preview (XLSX / XLS / CSV) --}}
                <div id="archive-excel-container"
                     style="display: {{ $initialKind === 'spreadsheet' ? 'flex' : 'none' }}; flex: 1 1 0%; flex-direction: column; min-height: 0; overflow: hidden; background-color: #ffffff;">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50 px-4 py-2 shrink-0">
                        <div id="archive-sheet-tabs" class="flex flex-wrap items-center gap-1.5"></div>
                        <span id="archive-excel-meta" class="text-xs font-medium text-slate-500"></span>
                    </div>

                    <div id="archive-excel-loading" class="flex flex-1 items-center justify-center gap-2 p-8 text-xs font-semibold text-slate-500">
                        <svg class="h-5 w-5 animate-spin text-violet-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z"></path>
                        </svg>
                        <span>Memuat pratinjau spreadsheet...</span>
                    </div>

                    <div id="archive-excel-error" style="display: none;" class="p-8 text-center"></div>
                    <div id="archive-excel-table-wrap" class="min-h-0 flex-1 overflow-x-auto overflow-y-auto p-3"></div>
                </div>

                {{-- 4. Download-Only Card (DOC / DOCX / ZIP / Other) --}}
                <div id="archive-download-container"
                     style="display: {{ $initialKind === 'download_only' ? 'flex' : 'none' }}; flex: 1 1 0%; align-items: center; justify-content: center; padding: 1.5rem;">
                    <div class="max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-200">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                        </div>
                        <span id="archive-dl-ext" class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold uppercase text-amber-800">
                            {{ $initialExt }}
                        </span>
                        <h2 id="archive-dl-filename" class="mt-3 text-base font-bold text-slate-900 break-words">
                            {{ $initialFilename }}
                        </h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Ukuran berkas: <span id="archive-dl-size" class="font-semibold text-slate-700">{{ $initialSize }}</span>
                        </p>
                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            Format berkas Word / arsip ZIP tidak dapat dirender langsung oleh browser. Klik tombol di bawah untuk mengunduh dan memeriksanya.
                        </p>
                        <a id="archive-dl-btn"
                           href="{{ $downloadUrl }}"
                           class="mt-5 ui-btn-primary">
                            Unduh Berkas Sekarang
                        </a>
                    </div>
                </div>
            @else
                {{-- Google Drive Links Only --}}
                <div class="flex flex-1 items-center justify-center p-6">
                    <div class="max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 ring-1 ring-violet-200">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Dokumen Tersedia via Tautan Eksternal</h2>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500">
                            Tidak ada berkas fisik yang diunggah untuk arsip ini. Gunakan daftar tautan Google Drive di panel kiri untuk membuka dokumen.
                        </p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        const ARCHIVE_FILES = @json($files);
        const ARCHIVE_INLINE_BASE = @json($inlineUrlTemplate);
        let archiveActiveIdx = {{ (int) $activeFileIndex }};
        let archiveWorkbook = null;

        function getInlineUrl(idx) {
            return `${ARCHIVE_INLINE_BASE}?file=${idx}`;
        }

        function copyArchiveViewerLink() {
            const active = ARCHIVE_FILES[archiveActiveIdx];
            const url = active?.view_url || window.location.href;
            const btn = document.getElementById('archive-btn-copy');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    if (btn) {
                        btn.textContent = 'Link Disalin!';
                        setTimeout(() => { btn.textContent = 'Salin Link'; }, 2000);
                    }
                }).catch(() => window.prompt('Salin tautan arsip berikut:', url));
            } else {
                window.prompt('Salin tautan arsip berikut:', url);
            }
        }

        function selectArchiveFile(idx) {
            const file = ARCHIVE_FILES[idx];
            if (!file) return;
            archiveActiveIdx = idx;

            const inlineUrl = getInlineUrl(idx);
            const extLabel = document.getElementById('archive-toolbar-ext');
            const nameLabel = document.getElementById('archive-toolbar-filename');
            const sizeLabel = document.getElementById('archive-toolbar-size');
            const tabBtn = document.getElementById('archive-btn-tab');
            const dlBtn = document.getElementById('archive-btn-download');

            if (extLabel) extLabel.textContent = (file.ext || 'FILE').toUpperCase();
            if (nameLabel) nameLabel.textContent = file.filename;
            if (sizeLabel) sizeLabel.textContent = file.size_label;
            if (tabBtn) {
                tabBtn.href = inlineUrl;
                tabBtn.style.display = file.preview_kind === 'download_only' ? 'none' : '';
            }
            if (dlBtn) dlBtn.href = file.download_url;

            const pdfFrame = document.getElementById('archive-pdf-frame');
            const imgBox = document.getElementById('archive-image-container');
            const imgEl = document.getElementById('archive-image-preview');
            const excelBox = document.getElementById('archive-excel-container');
            const dlBox = document.getElementById('archive-download-container');

            if (pdfFrame) {
                pdfFrame.style.display = file.preview_kind === 'pdf' ? 'block' : 'none';
                pdfFrame.src = file.preview_kind === 'pdf' ? inlineUrl : 'about:blank';
            }
            if (imgBox && imgEl) {
                imgBox.style.display = file.preview_kind === 'image' ? 'flex' : 'none';
                imgEl.src = file.preview_kind === 'image' ? inlineUrl : '';
                imgEl.alt = file.filename;
            }
            if (excelBox) {
                excelBox.style.display = file.preview_kind === 'spreadsheet' ? 'flex' : 'none';
                if (file.preview_kind === 'spreadsheet') {
                    loadArchiveSpreadsheet(inlineUrl);
                }
            }
            if (dlBox) {
                dlBox.style.display = file.preview_kind === 'download_only' ? 'flex' : 'none';
                if (file.preview_kind === 'download_only') {
                    const dlExt = document.getElementById('archive-dl-ext');
                    const dlName = document.getElementById('archive-dl-filename');
                    const dlSize = document.getElementById('archive-dl-size');
                    const dlCardBtn = document.getElementById('archive-dl-btn');
                    if (dlExt) dlExt.textContent = (file.ext || 'FILE').toUpperCase();
                    if (dlName) dlName.textContent = file.filename;
                    if (dlSize) dlSize.textContent = file.size_label;
                    if (dlCardBtn) dlCardBtn.href = file.download_url;
                }
            }

            document.querySelectorAll('.archive-file-btn').forEach(btn => {
                const btnIdx = parseInt(btn.dataset.fileIdx, 10);
                btn.className = btnIdx === idx
                    ? 'archive-file-btn w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition border-violet-500 bg-violet-50 text-violet-900 shadow-sm'
                    : 'archive-file-btn w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition border-slate-200 bg-white text-slate-700 hover:bg-slate-50';
            });
        }

        async function loadArchiveSpreadsheet(inlineUrl) {
            const loading = document.getElementById('archive-excel-loading');
            const errBox = document.getElementById('archive-excel-error');
            const tabs = document.getElementById('archive-sheet-tabs');
            const wrap = document.getElementById('archive-excel-table-wrap');
            const meta = document.getElementById('archive-excel-meta');

            if (loading) loading.style.display = 'flex';
            if (errBox) { errBox.style.display = 'none'; errBox.textContent = ''; }
            if (tabs) tabs.innerHTML = '';
            if (wrap) wrap.innerHTML = '';
            if (meta) meta.textContent = '';

            try {
                const res = await fetch(inlineUrl);
                if (!res.ok) throw new Error('Berkas gagal diambil dari server.');
                const buf = await res.arrayBuffer();
                archiveWorkbook = XLSX.read(buf, { type: 'array' });
                if (loading) loading.style.display = 'none';

                if (!archiveWorkbook.SheetNames || archiveWorkbook.SheetNames.length === 0) {
                    if (wrap) wrap.innerHTML = '<p class="p-8 text-center text-xs text-slate-400">Berkas spreadsheet tidak memiliki sheet.</p>';
                    return;
                }

                archiveWorkbook.SheetNames.forEach((name, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = name;
                    btn.className = i === 0
                        ? 'px-3 py-1.5 rounded-lg text-xs font-bold bg-violet-600 text-white shadow-sm'
                        : 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100';
                    btn.addEventListener('click', () => renderArchiveSheet(name, btn));
                    if (tabs) tabs.appendChild(btn);
                });

                renderArchiveSheet(archiveWorkbook.SheetNames[0], tabs?.firstChild);
            } catch (e) {
                if (loading) loading.style.display = 'none';
                if (errBox) {
                    errBox.style.display = 'block';
                    errBox.className = 'p-8 text-center text-xs font-semibold text-rose-600';
                    errBox.textContent = `Pratinjau spreadsheet gagal dimuat (${e.message}). Silakan klik tombol Unduh di atas.`;
                }
            }
        }

        function renderArchiveSheet(sheetName, activeBtn) {
            if (!archiveWorkbook) return;
            const tabs = document.getElementById('archive-sheet-tabs');
            const wrap = document.getElementById('archive-excel-table-wrap');
            const meta = document.getElementById('archive-excel-meta');

            if (tabs) {
                Array.from(tabs.children).forEach(btn => {
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100';
                });
            }
            if (activeBtn) {
                activeBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-violet-600 text-white shadow-sm';
            }

            const sheet = archiveWorkbook.Sheets[sheetName];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });
            if (!wrap) return;
            wrap.innerHTML = '';

            if (!rows.length) {
                const empty = document.createElement('p');
                empty.className = 'p-8 text-center text-xs text-slate-400';
                empty.textContent = 'Sheet ini kosong.';
                wrap.appendChild(empty);
                if (meta) meta.textContent = `0 baris · Sheet "${sheetName}"`;
                return;
            }

            const maxCols = Math.max(...rows.map(r => Array.isArray(r) ? r.length : 0), 1);
            const table = document.createElement('table');
            table.className = 'w-full text-left text-xs border-collapse';
            const tbody = document.createElement('tbody');

            rows.forEach((row, rIdx) => {
                const tr = document.createElement('tr');
                tr.className = rIdx === 0
                    ? 'bg-slate-100 font-bold text-slate-800 sticky top-0 border-b border-slate-200'
                    : 'hover:bg-violet-50 transition';

                for (let c = 0; c < maxCols; c++) {
                    const td = document.createElement(rIdx === 0 ? 'th' : 'td');
                    td.className = 'py-2 px-3 border border-slate-200 whitespace-nowrap';
                    td.textContent = Array.isArray(row) && row[c] !== undefined && row[c] !== null ? String(row[c]) : '';
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            wrap.appendChild(table);
            if (meta) meta.textContent = `${rows.length} baris · Sheet "${sheetName}"`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initial = ARCHIVE_FILES[archiveActiveIdx];
            if (initial && initial.preview_kind === 'spreadsheet') {
                loadArchiveSpreadsheet(getInlineUrl(archiveActiveIdx));
            }
        });
    </script>
</x-viewer-layout>
