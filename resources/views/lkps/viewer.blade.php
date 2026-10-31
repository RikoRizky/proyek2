<x-viewer-layout>
    <x-slot name="header">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-800">
                    Tabel LKPS
                </span>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Pratinjau Data Lengkap</span>
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
        $rows = $submission?->table_rows ?? [];
        $rowCount = count($rows);
        $headers = $submission?->dynamic_headers ?? [];
        if (empty($headers) && !empty($rows)) {
            $first = $rows[0];
            if (isset($first['cells']) && is_array($first['cells'])) {
                $headers = array_map(fn($i) => 'Kolom ' . ($i + 1), array_keys($first['cells']));
            } else {
                $headers = array_keys($first);
            }
        }
        $isApproved = $submission?->status === 'approved';
        $isRevision = $submission?->status === 'revision';
        $isSubmitted = in_array($submission?->status, ['submitted', 'uploaded']);
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

    {{-- Top Action Bar --}}
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 py-2.5 select-none shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-600 hover:text-violet-500 transition">
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
                @if (isset($submission->summary_data['total_sks']))
                    <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                        Total SKS: {{ $submission->summary_data['total_sks'] }}
                    </span>
                @endif
            @else
                <span class="ui-badge bg-slate-100 text-slate-500 ring-slate-500/15">Belum diisi</span>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($routePrefix === 'perti' && $submission && $rowCount > 0)
                {{-- Tombol Setujui Langsung --}}
                @if ($submission->status !== 'approved')
                    <form action="{{ route('perti.prodis.lkps.validate', [$prodi->id, $tableCode]) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                            Setujui (Sesuai)
                        </button>
                    </form>
                @endif

                {{-- Tombol Modal Perlu Revisi --}}
                <button type="button" onclick="openLkpsViewerRevisionModal()"
                    class="inline-flex items-center gap-1 rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition shadow-sm">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                    </svg>
                    {{ $isRevision ? 'Ubah Catatan Revisi' : 'Perlu Revisi' }}
                </button>
            @endif

            {{-- Unduh Excel --}}
            @if ($submission && $rowCount > 0)
                @php
                    $exportUrl = $routePrefix === 'perti'
                        ? route('perti.prodis.lkps.export', [$prodi->id, $tableCode])
                        : route('unit.lkps.export', $tableCode);
                @endphp
                <a href="{{ $exportUrl }}" class="ui-btn-secondary py-1.5 px-3.5 text-xs inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                    </svg>
                    Unduh Excel (.xlsx)
                </a>
            @endif
        </div>
    </div>

    {{-- Catatan Revisi Banner (Jika ada) --}}
    @if ($submission && $submission->validation_notes)
        <div class="shrink-0 border-b {{ $isRevision ? 'border-rose-200 bg-rose-50/90 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }} px-4 py-3 sm:px-6">
            <div class="flex items-start gap-2.5">
                <svg class="h-4 w-4 shrink-0 {{ $isRevision ? 'text-rose-600' : 'text-slate-500' }} mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                </svg>
                <div class="min-w-0 flex-1 text-xs">
                    <span class="font-bold">Catatan Review Perti:</span>
                    @if ($submission->validated_at)
                        <span class="text-[10px] opacity-75">({{ $submission->validated_at->translatedFormat('d M Y, H:i') }})</span>
                    @endif
                    <p class="mt-0.5 whitespace-pre-line leading-relaxed">{{ $submission->validation_notes }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Full-Width Table Viewer --}}
    <div class="flex-1 flex flex-col min-h-0 bg-slate-100 overflow-hidden" x-data="{ searchQuery: '' }">
        @if ($submission && $rowCount > 0)
            {{-- Toolbar: Search & Quick info --}}
            <div class="shrink-0 bg-white border-b border-slate-200 px-4 py-2.5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Pencarian Cepat:</span>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Cari data dalam tabel..."
                            class="w-64 rounded-xl border-slate-200 bg-slate-50 pl-8 pr-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
                        <svg class="h-3.5 w-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span>Menampilkan <strong>{{ $rowCount }}</strong> baris</span>
                    <span>·</span>
                    <span><strong>{{ count($headers) }}</strong> kolom</span>
                </div>
            </div>

            {{-- Table Canvas --}}
            <div class="flex-1 overflow-auto bg-white p-4 sm:p-6">
                <div class="rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200 shadow-xs">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center bg-slate-100/90 text-slate-500">No</th>
                                @if ($tableCode === '2.B.1')
                                    <th class="py-3 px-4 min-w-[240px]">Mata Kuliah</th>
                                    <th class="py-3 px-3 w-20 text-center">SKS</th>
                                    <th class="py-3 px-3 w-24 text-center">Semester</th>
                                    @foreach ($headers as $pl)
                                        <th class="py-3 px-3 text-center min-w-[90px] bg-violet-50/60 border-l border-violet-100 text-violet-900">{{ $pl }}</th>
                                    @endforeach
                                @elseif ($tableCode === '2.B.2')
                                    <th class="py-3 px-4 min-w-[280px]">Capaian Pembelajaran Lulusan (CPL)</th>
                                    @foreach ($headers as $pl)
                                        <th class="py-3 px-3 text-center min-w-[90px] bg-violet-50/60 border-l border-violet-100 text-violet-900">{{ $pl }}</th>
                                    @endforeach
                                @elseif ($tableCode === '2.B.3')
                                    <th class="py-3 px-4 min-w-[140px]">CPL</th>
                                    <th class="py-3 px-4 min-w-[180px]">CPMK</th>
                                    @for ($i = 1; $i <= 8; $i++)
                                        <th class="py-3 px-3 text-center min-w-[90px] bg-violet-50/40 border-l border-slate-200">Semester {{ $i }}</th>
                                    @endfor
                                @else
                                    @foreach ($headers as $h)
                                        <th class="py-3 px-4 font-bold text-slate-800 border-r border-slate-200/60 last:border-r-0 min-w-[140px]">{{ $h }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($rows as $idx => $r)
                                <tr class="hover:bg-violet-50/30 transition"
                                    x-show="searchQuery === '' || JSON.stringify({{ json_encode($r) }}).toLowerCase().includes(searchQuery.toLowerCase())">
                                    <td class="py-2.5 px-3.5 text-center font-bold text-slate-400 bg-slate-50/40">{{ $idx + 1 }}</td>
                                    @if ($tableCode === '2.B.1')
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
                                    @elseif ($tableCode === '2.B.3')
                                        <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $r['cpl'] ?? '-' }}</td>
                                        <td class="py-2.5 px-4 text-slate-700">{{ $r['cpmk'] ?? '-' }}</td>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <td class="py-2.5 px-3 text-center border-l border-slate-200 text-slate-700 font-medium">
                                                {{ $r['semesters']['semester_' . $i] ?? ($r['cells'][$i + 1] ?? '-') }}
                                            </td>
                                        @endfor
                                    @else
                                        @foreach ($headers as $hIdx => $colKey)
                                            @php
                                                $v = '';
                                                if (isset($r['cells']) && is_array($r['cells'])) {
                                                    $v = $r['cells'][$hIdx] ?? '';
                                                } elseif (isset($r[$colKey])) {
                                                    $v = $r[$colKey];
                                                } elseif (is_array($r)) {
                                                    $v = array_values($r)[$hIdx] ?? '';
                                                }
                                            @endphp
                                            <td class="py-2.5 px-4 text-slate-800 border-r border-slate-200/40 last:border-r-0">
                                                @if (is_array($v))
                                                    {{ json_encode($v) }}
                                                @elseif (filter_var($v, FILTER_VALIDATE_URL))
                                                    <a href="{{ $v }}" target="_blank" class="inline-flex items-center gap-1 text-violet-600 font-semibold hover:underline">
                                                        <span>Link Bukti</span>
                                                        <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                                        </svg>
                                                    </a>
                                                @else
                                                    {{ $v !== '' ? $v : '-' }}
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($tableCode === '2.B.1' && isset($submission->summary_data['total_sks']))
                            <tfoot class="border-t-2 border-slate-200 bg-slate-50 font-bold text-slate-800">
                                <tr>
                                    <td colspan="2" class="py-3 px-4 text-right">Total SKS Terhitung:</td>
                                    <td class="py-3 px-3 text-center text-violet-700 font-black text-sm">{{ $submission->summary_data['total_sks'] }}</td>
                                    <td colspan="{{ 1 + count($headers) }}" class="py-3 px-4 text-slate-500 font-normal">
                                        {{ $rowCount }} mata kuliah terdaftar
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
                <div style="width: 72px; height: 72px; border-radius: 20px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 4px 16px rgba(139,92,246,0.18);">
                    <svg class="h-8 w-8 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Data Tabel Belum Tersedia</h2>
                <p class="mt-1 text-xs text-slate-500 max-w-sm">
                    Program Studi belum mengisi atau mengunggah data untuk Tabel {{ $tableCode }} ini.
                </p>
                <div class="mt-5">
                    <a href="{{ $backUrl }}" class="ui-btn-secondary text-xs">
                        ← Kembali ke Halaman Progress
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Perlu Revisi untuk LKPS Viewer --}}
    @if ($routePrefix === 'perti' && $submission)
        <div id="lkpsViewerRevisionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </span>
                            Catatan Revisi Tabel {{ $tableCode }}
                        </h3>
                        <button type="button" onclick="closeLkpsViewerRevisionModal()" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('perti.prodis.lkps.validate', [$prodi->id, $tableCode]) }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="revision">
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Catatan / Arahan Perbaikan untuk Prodi:
                            </label>
                            <textarea name="validation_notes" rows="4" required
                                placeholder="Contoh: Mohon lengkapi kolom Tugas Pokok dan Fungsi pada baris 2..."
                                class="w-full rounded-xl border-slate-200 text-xs py-2 px-3 focus:border-rose-500 focus:ring-1 focus:ring-rose-500">{{ $submission->validation_notes }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="closeLkpsViewerRevisionModal()" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700 transition shadow-sm">
                                Kirim Catatan Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openLkpsViewerRevisionModal() {
                const modal = document.getElementById('lkpsViewerRevisionModal');
                if (modal) modal.classList.remove('hidden');
            }
            function closeLkpsViewerRevisionModal() {
                const modal = document.getElementById('lkpsViewerRevisionModal');
                if (modal) modal.classList.add('hidden');
            }
        </script>
    @endif
</x-viewer-layout>
