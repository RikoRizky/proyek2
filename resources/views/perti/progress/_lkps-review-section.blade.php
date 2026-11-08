@php
    $tableReqs = $requirements->filter(fn($r) => $r->isTable());
    $prodiUserId = $prodi->user_id;
@endphp

@if ($tableReqs->isEmpty())
    <div class="ui-empty py-12">
        <p class="text-sm font-semibold text-slate-700">Tidak ada tabel LKPS pada modul kriteria ini.</p>
        <p class="text-xs text-slate-400">Modul ini hanya berisi butir dokumen evaluasi diri (LED).</p>
    </div>
@else
    <ul class="divide-y divide-slate-100">
        @foreach ($tableReqs as $req)
            @php
                $tableCode = $req->table_code ?: 'TABLE';
                $lkps = $req->lkpsSubmissions->first() 
                    ?? \App\Models\LkpsSubmission::where('user_id', $prodiUserId)->where('table_code', $tableCode)->first();
                $rows = $lkps?->table_rows ?? [];
                $rowCount = count($rows);
                $isApproved = $lkps?->status === 'approved';
                $isRevision = $lkps?->status === 'revision';
                $isSubmitted = in_array($lkps?->status, ['submitted', 'uploaded']);
            @endphp
            <li id="table-{{ $tableCode }}" class="p-6 transition hover:bg-slate-50/60 scroll-mt-20 {{ $isRevision ? 'bg-rose-50/30' : '' }}" x-data="{ expanded: false }">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-violet-100 px-2.5 py-0.5 text-xs font-black text-violet-800">
                                {{ $tableCode }}
                            </span>
                            <span class="text-base font-bold text-slate-900">{{ $req->title }}</span>
                        </div>
                        @if ($req->description)
                            <p class="mt-1 text-sm text-slate-600">{{ $req->description }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($lkps && $rowCount > 0)
                                <span class="ui-badge {{ $lkps->statusBadgeClass() }}">{{ $lkps->statusLabel() }}</span>
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $rowCount }} baris data
                                </span>
                                @if (isset($lkps->summary_data['total_sks']))
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        Total SKS: {{ $lkps->summary_data['total_sks'] }}
                                    </span>
                                @endif
                                <span class="text-xs text-slate-400">· Diperbarui {{ $lkps->updated_at->diffForHumans() }}</span>
                            @else
                                <span class="ui-badge bg-slate-100 text-slate-500 ring-slate-500/15">Prodi belum mengisi data</span>
                            @endif
                        </div>

                        {{-- Catatan Review Sebelumnya --}}
                        @if ($lkps && $lkps->validation_notes)
                            <div class="mt-3 rounded-xl border p-3 text-xs {{ $isRevision ? 'border-rose-200 bg-rose-50/90 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }}">
                                <p class="font-bold flex items-center gap-1.5 {{ $isRevision ? 'text-rose-800' : 'text-slate-700' }}">
                                    Catatan Review:
                                    @if ($lkps->validated_at)
                                        <span class="text-[10px] opacity-75 font-normal">({{ $lkps->validated_at->translatedFormat('d M Y, H:i') }})</span>
                                    @endif
                                </p>
                                <p class="mt-1 leading-relaxed">{{ $lkps->validation_notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Action buttons Perti --}}
                    <div class="flex flex-wrap items-center gap-2 self-start lg:shrink-0">
                        @if ($lkps && $rowCount > 0)
                            <a href="{{ route('perti.prodis.lkps.viewer', [$prodi->id, $tableCode]) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-violet-200 bg-violet-50/80 px-3.5 py-2 text-xs font-bold text-violet-700 shadow-sm hover:bg-violet-100 hover:border-violet-300 transition"
                                title="Lihat lebih lengkap dalam tampilan viewer layar penuh">
                                <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                Lihat Lengkap
                            </a>

                            <button type="button" @click="expanded = !expanded"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                <span x-text="expanded ? 'Tutup Ringkas' : 'Periksa Ringkas'"></span>
                            </button>

                            <button type="button"
                                onclick="openPertiValidateTableModal('{{ $tableCode }}', '{{ addslashes($req->title) }}', '{{ $lkps->status }}', '{{ addslashes($lkps->validation_notes ?? '') }}')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Validasi Tabel
                            </button>

                            <a href="{{ route('perti.prodis.lkps.export', [$prodi->id, $tableCode]) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition"
                                title="Unduh data tabel dalam format Excel">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Unduh
                            </a>
                        @else
                            <span class="text-xs text-slate-400 italic">Data belum tersedia untuk divalidasi</span>
                        @endif
                    </div>
                </div>

                {{-- Full Data Table Inspector (Accordion) --}}
                @if ($lkps && $rowCount > 0)
                    <div x-show="expanded" x-collapse style="display: none;" class="mt-5 border-t border-slate-200 pt-4">
                        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                            <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700">Isi Data Tabel {{ $tableCode }} (Total {{ $rowCount }} baris)</span>
                                <div class="flex items-center gap-3">
                                    @if (isset($lkps->summary_data['total_sks']))
                                        <span class="font-semibold text-violet-700">Total SKS: {{ $lkps->summary_data['total_sks'] }}</span>
                                    @endif
                                    <a href="{{ route('perti.prodis.lkps.viewer', [$prodi->id, $tableCode]) }}" class="font-bold text-violet-600 hover:underline inline-flex items-center gap-1">
                                        <span>Buka Layar Penuh</span>
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="w-full text-left text-xs divide-y divide-slate-100">
                                    <thead class="bg-slate-50/80 text-slate-600 sticky top-0">
                                        <tr>
                                            <th class="py-2 px-3 w-10 text-center font-bold">No</th>
                                            @php
                                                $headers = $lkps->dynamic_headers ?? (isset($rows[0]) ? array_keys($rows[0]) : []);
                                            @endphp
                                            @foreach ($headers as $h)
                                                <th class="py-2 px-3 font-semibold text-slate-700">{{ is_string($h) ? $h : 'Kolom' }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($rows as $idx => $r)
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-2 px-3 text-center text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                                @if ($tableCode === '2.B.1')
                                                    <td class="py-2 px-3 font-semibold text-slate-800">{{ $r['mata_kuliah'] ?? '-' }}</td>
                                                    <td class="py-2 px-3 text-center">{{ $r['sks'] ?? '-' }}</td>
                                                    <td class="py-2 px-3 text-center">Semester {{ $r['semester'] ?? '-' }}</td>
                                                    @foreach ($headers as $pl)
                                                        @php $checked = isset($r['profil_lulusan'][$pl]) && $r['profil_lulusan'][$pl] == 1; @endphp
                                                        <td class="py-2 px-3 text-center">
                                                            {!! $checked ? '<span class="text-emerald-600 font-bold text-sm">✓</span>' : '<span class="text-slate-300">-</span>' !!}
                                                        </td>
                                                    @endforeach
                                                @elseif ($tableCode === '2.B.2')
                                                    <td class="py-2 px-3 font-semibold text-slate-800">{{ $r['cpl'] ?? '-' }}</td>
                                                    @foreach ($headers as $pl)
                                                        @php $checked = isset($r['profil_lulusan'][$pl]) && $r['profil_lulusan'][$pl] == 1; @endphp
                                                        <td class="py-2 px-3 text-center">
                                                            {!! $checked ? '<span class="text-emerald-600 font-bold text-sm">✓</span>' : '<span class="text-slate-300">-</span>' !!}
                                                        </td>
                                                    @endforeach
                                                @elseif ($tableCode === '2.B.3')
                                                    <td class="py-2 px-3 font-semibold text-slate-800">{{ $r['cpl'] ?? '-' }}</td>
                                                    <td class="py-2 px-3 text-slate-700">{{ $r['cpmk'] ?? '-' }}</td>
                                                    @for ($i = 1; $i <= 8; $i++)
                                                        <td class="py-2 px-2 text-center text-slate-700 font-medium">
                                                            {{ $r['semesters']['semester_' . $i] ?? '-' }}
                                                        </td>
                                                    @endfor
                                                @else
                                                    @foreach ($headers as $colKey)
                                                        @php $v = $r[$colKey] ?? (is_array($r) ? array_values($r)[$loop->index] ?? '' : ''); @endphp
                                                        <td class="py-2 px-3 text-slate-700">
                                                            @if (is_array($v))
                                                                {{ json_encode($v) }}
                                                            @elseif (filter_var($v, FILTER_VALIDATE_URL))
                                                                <a href="{{ $v }}" target="_blank" class="text-violet-600 hover:underline">Link Bukti ↗</a>
                                                            @else
                                                                {{ $v ?: '-' }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endif


