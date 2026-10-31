<x-app-layout>
    @php
        $modules = $progress['modules'];
        $moduleLabels = collect($modules)->pluck('short_label')->values()->all();
        $modulePercents = collect($modules)->pluck('percent')->values()->all();
        $moduleUploaded = collect($modules)->pluck('uploaded')->values()->all();
        $moduleTotals = collect($modules)->pluck('total')->values()->all();
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Perguruan Tinggi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Progress: {{ $prodi->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $progress['uploaded'] }}/{{ $progress['total'] }} dokumen terunggah ({{ $progress['percent'] }}%)</p>
        </div>
    </x-slot>

    @php
        $notUploaded = $progress['total'] - $progress['uploaded'];
        $hasRevision = ($progress['revision'] ?? 0) > 0;
        $hasPending = ($progress['pending_validation'] ?? 0) > 0;
    @endphp

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Progress Keseluruhan -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg {{ $hasRevision ? 'ring-2 ring-amber-500/40 bg-amber-50/40' : '' }}">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider {{ $hasRevision ? 'text-amber-800' : 'text-slate-500' }}">Progress Keseluruhan</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ $hasRevision ? 'bg-amber-100 text-amber-700 font-black' : 'bg-emerald-50 text-emerald-600 font-extrabold' }} text-xs">
                    {{ $hasRevision ? '!' : '%' }}
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight {{ $hasRevision ? 'text-amber-900' : 'text-slate-900' }}">{{ $progress['percent'] }}%</p>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full transition-all duration-500 {{ $hasRevision ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ $progress['percent'] }}%"></div>
            </div>
            @if ($hasRevision)
                <p class="mt-2 text-xs font-bold text-amber-800 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                    <span>{{ $progress['uploaded'] }} dari {{ $progress['total'] }} ({{ $progress['revision'] }} perlu revisi)</span>
                </p>
            @else
                <p class="mt-2 text-xs font-medium text-slate-500">{{ $progress['uploaded'] }} dari {{ $progress['total'] }} persyaratan</p>
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
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $progress['uploaded'] }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Dokumen telah terunggah ke sistem</p>
        </div>

        <!-- Perlu Divalidasi -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg {{ $hasPending ? 'ring-2 ring-amber-500/40 bg-amber-50/40' : '' }}">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider {{ $hasPending ? 'text-amber-800 font-extrabold' : 'text-slate-500' }}">Perlu Divalidasi</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ $hasPending ? 'bg-amber-100 text-amber-700 animate-pulse font-black' : 'bg-slate-100 text-slate-400' }}">
                    {{ $hasPending ? '!' : '✓' }}
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight {{ $hasPending ? 'text-amber-900' : 'text-slate-900' }}">{{ $progress['pending_validation'] }}</p>
            <p class="mt-4 text-xs {{ $hasPending ? 'font-bold text-amber-800' : 'font-medium text-slate-500' }}">
                {{ $hasPending ? 'Dokumen menunggu pemeriksaan' : 'Tidak ada antrean validasi' }}
            </p>
        </div>

        <!-- Belum Terunggah -->
        <div class="ui-card relative overflow-hidden p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Belum Terunggah</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $notUploaded }}</p>
            <p class="mt-4 text-xs font-medium text-slate-500">Dari {{ $progress['total'] }} total persyaratan</p>
        </div>
    </div>

    <div class="mb-8 grid gap-6 lg:grid-cols-2">
        <x-chart-card title="Progress per kriteria" subtitle="Persentase kelengkapan tiap modul akreditasi {{ $prodi->name }}" canvas-id="pertiModulesBar" />
        <x-chart-card title="Ringkasan keseluruhan" subtitle="Dokumen terunggah vs belum" canvas-id="pertiOverallDoughnut" height="280px" />
    </div>

    <div class="ui-card overflow-hidden">
        <div class="ui-section-header flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Detail per kriteria</h2>
            <span class="text-xs font-semibold text-slate-500">{{ $prodi->name }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Terunggah</th>
                        <th>Progress</th>
                        <th>Status Validasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $module)
                        @php
                            $pendingVal = $module['pending_validation'] ?? 0;
                            $revisionVal = $module['revision'] ?? 0;
                            $approvedVal = $module['approved'] ?? 0;
                            $uploadedVal = $module['uploaded'] ?? 0;
                        @endphp
                        <tr class="{{ $pendingVal > 0 ? 'bg-amber-50/30' : ($revisionVal > 0 ? 'bg-rose-50/20' : '') }}">
                            <td>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $module['short_label'] }}</p>
                                    @if ($pendingVal > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-100/90 px-2 py-0.5 text-[11px] font-extrabold text-amber-800 animate-pulse">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            {{ $pendingVal }} Perlu Validasi
                                        </span>
                                    @elseif ($revisionVal > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-100/90 px-2 py-0.5 text-[11px] font-extrabold text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            {{ $revisionVal }} Perlu Revisi
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500">{{ $module['name'] }}</p>
                            </td>
                            <td class="tabular-nums">{{ $module['uploaded'] }}/{{ $module['total'] }}</td>
                            <td class="min-w-[10rem]">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 flex-1 rounded-full bg-slate-100">
                                        <div class="h-full rounded-full {{ $pendingVal > 0 ? 'bg-amber-500' : ($revisionVal > 0 ? 'bg-rose-500' : 'bg-emerald-500') }}" style="width: {{ $module['percent'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold tabular-nums">{{ $module['percent'] }}%</span>
                                </div>
                            </td>
                            <td>
                                @if ($pendingVal > 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-100 px-2.5 py-0.5 text-xs font-extrabold text-amber-800 animate-pulse">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                        {{ $pendingVal }} pending
                                    </span>
                                @elseif ($revisionVal > 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-extrabold text-rose-700">
                                        <svg class="h-3 w-3 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                        {{ $revisionVal }} Perlu Revisi
                                    </span>
                                @elseif ($uploadedVal > 0 && $approvedVal === $uploadedVal)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                        ✓ Valid
                                    </span>
                                @elseif ($uploadedVal > 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                        {{ $uploadedVal }}/{{ $module['total'] }} Terunggah
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($pendingVal > 0)
                                    <a href="{{ route('perti.prodis.modul', [$prodi->id, $module['module_id']]) }}" class="text-xs font-extrabold text-amber-700 hover:text-amber-800 underline">
                                        Validasi sekarang →
                                    </a>
                                @elseif ($revisionVal > 0)
                                    <a href="{{ route('perti.prodis.modul', [$prodi->id, $module['module_id']]) }}" class="text-xs font-extrabold text-rose-600 hover:text-rose-700 underline">
                                        Lihat Revisi →
                                    </a>
                                @else
                                    <a href="{{ route('perti.prodis.modul', [$prodi->id, $module['module_id']]) }}" class="text-xs font-semibold text-violet-600 hover:text-violet-500">
                                        Lihat detail →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = @json($moduleLabels);
            const percents = @json($modulePercents);
            const uploaded = @json($moduleUploaded);
            const totals = @json($moduleTotals);
            const overall = @json($progress);

            new Chart(document.getElementById('pertiModulesBar'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Progress (%)',
                        data: percents,
                        backgroundColor: percents.map(p => p >= 100 ? 'rgba(16,185,129,0.85)' : p > 0 ? 'rgba(99,102,241,0.85)' : 'rgba(148,163,184,0.7)'),
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
                                afterLabel: (ctx) => uploaded[ctx.dataIndex] + '/' + totals[ctx.dataIndex] + ' dokumen',
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                    },
                },
            });

            new Chart(document.getElementById('pertiOverallDoughnut'), {
                type: 'doughnut',
                data: {
                    labels: ['Terunggah', 'Belum'],
                    datasets: [{
                        data: [overall.uploaded, overall.total - overall.uploaded],
                        backgroundColor: ['#10b981', '#e2e8f0'],
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
</x-app-layout>
