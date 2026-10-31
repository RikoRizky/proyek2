@php
    use App\Enums\UserRole;
    use App\Enums\SubmissionStatus;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Beranda</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Ringkasan</h1>
            <p class="mt-1 text-sm text-slate-600">Sistem penguploadan data akreditasi</p>
        </div>
    </x-slot>

    @if ($stats['role'] === UserRole::Admin)
        @php
            $summary = $progress['summary'];
            $pertiGrouped = collect($progress['units'])->groupBy('university_name');
            $pertiLabels = $pertiGrouped->keys()->all();
            $pertiPercents = $pertiGrouped->map(fn ($group) => round($group->avg('percent'), 1))->values()->all();
        @endphp

        {{-- Welcome Banner Admin --}}
        <div class="mb-6 relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 p-5 sm:p-8 shadow-xl shadow-violet-500/20 text-white">
            <div class="relative z-10">
                <p class="text-violet-300 text-xs font-bold uppercase tracking-widest">Admin · SILADATA</p>
                <h2 class="mt-1.5 text-xl font-black sm:text-2xl">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-sm text-violet-200">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-48 w-48 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -bottom-6 right-24 h-32 w-32 rounded-full bg-white/5"></div>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Perguruan Tinggi -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-violet-500 to-indigo-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Perguruan Tinggi</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 text-white shadow-md shadow-violet-500/30">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stats['pertiCount'] }}</p>
                <p class="mt-2 text-xs font-medium text-slate-500">Perguruan Tinggi terdaftar</p>
            </div>

            <!-- Program Studi -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Program Studi</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-md shadow-emerald-500/30">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stats['unitCount'] }}</p>
                <p class="mt-2 text-xs font-medium text-slate-500">Total prodi di seluruh perti</p>
            </div>

            <!-- Rata-rata Progress -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-sky-400 to-blue-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Rata-rata Progress</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-md shadow-sky-500/30 text-sm font-black">
                        %
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $summary['average_percent'] }}%</p>
                <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-all duration-500" style="width: {{ $summary['average_percent'] }}%"></div>
                </div>
                <p class="mt-2 text-xs font-medium text-slate-500">Rata-rata kelengkapan nasional</p>
            </div>

            <!-- Prodi Lengkap -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Prodi Lengkap</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/30">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $summary['complete_count'] }}</p>
                <p class="mt-2 text-xs font-medium text-slate-500">Prodi progress 100%</p>
            </div>
        </div>

        <div class="mb-8 grid gap-6 lg:grid-cols-2">
            <x-chart-card title="Progress per Perguruan Tinggi" subtitle="Rata-rata kelengkapan unggahan per perguruan tinggi" canvas-id="dashAdminBar" height="280px" />
            <div class="ui-card flex flex-col justify-center p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Analitik</p>
                <h2 class="mt-2 text-xl font-bold text-slate-900">Perbandingan antar prodi</h2>
                <p class="mt-2 text-sm text-slate-600">Lihat grafik lengkap, breakdown per kriteria, dan status kelengkapan setiap program studi.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.analytics') }}" class="ui-btn-primary text-sm">Buka grafik lengkap</a>
                    <a href="{{ route('home') }}" class="ui-btn-secondary text-sm">Dashboard publik</a>
                </div>
            </div>
        </div>

        <div class="ui-card overflow-hidden">
            <div class="ui-section-header">
                <h2 class="text-lg font-bold text-slate-900">Modul akreditasi</h2>
                <a href="{{ route('admin.modules.index') }}" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Kelola modul →</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($stats['modules'] as $module)
                    <li class="flex items-center justify-between gap-4 px-6 py-4 transition hover:bg-violet-50/30">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">{{ $module->name }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80">{{ $module->requirements_count }} syarat</span>
                    </li>
                @empty
                    <li class="ui-empty px-6 text-sm">Belum ada modul.</li>
                @endforelse
            </ul>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new Chart(document.getElementById('dashAdminBar'), {
                    type: 'bar',
                    data: {
                        labels: @json($pertiLabels),
                        datasets: [{
                            label: 'Progress (%)',
                            data: @json($pertiPercents),
                            backgroundColor: 'rgba(139,92,246,0.85)',
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                        },
                    },
                });
            });
        </script>
    @elseif ($stats['role'] === UserRole::Perti)
        @php
            $summary = $progress['summary'];
            $units = $progress['units'];
            $unitLabels = collect($units)->pluck('name')->values()->all();
            $unitPercents = collect($units)->pluck('percent')->values()->all();
            $unitUploaded = collect($units)->pluck('uploaded')->values()->all();
        @endphp

        {{-- Welcome Banner Perti --}}
        <div class="mb-6 relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 p-5 sm:p-8 shadow-xl shadow-violet-500/20 text-white">
            <div class="relative z-10">
                <p class="text-violet-300 text-xs font-bold uppercase tracking-widest">Perguruan Tinggi · SILADATA</p>
                <h2 class="mt-1.5 text-xl font-black sm:text-2xl">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-sm text-violet-200">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-48 w-48 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -bottom-6 right-24 h-32 w-32 rounded-full bg-white/5"></div>
        </div>

        @if (($stats['pendingValidationCount'] ?? 0) > 0)
            <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-500 via-amber-600 to-indigo-600 p-5 text-white shadow-xl shadow-amber-500/10 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold sm:text-lg">Ada {{ $stats['pendingValidationCount'] }} Dokumen Baru Memerlukan Validasi Perti!</h2>
                        <p class="mt-0.5 text-xs text-white/90 sm:text-sm">Program studi telah mengunggah dokumen baru. Periksa tabel di bawah untuk melakukan validasi.</p>
                    </div>
                </div>
                <a href="#prodi-table" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-extrabold text-amber-900 shadow-md transition hover:bg-amber-50">
                    Periksa Dokumen →
                </a>
            </div>
        @endif

        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Program Studi -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Program Studi</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-md shadow-emerald-500/30">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $stats['prodiCount'] }}</p>
                <p class="mt-2 text-xs font-medium text-slate-500">Total prodi terdaftar</p>
            </div>

            <!-- Rata-rata Progress -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-sky-400 to-blue-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Rata-rata Progress</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-md shadow-sky-500/30 text-sm font-black">
                        %
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $summary['average_percent'] }}%</p>
                <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-500 transition-all duration-500" style="width: {{ $summary['average_percent'] }}%"></div>
                </div>
                <p class="mt-2 text-xs font-medium text-slate-500">Rata-rata kelengkapan</p>
            </div>

            <!-- Prodi Lengkap -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-5 shadow-lg shadow-slate-200/50 transition duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Prodi Lengkap</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/30">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight text-slate-900">{{ $summary['complete_count'] }}</p>
                <p class="mt-2 text-xs font-medium text-slate-500">Prodi progress 100%</p>
            </div>

            <!-- Perlu Divalidasi -->
            <div class="relative overflow-hidden rounded-2xl border p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:shadow-xl
                {{ $stats['pendingValidationCount'] > 0 ? 'border-amber-300/60 bg-amber-50 shadow-amber-200/50' : 'border-slate-200/60 bg-white shadow-slate-200/50' }}">
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl
                    {{ $stats['pendingValidationCount'] > 0 ? 'bg-gradient-to-r from-amber-400 to-orange-400' : 'bg-gradient-to-r from-slate-300 to-slate-400' }}"></div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $stats['pendingValidationCount'] > 0 ? 'text-amber-800' : 'text-slate-500' }}">Perlu Divalidasi</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl text-white shadow-md
                        {{ $stats['pendingValidationCount'] > 0 ? 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-amber-500/30 animate-pulse' : 'bg-gradient-to-br from-slate-300 to-slate-400 shadow-slate-300/30' }}">
                        {{ $stats['pendingValidationCount'] > 0 ? '!' : '✓' }}
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black tracking-tight {{ $stats['pendingValidationCount'] > 0 ? 'text-amber-900' : 'text-slate-900' }}">{{ $stats['pendingValidationCount'] }}</p>
                <p class="mt-2 text-xs {{ $stats['pendingValidationCount'] > 0 ? 'font-bold text-amber-800' : 'font-medium text-slate-500' }}">
                    {{ $stats['pendingValidationCount'] > 0 ? 'Menunggu pemeriksaan Perti' : 'Semua dokumen divalidasi' }}
                </p>
            </div>
        </div>

        <div class="mb-8 grid gap-6 lg:grid-cols-2">
            <x-chart-card title="Perbandingan progress prodi" subtitle="Snapshot kelengkapan unggahan prodi Anda" canvas-id="dashPertiBar" height="280px" />
            <x-chart-card title="Status kelengkapan" subtitle="Distribusi prodi Anda berdasarkan progress" canvas-id="dashPertiStatusDoughnut" height="280px" />
        </div>

        <div id="prodi-table" class="mb-8 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 ui-card overflow-hidden">
                <div class="ui-section-header flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Detail per program studi</h2>
                    <span class="text-xs font-semibold text-slate-500">Total: {{ count($units) }} Program Studi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Program studi</th>
                                <th>Terunggah</th>
                                <th>Progress</th>
                                <th>Status Validasi</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $unit)
                                @php
                                    $pendingVal = $unit['pending_validation'] ?? 0;
                                    $revisionVal = $unit['revision'] ?? 0;
                                    $approvedVal = $unit['approved'] ?? 0;
                                    $uploadedVal = $unit['uploaded'] ?? 0;
                                    $prodiTargetId = $unit['prodi_id'] ?? $unit['id'];
                                @endphp
                                <tr>
                                    <td class="font-semibold text-slate-900">{{ $unit['name'] }}</td>
                                    <td class="tabular-nums text-slate-600">{{ $unit['uploaded'] }}/{{ $unit['total'] }}</td>
                                    <td class="min-w-[10rem]">
                                        <div class="flex items-center gap-3">
                                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                                                <div class="h-full rounded-full {{ $pendingVal > 0 ? 'bg-amber-500' : ($revisionVal > 0 ? 'bg-rose-500' : 'bg-emerald-500') }}" style="width: {{ $unit['percent'] }}%"></div>
                                            </div>
                                            <span class="w-10 text-right text-sm font-bold tabular-nums text-slate-700">{{ $unit['percent'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($pendingVal > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-xs font-extrabold text-amber-800 animate-pulse">
                                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                {{ $pendingVal }} pending
                                            </span>
                                        @elseif ($revisionVal > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-extrabold text-rose-700">
                                                <svg class="h-3 w-3 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                                {{ $revisionVal }} perlu revisi
                                            </span>
                                        @elseif ($uploadedVal > 0 && $approvedVal === $uploadedVal)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                                ✓ Selesai divalidasi
                                            </span>
                                        @elseif ($uploadedVal > 0)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                                {{ $uploadedVal }}/{{ $unit['total'] }} terunggah
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($prodiTargetId)
                                            @if ($pendingVal > 0)
                                                <a href="{{ route('perti.prodis.progress', $prodiTargetId) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-amber-700 hover:text-amber-800 underline">
                                                    Periksa Dokumen →
                                                </a>
                                            @elseif ($revisionVal > 0)
                                                <a href="{{ route('perti.prodis.progress', $prodiTargetId) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-rose-600 hover:text-rose-700 underline">
                                                    Lihat Revisi →
                                                </a>
                                            @else
                                                <a href="{{ route('perti.prodis.progress', $prodiTargetId) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-600 hover:text-violet-500">
                                                    Lihat Detail →
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="ui-empty text-sm py-6">Belum ada program studi terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ui-card flex flex-col justify-center p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Manajemen</p>
                <h2 class="mt-2 text-xl font-bold text-slate-900">Kelola akun program studi</h2>
                <p class="mt-2 text-sm text-slate-600">Buat, edit, dan kelola akun program studi di bawah perguruan tinggi Anda.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('perti.prodis.index') }}" class="ui-btn-primary text-sm">Kelola program studi</a>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const labels = @json($unitLabels);
                const percents = @json($unitPercents);
                const uploaded = @json($unitUploaded);
                const summary = @json($summary);

                new Chart(document.getElementById('dashPertiBar'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Progress (%)',
                            data: percents,
                            backgroundColor: percents.map(p => p >= 100 ? 'rgba(16,185,129,0.85)' : p > 0 ? 'rgba(59,130,246,0.85)' : 'rgba(148,163,184,0.7)'),
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterLabel: (ctx) => {
                                        const i = ctx.dataIndex;
                                        return uploaded[i] + ' dokumen terunggah';
                                    },
                                },
                            },
                        },
                        scales: {
                            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                            x: { ticks: { maxRotation: 45, minRotation: 0 } },
                        },
                    },
                });

                new Chart(document.getElementById('dashPertiStatusDoughnut'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Lengkap', 'Berjalan', 'Belum mulai'],
                        datasets: [{
                            data: [summary.complete_count, summary.in_progress_count, summary.empty_count],
                            backgroundColor: ['#10b981', '#8b5cf6', '#cbd5e1'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            });
        </script>
    @else
        @php
            $uploadedTotal = $stats['totalRequirements'] - $stats['notUploadedCount'];
            $moduleLabels = collect($progress['modules'])->pluck('short_label')->values()->all();
            $modulePercents = collect($progress['modules'])->pluck('percent')->values()->all();
        @endphp

        {{-- Welcome Banner Prodi --}}
        <div class="mb-6 relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 p-5 sm:p-8 shadow-xl shadow-violet-500/20 text-white">
            <div class="relative z-10">
                <p class="text-violet-300 text-xs font-bold uppercase tracking-widest">Program Studi · SILADATA</p>
                <h2 class="mt-1.5 text-xl font-black sm:text-2xl">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-sm text-violet-200">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-48 w-48 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -bottom-6 right-24 h-32 w-32 rounded-full bg-white/5"></div>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Progress Keseluruhan -->
            <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg {{ $stats['revisionCount'] > 0 ? 'ring-2 ring-amber-500/40 bg-amber-50/40' : '' }}">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $stats['revisionCount'] > 0 ? 'text-amber-800' : 'text-slate-500' }}">Progress Keseluruhan</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ $stats['revisionCount'] > 0 ? 'bg-amber-100 text-amber-700 font-black' : 'bg-emerald-50 text-emerald-600 font-extrabold' }} text-xs">
                        {{ $stats['revisionCount'] > 0 ? '!' : '%' }}
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight {{ $stats['revisionCount'] > 0 ? 'text-amber-900' : 'text-slate-900' }}">{{ $stats['progressPercent'] }}%</p>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full transition-all duration-500 {{ $stats['revisionCount'] > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ $stats['progressPercent'] }}%"></div>
                </div>
                @if ($stats['revisionCount'] > 0)
                    <p class="mt-2 text-xs font-bold text-amber-800 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                        <span>{{ $uploadedTotal }} dari {{ $stats['totalRequirements'] }} ({{ $stats['revisionCount'] }} perlu revisi)</span>
                    </p>
                @else
                    <p class="mt-2 text-xs font-medium text-slate-500">{{ $uploadedTotal }} dari {{ $stats['totalRequirements'] }} persyaratan</p>
                @endif
            </div>

            <!-- Sudah Terunggah -->
            <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sudah Terunggah</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $uploadedTotal }}</p>
                <p class="mt-4 text-xs font-medium text-slate-500">Dokumen telah diunggah ke sistem</p>
            </div>

            <!-- Perlu Revisi -->
            <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg {{ $stats['revisionCount'] > 0 ? 'ring-2 ring-rose-500/20 bg-rose-50/30' : '' }}">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $stats['revisionCount'] > 0 ? 'text-rose-600' : 'text-slate-500' }}">Perlu Revisi</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ $stats['revisionCount'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-400' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight {{ $stats['revisionCount'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $stats['revisionCount'] }}</p>
                <p class="mt-4 text-xs {{ $stats['revisionCount'] > 0 ? 'font-semibold text-rose-600' : 'font-medium text-slate-500' }}">
                    {{ $stats['revisionCount'] > 0 ? 'Dokumen memerlukan perbaikan' : 'Tidak ada catatan revisi' }}
                </p>
            </div>

            <!-- Belum Diunggah -->
            <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Belum Diunggah</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $stats['notUploadedCount'] }}</p>
                <p class="mt-4 text-xs font-medium text-slate-500">Dari {{ $stats['totalRequirements'] }} total persyaratan</p>
            </div>
        </div>

        <div class="mb-8">
            <x-chart-card title="Progress per kriteria" subtitle="Kelengkapan dokumen tiap modul" canvas-id="dashUnitBar" height="260px" />
        </div>

        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Kriteria akreditasi</h2>
                <p class="mt-1 text-sm text-slate-600">Pilih kriteria di sidebar atau kartu di bawah untuk mengunggah dokumen.</p>
            </div>
            <a href="{{ route('unit.reports.pdf') }}" class="ui-btn-secondary shrink-0 text-sm">Laporan PDF</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($stats['modules'] as $module)
                @php
                    $latestSubs = $module->requirements->map(fn ($req) => $req->submissions->first());
                    $uploaded = $latestSubs->filter(fn ($sub) => $sub && $sub->status !== SubmissionStatus::Pending)->count();
                    $revisionCount = $latestSubs->filter(fn ($sub) => $sub && $sub->status === SubmissionStatus::Revision)->count();
                    $approvedCount = $latestSubs->filter(fn ($sub) => $sub && $sub->status === SubmissionStatus::Approved)->count();
                    $total = $module->requirements->count();
                    $moduleProgress = $total > 0 ? round(($uploaded / $total) * 100) : 0;
                    $hasRevision = $revisionCount > 0;
                    $isAllApproved = $approvedCount === $total && $total > 0;
                @endphp
                <a href="{{ route('unit.submissions.module', $module) }}" class="ui-card group overflow-hidden transition duration-200 hover:-translate-y-0.5 hover:shadow-lg {{ $hasRevision ? 'ring-2 ring-rose-500/30 border-rose-200 bg-rose-50/20 hover:shadow-rose-500/10' : 'hover:shadow-violet-500/10' }}">
                    <div class="border-b px-5 py-4 {{ $hasRevision ? 'border-rose-100 bg-gradient-to-r from-rose-50 via-rose-50/60 to-white' : 'border-slate-100 bg-gradient-to-r from-violet-50/80 to-white' }}">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-bold uppercase tracking-wider {{ $hasRevision ? 'text-rose-700' : 'text-violet-700' }}">{{ $module->shortLabel() }}</p>
                            @if ($hasRevision)
                                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-100/90 px-2 py-0.5 text-[11px] font-extrabold text-rose-700 shadow-sm animate-pulse">
                                    <svg class="h-3 w-3 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                    </svg>
                                    {{ $revisionCount }} Perlu Revisi
                                </span>
                            @elseif ($isAllApproved)
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-100/90 px-2 py-0.5 text-[11px] font-extrabold text-emerald-700">
                                    ✓ Sesuai
                                </span>
                            @endif
                        </div>
                        <h3 class="mt-1.5 line-clamp-2 text-base font-bold text-slate-900 {{ $hasRevision ? 'group-hover:text-rose-700' : 'group-hover:text-violet-700' }}">{{ $module->name }}</h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="font-semibold text-slate-700">{{ $uploaded }}/{{ $total }} terunggah</span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $hasRevision ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">{{ $moduleProgress }}%</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all duration-500 {{ $hasRevision ? 'bg-rose-500' : 'bg-violet-500' }}" style="width: {{ $moduleProgress }}%"></div>
                        </div>
                        
                        @if ($hasRevision)
                            <div class="mt-4 flex items-center justify-between text-xs font-bold text-rose-600 group-hover:text-rose-700">
                                <span>Perbaiki dokumen revisi →</span>
                                <span class="rounded-full bg-rose-100 px-2 py-0.5 font-extrabold text-rose-700">{{ $revisionCount }} revisi</span>
                            </div>
                        @else
                            <p class="mt-4 text-sm font-semibold text-violet-600 group-hover:text-violet-500">Lihat lebih detail →</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new Chart(document.getElementById('dashUnitBar'), {
                    type: 'bar',
                    data: {
                        labels: @json($moduleLabels),
                        datasets: [{
                            label: 'Progress (%)',
                            data: @json($modulePercents),
                            backgroundColor: 'rgba(99,102,241,0.85)',
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                        },
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
