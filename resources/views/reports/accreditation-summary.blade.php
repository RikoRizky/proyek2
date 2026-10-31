<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        h1 { font-size: 16px; margin-bottom: 2px; color: #0f172a; font-weight: bold; }
        .subtitle { font-size: 12px; font-weight: bold; color: #4f46e5; margin: 2px 0 6px 0; }
        .muted { color: #64748b; font-size: 9px; }
        
        .unit-header { margin-top: 15px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #e2e8f0; }
        .unit-title { font-size: 13px; font-weight: bold; color: #0f172a; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 8px; text-align: left; font-size: 9.5px; }
        th { background: #f8fafc; font-weight: bold; color: #334155; }

        /* Module Header Row */
        .module-row { background: #eef2ff; color: #3730a3; font-weight: bold; font-size: 10px; }
        .module-summary-text { font-size: 8.5px; color: #4338ca; font-weight: normal; float: right; }
        
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: bold; }
        .badge-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-revision { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
        .badge-uploaded { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-pending { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

        .notes-box { font-size: 8.5px; color: #991b1b; background: #fff1f2; padding: 3px 6px; border-radius: 4px; margin-top: 3px; border: 1px dashed #fecdd3; }
        
        .total-row { background: #f8fafc; font-weight: bold; font-size: 10px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <h1>Laporan Ringkasan & Validasi Unggahan Akreditasi</h1>
    @if(isset($pertiName))
        <p class="subtitle">{{ $pertiName }}</p>
    @endif
    <p class="muted">Dihasilkan pada: {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB</p>

    @foreach ($summaries as $block)
        @if (!$loop->first)
            <div class="page-break"></div>
        @endif

        <div class="unit-header">
            <span class="unit-title">{{ $block['user']->name }}</span>
            <span class="muted">({{ $block['user']->email }})</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 68%;">Kriteria & Persyaratan Dokumen</th>
                    <th style="width: 32%; text-align: center;">Status Validasi Perti</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($block['modules'] as $row)
                    <!-- Header Baris Kriteria Modul -->
                    <tr class="module-row">
                        <td colspan="2" style="padding: 6px 8px;">
                            <span>{{ $row['module']->name }}</span>
                            <span class="module-summary-text">
                                Terunggah: <strong>{{ $row['uploaded'] }}/{{ $row['total'] }}</strong> · 
                                Sesuai: <strong>{{ $row['approved'] }}</strong> · 
                                Revisi: <strong>{{ $row['revision'] }}</strong> · 
                                Progress: <strong>{{ $row['progressPercent'] }}%</strong>
                            </span>
                        </td>
                    </tr>

                    <!-- Baris Tiap Persyaratan / Dokumen -->
                    @foreach ($row['requirements'] as $req)
                        <tr>
                            <td style="padding-left: 16px;">
                                <span style="font-weight: 500; color: #334155;">{{ $req['title'] }}</span>
                                @if ($req['validation_notes'])
                                    <div class="notes-box">
                                        <strong>Catatan Revisi Perti:</strong> {{ $req['validation_notes'] }}
                                    </div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if ($req['status_key'] === 'approved')
                                    <span class="badge badge-approved">✓ Sesuai (Valid)</span>
                                @elseif ($req['status_key'] === 'revision')
                                    <span class="badge badge-revision">! Perlu Revisi</span>
                                @elseif ($req['status_key'] === 'uploaded')
                                    <span class="badge badge-uploaded">⏳ Menunggu Validasi</span>
                                @else
                                    <span class="badge badge-pending">⚪ Belum Diunggah</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Baris Total Overall Progress -->
                <tr class="total-row">
                    <td style="padding: 8px;">
                        TOTAL OVERALL PROGRESS PRODI: Terunggah {{ $block['uploadedCount'] }}/{{ $block['totalRequirements'] }} · Sesuai: {{ $block['approvedCount'] }} · Revisi: {{ $block['revisionCount'] }}
                    </td>
                    <td style="text-align: center; padding: 8px;">
                        <span style="color: #4f46e5;">{{ $block['progressPercent'] }}% Progress</span>
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>
