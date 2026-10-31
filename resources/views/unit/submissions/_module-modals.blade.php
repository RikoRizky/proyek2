@php
    $maxUploadMb = \App\Support\AccreditationUpload::maxUploadMb();
    $postMaxSizeStr = ini_get('post_max_size');
    $val = trim($postMaxSizeStr);
    $last = strtolower($val[strlen($val)-1]);
    $val = (float)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    $postMaxSizeBytes = $val;
    $postMaxSizeLabel = \App\Support\AccreditationUpload::iniSizeLabel($postMaxSizeStr);
@endphp


@foreach ($module->requirements as $req)
    <!-- Floating Modal for each Requirement -->
    @php
        $isFailed = session('failed_requirement_id') == $req->id;
        $links = $isFailed ? old('google_drive_links', []) : [['name' => '', 'url' => '']];
        if (empty($links)) {
            $links = [['name' => '', 'url' => '']];
        }
        $latest = $req->submissions->first();
    @endphp
    <div id="upload-modal-{{ $req->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title-{{ $req->id }}">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" style="background: rgba(10,10,20,0.82)" onclick="closeUploadModal('{{ $req->id }}')"></div>

        <!-- Modal wrapper -->
        <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
            <div class="relative overflow-hidden transform transition-all duration-300 scale-95 opacity-0" style="width: 480px; max-width: calc(100vw - 2rem); margin: 0 auto" id="modal-box-{{ $req->id }}">

                <!-- Modal Card -->
                <div class="rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10 w-full" style="background:#fff">

                    <!-- Header: dark navy gradient -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e1b4b 100%); padding: 1.25rem 1.5rem 1.5rem;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <!-- Breadcrumb label -->
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-md" style="background:rgba(139,92,246,0.3)">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#a78bfa"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest" style="color:#a78bfa">{{ $module->name }}</span>
                                </div>
                                <!-- Title -->
                                <h3 id="modal-title-{{ $req->id }}" class="text-base font-bold leading-snug" style="color:#f8fafc" title="{{ $req->title }}">{{ $req->title }}</h3>
                                @if ($req->description)
                                    <p class="mt-1 text-xs leading-relaxed line-clamp-2" style="color:#94a3b8">{{ $req->description }}</p>
                                @endif
                            </div>
                            <!-- Close button -->
                            <button type="button" onclick="closeUploadModal('{{ $req->id }}')"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition"
                                style="background:rgba(255,255,255,0.08); color:#94a3b8"
                                onmouseover="this.style.background='rgba(255,255,255,0.15)';this.style.color='#f1f5f9'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)';this.style.color='#94a3b8'">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <form id="modal-upload-form-{{ $req->id }}" action="{{ route('unit.submissions.store', $req) }}" method="POST" enctype="multipart/form-data" onsubmit="disableSubmitButton('{{ $req->id }}')">
                        @csrf
                        <div class="px-6 pt-5 pb-3 space-y-5">

                            <!-- Google Drive Links -->
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">Link Google Drive</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Minimal isi 1 link jika tidak mengunggah berkas</p>
                                    </div>
                                    <button type="button" onclick="addDriveLinkRow('{{ $req->id }}')"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition"
                                        style="background:#f5f3ff; color:#7c3aed"
                                        onmouseover="this.style.background='#ede9fe'"
                                        onmouseout="this.style.background='#f5f3ff'">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Tambah Link
                                    </button>
                                </div>

                                <div id="drive-links-container-{{ $req->id }}" class="space-y-2">
                                    @foreach ($links as $index => $link)
                                        <div class="flex items-start gap-2" id="drive-row-{{ $req->id }}-{{ $index }}">
                                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nama Dokumen</label>
                                                    <input type="text" name="google_drive_links[{{ $index }}][name]" value="{{ $link['name'] ?? '' }}" placeholder="Contoh: SK Rektor..."
                                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Link Google Drive</label>
                                                    <input type="url" name="google_drive_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="https://drive.google.com/..."
                                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition">
                                                </div>
                                            </div>
                                            @if ($index > 0)
                                                <button type="button" onclick="removeDriveLinkRow('{{ $req->id }}', {{ $index }})"
                                                    class="mt-5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-300 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                </button>
                                            @else
                                                <div class="mt-5 h-8 w-8 shrink-0"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if (auth()->user()->effective_package !== 'Starter')
                                <!-- Divider -->
                                <div class="relative flex items-center">
                                    <div class="flex-1 border-t border-slate-100"></div>
                                    <span class="mx-3 text-[10px] font-bold uppercase tracking-widest text-slate-300">Atau unggah berkas</span>
                                    <div class="flex-1 border-t border-slate-100"></div>
                                </div>

                                <!-- File Upload -->
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 mb-0.5">
                                        Berkas Dokumen
                                        <span class="ml-1 text-xs font-normal text-slate-400">(Opsional)</span>
                                    </p>
                                    <p class="text-xs text-slate-400 mb-3">PDF atau Excel · Maks. {{ $maxUploadMb }} MB · Bisa pilih lebih dari 1</p>

                                    <!-- Dropzone -->
                                    <label for="file-input-{{ $req->id }}" id="dropzone-{{ $req->id }}"
                                        class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-4 py-7 text-center cursor-pointer transition-all duration-200 hover:border-violet-400 hover:bg-violet-50/40 group">
                                        <!-- Icon -->
                                        <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-sm text-slate-400 group-hover:border-violet-300 group-hover:text-violet-500 transition">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <p id="dropzone-label-{{ $req->id }}" class="text-sm font-semibold text-slate-600 group-hover:text-violet-700 transition">Klik atau seret berkas ke sini</p>
                                            <p class="mt-0.5 text-xs text-slate-400">PDF, XLSX, XLS</p>
                                        </div>
                                        <input id="file-input-{{ $req->id }}" type="file" name="documents[]" multiple accept=".pdf,.xlsx,.xls"
                                            class="sr-only" onchange="updateFilePreview('{{ $req->id }}', this)">
                                    </label>

                                    <!-- File preview list (newly selected files) -->
                                    <div id="file-preview-{{ $req->id }}" class="mt-2 space-y-1.5 hidden"></div>

                                    <!-- Oversized file warning (shown after failed upload due to file size) -->
                                    <div id="oversized-warning-{{ $req->id }}" class="mt-2 hidden rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-3">
                                        <div class="flex items-start gap-2.5">
                                            <svg class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-rose-700">Berkas terlalu besar:</p>
                                                <ul id="oversized-list-{{ $req->id }}" class="mt-1 space-y-0.5 text-[11px] text-rose-600 list-disc pl-4"></ul>
                                                <p class="mt-1.5 text-[11px] text-rose-600">Hapus berkas di atas lalu pilih ulang berkas yang lebih kecil dari {{ $maxUploadMb }} MB.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total request size warning -->
                                    <div id="total-size-warning-{{ $req->id }}" class="mt-2 hidden rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-3">
                                        <div class="flex items-start gap-2.5">
                                            <svg class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-rose-700">Total berkas melebihi batas request:</p>
                                                <p class="mt-1 text-[11px] text-rose-600">
                                                    Total ukuran berkas terpilih adalah <span id="total-size-current-{{ $req->id }}" class="font-bold">0 MB</span>, melebihi batas total maksimal sebesar <span class="font-bold">{{ $maxUploadMb }} MB</span>.
                                                </p>
                                                <p class="mt-1.5 text-[11px] text-rose-600">Silakan hapus beberapa berkas atau kurangi dokumen agar dapat diunggah bersamaan.</p>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Container for existing files (retained files) -->
                                    <div id="existing-files-container-{{ $req->id }}" class="mt-3 space-y-2"></div>

                                </div>
                            @else
                                <!-- Feature limited state -->
                                <div class="mt-3 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-3 flex items-start gap-3">
                                    <svg class="h-5 w-5 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-800">Unggah file langsung tidak tersedia</p>
                                        <p class="mt-1 text-xs text-blue-600">Paket <span class="font-bold">Starter</span> Anda hanya mendukung penyematan tautan via Google Drive. Tingkatkan langganan Anda untuk dapat mengunggah file PDF dan Excel langsung.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-3.5">
                            <p class="text-[11px] text-slate-400 leading-tight hidden sm:block">Dokumen lama &amp; link lama dapat dipertahankan atau dihapus secara selektif.</p>
                            <div class="flex items-center gap-2 ml-auto">
                                <button type="button" onclick="closeUploadModal('{{ $req->id }}')"
                                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition">
                                    Batal
                                </button>
                                <button type="submit" id="btn-submit-{{ $req->id }}"
                                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-bold text-white transition"
                                    style="background: linear-gradient(135deg, #7c3aed, #6d28d9)"
                                    onmouseover="this.style.background='linear-gradient(135deg,#6d28d9,#5b21b6)'"
                                    onmouseout="this.style.background='linear-gradient(135deg,#7c3aed,#6d28d9)'">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div><!-- /Modal Card -->
            </div>
        </div>
    </div>
@endforeach

<script>
    (() => {
        let linkIndices = {};

        const getLinkIndex = (reqId, initialCount) => {
            if (linkIndices[reqId] === undefined) linkIndices[reqId] = initialCount;
            return linkIndices[reqId]++;
        };

        const escapeHtml = (str) => {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        };

        const parseValidationNotes = (notesStr) => {
            if (!notesStr) return { targetFiles: [], targetLinks: [], customNote: '' };

            let targetFiles = [];
            let targetLinks = [];
            let customNote = String(notesStr);

            if (customNote.includes('📌 Target Revisi:')) {
                const parts = customNote.split('💬 Catatan Perti:');
                const headerPart = parts[0];
                customNote = parts[1] ? parts[1].trim() : '';

                const fileMatch = headerPart.match(/• Berkas Dokumen:\s*([^\r\n]+)/);
                if (fileMatch && fileMatch[1]) {
                    targetFiles = fileMatch[1].split(',').map(s => s.trim().toLowerCase());
                }

                const linkMatch = headerPart.match(/• Link Google Drive:\s*([^\r\n]+)/);
                if (linkMatch && linkMatch[1]) {
                    targetLinks = linkMatch[1].split(',').map(s => s.trim().toLowerCase());
                }
            }

            return { targetFiles, targetLinks, customNote };
        };

        window.clearTargetHighlight = (rowId) => {
            const row = document.getElementById(rowId);
            if (row) {
                row.className = 'flex items-start gap-2 p-2 rounded-xl border border-slate-200 bg-white transition duration-150';
            }
        };

        const buildDriveLinkRow = (reqId, idx, nameVal, urlVal, isTargeted = false) => {
            const row = document.createElement('div');
            row.className = 'flex items-start gap-2 p-2 rounded-xl transition duration-150 ' +
                (isTargeted ? 'border-2 border-rose-500 bg-rose-50/60 shadow-sm' : 'border border-slate-200 bg-white');
            row.id = `drive-row-${reqId}-${idx}`;

            const removeBtnHtml = `<button type="button" onclick="removeDriveLinkRow('${reqId}', ${idx})"
                    class="mt-5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
               </button>`;

            row.innerHTML = `
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nama Dokumen</label>
                        <input type="text" name="google_drive_links[${idx}][name]" value="${nameVal}" placeholder="Contoh: SK Rektor..."
                            oninput="clearTargetHighlight('drive-row-${reqId}-${idx}')"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Link Google Drive</label>
                        <input type="url" name="google_drive_links[${idx}][url]" value="${urlVal}" placeholder="https://drive.google.com/..."
                            oninput="clearTargetHighlight('drive-row-${reqId}-${idx}')"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition">
                    </div>
                </div>
                ${removeBtnHtml}
            `;
            return row;
        };

        window.openUploadModal = (reqId, existingLinks, existingFiles, validationNotes) => {
            const modal = document.getElementById(`upload-modal-${reqId}`);
            const modalBox = document.getElementById(`modal-box-${reqId}`);

            if (modal) {
                modal.querySelectorAll('.fixed.inset-0').forEach(el => el.classList.remove('hidden'));
            }

            const { targetFiles, targetLinks } = parseValidationNotes(validationNotes || '');

            // Pre-fill existing Google Drive links (Perbarui Berkas mode)
            const container = document.getElementById(`drive-links-container-${reqId}`);
            if (container) {
                container.innerHTML = '';
                if (existingLinks && existingLinks.length > 0) {
                    existingLinks.forEach((link, idx) => {
                        const linkNameStr = String(link.name || '').trim().toLowerCase();
                        const linkUrlStr = String(link.url || '').trim().toLowerCase();
                        const isTargeted = targetLinks.some(tl => tl === linkNameStr || tl === linkUrlStr);
                        const row = buildDriveLinkRow(reqId, idx, link.name || '', link.url || '', isTargeted);
                        container.appendChild(row);
                    });
                    linkIndices[reqId] = existingLinks.length + 10;
                } else {
                    const row = buildDriveLinkRow(reqId, 0, '', '', false);
                    container.appendChild(row);
                    linkIndices[reqId] = 1;
                }
            }

            // Pre-fill existing uploaded files
            const existingFilesContainer = document.getElementById(`existing-files-container-${reqId}`);
            if (existingFilesContainer) {
                existingFilesContainer.innerHTML = '';
                if (existingFiles && existingFiles.length > 0) {
                    // Add a label
                    const header = document.createElement('p');
                    header.className = 'text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
                    header.textContent = 'Berkas terunggah sebelumnya (tetap disimpan):';
                    existingFilesContainer.appendChild(header);

                    existingFiles.forEach((file, idx) => {
                        const fileRow = document.createElement('div');
                        const fileNameStr = String(file.original_filename || '').trim().toLowerCase();
                        const isTargeted = targetFiles.some(tf => tf === fileNameStr);
                        const borderClass = isTargeted ? 'border-2 border-rose-500 bg-rose-50/60 shadow-sm' : 'border border-slate-200 bg-slate-50';

                        fileRow.className = `flex items-center gap-3 rounded-xl px-3 py-2.5 ${borderClass}`;
                        fileRow.id = `existing-file-row-${reqId}-${idx}`;
                        
                        const sizeKb = (file.file_size / 1024).toFixed(1);
                        const ext = file.original_filename.split('.').pop().toLowerCase();
                        const bg = ext === 'pdf' ? '#fef2f2' : '#f0fdf4';
                        const fg = ext === 'pdf' ? '#b91c1c' : '#15803d';

                        fileRow.innerHTML = `
                            <input type="hidden" name="keep_files[]" value='${JSON.stringify(file).replace(/'/g, "&#39;")}' id="keep-file-input-${reqId}-${idx}">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[10px] font-bold" style="background:${bg};color:${fg}">${ext.toUpperCase()}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-slate-700 font-medium">${escapeHtml(file.original_filename)}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">${sizeKb} KB</p>
                            </div>
                            <button type="button" onclick="removeExistingFileRow('${reqId}', ${idx})"
                                class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        `;
                        existingFilesContainer.appendChild(fileRow);
                    });
                }
            }

            // Reset file input preview and selected files state
            selectedFilesMap[reqId] = [];
            const preview = document.getElementById(`file-preview-${reqId}`);
            if (preview) { preview.classList.add('hidden'); preview.innerHTML = ''; }
            const label = document.getElementById(`dropzone-label-${reqId}`);
            if (label) label.textContent = 'Klik atau seret berkas ke sini';
            const input = document.getElementById(`file-input-${reqId}`);
            if (input) input.value = '';


            modal.classList.remove('hidden');
            setTimeout(() => {
                modalBox.classList.remove('scale-95', 'opacity-0');
                modalBox.classList.add('scale-100', 'opacity-100');
            }, 50);
        };

        window.removeExistingFileRow = (reqId, idx) => {
            const row = document.getElementById(`existing-file-row-${reqId}-${idx}`);
            if (row) {
                row.remove();
            }
            const container = document.getElementById(`existing-files-container-${reqId}`);
            if (container && container.querySelectorAll('[id^="existing-file-row-"]').length === 0) {
                container.innerHTML = '';
            }
        };

        window.closeUploadModal = (reqId) => {
            const modal = document.getElementById(`upload-modal-${reqId}`);
            const modalBox = document.getElementById(`modal-box-${reqId}`);
            modalBox.classList.remove('scale-100', 'opacity-100');
            modalBox.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        window.addDriveLinkRow = (reqId) => {
            const container = document.getElementById(`drive-links-container-${reqId}`);
            const rows = container.querySelectorAll('[id^="drive-row-"]');
            const idx = getLinkIndex(reqId, rows.length + 10);
            const row = buildDriveLinkRow(reqId, idx, '', '', false);
            container.appendChild(row);
        };

        window.removeDriveLinkRow = (reqId, idx) => {
            const row = document.getElementById(`drive-row-${reqId}-${idx}`);
            if (row) row.remove();
        };

        window.disableSubmitButton = (reqId) => {
            const btn = document.getElementById(`btn-submit-${reqId}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg> Menyimpan...`;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            }
        };

        // Per-modal mutable list of selected files (so we can remove individual ones)
        const selectedFilesMap = {};

        const MAX_UPLOAD_MB = {{ \App\Support\AccreditationUpload::maxUploadMb() }};
        const MAX_UPLOAD_BYTES = MAX_UPLOAD_MB * 1024 * 1024;
        const POST_MAX_SIZE_BYTES = {{ $postMaxSizeBytes }};

        const rebuildInputFiles = (reqId) => {
            const input = document.getElementById(`file-input-${reqId}`);
            if (!input) return;
            const dt = new DataTransfer();
            // Only add non-oversized files to the actual input
            (selectedFilesMap[reqId] || [])
                .filter(f => f.size <= MAX_UPLOAD_BYTES)
                .forEach(f => dt.items.add(f));
            input.files = dt.files;
        };

        const renderFilePreview = (reqId) => {
            const preview = document.getElementById(`file-preview-${reqId}`);
            const label = document.getElementById(`dropzone-label-${reqId}`);
            if (!preview) return;
            const files = selectedFilesMap[reqId] || [];
            if (!files.length) {
                preview.classList.add('hidden');
                preview.innerHTML = '';
                if (label) label.textContent = 'Klik atau seret berkas ke sini';
                // Hide warnings
                const warn = document.getElementById(`oversized-warning-${reqId}`);
                if (warn) warn.classList.add('hidden');
                const totalWarn = document.getElementById(`total-size-warning-${reqId}`);
                if (totalWarn) totalWarn.classList.add('hidden');
                return;
            }

            preview.innerHTML = '';
            preview.classList.remove('hidden');

            const oversizedInList = files.filter(f => f.size > MAX_UPLOAD_BYTES);
            const validFiles = files.filter(f => f.size <= MAX_UPLOAD_BYTES);
            const totalBytes = validFiles.reduce((sum, f) => sum + f.size, 0);
            const TOTAL_MAX_BYTES = Math.min(POST_MAX_SIZE_BYTES, MAX_UPLOAD_BYTES);
            const isTotalOver = totalBytes > TOTAL_MAX_BYTES;

            files.forEach((file, idx) => {
                const sizeKb = (file.size / 1024).toFixed(1);
                const sizeMb = (file.size / 1024 / 1024).toFixed(1);
                const isOver = file.size > MAX_UPLOAD_BYTES;
                const ext = file.name.split('.').pop().toLowerCase();

                const bg = isOver ? '#fff1f2' : (ext === 'pdf' ? '#fef2f2' : '#f0fdf4');
                const fg = isOver ? '#be123c' : (ext === 'pdf' ? '#b91c1c' : '#15803d');
                const border = isOver ? 'border-rose-300 bg-rose-50' : 'border-slate-200 bg-white';

                const item = document.createElement('div');
                item.className = `flex items-center gap-3 rounded-xl border px-3 py-2.5 ${border}`;
                item.dataset.fileIdx = idx;
                item.innerHTML = `
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[10px] font-bold" style="background:${bg};color:${fg}">${ext.toUpperCase()}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold ${isOver ? 'text-rose-700' : 'text-slate-700'}">${file.name}</p>
                        <p class="text-[10px] mt-0.5 ${isOver ? 'text-rose-500 font-semibold' : 'text-slate-400'}">
                            ${isOver ? `⚠ ${sizeMb} MB — melebihi batas ${MAX_UPLOAD_MB} MB` : `${sizeKb} KB`}
                        </p>
                    </div>
                    <button type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-lg border ${isOver ? 'border-rose-300 bg-rose-100 text-rose-500 hover:bg-rose-200' : 'border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500'} transition"
                        title="Hapus berkas ini"
                        onclick="removeSelectedFile('${reqId}', ${idx})">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                preview.appendChild(item);
            });

            // Update label
            if (label) {
                label.textContent = oversizedInList.length > 0
                    ? `${files.length} berkas dipilih (${oversizedInList.length} terlalu besar)`
                    : `${files.length} berkas dipilih`;
            }

            // Show/hide oversized banner
            const warnBox = document.getElementById(`oversized-warning-${reqId}`);
            const warnList = document.getElementById(`oversized-list-${reqId}`);
            if (warnBox && warnList) {
                if (oversizedInList.length > 0) {
                    warnList.innerHTML = oversizedInList.map(f =>
                        `<li>${f.name} <span class="text-rose-400">(${(f.size/1024/1024).toFixed(1)} MB)</span></li>`
                    ).join('');
                    warnBox.classList.remove('hidden');
                } else {
                    warnBox.classList.add('hidden');
                }
            }

            // Total size warning handling
            const totalSizeWarnBox = document.getElementById(`total-size-warning-${reqId}`);
            const totalSizeCurrentSpan = document.getElementById(`total-size-current-${reqId}`);
            if (totalSizeWarnBox) {
                if (isTotalOver) {
                    if (totalSizeCurrentSpan) {
                        totalSizeCurrentSpan.textContent = (totalBytes / 1024 / 1024).toFixed(1) + ' MB';
                    }
                    totalSizeWarnBox.classList.remove('hidden');
                } else {
                    totalSizeWarnBox.classList.add('hidden');
                }
            }

            // Enable/disable submit button based on oversized files and total request size
            const submitBtn = document.getElementById(`btn-submit-${reqId}`);
            if (submitBtn) {
                if (oversizedInList.length > 0) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.title = `Hapus ${oversizedInList.length} berkas yang terlalu besar terlebih dahulu`;
                } else if (isTotalOver) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.title = `Total ukuran berkas melebihi batas request server`;
                } else {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '';
                    submitBtn.style.cursor = '';
                    submitBtn.title = '';
                }
            }
        };


        window.removeSelectedFile = (reqId, idx) => {
            if (!selectedFilesMap[reqId]) return;
            selectedFilesMap[reqId].splice(idx, 1);
            rebuildInputFiles(reqId);
            renderFilePreview(reqId);
        };

        window.updateFilePreview = (reqId, input) => {
            const newFiles = Array.from(input.files);
            if (!newFiles.length) return;
            if (!selectedFilesMap[reqId]) selectedFilesMap[reqId] = [];
            newFiles.forEach(f => selectedFilesMap[reqId].push(f));
            rebuildInputFiles(reqId);
            renderFilePreview(reqId);
        };


        // Drag and drop
        document.querySelectorAll('[id^="dropzone-"]').forEach(zone => {
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-violet-400', 'bg-violet-50'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('border-violet-400', 'bg-violet-50'));
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('border-violet-400', 'bg-violet-50');
                const reqId = zone.id.replace('dropzone-', '');
                const input = document.getElementById(`file-input-${reqId}`);
                if (input && e.dataTransfer.files.length > 0) {
                    input.files = e.dataTransfer.files;
                    updateFilePreview(reqId, input);
                }
            });
        });

        @if (session('failed_requirement_id'))
            const failedReqId = "{{ session('failed_requirement_id') }}";
            setTimeout(() => openUploadModal(failedReqId), 300);
        @endif
    })();
</script>

<!-- Detail Catatan Validasi Modal (Diskusi style) -->
<div id="detailValidationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 p-6 text-white relative">
                <button type="button" onclick="closeDetailValidationModal()" class="absolute top-4 right-4 text-white/80 hover:text-white rounded-full p-1 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
                <p class="text-[11px] font-bold uppercase tracking-wider text-violet-200">Catatan Revisi Perti</p>
                <h3 class="mt-1 text-lg font-extrabold text-white leading-snug" id="detailValTitle">Persyaratan</h3>
                <p class="mt-1 text-xs text-violet-100" id="detailValDate"></p>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-5">
                <!-- Target Items Section -->
                <div id="detailValTargetSection" class="hidden">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Dokumen / Link yang Perlu Direvisi:</p>
                    <div id="detailValTargetList" class="space-y-2"></div>
                </div>

                <!-- Custom Note Section -->
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Instruksi / Catatan Perti:</p>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-wrap" id="detailValNotes"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeDetailValidationModal()" class="ui-btn-secondary py-2 px-5 text-xs font-bold">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.openDetailValidationModal = (title, notesStr, dateStr) => {
        document.getElementById('detailValTitle').textContent = title || 'Persyaratan';
        document.getElementById('detailValDate').textContent = dateStr ? ('Divalidasi pada ' + dateStr) : '';

        const targetSection = document.getElementById('detailValTargetSection');
        const targetList = document.getElementById('detailValTargetList');
        const notesEl = document.getElementById('detailValNotes');

        targetList.innerHTML = '';

        const parseNotes = (str) => {
            if (!str) return { targetFiles: [], targetLinks: [], customNote: '' };
            let targetFiles = [];
            let targetLinks = [];
            let customNote = str;

            if (str.includes('📌 Target Revisi:')) {
                const parts = str.split('💬 Catatan Perti:');
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
        };

        const escapeHtml = (str) => {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        };
        
        const { targetFiles, targetLinks, customNote } = parseNotes(notesStr || '');
        notesEl.textContent = customNote || 'Tidak ada catatan tertulis khusus.';

        let hasTargets = false;

        if (targetFiles && targetFiles.length > 0) {
            hasTargets = true;
            targetFiles.forEach(f => {
                const card = document.createElement('div');
                card.className = 'flex items-center gap-2.5 rounded-xl border border-rose-200 bg-rose-50/70 p-3 text-xs text-rose-900 font-semibold';
                card.innerHTML = `<span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-700">📄</span> <span class="truncate">${escapeHtml(f)}</span> <span class="ml-auto shrink-0 rounded-full bg-rose-200/80 px-2 py-0.5 text-[10px] font-bold text-rose-800">Berkas</span>`;
                targetList.appendChild(card);
            });
        }

        if (targetLinks && targetLinks.length > 0) {
            hasTargets = true;
            targetLinks.forEach(l => {
                const card = document.createElement('div');
                card.className = 'flex items-center gap-2.5 rounded-xl border border-violet-200 bg-violet-50/70 p-3 text-xs text-violet-900 font-semibold';
                card.innerHTML = `<span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-700">🔗</span> <span class="truncate">${escapeHtml(l)}</span> <span class="ml-auto shrink-0 rounded-full bg-violet-200/80 px-2 py-0.5 text-[10px] font-bold text-violet-800">Link Drive</span>`;
                targetList.appendChild(card);
            });
        }

        if (hasTargets) {
            targetSection.classList.remove('hidden');
        } else {
            targetSection.classList.add('hidden');
        }

        document.getElementById('detailValidationModal').classList.remove('hidden');
    };

    window.closeDetailValidationModal = () => {
        document.getElementById('detailValidationModal').classList.add('hidden');
    };
</script>

