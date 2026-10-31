<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Riwayat</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $submission->requirement->title }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $submission->requirement->module->name }}</p>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('unit.submissions.view', $submission) }}" class="ui-btn-primary">Lihat di aplikasi</a>
        @if ($submission->file_path && (empty($submission->files) || count($submission->files) === 1))
            <a href="{{ route('unit.submissions.download', $submission) }}" class="ui-btn-secondary">Unduh versi ini</a>
        @endif
        <a href="{{ route('unit.submissions.module', $submission->requirement->module) }}" class="ui-btn-secondary">Kembali</a>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Versi</th>
                    <th>Status</th>
                    <th>Berkas & Link</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($history as $row)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $row->version }}</td>
                        <td><span class="ui-badge {{ $row->status->badgeClass() }}">{{ $row->status->label() }}</span></td>
                        <td>
                            <div class="space-y-1.5 py-1 text-xs text-slate-600 max-w-md">
                                @if (!empty($row->google_drive_links))
                                    <div class="font-semibold text-violet-700">Link Google Drive:</div>
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach ($row->google_drive_links as $link)
                                            <li>
                                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="hover:underline text-violet-600 font-semibold inline-flex items-center gap-0.5">
                                                    {{ $link['name'] }}
                                                    <svg class="h-3 w-3 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($row->files))
                                    <div class="font-semibold text-slate-700 @if(!empty($row->google_drive_links)) mt-2 @endif">Berkas Dokumen:</div>
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach ($row->files as $index => $file)
                                            <li>
                                                <a href="{{ route('unit.submissions.view', [$row, 'file' => $index]) }}" class="hover:underline text-slate-800 font-medium">{{ $file['original_filename'] }}</a>
                                                <span class="text-slate-400">({{ number_format($file['file_size'] / 1024, 1) }} KB)</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif ($row->original_filename)
                                    <div class="font-semibold text-slate-700 @if(!empty($row->google_drive_links)) mt-2 @endif">Berkas Dokumen:</div>
                                    <ul class="list-disc pl-4">
                                        <li>
                                            <a href="{{ route('unit.submissions.view', $row) }}" class="hover:underline text-slate-800 font-medium">{{ $row->original_filename }}</a>
                                            <span class="text-slate-400">({{ number_format($row->file_size / 1024, 1) }} KB)</span>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </td>
                        <td class="text-right space-x-3">
                            <a href="{{ route('unit.submissions.view', $row) }}" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
