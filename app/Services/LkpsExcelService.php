<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LkpsExcelService
{
    /**
     * Parse uploaded Excel file for a specific sheet.
     */
    public function parseSheet(UploadedFile $file, string $tableCode): array
    {
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());

        // Find matching sheet
        $sheet = $spreadsheet->getSheetByName($tableCode);
        if (!$sheet) {
            // Try partial match
            foreach ($spreadsheet->getSheetNames() as $name) {
                if (strcasecmp(trim($name), trim($tableCode)) === 0 || str_starts_with(trim($name), $tableCode)) {
                    $sheet = $spreadsheet->getSheetByName($name);
                    break;
                }
            }
        }

        if (!$sheet) {
            throw new \Exception("Sheet dengan nama/kode '{$tableCode}' tidak ditemukan di dalam file Excel.");
        }

        $allData = $sheet->toArray(null, true, true, true);

        return match ($tableCode) {
            '2.B.1' => $this->parseTable2B1($allData),
            '2.B.2' => $this->parseTable2B2($allData),
            '2.B.3' => $this->parseTable2B3($allData),
            '1.A.1' => $this->parseTable1A1($allData),
            '1.A.4' => $this->parseTable1A4($allData),
            '2.A.1' => $this->parseTable2A1($allData),
            '3.A.1' => $this->parseTable3A1($allData),
            '3.C.1' => $this->parseTable3C1($allData),
            '3.C.2' => $this->parseTable3C2($allData),
            '3.C.3' => $this->parseTable3C3($allData),
            '5.1'   => $this->parseTable51($allData),
            '5.2'   => $this->parseTable52($allData),
            '6'     => $this->parseTable6($allData),
            default => $this->parseGenericTable($allData, $tableCode),
        };
    }

    /**
     * Tabel 2.B.1: Isi Pembelajaran (MK, SKS, Semester, PL 1..n)
     */
    private function parseTable2B1(array $data): array
    {
        // Row 4 has PL labels: E4, F4, ...
        $plColumns = [];
        $colLetters = range('E', 'Z');
        foreach ($colLetters as $letter) {
            $val = trim((string)($data[4][$letter] ?? ''));
            if ($val !== '' && $val !== '…' && $val !== '...') {
                $plColumns[$letter] = $val;
            } elseif (str_contains($val, 'PL') || str_contains($val, 'pl')) {
                $plColumns[$letter] = $val;
            }
        }
        if (empty($plColumns)) {
            $plColumns = ['E' => 'PL 1', 'F' => 'PL 2', 'G' => 'PL 3', 'H' => 'PL 4'];
        }

        $rows = [];
        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $mk = trim((string)($row['B'] ?? ''));
            $sks = trim((string)($row['C'] ?? ''));
            $smt = trim((string)($row['D'] ?? ''));

            if ($mk === '' && $no === '' && $sks === '') {
                continue;
            }
            if ($no === '…' || $no === '...' || $no === 'n') {
                continue;
            }

            $plMap = [];
            foreach ($plColumns as $colKey => $plName) {
                $cellVal = trim((string)($row[$colKey] ?? ''));
                $plMap[$plName] = in_array(strtolower($cellVal), ['v', '1', 'x', 'ya', '√', 'true']) ? 1 : ($cellVal !== '' ? 1 : 0);
            }

            $rows[] = [
                'no' => $no ?: (count($rows) + 1),
                'mata_kuliah' => $mk,
                'sks' => is_numeric($sks) ? (int)$sks : $sks,
                'semester' => is_numeric($smt) ? (int)$smt : $smt,
                'profil_lulusan' => $plMap,
            ];
        }

        return [
            'dynamic_headers' => array_values($plColumns),
            'table_rows' => $rows,
            'summary_data' => [
                'total_mk' => count($rows),
                'total_sks' => array_sum(array_map('floatval', array_column($rows, 'sks'))),
            ],
        ];
    }

    /**
     * Tabel 2.B.2: Pemetaan CPL dan Profil Lulusan
     */
    private function parseTable2B2(array $data): array
    {
        // Row 3 has PL headers: B3, C3, D3..
        $plColumns = [];
        $colLetters = range('B', 'Z');
        foreach ($colLetters as $letter) {
            $val = trim((string)($data[3][$letter] ?? ''));
            if ($val !== '' && $val !== '…' && $val !== '...') {
                $plColumns[$letter] = $val;
            }
        }
        if (empty($plColumns)) {
            $plColumns = ['B' => 'PL 1', 'C' => 'PL 2', 'D' => 'PL 3', 'E' => 'PL 4'];
        }

        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $cpl = trim((string)($row['A'] ?? ''));
            if ($cpl === '' || $cpl === '…' || $cpl === '...' || $cpl === 'CPL n') {
                continue;
            }

            $plMap = [];
            foreach ($plColumns as $colKey => $plName) {
                $cellVal = trim((string)($row[$colKey] ?? ''));
                $plMap[$plName] = in_array(strtolower($cellVal), ['v', '1', 'x', 'ya', '√', 'true']) ? 1 : ($cellVal !== '' ? 1 : 0);
            }

            $rows[] = [
                'cpl' => $cpl,
                'profil_lulusan' => $plMap,
            ];
        }

        return [
            'dynamic_headers' => array_values($plColumns),
            'table_rows' => $rows,
            'summary_data' => [
                'total_cpl' => count($rows),
            ],
        ];
    }

    /**
     * Tabel 2.B.3: Peta Pemenuhan CPL (CPL, CPMK, Semester 1..8)
     */
    private function parseTable2B3(array $data): array
    {
        $rows = [];
        $currentCpl = '';
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $cpl = trim((string)($row['A'] ?? ''));
            $cpmk = trim((string)($row['B'] ?? ''));

            if ($cpl !== '') {
                $currentCpl = $cpl;
            }
            if ($currentCpl === '' && $cpmk === '') {
                continue;
            }
            if ($cpl === '…' || $cpl === '...') {
                continue;
            }

            $semesters = [];
            $semCols = ['C' => 1, 'D' => 2, 'E' => 3, 'F' => 4, 'G' => 5, 'H' => 6, 'I' => 7, 'J' => 8];
            foreach ($semCols as $letter => $semNum) {
                $semesters['semester_' . $semNum] = trim((string)($row[$letter] ?? ''));
            }

            $rows[] = [
                'cpl' => $currentCpl,
                'cpmk' => $cpmk,
                'semesters' => $semesters,
            ];
        }

        return [
            'dynamic_headers' => ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
            ],
        ];
    }

    /**
     * Tabel 1.A.1: Pimpinan dan Tupoksi
     */
    private function parseTable1A1(array $data): array
    {
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $unitKerja = trim((string)($row['A'] ?? ''));
            $namaKetua = trim((string)($row['B'] ?? ''));
            if ($unitKerja === '' && $namaKetua === '') {
                continue;
            }

            $rows[] = [
                'unit_kerja' => $unitKerja,
                'nama_ketua' => $namaKetua,
                'periode_jabatan' => trim((string)($row['C'] ?? '')),
                'pendidikan_terakhir' => trim((string)($row['D'] ?? '')),
                'jabatan_fungsional' => trim((string)($row['E'] ?? '')),
                'tupoksi' => trim((string)($row['F'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['Unit Kerja', 'Nama Ketua', 'Periode Jabatan', 'Pendidikan Terakhir', 'Jabatan Fungsional', 'Tugas Pokok & Fungsi'],
            'table_rows' => $rows,
            'summary_data' => ['total' => count($rows)],
        ];
    }

    /**
     * Tabel 1.A.4: EWMP Beban DTPR
     */
    private function parseTable1A4(array $data): array
    {
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $nama = trim((string)($row['B'] ?? ''));
            if ($nama === '' || str_contains(strtolower($no), 'jumlah') || str_contains(strtolower($no), 'rata')) {
                continue;
            }

            $rows[] = [
                'no' => $no ?: (count($rows) + 1),
                'nama_dtpr' => $nama,
                'sks_ps_sendiri' => (float)($row['C'] ?? 0),
                'sks_ps_lain_pt_sendiri' => (float)($row['D'] ?? 0),
                'sks_pt_lain' => (float)($row['E'] ?? 0),
                'sks_penelitian' => (float)($row['F'] ?? 0),
                'sks_pkm' => (float)($row['G'] ?? 0),
                'sks_manajemen_pt_sendiri' => (float)($row['H'] ?? 0),
                'sks_manajemen_pt_lain' => (float)($row['I'] ?? 0),
                'total_sks' => (float)($row['J'] ?? 0),
            ];
        }

        return [
            'dynamic_headers' => ['No', 'Nama DTPR', 'PS Sendiri', 'PS Lain PT Sendiri', 'PT Lain', 'Penelitian', 'PkM', 'Manajemen PT Sendiri', 'Manajemen PT Lain', 'Total SKS'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_dosen' => count($rows),
                'rata_rata_sks' => count($rows) > 0 ? round(array_sum(array_column($rows, 'total_sks')) / count($rows), 2) : 0,
            ],
        ];
    }

    /**
     * Tabel 2.A.1: Data Mahasiswa
     */
    private function parseTable2A1(array $data): array
    {
        $rows = [];
        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $ts = trim((string)($row['A'] ?? ''));
            if ($ts === '' || str_contains(strtolower($ts), 'jumlah')) {
                continue;
            }

            $rows[] = [
                'ts' => $ts,
                'daya_tampung' => (int)($row['B'] ?? 0),
                'calon_pendaftar' => (int)($row['C'] ?? 0),
                'calon_afirmasi' => (int)($row['D'] ?? 0),
                'calon_disabilitas' => (int)($row['E'] ?? 0),
                'baru_reguler_diterima' => (int)($row['F'] ?? 0),
                'baru_reguler_afirmasi' => (int)($row['G'] ?? 0),
                'baru_rpl_diterima' => (int)($row['I'] ?? 0),
                'aktif_reguler_diterima' => (int)($row['L'] ?? 0),
                'aktif_rpl_diterima' => (int)($row['O'] ?? 0),
            ];
        }

        return [
            'dynamic_headers' => ['TS', 'Daya Tampung', 'Pendaftar', 'Pendaftar Afirmasi', 'Pendaftar Khusus', 'Baru Reguler', 'Baru RPL', 'Aktif Reguler', 'Aktif RPL'],
            'table_rows' => $rows,
            'summary_data' => ['total_tahun' => count($rows)],
        ];
    }

    /**
     * Tabel 3.A.1: Sarana Prasarana Penelitian (Lab)
     */
    private function parseTable3A1(array $data): array
    {
        $rows = [];
        for ($r = 3; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $prasarana = trim((string)($row['A'] ?? ''));
            if ($prasarana === '' || str_starts_with($prasarana, 'Keterangan')) {
                continue;
            }

            $rows[] = [
                'nama_prasarana' => $prasarana,
                'daya_tampung' => trim((string)($row['B'] ?? '')),
                'luas_ruang' => trim((string)($row['C'] ?? '')),
                'kepemilikan' => trim((string)($row['D'] ?? '')),
                'lisensi' => trim((string)($row['E'] ?? '')),
                'perangkat' => trim((string)($row['F'] ?? '')),
                'link_bukti' => trim((string)($row['H'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total_sarpras' => count($rows)],
        ];
    }

    /**
     * Tabel 3.C.1: Kerjasama Penelitian
     */
    private function parseTable3C1(array $data): array
    {
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $judul = trim((string)($row['B'] ?? ''));
            if ($judul === '' || str_contains(strtolower($judul), 'jumlah')) {
                continue;
            }

            $rows[] = [
                'no' => trim((string)($row['A'] ?? (count($rows) + 1))),
                'judul' => $judul,
                'mitra' => trim((string)($row['C'] ?? '')),
                'tingkat' => trim((string)($row['D'] ?? '')),
                'durasi' => trim((string)($row['E'] ?? '')),
                'dana_ts2' => (float)($row['F'] ?? 0),
                'dana_ts1' => (float)($row['G'] ?? 0),
                'dana_ts' => (float)($row['H'] ?? 0),
                'link_bukti' => trim((string)($row['I'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['No', 'Judul Kerjasama', 'Mitra', 'Tingkat (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total_kerjasama' => count($rows)],
        ];
    }

    /**
     * Tabel 3.C.2: Publikasi Penelitian
     */
    private function parseTable3C2(array $data): array
    {
        $rows = [];
        for ($r = 3; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $nama = trim((string)($row['B'] ?? ''));
            $judul = trim((string)($row['C'] ?? ''));
            if ($nama === '' && $judul === '') {
                continue;
            }
            if (str_contains(strtolower($nama), 'jumlah')) {
                continue;
            }

            $rows[] = [
                'no' => trim((string)($row['A'] ?? (count($rows) + 1))),
                'nama_dtpr' => $nama,
                'judul_publikasi' => $judul,
                'jenis_publikasi' => trim((string)($row['D'] ?? '')),
                'ts2' => trim((string)($row['E'] ?? '')) ? 1 : 0,
                'ts1' => trim((string)($row['F'] ?? '')) ? 1 : 0,
                'ts' => trim((string)($row['G'] ?? '')) ? 1 : 0,
                'link_bukti' => trim((string)($row['H'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['No', 'Nama DTPR', 'Judul Publikasi', 'Jenis Publikasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total_publikasi' => count($rows)],
        ];
    }

    /**
     * Tabel 3.C.3: Perolehan HKI Granted
     */
    private function parseTable3C3(array $data): array
    {
        $rows = [];
        for ($r = 3; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $judul = trim((string)($row['B'] ?? ''));
            if ($judul === '' || str_contains(strtolower($judul), 'jumlah')) {
                continue;
            }

            $rows[] = [
                'no' => trim((string)($row['A'] ?? (count($rows) + 1))),
                'judul' => $judul,
                'jenis_hki' => trim((string)($row['C'] ?? '')),
                'nama_dtpr' => trim((string)($row['D'] ?? '')),
                'ts2' => trim((string)($row['E'] ?? '')) ? 1 : 0,
                'ts1' => trim((string)($row['F'] ?? '')) ? 1 : 0,
                'ts' => trim((string)($row['G'] ?? '')) ? 1 : 0,
                'link_bukti' => trim((string)($row['H'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['No', 'Judul HKI', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total_hki' => count($rows)],
        ];
    }

    /**
     * Tabel 5.1: Sistem Tata Kelola
     */
    private function parseTable51(array $data): array
    {
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $jenis = trim((string)($row['B'] ?? ''));
            if ($jenis === '' || $jenis === '…' || $jenis === '...') {
                continue;
            }

            $rows[] = [
                'no' => trim((string)($row['A'] ?? (count($rows) + 1))),
                'jenis_tata_kelola' => $jenis,
                'nama_sistem' => trim((string)($row['C'] ?? '')),
                'akses' => trim((string)($row['D'] ?? '')),
                'unit_pengelola' => trim((string)($row['E'] ?? '')),
                'link_bukti' => trim((string)($row['F'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['No', 'Jenis Tata Kelola', 'Nama Sistem Informasi', 'Akses (Lokal/Internet)', 'Unit Kerja Pengelola', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total' => count($rows)],
        ];
    }

    /**
     * Tabel 5.2: Sarpras Pendidikan
     */
    private function parseTable52(array $data): array
    {
        $rows = [];
        for ($r = 3; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $prasarana = trim((string)($row['A'] ?? ''));
            if ($prasarana === '' || str_starts_with($prasarana, 'Keterangan')) {
                continue;
            }

            $rows[] = [
                'nama_prasarana' => $prasarana,
                'daya_tampung' => trim((string)($row['B'] ?? '')),
                'luas_ruang' => trim((string)($row['C'] ?? '')),
                'kepemilikan' => trim((string)($row['D'] ?? '')),
                'lisensi' => trim((string)($row['E'] ?? '')),
                'perangkat' => trim((string)($row['F'] ?? '')),
                'link_bukti' => trim((string)($row['H'] ?? '')),
            ];
        }

        return [
            'dynamic_headers' => ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
            'table_rows' => $rows,
            'summary_data' => ['total' => count($rows)],
        ];
    }

    /**
     * Tabel 6: Kesesuaian Visi, Misi
     */
    private function parseTable6(array $data): array
    {
        return [
            'dynamic_headers' => ['Visi PT', 'Visi UPPS', 'Visi Keilmuan PS', 'Misi PT', 'Misi UPPS'],
            'table_rows' => [
                [
                    'visi_pt' => trim((string)($data[3]['A'] ?? '')),
                    'visi_upps' => trim((string)($data[3]['B'] ?? '')),
                    'visi_keilmuan_ps' => trim((string)($data[3]['C'] ?? '')),
                    'misi_pt' => trim((string)($data[5]['A'] ?? '')),
                    'misi_upps' => trim((string)($data[5]['B'] ?? '')),
                ]
            ],
            'summary_data' => [],
        ];
    }

    /**
     * Generic parser for any other sheets.
     */
    private function parseGenericTable(array $data, string $tableCode): array
    {
        $headerRow = 3;
        // Search first non-empty header row
        for ($r = 1; $r <= min(5, count($data)); $r++) {
            $nonEmpty = array_filter($data[$r] ?? [], fn($v) => $v !== null && trim((string)$v) !== '');
            if (count($nonEmpty) >= 3) {
                $headerRow = $r;
                break;
            }
        }

        $headers = [];
        foreach ($data[$headerRow] ?? [] as $col => $val) {
            $val = trim((string)$val);
            if ($val !== '') {
                $headers[$col] = $val;
            }
        }

        $rows = [];
        for ($r = $headerRow + 1; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $nonEmpty = array_filter($row, fn($v) => $v !== null && trim((string)$v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }
            $rowData = [];
            foreach ($headers as $col => $hName) {
                $rowData[$hName] = trim((string)($row[$col] ?? ''));
            }
            $rows[] = $rowData;
        }

        return [
            'dynamic_headers' => array_values($headers),
            'table_rows' => $rows,
            'summary_data' => ['total' => count($rows)],
        ];
    }

    /**
     * Export LKPS submission data to a clean Excel file.
     */
    public function exportToExcel(string $tableCode, string $tableName, array $headers, array $rows): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($tableCode);

        // Title
        $sheet->setCellValue('A1', "Tabel {$tableCode} {$tableName}");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        // Headers
        $colIdx = 1;
        foreach ($headers as $header) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx) . '3';
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true);
            $colIdx++;
        }

        // Rows
        $rowNum = 4;
        foreach ($rows as $row) {
            $colIdx = 1;
            foreach ($headers as $headerKey => $headerName) {
                $val = '';
                if (isset($row[$headerName])) {
                    $val = $row[$headerName];
                } elseif (isset($row[$headerKey])) {
                    $val = $row[$headerKey];
                } elseif (is_array($row)) {
                    // Try to match key
                    $val = array_values($row)[$colIdx - 1] ?? '';
                }
                if (is_array($val)) {
                    $val = json_encode($val);
                }

                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx) . $rowNum;
                $sheet->setCellValue($cellCoord, $val);
                $colIdx++;
            }
            $rowNum++;
        }

        // Auto size columns
        for ($i = 1; $i < $colIdx; $i++) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colString)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "LKPS_Tabel_{$tableCode}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
