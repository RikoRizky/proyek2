@php
    $maxUploadMb = \App\Support\AccreditationUpload::maxUploadMb();
    $maxUploadBytes = \App\Support\AccreditationUpload::maxUploadBytes();
@endphp

@if ($errors->any())
    <div class="border-b border-red-100 bg-red-50/70 px-6 py-4 text-sm text-red-900">
        <p class="font-semibold flex items-center gap-1.5">
            <svg class="h-4.5 w-4.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
            Gagal menyimpan berkas. Periksa kembali isian Anda:
        </p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="divide-y divide-slate-100">
    @foreach ($module->requirements as $req)
        @php
            $latest = $req->submissions->first();
            $hasError = session('failed_requirement_id') == $req->id;
            $isRevision = $latest?->status === \App\Enums\SubmissionStatus::Revision;
            $isApproved = $latest?->status === \App\Enums\SubmissionStatus::Approved;
        @endphp
        <div class="grid gap-6 px-6 py-6 lg:grid-cols-3 lg:items-center {{ $isRevision ? 'bg-rose-50/40' : '' }}" data-requirement-row="{{ $req->id }}">
            <div class="min-w-0 lg:col-span-2">
                <p class="text-lg font-semibold text-slate-900">{{ $req->title }}</p>
                @if ($req->description)
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $req->description }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @if ($latest)
                        <span class="ui-badge {{ $latest->status->badgeClass() }}">{{ $latest->status->label() }}</span>
                        <span class="text-xs font-medium text-slate-500">Versi {{ $latest->version }}</span>
                        <a href="{{ route('unit.submissions.view', $latest) }}" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Lihat dokumen</a>
                        <span class="text-slate-300">·</span>
                        <a href="{{ route('unit.submissions.show', $latest) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-500">Riwayat</a>
                    @else
                        <span class="ui-badge bg-slate-100 text-slate-700 ring-slate-500/15">Menunggu unggah</span>
                    @endif
                </div>

                @if ($latest && $latest->validation_notes)
                    <div class="mt-3 rounded-xl border p-3 text-xs flex flex-wrap items-center justify-between gap-2 {{ $isRevision ? 'border-rose-200 bg-rose-50/80 text-rose-900' : 'border-slate-200 bg-slate-50 text-slate-800' }}">
                        <p class="font-bold flex items-center gap-1.5 {{ $isRevision ? 'text-rose-800' : 'text-slate-700' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                            </svg>
                            Catatan Revisi
                            @if ($latest->validated_at)
                                <span class="text-[10px] opacity-75 font-normal">({{ $latest->validated_at->translatedFormat('d M Y, H:i') }})</span>
                            @endif
                        </p>
                        <button type="button"
                            onclick='openDetailValidationModal({{ json_encode($req->title) }}, {{ json_encode($latest->validation_notes) }}, {{ json_encode($latest->validated_at ? $latest->validated_at->translatedFormat("d M Y, H:i") : null) }})'
                            class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-2.5 py-1 text-xs font-bold text-rose-800 hover:bg-rose-100/70 transition shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            Lihat Detail Catatan
                        </button>
                    </div>
                @endif
            </div>
            <div class="flex items-center justify-end">
                @php
                    $existingLinks = $latest->google_drive_links ?? [];
                    $existingLinksJson = json_encode($existingLinks);
                    $existingFiles = $latest->files ?? [];
                    $existingFilesJson = json_encode($existingFiles);
                    $valNotesJson = json_encode($latest->validation_notes ?? '');
                    $isExpired = auth()->user()->isSubscriptionExpired();
                @endphp
                
                @if ($latest && $isRevision)
                    @if ($isExpired)
                        <form method="POST" action="{{ route('unit.submissions.store', $req->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg px-4 py-2 text-sm font-bold text-white shadow-sm transition-all duration-150" style="background-color: #e11d48 !important; color: #ffffff !important;">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                Unggah Revisi Dokumen
                            </button>
                        </form>
                    @else
                        <button
                            type="button"
                            data-upload-btn="{{ $req->id }}"
                            onclick="openUploadModal('{{ $req->id }}', {{ $existingLinksJson }}, {{ $existingFilesJson }}, {{ $valNotesJson }})"
                            class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg px-4 py-2 text-sm font-bold text-white shadow-sm transition-all duration-150"
                            style="background-color: #e11d48 !important; color: #ffffff !important;"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            Unggah Revisi Dokumen
                        </button>
                    @endif
                @elseif ($latest)
                    @if ($isExpired)
                        <form method="POST" action="{{ route('unit.submissions.store', $req->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 transition-all duration-150">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                Perbarui Berkas
                            </button>
                        </form>
                    @else
                        <button
                            type="button"
                            data-upload-btn="{{ $req->id }}"
                            onclick="openUploadModal('{{ $req->id }}', {{ $existingLinksJson }}, {{ $existingFilesJson }}, {{ $valNotesJson }})"
                            class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 transition-all duration-150"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            Perbarui Berkas
                        </button>
                    @endif
                @else
                    @if ($isExpired)
                        <form method="POST" action="{{ route('unit.submissions.store', $req->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg px-4 py-2 text-sm font-bold text-white shadow-sm transition-all duration-150" style="background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                Unggah Berkas
                            </button>
                        </form>
                    @else
                        <button
                            type="button"
                            data-upload-btn="{{ $req->id }}"
                            onclick="openUploadModal('{{ $req->id }}')"
                            class="inline-flex shrink-0 items-center gap-2 self-center rounded-lg px-4 py-2 text-sm font-bold text-white shadow-sm transition-all duration-150"
                            style="background:linear-gradient(135deg,#7c3aed,#6d28d9)"
                            onmouseover="this.style.background='linear-gradient(135deg,#6d28d9,#5b21b6)';this.style.boxShadow='0 3px 10px rgba(109,40,217,0.3)'"
                            onmouseout="this.style.background='linear-gradient(135deg,#7c3aed,#6d28d9)';this.style.boxShadow=''"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            Unggah Berkas
                        </button>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
</div>
