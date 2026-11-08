@php 
    use App\Enums\SubmissionStatus; 
    $prodiUserId = $prodi->user_id;

    $docReqs = $requirements->filter(fn($r) => !$r->isTable());
    $tableReqs = $requirements->filter(fn($r) => $r->isTable());

    $pendingDocCount = $docReqs->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Uploaded)->count();
    $approvedDocCount = $docReqs->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Approved)->count();
    $revisionDocCount = $docReqs->filter(fn($r) => $r->submissions->first()?->status === SubmissionStatus::Revision)->count();
    $uploadedDocTotal = $docReqs->filter(fn($r) => $r->submissions->first() && $r->submissions->first()->status !== SubmissionStatus::Pending)->count();

    $tableSubmissions = \App\Models\LkpsSubmission::where('user_id', $prodiUserId)->get()->keyBy('table_code');
    $pendingTableCount = $tableReqs->filter(function($r) use ($tableSubmissions) {
        $sub = $tableSubmissions->get($r->table_code);
        return $sub && in_array($sub->status, ['submitted', 'uploaded']);
    })->count();
    $approvedTableCount = $tableReqs->filter(fn($r) => $tableSubmissions->get($r->table_code)?->status === 'approved')->count();
    $revisionTableCount = $tableReqs->filter(fn($r) => $tableSubmissions->get($r->table_code)?->status === 'revision')->count();
    $uploadedTableTotal = $tableReqs->filter(fn($r) => !empty($tableSubmissions->get($r->table_code)?->table_rows))->count();

    $pendingValidationCount = $pendingDocCount + $pendingTableCount;
    $approvedCount = $approvedDocCount + $approvedTableCount;
    $revisionCount = $revisionDocCount + $revisionTableCount;
    $uploadedTotal = $uploadedDocTotal + $uploadedTableTotal;
    $totalCount = $requirements->count();
    $progressPercent = $totalCount > 0 ? round(($uploadedTotal / $totalCount) * 100) : 0;

    $hasTable = $tableReqs->isNotEmpty();
    $hasDoc = $docReqs->isNotEmpty();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi · Monitoring & Validasi</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $module->name }}</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Prodi: <span class="font-semibold text-slate-800">{{ $prodi->name }}</span>
                    @if ($module->description)
                        · {{ $module->description }}
                    @endif
                </p>
            </div>
            @if ($pendingDocCount > 0)
                <form action="{{ route('perti.prodis.modul.batch-validate', [$prodi->id, $module->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui semua dokumen yang perlu divalidasi pada modul kriteria ini?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:opacity-90" style="background-color: #059669 !important; color: #ffffff !important;">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Setujui Semua Dokumen (Batch Validasi)
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

    {{-- Tabs Section: Dokumen LED vs Tabel LKPS --}}
    <div x-data="{ activeTab: '{{ request('tab', ($hasDoc ? 'led' : 'lkps')) }}' }">
        @if ($hasTable && $hasDoc)
            <div class="mb-6 flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-3">
                <button type="button" @click="activeTab = 'led'"
                    :class="activeTab === 'led' ? 'border-violet-600 bg-violet-50/80 text-violet-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                    <span>Dokumen LED (PPEPP)</span>
                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-violet-700 shadow-sm ring-1 ring-violet-200">{{ $uploadedDocTotal }}/{{ $docReqs->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'lkps'"
                    :class="activeTab === 'lkps' ? 'border-violet-600 bg-violet-50/80 text-violet-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"/>
                    </svg>
                    <span>Tabel Kinerja LKPS (Data Tabel)</span>
                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-violet-700 shadow-sm ring-1 ring-violet-200">{{ $uploadedTableTotal }}/{{ $tableReqs->count() }}</span>
                </button>
            </div>
        @endif

        {{-- Section 1: Dokumen LED --}}
        @if ($hasDoc)
            <div x-show="activeTab === 'led'" class="ui-card overflow-hidden">
                <div class="ui-section-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Dokumen Evaluasi Diri (Siklus PPEPP)</h2>
                        <p class="text-xs text-slate-500">Periksa dan validasi dokumen bukti yang diunggah oleh prodi.</p>
                    </div>
                    <span class="text-xs text-slate-500 font-semibold">{{ $uploadedDocTotal }}/{{ $docReqs->count() }} terunggah</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($docReqs as $req)
                        @php
                            $submission = $req->submissions->first();
                            $hasSubmission = $submission && $submission->status !== SubmissionStatus::Pending;
                            $status = $submission?->status;
                        @endphp
                        <li id="req-{{ $req->id }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 px-6 py-5 transition hover:bg-slate-50/60 scroll-mt-20">
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
                                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $req->description }}</p>
                                    @endif

                                    @if ($hasSubmission)
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            <span class="ui-badge {{ $status->badgeClass() }}">
                                                {{ $status->label() }}
                                            </span>
                                            <span class="text-xs text-slate-500 font-medium">Versi {{ $submission->version }}</span>
                                            <span class="text-xs text-slate-400">· {{ $submission->created_at->diffForHumans() }}</span>
                                        </div>

                                        {{-- File info & links --}}
                                        <div class="mt-2.5 flex flex-wrap items-center gap-3">
                                            @php
                                                $filesList = $submission->files ?? [];
                                                if (empty($filesList) && $submission->file_path) {
                                                    $filesList = [[
                                                        'path' => $submission->file_path,
                                                        'original_name' => $submission->original_filename,
                                                        'file_size' => $submission->file_size,
                                                        'mime_type' => $submission->mime_type,
                                                    ]];
                                                }
                                                $driveLinks = $submission->google_drive_links ?? [];
                                            @endphp

                                            @if (count($filesList) > 0)
                                                <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                                                    <span>📁</span>
                                                    <span>{{ count($filesList) }} Berkas</span>
                                                </div>
                                            @endif

                                            @if (count($driveLinks) > 0)
                                                <div class="flex items-center gap-1.5 text-xs text-violet-700 bg-violet-50 px-2.5 py-1 rounded-lg">
                                                    <span>🔗</span>
                                                    <span>{{ count($driveLinks) }} Link Google Drive</span>
                                                </div>
                                            @endif

                                            <a href="{{ route('perti.submissions.view', $submission) }}" class="text-xs font-bold text-violet-600 hover:text-violet-500 hover:underline">
                                                Buka Berkas Dokumen →
                                            </a>
                                        </div>
                                    @else
                                        <p class="mt-2 text-xs text-slate-400 italic">Belum ada dokumen yang diunggah oleh prodi.</p>
                                    @endif
                                </div>
                            </div>

                            @if ($hasSubmission)
                                <div class="flex items-center gap-2 self-start lg:self-center shrink-0">
                                    <button
                                        type="button"
                                        onclick="openValidateModal({{ $submission->id }}, '{{ addslashes($req->title) }}', '{{ $submission->status->value }}', {{ json_encode($submission->validation_notes ?? '') }}, {{ json_encode($submission->files ?? []) }}, {{ json_encode($submission->google_drive_links ?? []) }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 transition"
                                    >
                                        Validasi Dokumen
                                    </button>
                                </div>
                            @endif
                        </li>
                    @empty
                        <li class="p-6 text-center text-xs text-slate-400">Tidak ada persyaratan dokumen.</li>
                    @endforelse
                </ul>
            </div>
        @endif

        {{-- Section 2: Tabel LKPS --}}
        @if ($hasTable)
            <div x-show="activeTab === 'lkps'" class="ui-card overflow-hidden">
                <div class="ui-section-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tabel Kinerja LKPS (Data Kuantitatif & Matriks)</h2>
                        <p class="text-xs text-slate-500">Periksa baris data tabel prodi secara langsung di sistem atau unduh format Excel resminya.</p>
                    </div>
                    <span class="text-xs text-slate-500 font-semibold">{{ $uploadedTableTotal }}/{{ $tableReqs->count() }} terisi</span>
                </div>
                @include('perti.progress._lkps-review-section', [
                    'requirements' => $requirements,
                    'prodi' => $prodi,
                ])
            </div>
        @endif
    </div>

    {{-- Modal Validasi Dokumen PPEPP (Single item) --}}
    <div id="validateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="valModalTitle">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeValidateModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-950/10">
                <div class="border-b border-slate-100 bg-gradient-to-r from-violet-50/80 to-white px-6 py-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700">VALIDASI DOKUMEN</p>
                    <h3 class="mt-1 text-base font-bold text-slate-900" id="valModalTitle">Nama Persyaratan</h3>
                </div>

                <form id="valForm" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">STATUS VALIDASI</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="status" value="approved" id="status_approved" class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block text-xs font-bold text-emerald-800">Sesuai</span>
                                    <span class="block text-[11px] text-slate-500">Dokumen sah & sesuai</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="status" value="revision" id="status_revision" class="text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="block text-xs font-bold text-rose-800">Perlu Revisi</span>
                                    <span class="block text-[11px] text-slate-500">Ada berkas keliru</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="validation_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">CATATAN VALIDASI (WAJIB JIKA REVISI)</label>
                        <textarea id="validation_notes" name="validation_notes" rows="3" placeholder="Tulis catatan atau instruksi perbaikan untuk prodi..."
                            class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-violet-500 focus:ring-1 focus:ring-violet-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" onclick="closeValidateModal()" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-violet-600 px-5 py-2 text-xs font-bold text-white hover:bg-violet-700 transition shadow-sm">
                            Simpan Validasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openValidateModal(subId, reqTitle, currentStatus, currentNotes) {
            document.getElementById('valModalTitle').textContent = reqTitle;
            document.getElementById('valForm').action = '/perti/submissions/' + subId + '/validate';
            document.getElementById('validation_notes').value = currentNotes || '';

            if (currentStatus === 'revision') {
                document.getElementById('status_revision').checked = true;
            } else {
                document.getElementById('status_approved').checked = true;
            }

            document.getElementById('validateModal').classList.remove('hidden');
        }

        function openPertiValidateTableModal(tableCode, tableTitle, currentStatus, currentNotes) {
            const displayTitle = tableTitle.startsWith('Tabel') ? tableTitle : ('Tabel ' + tableCode + ': ' + tableTitle);
            document.getElementById('valModalTitle').textContent = displayTitle;
            document.getElementById('valForm').action = '/perti/prodis/{{ $prodi->id }}/lkps/' + encodeURIComponent(tableCode) + '/validate';
            document.getElementById('validation_notes').value = currentNotes || '';

            if (currentStatus === 'revision') {
                document.getElementById('status_revision').checked = true;
            } else {
                document.getElementById('status_approved').checked = true;
            }

            document.getElementById('validateModal').classList.remove('hidden');
        }

        function closeValidateModal() {
            document.getElementById('validateModal').classList.add('hidden');
        }

        function closePertiValidateTableModal() {
            closeValidateModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeValidateModal();
            }
        });
    </script>
</x-app-layout>
