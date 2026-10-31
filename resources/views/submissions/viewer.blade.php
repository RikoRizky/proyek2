<x-viewer-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Pratinjau dokumen</p>
            <h1 class="mt-0.5 truncate text-base font-bold text-slate-900 sm:text-lg">{{ $title }}</h1>
            <p class="mt-0.5 truncate text-xs text-slate-500" id="header-filename">{{ $filename }}</p>
        </div>
    </x-slot>

    @php
        $googleDriveLinks = $submission->google_drive_links ?? [];
        $files = $submission->files ?? [];
        $hasMultiple = !empty($googleDriveLinks) || count($files) > 1;
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
    @endphp

    @if (session('status'))
        <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-900 flex items-center gap-2">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <div class="flex shrink-0 flex-wrap items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 py-2.5 select-none">
        <div class="flex items-center gap-3">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-600 hover:text-violet-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
            <span class="text-slate-300">|</span>
            <span class="ui-badge {{ $submission->status->badgeClass() }}">{{ $submission->status->label() }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if ($routePrefix === 'perti' && $submission->status !== \App\Enums\SubmissionStatus::Approved)
                <form action="{{ route('perti.submissions.validate', $submission) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Setujui (Sesuai)
                    </button>
                </form>

                <button type="button"
                    id="btnViewerRevision"
                    class="inline-flex items-center gap-1 rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-sm">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                    {{ $submission->status === \App\Enums\SubmissionStatus::Revision ? 'Edit Catatan Revisi' : 'Perlu Revisi' }}
                </button>
            @endif

            @if ($submission->file_path || (!empty($submission->files)))
                <a href="{{ $inlineUrl }}" id="btn-tab-baru" target="_blank" rel="noopener" class="ui-btn-secondary py-1.5 px-3.5 text-xs">Tab baru</a>
                <a href="{{ $downloadUrl }}" id="btn-download" class="ui-btn-secondary py-1.5 px-3.5 text-xs">Unduh</a>
            @endif
        </div>
    </div>

    @if ($hasMultiple)
        <div class="flex flex-1 overflow-hidden min-h-0">
            <!-- Left panel: Sidebar listing files and links -->
            <div class="w-80 shrink-0 border-r border-slate-200 bg-slate-50 flex flex-col overflow-y-auto p-4 select-none">
                <!-- Google Drive Links Section -->
                @if (!empty($googleDriveLinks))
                    <div class="mb-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Link Google Drive</h3>
                        <div class="space-y-2">
                            @foreach ($googleDriveLinks as $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="flex items-start gap-2.5 rounded-xl border border-slate-200/60 bg-white p-3 hover:border-violet-300 hover:shadow-sm transition">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 break-words">{{ $link['name'] }}</p>
                                        <span class="mt-0.5 inline-flex items-center gap-0.5 text-[10px] font-medium text-violet-600">
                                            Buka Link
                                            <svg style="width:10px;height:10px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Files Section -->
                @if (!empty($files))
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Berkas Dokumen</h3>
                        <div class="space-y-2" id="file-list">
                            @foreach ($files as $index => $file)
                                @php
                                    $fileInlineUrl = route($routePrefix . '.submissions.inline', [$submission, 'file' => $index]);
                                    $fileDownloadUrl = route($routePrefix . '.submissions.download', [$submission, 'file' => $index]);
                                    $isActive = (int)$activeFileIndex === $index;
                                @endphp
                                <button
                                    type="button"
                                    data-file-index="{{ $index }}"
                                    data-inline-url="{{ $fileInlineUrl }}"
                                    data-download-url="{{ $fileDownloadUrl }}"
                                    data-filename="{{ $file['original_filename'] }}"
                                    onclick='selectFile({{ $index }}, {{ json_encode($fileInlineUrl) }}, {{ json_encode($fileDownloadUrl) }}, {{ json_encode($file["original_filename"]) }})'
                                    class="w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition file-item-btn
                                        {{ $isActive 
                                            ? 'border-violet-500 bg-violet-50/50 text-violet-900 font-medium' 
                                            : 'border-slate-200/60 bg-white hover:border-slate-300' }}"
                                >
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $file['original_filename'] }}">{{ $file['original_filename'] }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-500">{{ number_format($file['file_size'] / 1024, 1) }} KB</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right panel: iframe or empty state -->
            <div class="flex-1 flex flex-col min-h-0 bg-slate-100 relative">
                @if (!empty($files))
                    <div id="excel-warning" class="{{ $isPdf ? 'hidden' : '' }} shrink-0 border-b border-amber-200/60 bg-amber-50/95 px-4 py-2.5 text-xs text-amber-950 sm:px-6">
                        Berkas <strong>Excel</strong> tidak selalu dapat ditampilkan di dalam peramban. Gunakan <strong>Tab baru</strong> atau <strong>Unduh</strong> jika tampilan kosong.
                    </div>
                    <iframe
                        id="viewer-iframe"
                        src="{{ route($routePrefix . '.submissions.inline', [$submission, 'file' => $activeFileIndex]) }}"
                        title="Pratinjau Dokumen"
                        class="min-h-0 w-full flex-1 border-0 bg-white"
                    ></iframe>
                @else
                    <!-- No files, links only dashboard -->
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center" style="background: linear-gradient(145deg, #f8f7ff 0%, #ffffff 60%, #f0f4ff 100%);">
                        <div style="max-width: 360px; width: 100%;">
                            <!-- Icon container - strictly sized with inline style -->
                            <div style="width: 72px; height: 72px; border-radius: 20px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 4px 16px rgba(139,92,246,0.18);">
                                <svg style="width: 32px; height: 32px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#7c3aed">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                                </svg>
                            </div>

                            <!-- Decorative badge -->
                            <div style="display: inline-flex; align-items: center; gap: 6px; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 99px; padding: 4px 12px; margin-bottom: 16px;">
                                <svg style="width: 10px; height: 10px; flex-shrink: 0;" fill="#8b5cf6" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #7c3aed;">Google Drive</span>
                            </div>

                            <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 10px; line-height: 1.3;">Dokumen Berupa Link</h2>
                            <p style="font-size: 13px; color: #64748b; line-height: 1.65; margin: 0 0 24px;">
                                Berkas fisik tidak diunggah untuk persyaratan ini.<br>
                                Gunakan tautan di panel sebelah kiri untuk mengakses dokumen.
                            </p>

                            <!-- Arrow hint -->
                            <div style="display: inline-flex; align-items: center; gap: 8px; color: #a78bfa; font-size: 12px; font-weight: 600;">
                                <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                                </svg>
                                Pilih link di panel kiri
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function selectFile(index, inlineUrl, downloadUrl, filename) {
                // Update iframe source
                const iframe = document.getElementById('viewer-iframe');
                if (iframe) {
                    iframe.src = inlineUrl;
                }

                // Update headers & links
                document.getElementById('header-filename').textContent = filename;
                document.getElementById('btn-tab-baru').href = inlineUrl;
                document.getElementById('btn-download').href = downloadUrl;

                // Handle warning bar visibility
                const ext = filename.split('.').pop().toLowerCase();
                const warningBar = document.getElementById('excel-warning');
                if (warningBar) {
                    if (ext === 'pdf') {
                        warningBar.classList.add('hidden');
                    } else {
                        warningBar.classList.remove('hidden');
                    }
                }

                // Update active state in sidebar
                document.querySelectorAll('.file-item-btn').forEach(btn => {
                    const btnIdx = parseInt(btn.dataset.fileIndex);
                    if (btnIdx === index) {
                        btn.className = "w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition file-item-btn border-violet-500 bg-violet-50/50 text-violet-900 font-medium";
                    } else {
                        btn.className = "w-full text-left flex items-start gap-2.5 rounded-xl border p-3 transition file-item-btn border-slate-200/60 bg-white hover:border-slate-300";
                    }
                });
            }
        </script>
    @else
        <!-- Legacy / single file layout -->
        <div class="flex min-h-0 flex-1 flex-col">
            @unless ($isPdf)
                <div class="shrink-0 border-b border-amber-200/60 bg-amber-50/95 px-4 py-2.5 text-xs text-amber-950 sm:px-6">
                    Berkas <strong>Excel</strong> tidak selalu dapat ditampilkan di dalam peramban. Gunakan <strong>Tab baru</strong> atau <strong>Unduh</strong> jika tampilan kosong.
                </div>
            @endunless

            <iframe
                src="{{ $inlineUrl }}"
                title="Pratinjau {{ $filename }}"
                class="min-h-0 w-full flex-1 border-0 bg-white"
            ></iframe>
        </div>
    @endif

    @if ($routePrefix === 'perti')
        {{-- Revision Modal in Viewer --}}
        <div id="viewerRevisionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Z"/></svg>
                            </span>
                            Catatan Perlu Revisi
                        </h3>
                        <button type="button" onclick="closeViewerRevisionModal()" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form id="viewerRevisionForm" method="POST" action="" class="mt-4">
                        @csrf
                        <input type="hidden" name="status" value="revision">
                        
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Persyaratan:</p>
                        <p class="text-sm font-semibold text-slate-800 mb-4" id="viewerModalReqTitle"></p>

                        <!-- Selection for Files & Links to Revise -->
                        @php
                            $modalFiles = $submission->files ?? [];
                            $modalDriveLinks = $submission->google_drive_links ?? [];
                            $hasFilesOrLinks = !empty($modalFiles) || !empty($modalDriveLinks);
                        @endphp

                        @if ($hasFilesOrLinks)
                            <div class="mb-4 rounded-xl border border-rose-200/60 bg-rose-50/40 p-3.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-rose-900 mb-2">Pilih Berkas / Link yang Perlu Direvisi:</label>
                                
                                @if (!empty($modalDriveLinks))
                                    <div class="mb-3">
                                        <p class="text-[11px] font-bold text-violet-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                            Link Google Drive:
                                        </p>
                                        <div class="space-y-1.5">
                                            @foreach ($modalDriveLinks as $idx => $link)
                                                <label class="flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-800 cursor-pointer hover:border-rose-400 transition select-none">
                                                    <input type="checkbox" name="target_links[]" value="{{ $link['name'] }}" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="font-semibold text-violet-700">🔗 {{ $link['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($modalFiles))
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                            Berkas Dokumen:
                                        </p>
                                        <div class="space-y-1.5">
                                            @foreach ($modalFiles as $idx => $file)
                                                <label class="flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-800 cursor-pointer hover:border-rose-400 transition select-none">
                                                    <input type="checkbox" name="target_files[]" value="{{ $file['original_filename'] }}" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="font-semibold text-slate-800 truncate">📄 {{ $file['original_filename'] }}</span>
                                                    <span class="text-[10px] text-slate-400">({{ number_format($file['file_size'] / 1024, 1) }} KB)</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div>
                            <label for="viewer_validation_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan / Alasan Revisi untuk Prodi:</label>
                            <p class="mt-1 text-[11px] text-slate-500">Tentukan instruksi/catatan detail mengenai perbaikan yang harus dilakukan.</p>
                            <textarea id="viewer_validation_notes" name="validation_notes" rows="3"
                                placeholder="Contoh: Berkas Dokumen A halaman 2 TTD Dekan belum ada. Link Google Drive B tidak dapat diakses (Access Denied). Harap diperbaiki..."
                                class="mt-1.5 w-full rounded-xl border border-slate-200 p-3 text-sm text-slate-800 placeholder-slate-400 focus:border-rose-500 focus:ring-rose-500"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeViewerRevisionModal()" class="ui-btn-secondary py-2 px-4 text-xs">Batal</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-bold text-white shadow-md transition hover:opacity-90" style="background-color: #e11d48 !important; color: #ffffff !important;">Kirim Catatan Revisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function parseValidationNotes(notesStr) {
                if (!notesStr) return { targetFiles: [], targetLinks: [], customNote: '' };

                let targetFiles = [];
                let targetLinks = [];
                let customNote = notesStr;

                if (notesStr.includes('📌 Target Revisi:')) {
                    const parts = notesStr.split('💬 Catatan Perti:');
                    const headerPart = parts[0];
                    customNote = parts[1] ? parts[1].trim() : '';

                    const fileMatch = headerPart.match(/• Berkas Dokumen:\s*(.+)/);
                    if (fileMatch && fileMatch[1]) {
                        targetFiles = fileMatch[1].split(',').map(s => s.trim());
                    }

                    const linkMatch = headerPart.match(/• Link Google Drive:\s*(.+)/);
                    if (linkMatch && linkMatch[1]) {
                        targetLinks = linkMatch[1].split(',').map(s => s.trim());
                    }
                }

                return { targetFiles, targetLinks, customNote };
            }

            function openViewerRevisionModal() {
                const form = document.getElementById('viewerRevisionForm');
                if (form) {
                    form.action = @json(route('perti.submissions.validate', $submission));
                    document.getElementById('viewerModalReqTitle').textContent = @json($submission->requirement->title);
                    
                    const rawNotes = @json($submission->validation_notes ?? '');
                    const { targetFiles, targetLinks, customNote } = parseValidationNotes(rawNotes);

                    document.getElementById('viewer_validation_notes').value = customNote;

                    // Pre-check target links
                    document.querySelectorAll('#viewerRevisionModal input[name="target_links[]"]').forEach(cb => {
                        cb.checked = targetLinks.includes(cb.value);
                    });

                    // Pre-check target files
                    document.querySelectorAll('#viewerRevisionModal input[name="target_files[]"]').forEach(cb => {
                        cb.checked = targetFiles.includes(cb.value);
                    });

                    document.getElementById('viewerRevisionModal').classList.remove('hidden');
                }
            }

            window.closeViewerRevisionModal = function() {
                const modal = document.getElementById('viewerRevisionModal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('btnViewerRevision');
                if (btn) {
                    btn.addEventListener('click', openViewerRevisionModal);
                }

                @if(request('action') === 'revision')
                    openViewerRevisionModal();
                @endif
            });
        </script>
    @endif
</x-viewer-layout>
