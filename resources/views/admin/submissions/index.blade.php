@php
    use App\Enums\SubmissionStatus;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Dokumen</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Unggahan per perguruan tinggi</h1>
            <p class="mt-1 text-sm text-slate-600">Data dikelompokkan berdasarkan Perguruan Tinggi induk.</p>
        </div>
    </x-slot>

    {{-- Search Bar --}}
    <div class="mb-6">
        <form method="GET" class="flex gap-3">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Cari nama prodi, syarat, berkas..."
                    class="w-full rounded-xl border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-slate-200 focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400"
                >
            </div>
            <button type="submit" class="ui-btn-primary px-5 py-2.5 text-sm">Cari</button>
            @if($q)
                <a href="{{ route('admin.submissions.index') }}" class="ui-btn-secondary px-5 py-2.5 text-sm">Reset</a>
            @endif
        </form>
    </div>

    @forelse ($pertis as $perti)
        <div class="mb-10">
            <!-- University Header -->
            <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-2">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-850 flex items-center gap-2">
                        <svg class="h-6 w-6 text-violet-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33l-7.5-5-7.5 5V21m16.5 0H3"/></svg>
                        {{ $perti->name }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Kode PT: {{ $perti->pertiProfile?->kode_pt ?? '-' }} | Alamat: {{ $perti->pertiProfile?->alamat ?? '-' }}</p>
                </div>
            </div>

            <!-- List of Prodis under this university -->
            <div class="grid gap-6">
                @php
                    $prodis = $perti->pertiProfile?->prodis ?? collect();
                @endphp
                @forelse ($prodis as $prodiRecord)
                    @php
                        $unit = $prodiRecord->user;
                    @endphp
                    @if ($unit)
                        {{-- Alpine: each prodi card has its own showAll state --}}
                        {{-- Each Prodi Card --}}
                        <div class="ui-card overflow-hidden">
                            <div class="ui-section-header bg-gradient-to-r from-violet-50/90 to-white flex-wrap gap-3">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $unit->name }}</h3>
                                    <p class="text-xs text-slate-500">{{ $unit->email }} | Kode Prodi: {{ $prodiRecord->kode_prodi ?? '-' }}</p>
                                </div>
                                
                                @php
                                    $allSubs = $unit->submissions;
                                    $pendingCount = $allSubs->filter(fn($sub) => $sub->status === SubmissionStatus::Uploaded)->count();
                                    $revisionCount = $allSubs->filter(fn($sub) => $sub->status === SubmissionStatus::Revision)->count();
                                    $approvedCount = $allSubs->filter(fn($sub) => $sub->status === SubmissionStatus::Approved)->count();
                                @endphp

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ $allSubs->count() }} dokumen</span>
                                    @if ($pendingCount > 0)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-800 ring-1 ring-amber-200 animate-pulse">⏳ {{ $pendingCount }} Menunggu Validasi</span>
                                    @endif
                                    @if ($revisionCount > 0)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-extrabold text-rose-700 ring-1 ring-rose-200">! {{ $revisionCount }} Perlu Revisi</span>
                                    @endif
                                    @if ($approvedCount > 0)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">✓ {{ $approvedCount }} Sesuai</span>
                                    @endif
                                </div>
                            </div>

                            @if ($unit->submissions->isEmpty())
                                <p class="px-6 py-8 text-center text-sm text-slate-500">Belum ada dokumen terunggah.</p>
                            @else
                                @php
                                    $groupedByModule = $allSubs->groupBy(fn($sub) => $sub->requirement->module_id);
                                @endphp

                                <div class="p-4 space-y-3 bg-slate-50/40">
                                    @foreach ($groupedByModule as $moduleId => $subs)
                                        @php
                                            $moduleObj = $subs->first()->requirement->module;
                                            $modPending = $subs->filter(fn($s) => $s->status === SubmissionStatus::Uploaded)->count();
                                            $modRevision = $subs->filter(fn($s) => $s->status === SubmissionStatus::Revision)->count();
                                            $modApproved = $subs->filter(fn($s) => $s->status === SubmissionStatus::Approved)->count();
                                            $hasAction = $modPending > 0 || $modRevision > 0;
                                        @endphp

                                        <div class="rounded-2xl border transition duration-150 overflow-hidden {{ $hasAction ? 'border-amber-200 bg-white shadow-sm ring-1 ring-amber-200/50' : 'border-slate-200/80 bg-white' }}" x-data="{ open: {{ $hasAction ? 'true' : 'false' }} }">
                                            <!-- Module Header -->
                                            <button type="button" @click="open = !open" class="flex w-full items-center justify-between px-5 py-4 text-left hover:bg-slate-50/80 transition">
                                                <div class="flex items-center gap-3">
                                                    <span class="rounded-lg bg-violet-100/80 px-2.5 py-1 text-xs font-extrabold text-violet-700 shrink-0">{{ $moduleObj->shortLabel() }}</span>
                                                    <div>
                                                        <h4 class="text-sm font-bold text-slate-900">{{ $moduleObj->name }}</h4>
                                                        <p class="text-xs text-slate-500 font-medium">{{ $subs->count() }} dokumen terunggah</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    @if ($modPending > 0)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-extrabold text-amber-800 animate-pulse">⏳ {{ $modPending }} pending</span>
                                                    @endif
                                                    @if ($modRevision > 0)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-extrabold text-rose-700">! {{ $modRevision }} revisi</span>
                                                    @endif
                                                    @if ($modApproved === $subs->count())
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">✓ Sesuai</span>
                                                    @endif
                                                    <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                    </svg>
                                                </div>
                                            </button>

                                            <!-- Module Submissions Table -->
                                            <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-3 bg-slate-50/30">
                                                <div class="overflow-x-auto">
                                                    <table class="ui-table w-full">
                                                        <thead>
                                                            <tr>
                                                                <th>Persyaratan / Dokumen</th>
                                                                <th>Status</th>
                                                                <th class="text-right">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($subs as $sub)
                                                                <tr>
                                                                    <td>
                                                                        <div class="text-sm font-semibold text-slate-900">{{ $sub->requirement->title }}</div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="ui-badge {{ $sub->status->badgeClass() }}">{{ $sub->status->label() }}</span>
                                                                    </td>
                                                                    <td class="text-right space-x-3 text-xs font-bold">
                                                                        <a href="{{ route('admin.submissions.view', $sub) }}" class="text-violet-600 hover:text-violet-700 underline">Lihat →</a>
                                                                        <a href="{{ route('admin.submissions.download', $sub) }}" class="text-slate-600 hover:text-slate-900">Unduh ↓</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="ui-card p-6 text-center text-sm text-slate-500 bg-slate-50/50">Belum ada program studi terdaftar.</div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="ui-empty ui-card py-16 text-sm">Tidak ada perguruan tinggi/program studi yang cocok.</div>
    @endforelse

    {{-- Pagination Perguruan Tinggi --}}
    @if ($pertis->hasPages())
        <div class="mt-8 flex flex-col items-center gap-2">
            <p class="text-xs text-slate-500">
                Menampilkan halaman <span class="font-semibold text-slate-700">{{ $pertis->currentPage() }}</span>
                dari <span class="font-semibold text-slate-700">{{ $pertis->lastPage() }}</span>
                (total <span class="font-semibold text-slate-700">{{ $pertis->total() }}</span> universitas)
            </p>
            <div class="mt-1">{{ $pertis->links() }}</div>
        </div>
    @endif
</x-app-layout>
