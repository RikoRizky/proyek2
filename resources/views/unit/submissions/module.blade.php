<x-app-layout>
    @php
        $userId = auth()->id();
        $docReqs = $module->requirements->filter(fn ($req) => !$req->isTable());
        $tableReqs = $module->requirements->filter(fn ($req) => $req->isTable());

        $docUploadedCount = $docReqs->filter(fn ($req) => $req->submissions->first() && $req->submissions->first()->status !== \App\Enums\SubmissionStatus::Pending)->count();
        $tableUploadedCount = $tableReqs->filter(function ($req) use ($userId) {
            $sub = $req->lkpsSubmissions->first() ?? \App\Models\LkpsSubmission::where('user_id', $userId)->where('table_code', $req->table_code)->first();
            return $sub && !empty($sub->table_rows);
        })->count();

        $uploadedCount = $docUploadedCount + $tableUploadedCount;
        $totalCount = $module->requirements->count();
        $progressPercent = $totalCount > 0 ? round(($uploadedCount / $totalCount) * 100) : 0;

        $hasTable = $tableReqs->isNotEmpty();
        $hasDoc = $docReqs->isNotEmpty();
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Unit kerja · Program Studi</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900" data-module-title>{{ $module->name }}</h1>
                @if ($module->description)
                    <p class="mt-1 max-w-3xl text-sm text-slate-600">{{ $module->description }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-violet-200/80 bg-violet-50/80 px-4 py-3 text-right">
                <p class="text-xs font-bold uppercase tracking-wider text-violet-700">Progress modul</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-violet-900">{{ $uploadedCount }}/{{ $totalCount }}</p>
                <p class="text-xs text-violet-700">{{ $progressPercent }}% terpenuhi</p>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="ui-alert-success mb-6">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-bold">✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('upload_partial_failure'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-950 ring-1 ring-amber-500/15">
            <p class="font-semibold">Beberapa berkas gagal diunggah</p>
            <p class="mt-1">Berkas yang valid sudah tersimpan. Periksa bagian yang ditandai merah di bawah, lalu unggah ulang hanya berkas yang bermasalah.</p>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 ring-1 ring-red-500/10">
            {{ $errors->first() }}
        </div>
    @endif

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
                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-violet-700 shadow-sm ring-1 ring-violet-200">{{ $docUploadedCount }}/{{ $docReqs->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'lkps'"
                    :class="activeTab === 'lkps' ? 'border-violet-600 bg-violet-50/80 text-violet-900 shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"/>
                    </svg>
                    <span>Tabel Kinerja LKPS (Data & Excel)</span>
                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-violet-700 shadow-sm ring-1 ring-violet-200">{{ $tableUploadedCount }}/{{ $tableReqs->count() }}</span>
                </button>
            </div>
        @endif

        {{-- Content Tab 1: Dokumen LED --}}
        @if ($hasDoc)
            <div x-show="activeTab === 'led'" class="ui-card overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-4">
                    <h2 class="text-sm font-bold text-slate-800">Dokumen Bukti Evaluasi Diri (Siklus PPEPP)</h2>
                    <p class="text-xs text-slate-500">Unggah berkas dokumen (PDF/XLSX) atau sertakan link Google Drive untuk memenuhi setiap tahapan PPEPP.</p>
                </div>
                @include('unit.submissions._module-form', ['module' => $module])
            </div>
        @endif

        {{-- Content Tab 2: Tabel LKPS --}}
        @if ($hasTable)
            <div x-show="activeTab === 'lkps'" class="ui-card overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Tabel Kinerja LKPS (Data Kuantitatif & Matriks)</h2>
                        <p class="text-xs text-slate-500">Input data langsung di web atau import sekali klik dari file Excel LKPS LAM INFOKOM.</p>
                    </div>
                </div>
                @include('unit.submissions._lkps-tables', ['module' => $module])
            </div>
        @endif
    </div>

    @include('unit.submissions._module-modals', ['module' => $module])
    @include('unit.submissions._lkps-modals', ['module' => $module])

    <script>
        const uploadStatus = sessionStorage.getItem('upload_status');
        if (uploadStatus) {
            sessionStorage.removeItem('upload_status');
            const alert = document.createElement('div');
            alert.className = 'ui-alert-success mb-6';
            alert.setAttribute('role', 'status');
            alert.innerHTML = '<span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-bold">✓</span><span>' + uploadStatus + '</span>';
            document.querySelector('main')?.prepend(alert);
        }
    </script>
</x-app-layout>
