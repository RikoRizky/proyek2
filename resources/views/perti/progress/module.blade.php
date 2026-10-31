@php use App\Enums\SubmissionStatus; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi · Validasi Dokumen</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $module->name }}</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Prodi: <span class="font-semibold text-slate-800">{{ $prodi->name }}</span>
                    @if ($module->description)
                        · {{ $module->description }}
                    @endif
                </p>
            </div>
            @php
                $pendingValidationCount = $requirements->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Uploaded)->count();
                $approvedCount = $requirements->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Approved)->count();
                $revisionCount = $requirements->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Revision)->count();
                $uploadedTotal = $requirements->filter(fn($r) => $r->submissions->first() && $r->submissions->first()->status !== SubmissionStatus::Pending)->count();
                $totalCount = $requirements->count();
                $progressPercent = $totalCount > 0 ? round(($uploadedTotal / $totalCount) * 100) : 0;
            @endphp
            @if ($pendingValidationCount > 0)
                <form action="{{ route('perti.prodis.modul.batch-validate', [$prodi->id, $module->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui semua dokumen yang perlu divalidasi pada modul kriteria ini?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:opacity-90" style="background-color: #059669 !important; color: #ffffff !important;">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Setujui Semua (Batch Validasi)
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm font-semibold text-emerald-950 ring-1 ring-emerald-500/15 flex items-center gap-2">
            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('perti.prodis.progress', $prodi->id) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-600 hover:text-violet-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke progress prodi
        </a>
    </div>

    {{-- Module quick nav --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($allModules as $m)
            <a
                href="{{ route('perti.prodis.modul', [$prodi->id, $m->id]) }}"
                class="rounded-xl px-3 py-1.5 text-xs font-semibold transition
                    {{ $m->id === $module->id
                        ? 'bg-violet-600 text-white shadow-sm'
                        : 'bg-slate-100 text-slate-600 hover:bg-violet-50 hover:text-violet-700' }}"
                title="{{ $m->name }}">
                {{ $m->shortLabel() }}
            </a>
        @endforeach
    </div>

    {{-- Summary Stats --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Total Kelengkapan</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900">{{ $uploadedTotal }}/{{ $totalCount }} <span class="text-xs font-medium text-slate-500">({{ $progressPercent }}%)</span></p>
        </div>
        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-amber-700">Menunggu Validasi</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-amber-900">{{ $pendingValidationCount }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-emerald-700">Disetujui (Sesuai)</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-900">{{ $approvedCount }}</p>
        </div>
        <div class="rounded-2xl border border-rose-200/80 bg-rose-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-rose-700">Perlu Revisi</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-rose-900">{{ $revisionCount }}</p>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="ui-section-header flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Daftar persyaratan modul</h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $uploadedTotal }}/{{ $totalCount }} terunggah</span>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse ($requirements as $req)
                @php
                    $submission = $req->submissions->first();
                    $hasSubmission = $submission && $submission->status !== SubmissionStatus::Pending;
                    $status = $submission?->status;
                @endphp
                <li class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 px-6 py-5 transition hover:bg-slate-50/60">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        {{-- Status icon --}}
                        @if ($status === SubmissionStatus::Approved)
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600" title="Disetujui / Sesuai">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </span>
                        @elseif ($status === SubmissionStatus::Revision)
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600" title="Perlu Revisi">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Z"/></svg>
                            </span>
                        @elseif ($status === SubmissionStatus::Uploaded)
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600" title="Menunggu Validasi">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            </span>
                        @else
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-400" title="Belum diunggah">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900 leading-snug">{{ $req->title }}</p>
                            @if ($req->description)
                                <p class="mt-1 text-xs text-slate-500">{{ $req->description }}</p>
                            @endif

                            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                @if ($hasSubmission)
                                    <span class="ui-badge {{ $submission->status->badgeClass() }}">{{ $submission->status->label() }}</span>
                                    <span class="text-xs font-medium text-slate-500">Versi {{ $submission->version }}</span>
                                    <a href="{{ route('perti.submissions.view', $submission) }}" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Lihat dokumen</a>
                                    <span class="text-slate-300">·</span>
                                    <a href="{{ route('perti.submissions.show', $submission) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-500">Riwayat</a>
                                    <span class="text-[10px] text-slate-400">({{ $submission->updated_at->diffForHumans() }})</span>
                                @else
                                    <span class="ui-badge bg-slate-100 text-slate-700 ring-slate-500/15">Menunggu unggah</span>
                                @endif
                            </div>

                            @if ($hasSubmission && $submission->validation_notes)
                                <div class="mt-3 rounded-xl border border-amber-200/70 bg-amber-50/60 p-3 text-xs flex flex-wrap items-center justify-between gap-2 text-amber-900">
                                    <p class="font-bold flex items-center gap-1.5 text-amber-800">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                                        Catatan Revisi
                                        @if ($submission->validated_at)
                                            <span class="text-[10px] text-amber-700/80 font-normal">({{ $submission->validated_at->translatedFormat('d M Y, H:i') }})</span>
                                        @endif
                                    </p>
                                    <button type="button"
                                        onclick='openDetailValidationModal({{ json_encode($req->title) }}, {{ json_encode($submission->validation_notes) }}, {{ json_encode($submission->validated_at ? $submission->validated_at->translatedFormat("d M Y, H:i") : null) }})'
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-white px-2.5 py-1 text-xs font-bold text-amber-800 hover:bg-amber-100/70 transition shadow-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        Lihat Detail Catatan
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action buttons for Perti --}}
                    @if ($hasSubmission && $status !== SubmissionStatus::Approved)
                        <div class="flex items-center gap-2 shrink-0 self-end lg:self-center">
                            <form action="{{ route('perti.submissions.validate', $submission) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Setujui (Sesuai)
                                </button>
                            </form>

                            @if (auth()->user()->isSubscriptionExpired())
                                <form action="{{ route('perti.submissions.validate', $submission) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        {{ $status === SubmissionStatus::Revision ? 'Edit Catatan Revisi' : 'Perlu Revisi' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('perti.submissions.view', [$submission, 'action' => 'revision']) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                    {{ $status === SubmissionStatus::Revision ? 'Edit Catatan Revisi' : 'Perlu Revisi' }}
                                </a>
                            @endif
                        </div>
                    @endif
                </li>
            @empty
                <li class="ui-empty px-6">Belum ada persyaratan di modul ini.</li>
            @endforelse
        </ul>
    </div>

    {{-- Revision Modal --}}
    <div id="revisionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Z"/></svg>
                        </span>
                        Catatan Perlu Revisi
                    </h3>
                    <button type="button" onclick="closeRevisionModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="revisionForm" method="POST" action="" class="mt-4">
                    @csrf
                    <input type="hidden" name="status" value="revision">
                    
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Persyaratan:</p>
                    <p class="text-sm font-semibold text-slate-800 mb-4" id="modalReqTitle"></p>

                    <div id="moduleTargetSection" class="mb-4 hidden rounded-xl border border-rose-200/60 bg-rose-50/40 p-3.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-rose-900 mb-2">Pilih Berkas / Link yang Perlu Direvisi:</label>
                        
                        <div id="moduleLinksBox" class="mb-3 hidden">
                            <p class="text-[11px] font-bold text-violet-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">🔗 Link Google Drive:</p>
                            <div id="moduleLinksList" class="space-y-1.5"></div>
                        </div>

                        <div id="moduleFilesBox" class="hidden">
                            <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">📄 Berkas Dokumen:</p>
                            <div id="moduleFilesList" class="space-y-1.5"></div>
                        </div>
                    </div>

                    <div>
                        <label for="validation_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan / Alasan Revisi untuk Prodi:</label>
                        <p class="mt-1 text-[11px] text-slate-500 mb-1.5">Tentukan instruksi/catatan detail mengenai perbaikan yang harus dilakukan.</p>
                        <textarea id="validation_notes" name="validation_notes" rows="3"
                            placeholder="Contoh: Berkas Dokumen A halaman 2 TTD Dekan belum ada. Link Google Drive B tidak dapat diakses (Access Denied). Harap diperbaiki..."
                            class="mt-1.5 w-full rounded-xl border border-slate-200 p-3 text-sm text-slate-800 placeholder-slate-400 focus:border-rose-500 focus:ring-rose-500"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeRevisionModal()" class="ui-btn-secondary py-2 px-4 text-xs">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-bold text-white shadow-md transition hover:opacity-90" style="background-color: #e11d48 !important; color: #ffffff !important;">Kirim Catatan Revisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

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

        function openRevisionModal(submissionId, title, currentNotes, files = [], links = []) {
            const form = document.getElementById('revisionForm');
            form.action = `/perti/submissions/${submissionId}/validate`;
            document.getElementById('modalReqTitle').textContent = title;
            
            const { targetFiles, targetLinks, customNote } = parseValidationNotes(currentNotes);
            document.getElementById('validation_notes').value = customNote;

            const targetSection = document.getElementById('moduleTargetSection');
            const linksBox = document.getElementById('moduleLinksBox');
            const linksList = document.getElementById('moduleLinksList');
            const filesBox = document.getElementById('moduleFilesBox');
            const filesList = document.getElementById('moduleFilesList');

            linksList.innerHTML = '';
            filesList.innerHTML = '';
            let hasItems = false;

            if (links && links.length > 0) {
                hasItems = true;
                linksBox.classList.remove('hidden');
                links.forEach(link => {
                    const name = link.name || 'Link Drive';
                    const isChecked = targetLinks.includes(name) ? 'checked' : '';
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-800 cursor-pointer hover:border-rose-400 transition select-none';
                    label.innerHTML = `<input type="checkbox" name="target_links[]" value="${escapeHtml(name)}" ${isChecked} class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"> <span class="font-semibold text-violet-700">🔗 ${escapeHtml(name)}</span>`;
                    linksList.appendChild(label);
                });
            } else {
                linksBox.classList.add('hidden');
            }

            if (files && files.length > 0) {
                hasItems = true;
                filesBox.classList.remove('hidden');
                files.forEach(file => {
                    const fname = file.original_filename || 'Berkas Dokumen';
                    const isChecked = targetFiles.includes(fname) ? 'checked' : '';
                    const label = document.createElement('label');
                    label.className = 'flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-800 cursor-pointer hover:border-rose-400 transition select-none';
                    label.innerHTML = `<input type="checkbox" name="target_files[]" value="${escapeHtml(fname)}" ${isChecked} class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"> <span class="font-semibold text-slate-800 truncate">📄 ${escapeHtml(fname)}</span>`;
                    filesList.appendChild(label);
                });
            } else {
                filesBox.classList.add('hidden');
            }

            if (hasItems) {
                targetSection.classList.remove('hidden');
            } else {
                targetSection.classList.add('hidden');
            }

            document.getElementById('revisionModal').classList.remove('hidden');
        }

        window.closeRevisionModal = function() {
            document.getElementById('revisionModal').classList.add('hidden');
        };
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
        function openDetailValidationModal(title, notesStr, dateStr) {
            document.getElementById('detailValTitle').textContent = title || 'Persyaratan';
            document.getElementById('detailValDate').textContent = dateStr ? ('Divalidasi pada ' + dateStr) : '';

            const targetSection = document.getElementById('detailValTargetSection');
            const targetList = document.getElementById('detailValTargetList');
            const notesEl = document.getElementById('detailValNotes');

            targetList.innerHTML = '';

            const { targetFiles, targetLinks, customNote } = parseValidationNotes(notesStr || '');
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
        }

        function closeDetailValidationModal() {
            document.getElementById('detailValidationModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
