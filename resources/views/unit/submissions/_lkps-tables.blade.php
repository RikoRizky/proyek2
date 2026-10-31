@php
    $tableReqs = $module->requirements->filter(fn($r) => $r->isTable());
    $userId = auth()->id();
@endphp

@if ($tableReqs->isEmpty())
    <div class="ui-empty py-12">
        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
        </svg>
        <p class="mt-2 text-base font-semibold text-slate-700">Tidak ada tabel LKPS pada modul ini.</p>
        <p class="text-xs text-slate-400">Modul ini hanya memuat dokumen evaluasi diri (LED).</p>
    </div>
@else
    <div class="divide-y divide-slate-100">
        @foreach ($tableReqs as $req)
            @php
                $tableCode = $req->table_code ?: 'TABLE';
                $lkps = $req->lkpsSubmissions->first() 
                    ?? \App\Models\LkpsSubmission::where('user_id', $userId)->where('table_code', $tableCode)->first();
                $rows = $lkps?->table_rows ?? [];
                $rowCount = count($rows);
                $isApproved = $lkps?->status === 'approved';
                $isRevision = $lkps?->status === 'revision';
                $isSubmitted = in_array($lkps?->status, ['submitted', 'uploaded']);
            @endphp
            <div class="px-6 py-6 {{ $isRevision ? 'bg-rose-50/40' : '' }}" x-data="{ expanded: false }">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-violet-100 px-2.5 py-0.5 text-xs font-black text-violet-800">
                                {{ $tableCode }}
                            </span>
                            <span class="text-base font-bold text-slate-900">{{ $req->title }}</span>
                        </div>
                        @if ($req->description)
                            <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $req->description }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($lkps && $rowCount > 0)
                                <span class="ui-badge {{ $lkps->statusBadgeClass() }}">{{ $lkps->statusLabel() }}</span>
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                    {{ $rowCount }} baris data terisi
                                </span>
                                @if (isset($lkps->summary_data['total_sks']))
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        Total SKS: {{ $lkps->summary_data['total_sks'] }}
                                    </span>
                                @endif
                                <span class="text-xs text-slate-400">· Terakhir diupdate {{ $lkps->updated_at->diffForHumans() }}</span>
                            @else
                                <span class="ui-badge bg-slate-100 text-slate-700 ring-slate-500/15">Belum diisi</span>
                            @endif
                        </div>

                        {{-- Catatan Revisi dari Perti --}}
                        @if ($lkps && $lkps->validation_notes)
                            <div class="mt-3 rounded-xl border p-3 text-xs {{ $isRevision ? 'border-rose-200 bg-rose-50/90 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }}">
                                <p class="font-bold flex items-center gap-1.5 {{ $isRevision ? 'text-rose-800' : 'text-slate-700' }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                                    </svg>
                                    Catatan Review Perti:
                                    @if ($lkps->validated_at)
                                        <span class="text-[10px] opacity-75 font-normal">({{ $lkps->validated_at->translatedFormat('d M Y, H:i') }})</span>
                                    @endif
                                </p>
                                <p class="mt-1 leading-relaxed">{{ $lkps->validation_notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-2 self-start lg:shrink-0">
                        {{-- Tombol Pratinjau Layar Penuh --}}
                        @if ($lkps && $rowCount > 0)
                            <a href="{{ route('unit.lkps.viewer', $tableCode) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-violet-200 bg-violet-50/70 px-3 py-2 text-xs font-bold text-violet-700 shadow-sm transition hover:bg-violet-100 hover:border-violet-300"
                                title="Lihat tabel lebih lengkap dalam tampilan layar penuh">
                                <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                Pratinjau Penuh
                            </a>
                        @endif

                        {{-- Tombol Import Excel --}}
                        <button type="button"
                            onclick="openLkpsImportModal('{{ $tableCode }}', '{{ addslashes($req->title) }}')"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-100 hover:border-emerald-400">
                            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                            Import Excel
                        </button>

                        {{-- Tombol Edit/Buka di Web --}}
                        <button type="button"
                            @click="expanded = !expanded"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-violet-50 hover:text-violet-700 hover:border-violet-300">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <span x-text="expanded ? 'Tutup Editor Web' : 'Input / Edit di Web'"></span>
                        </button>

                        {{-- Tombol Unduh Excel --}}
                        @if ($lkps && $rowCount > 0)
                            <a href="{{ route('unit.lkps.export', $tableCode) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
                                title="Unduh data tabel dalam format Excel">
                                <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Interactive Web Table Editor Section (Accordion) --}}
                <div x-show="expanded" x-collapse style="display: none;" class="mt-6 border-t border-slate-200 pt-5">
                    @include('unit.submissions.tables._editor', [
                        'tableCode' => $tableCode,
                        'req' => $req,
                        'lkps' => $lkps,
                    ])
                </div>

                {{-- Preview Singkat jika tidak sedang expanded --}}
                @if ($lkps && $rowCount > 0)
                    <div x-show="!expanded" class="mt-4 rounded-xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-xs">
                        <div class="flex items-center justify-between text-slate-600 mb-2 font-medium">
                            <span>Pratinjau Data (menampilkan {{ min(3, $rowCount) }} dari {{ $rowCount }} baris):</span>
                            <button type="button" @click="expanded = true" class="text-violet-600 hover:text-violet-700 font-bold underline">
                                Lihat / Edit Semua Baris →
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[11px] text-slate-700">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500">
                                        @foreach (array_slice($lkps->dynamic_headers ?? (isset($rows[0]) ? array_keys($rows[0]) : []), 0, 6) as $h)
                                            <th class="py-1 px-2 font-semibold">{{ is_string($h) ? $h : 'Kolom' }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($rows, 0, 3) as $r)
                                        <tr class="border-b border-slate-100 last:border-0">
                                            @foreach (array_slice((array)$r, 0, 6) as $val)
                                                <td class="py-1.5 px-2">
                                                    @if (is_array($val))
                                                        <span class="text-slate-400">...</span>
                                                    @else
                                                        {{ \Illuminate\Support\Str::limit((string)$val, 25) }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

