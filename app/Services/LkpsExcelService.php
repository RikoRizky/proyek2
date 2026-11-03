<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LkpsExcelService
{
    /**
     * Centralized Schema Registry for official LAM INFOKOM Tables with multi-level merged headers.
     */
    public static function getTableSchema(string $tableCode): ?array
    {
        $schemas = [
            '1.A.1' => [
                'leaf_headers' => ['Unit Kerja', 'Nama Ketua', 'Periode Jabatan', 'Pendidikan Terakhir', 'Jabatan Fungsional', 'Tugas Pokok & Fungsi'],
                'has_total' => false,
                'note' => 'Data yang ditulis dalam tabel ini termasuk Unit Penjaminan Mutu.',
                'default_rows' => [
                    ['cells' => ['', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '']],
                ],
            ],
            '1.A.2' => [
                'leaf_headers' => ['Sumber Pendanaan', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3],
                'default_rows' => [
                    ['cells' => ['Mahasiswa (SPP)', '0', '0', '0', '']],
                    ['cells' => ['Yayasan', '0', '0', '0', '']],
                    ['cells' => ['PTN/PTS Lain', '0', '0', '0', '']],
                    ['cells' => ['Pemerintah (Pusat/Daerah)', '0', '0', '0', '']],
                    ['cells' => ['Industri/Masyarakat', '0', '0', '0', '']],
                    ['cells' => ['Lainnya', '0', '0', '0', '']],
                ],
            ],
            '1.A.3' => [
                'leaf_headers' => ['Penggunaan Dana', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3],
                'default_rows' => [
                    ['cells' => ['Pendidikan', '0', '0', '0', '']],
                    ['cells' => ['Penelitian', '0', '0', '0', '']],
                    ['cells' => ['PKM', '0', '0', '0', '']],
                    ['cells' => ['Investasi Prasarana', '0', '0', '0', '']],
                    ['cells' => ['Investasi Sarana', '0', '0', '0', '']],
                    ['cells' => ['Investasi SDM', '0', '0', '0', '']],
                    ['cells' => ['Lainnya', '0', '0', '0', '']],
                ],
            ],
            '1.A.4' => [
                'header_rows' => [
                    [
                        ['label' => 'No.', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Nama DTPR', 'rowspan' => 2, 'colIdx' => 1, 'width' => '220px'],
                        ['label' => 'SKS Pengajaran¹) Pada', 'colspan' => 3, 'colIdx' => 2],
                        ['label' => 'SKS Penelitian²)', 'rowspan' => 2, 'colIdx' => 5, 'width' => '130px'],
                        ['label' => 'SKS Pengabdian kepada Masyarakat²)', 'rowspan' => 2, 'colIdx' => 6, 'width' => '150px'],
                        ['label' => 'SKS Manajemen³)', 'colspan' => 2, 'colIdx' => 7],
                        ['label' => 'Total SKS', 'rowspan' => 2, 'colIdx' => 9, 'width' => '110px'],
                    ],
                    [
                        ['label' => 'PS Sendiri', 'colIdx' => 2, 'width' => '110px'],
                        ['label' => 'PS Lain, PT Sendiri', 'colIdx' => 3, 'width' => '140px'],
                        ['label' => 'PT Lain', 'colIdx' => 4, 'width' => '110px'],
                        ['label' => 'PT Sendiri', 'colIdx' => 7, 'width' => '110px'],
                        ['label' => 'PT Lain', 'colIdx' => 8, 'width' => '110px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Nama DTPR', 'SKS Pengajaran (PS Sendiri)', 'SKS Pengajaran (PS Lain PT Sendiri)', 'SKS Pengajaran (PT Lain)', 'SKS Penelitian', 'SKS Pengabdian (PkM)', 'SKS Manajemen (PT Sendiri)', 'SKS Manajemen (PT Lain)', 'Total SKS'],
                'has_total' => false,
                'total_label_col' => 1,
                'sum_cols' => [2, 3, 4, 5, 6, 7, 8, 9],
                'note' => '1) SKS pengajaran sama dengan sks mata kuliah yang diajarkan. 2) SKS penelitian dan pengabdian kepada masyarakat dihitung sesuai dengan peraturan yang berlaku di PT. 3) SKS manajemen dihitung sesuai jabatan.',
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:E2', 'F2:F3', 'G2:G3', 'H2:I2', 'J2:J3'],
            ],
            '1.A.5' => [
                'header_rows' => [
                    [
                        ['label' => 'No.', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Jenis Tenaga Kependidikan', 'rowspan' => 2, 'colIdx' => 1, 'width' => '220px'],
                        ['label' => 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', 'colspan' => 8, 'colIdx' => 2],
                        ['label' => 'Unit Kerja', 'rowspan' => 2, 'colIdx' => 10, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'S3', 'colIdx' => 2, 'width' => '75px'],
                        ['label' => 'S2', 'colIdx' => 3, 'width' => '75px'],
                        ['label' => 'S1', 'colIdx' => 4, 'width' => '75px'],
                        ['label' => 'D4', 'colIdx' => 5, 'width' => '75px'],
                        ['label' => 'D3', 'colIdx' => 6, 'width' => '75px'],
                        ['label' => 'D2', 'colIdx' => 7, 'width' => '75px'],
                        ['label' => 'D1', 'colIdx' => 8, 'width' => '75px'],
                        ['label' => 'SMA/SMK/MA', 'colIdx' => 9, 'width' => '110px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'],
                'default_rows' => [
                    ['cells' => ['1.', 'Pustakawan *)', '0', '0', '0', '0', '0', '0', '0', '0', '']],
                    ['cells' => ['2.', 'Laboran/Teknisi', '0', '0', '0', '0', '0', '0', '0', '0', '']],
                    ['cells' => ['3.', 'Administrasi', '0', '0', '0', '0', '0', '0', '0', '0', '']],
                    ['cells' => ['4.', 'Lainnya', '0', '0', '0', '0', '0', '0', '0', '0', '']],
                ],
                'has_total' => false,
                'total_label_col' => 1,
                'sum_cols' => [2, 3, 4, 5, 6, 7, 8, 9],
                'note' => '*) Pustakawan adalah staf perpustakaan yang memiliki ijazah atau sertifikat kompetensi pada bidang ilmu perpustakaan.',
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:J2', 'K2:K3'],
            ],
            '1.B' => [
                'header_rows' => [
                    [
                        ['label' => 'Unit SPMI', 'rowspan' => 2, 'colIdx' => 0, 'width' => '120px'],
                        ['label' => 'Nama Unit SPMI', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Dokumen SPMI', 'rowspan' => 2, 'colIdx' => 2, 'width' => '220px'],
                        ['label' => 'Jumlah Auditor Mutu Internal', 'colspan' => 2, 'colIdx' => 3],
                        ['label' => 'Frekuensi audit/monev per tahun', 'rowspan' => 2, 'colIdx' => 5, 'width' => '160px'],
                        ['label' => 'Bukti Certified Auditor', 'rowspan' => 2, 'colIdx' => 6, 'width' => '180px'],
                        ['label' => 'Laporan Audit', 'rowspan' => 2, 'colIdx' => 7, 'width' => '180px'],
                    ],
                    [
                        ['label' => 'Certified', 'colIdx' => 3, 'width' => '110px'],
                        ['label' => 'Non Certified', 'colIdx' => 4, 'width' => '130px'],
                    ],
                ],
                'leaf_headers' => ['Unit SPMI', 'Nama Unit SPMI', 'Dokumen SPMI', 'Auditor Certified', 'Auditor Non-Certified', 'Frekuensi Audit/Tahun', 'Bukti Certified', 'Laporan Audit'],
                'default_rows' => [
                    ['cells' => ['PT', '', '', '', '', '', '', '']],
                    ['cells' => ['UPPS', '', '', '', '', '', '', '']],
                ],
                'merges_excel' => ['A3:A4', 'B3:B4', 'C3:C4', 'D3:E3', 'F3:F4', 'G3:G4', 'H3:H4'],
            ],
            '2.A.1' => [
                'header_rows' => [
                    [
                        ['label' => 'TS', 'rowspan' => 2, 'colIdx' => 0, 'width' => '90px'],
                        ['label' => 'Daya Tampung', 'rowspan' => 2, 'colIdx' => 1, 'width' => '120px'],
                        ['label' => 'Jumlah Calon Mahasiswa', 'colspan' => 3, 'colIdx' => 2],
                        ['label' => 'Jumlah Mahasiswa Baru', 'colspan' => 2, 'colIdx' => 5],
                        ['label' => 'Jumlah Mahasiswa Aktif', 'colspan' => 2, 'colIdx' => 7],
                    ],
                    [
                        ['label' => 'Pendaftar', 'colIdx' => 2, 'width' => '110px'],
                        ['label' => 'Afirmasi', 'colIdx' => 3, 'width' => '110px'],
                        ['label' => 'Kebutuhan Khusus', 'colIdx' => 4, 'width' => '140px'],
                        ['label' => 'Reguler', 'colIdx' => 5, 'width' => '110px'],
                        ['label' => 'RPL', 'colIdx' => 6, 'width' => '110px'],
                        ['label' => 'Reguler', 'colIdx' => 7, 'width' => '110px'],
                        ['label' => 'RPL', 'colIdx' => 8, 'width' => '110px'],
                    ],
                ],
                'leaf_headers' => ['TS', 'Daya Tampung', 'Pendaftar', 'Afirmasi', 'Kebutuhan Khusus', 'Mhs Baru Reguler', 'Mhs Baru RPL', 'Mhs Aktif Reguler', 'Mhs Aktif RPL'],
                'default_rows' => [
                    ['cells' => ['TS-3', '', '', '', '', '', '', '', '']],
                    ['cells' => ['TS-2', '', '', '', '', '', '', '', '']],
                    ['cells' => ['TS-1', '', '', '', '', '', '', '', '']],
                    ['cells' => ['TS', '', '', '', '', '', '', '', '']],
                ],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            '2.B.5' => [
                'header_rows' => [
                    [
                        ['label' => 'Tahun Lulus', 'rowspan' => 2, 'colIdx' => 0, 'width' => '110px'],
                        ['label' => 'Jumlah Lulusan', 'rowspan' => 2, 'colIdx' => 1, 'width' => '120px'],
                        ['label' => 'Jumlah Lulusan yang Terlacak', 'rowspan' => 2, 'colIdx' => 2, 'width' => '160px'],
                        ['label' => 'Profesi Kerja Bidang Infokom', 'rowspan' => 2, 'colIdx' => 3, 'width' => '160px'],
                        ['label' => 'Profesi Kerja Bidang NON Infokom', 'rowspan' => 2, 'colIdx' => 4, 'width' => '170px'],
                        ['label' => 'Lingkup Tempat Kerja', 'colspan' => 3, 'colIdx' => 5],
                    ],
                    [
                        ['label' => 'Multinasional/Internasional', 'colIdx' => 5, 'width' => '180px'],
                        ['label' => 'Nasional', 'colIdx' => 6, 'width' => '120px'],
                        ['label' => 'Wirausaha', 'colIdx' => 7, 'width' => '120px'],
                    ],
                ],
                'leaf_headers' => ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Profesi Infokom', 'Profesi Non-Infokom', 'Multinasional', 'Nasional', 'Wirausaha'],
                'default_rows' => [
                    ['cells' => ['TS-2', '', '', '', '', '', '', '']],
                    ['cells' => ['TS-1', '', '', '', '', '', '', '']],
                    ['cells' => ['TS', '', '', '', '', '', '', '']],
                ],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3, 4, 5, 6, 7],
                'merges_excel' => ['A3:A4', 'B3:B4', 'C3:C4', 'D3:D4', 'E3:E4', 'F3:H3'],
            ],
            '2.B.6' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Jenis Kemampuan', 'rowspan' => 2, 'colIdx' => 1, 'width' => '260px'],
                        ['label' => 'Tingkat Kepuasan Pengguna (%)', 'colspan' => 4, 'colIdx' => 2],
                        ['label' => 'Rencana Tindak Lanjut oleh UPPS/PS', 'rowspan' => 2, 'colIdx' => 6, 'width' => '260px'],
                    ],
                    [
                        ['label' => 'Sangat Baik', 'colIdx' => 2, 'width' => '110px'],
                        ['label' => 'Baik', 'colIdx' => 3, 'width' => '110px'],
                        ['label' => 'Cukup', 'colIdx' => 4, 'width' => '110px'],
                        ['label' => 'Kurang', 'colIdx' => 5, 'width' => '110px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Jenis Kemampuan', 'Sangat Baik (%)', 'Baik (%)', 'Cukup (%)', 'Kurang (%)', 'Rencana Tindak Lanjut'],
                'default_rows' => [
                    ['cells' => ['1', 'Kerjasama Tim', '', '', '', '', '']],
                    ['cells' => ['2', 'Keahlian di Bidang Prodi', '', '', '', '', '']],
                    ['cells' => ['3', 'Kemampuan Berbahasa Asing (Inggris)', '', '', '', '', '']],
                    ['cells' => ['4', 'Kemampuan Berkomunikasi', '', '', '', '', '']],
                    ['cells' => ['5', 'Pengembangan Diri', '', '', '', '', '']],
                    ['cells' => ['6', 'Kepemimpinan', '', '', '', '', '']],
                    ['cells' => ['7', 'Etos Kerja', '', '', '', '', '']],
                ],
                'has_total' => false,
                'total_label_col' => 1,
                'sum_cols' => [2, 3, 4, 5],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:F2', 'G2:G3'],
            ],
            '3.A.2' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Nama DTPR (Ketua)', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Judul Penelitian', 'rowspan' => 2, 'colIdx' => 2, 'width' => '260px'],
                        ['label' => 'Jumlah Mhs Terlibat', 'rowspan' => 2, 'colIdx' => 3, 'width' => '140px'],
                        ['label' => 'Jenis Hibah', 'rowspan' => 2, 'colIdx' => 4, 'width' => '150px'],
                        ['label' => 'Sumber (L/N/I)', 'rowspan' => 2, 'colIdx' => 5, 'width' => '120px'],
                        ['label' => 'Durasi (Tahun)', 'rowspan' => 2, 'colIdx' => 6, 'width' => '110px'],
                        ['label' => 'Pendanaan (Rp Juta)', 'colspan' => 3, 'colIdx' => 7],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 10, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 7, 'width' => '100px'],
                        ['label' => 'TS-1', 'colIdx' => 8, 'width' => '100px'],
                        ['label' => 'TS', 'colIdx' => 9, 'width' => '100px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Nama DTPR (Ketua)', 'Judul Penelitian', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 2,
                'sum_cols' => [3, 6, 7, 8, 9],
                'merges_excel' => ['A3:A4', 'B3:B4', 'C3:C4', 'D3:D4', 'E3:E4', 'G3:G4', 'H3:J3', 'K3:K4'],
            ],
            '4.A.2' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Nama DTPR (Ketua PkM)', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Judul PkM', 'rowspan' => 2, 'colIdx' => 2, 'width' => '260px'],
                        ['label' => 'Jumlah Mhs Terlibat', 'rowspan' => 2, 'colIdx' => 3, 'width' => '140px'],
                        ['label' => 'Jenis Hibah', 'rowspan' => 2, 'colIdx' => 4, 'width' => '150px'],
                        ['label' => 'Sumber (L/N/I)', 'rowspan' => 2, 'colIdx' => 5, 'width' => '120px'],
                        ['label' => 'Durasi (Tahun)', 'rowspan' => 2, 'colIdx' => 6, 'width' => '110px'],
                        ['label' => 'Pendanaan (Rp Juta)', 'colspan' => 3, 'colIdx' => 7],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 10, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 7, 'width' => '100px'],
                        ['label' => 'TS-1', 'colIdx' => 8, 'width' => '100px'],
                        ['label' => 'TS', 'colIdx' => 9, 'width' => '100px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Nama DTPR (Ketua PkM)', 'Judul PkM', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 2,
                'sum_cols' => [3, 6, 7, 8, 9],
                'merges_excel' => ['A3:A4', 'B3:B4', 'C3:C4', 'D3:D4', 'E3:E4', 'G3:G4', 'H3:J3', 'K3:K4'],
            ],
            '3.C.1' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Judul Kerjasama', 'rowspan' => 2, 'colIdx' => 1, 'width' => '220px'],
                        ['label' => 'Mitra Kerja Sama', 'rowspan' => 2, 'colIdx' => 2, 'width' => '200px'],
                        ['label' => 'Sumber L/N/I', 'rowspan' => 2, 'colIdx' => 3, 'width' => '120px'],
                        ['label' => 'Durasi (Tahun)', 'rowspan' => 2, 'colIdx' => 4, 'width' => '110px'],
                        ['label' => 'Pendanaan (Rp Juta)', 'colspan' => 3, 'colIdx' => 5],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 8, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 5, 'width' => '100px'],
                        ['label' => 'TS-1', 'colIdx' => 6, 'width' => '100px'],
                        ['label' => 'TS', 'colIdx' => 7, 'width' => '100px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Judul Kerjasama', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 1,
                'sum_cols' => [4, 5, 6, 7],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'D2:D3', 'E2:E3', 'F2:H2', 'I2:I3'],
            ],
            '4.C.1' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Judul Kerjasama PkM', 'rowspan' => 2, 'colIdx' => 1, 'width' => '220px'],
                        ['label' => 'Mitra Kerja Sama', 'rowspan' => 2, 'colIdx' => 2, 'width' => '200px'],
                        ['label' => 'Sumber (L/N/I)', 'rowspan' => 2, 'colIdx' => 3, 'width' => '120px'],
                        ['label' => 'Durasi (Tahun)', 'rowspan' => 2, 'colIdx' => 4, 'width' => '110px'],
                        ['label' => 'Pendanaan (Rp Juta)', 'colspan' => 3, 'colIdx' => 5],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 8, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 5, 'width' => '100px'],
                        ['label' => 'TS-1', 'colIdx' => 6, 'width' => '100px'],
                        ['label' => 'TS', 'colIdx' => 7, 'width' => '100px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Judul Kerjasama PkM', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 1,
                'sum_cols' => [4, 5, 6, 7],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'E2:E3', 'F2:H2', 'I2:I3'],
            ],
            '3.C.2' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Nama DTPR', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Judul Publikasi', 'rowspan' => 2, 'colIdx' => 2, 'width' => '260px'],
                        ['label' => 'Jenis Publikasi', 'rowspan' => 2, 'colIdx' => 3, 'width' => '150px'],
                        ['label' => 'Tahun Terbit (Beri tanda √)', 'colspan' => 3, 'colIdx' => 4],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 7, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 4, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 5, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 6, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Nama DTPR', 'Judul Publikasi', 'Jenis Publikasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'D2:D3', 'E2:G2', 'H2:H3'],
            ],
            '4.C.2' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Nama DTPR (Ketua)', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Judul', 'rowspan' => 2, 'colIdx' => 2, 'width' => '260px'],
                        ['label' => 'Diseminasi (L/N/I)', 'rowspan' => 2, 'colIdx' => 3, 'width' => '150px'],
                        ['label' => 'Tahun Terbit', 'colspan' => 3, 'colIdx' => 4],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 7, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 4, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 5, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 6, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Nama DTPR (Ketua)', 'Judul', 'Diseminasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'D2:D3', 'H2:H3'],
            ],
            '3.C.3' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Judul', 'rowspan' => 2, 'colIdx' => 1, 'width' => '240px'],
                        ['label' => 'Jenis HKI', 'rowspan' => 2, 'colIdx' => 2, 'width' => '150px'],
                        ['label' => 'Nama DTPR', 'rowspan' => 2, 'colIdx' => 3, 'width' => '200px'],
                        ['label' => 'Tahun Perolehan (Beri Tanda √)', 'colspan' => 3, 'colIdx' => 4],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 7, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 4, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 5, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 6, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Judul', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'D2:D3', 'E2:G2', 'H2:H3'],
            ],
            '4.C.3' => [
                'header_rows' => [
                    [
                        ['label' => 'No', 'rowspan' => 2, 'colIdx' => 0, 'width' => '50px'],
                        ['label' => 'Judul PkM', 'rowspan' => 2, 'colIdx' => 1, 'width' => '240px'],
                        ['label' => 'Jenis HKI', 'rowspan' => 2, 'colIdx' => 2, 'width' => '150px'],
                        ['label' => 'Nama DTPR', 'rowspan' => 2, 'colIdx' => 3, 'width' => '200px'],
                        ['label' => 'Tahun Perolehan (Beri Tanda √)', 'colspan' => 3, 'colIdx' => 4],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 7, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 4, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 5, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 6, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['No', 'Judul PkM', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'merges_excel' => ['A2:A3', 'B2:B3', 'C2:C3', 'D2:D3', 'E2:G2', 'H2:H3'],
            ],
            '2.A.2' => [
                'header_rows' => [
                    [
                        ['label' => 'Asal Mahasiswa', 'rowspan' => 2, 'colIdx' => 0, 'width' => '220px'],
                        ['label' => 'Jumlah Mahasiswa Baru', 'colspan' => 4, 'colIdx' => 1],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 1, 'width' => '100px'],
                        ['label' => 'TS-1', 'colIdx' => 2, 'width' => '100px'],
                        ['label' => 'TS', 'colIdx' => 3, 'width' => '100px'],
                        ['label' => 'Link Bukti', 'colIdx' => 4, 'width' => '200px'],
                    ],
                ],
                'leaf_headers' => ['Asal Mahasiswa', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3],
                'default_rows' => [
                    ['cells' => ['Kota/Kab sama dengan PS', '0', '0', '0', '']],
                    ['cells' => ['Kota/Kabupaten Lain', '0', '0', '0', '']],
                    ['cells' => ['Provinsi Lain', '0', '0', '0', '']],
                    ['cells' => ['Negara Lain', '0', '0', '0', '']],
                ],
            ],
            '2.A.3' => [
                'leaf_headers' => ['Kategori Mahasiswa Asing', 'TS-2', 'TS-1', 'TS', 'Jumlah'],
                'has_total' => false,
                'total_label_col' => 0,
                'sum_cols' => [1, 2, 3, 4],
                'default_rows' => [
                    ['cells' => ['Mahasiswa Baru', '0', '0', '0', '0']],
                    ['cells' => ['Mahasiswa Aktif pada saat TS', '0', '0', '0', '0']],
                    ['cells' => ['Lulus pada saat TS', '0', '0', '0', '0']],
                    ['cells' => ['Mengundurkan Diri/DO pada saat TS', '0', '0', '0', '0']],
                ],
            ],
            '2.B.4' => [
                'leaf_headers' => ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Rata-rata Waktu Tunggu (Bulan)'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['TS-2', '0', '0', '0']],
                    ['cells' => ['TS-1', '0', '0', '0']],
                    ['cells' => ['TS', '0', '0', '0']],
                ],
            ],
            '2.C' => [
                'leaf_headers' => ['Tahun Akademik / Bentuk Pembelajaran', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['Jumlah Mahasiswa Aktif', '', '', '', '']],
                    ['cells' => ['Micro-credensial', '', '', '', '']],
                    ['cells' => ['RPL tipe A-2', '', '', '', '']],
                    ['cells' => ['Pembelajaran di PS lain', '', '', '', '']],
                    ['cells' => ['Magang/Praktik Kerja', '', '', '', '']],
                    ['cells' => ['Lainnya', '', '', '', '']],
                ],
            ],
            '2.D' => [
                'header_rows' => [
                    [
                        ['label' => 'Sumber Rekognisi', 'rowspan' => 2, 'colIdx' => 0, 'width' => '180px'],
                        ['label' => 'Jenis Pengakuan Lulusan (Rekognisi)', 'rowspan' => 2, 'colIdx' => 1, 'width' => '240px'],
                        ['label' => 'Tahun Akademik', 'colspan' => 3, 'colIdx' => 2],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 5, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 2, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 3, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 4, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['Sumber Rekognisi', 'Jenis Pengakuan (Rekognisi)', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['Masyarakat', '', '', '', '', '']],
                    ['cells' => ['Dunia Usaha', '', '', '', '', '']],
                    ['cells' => ['Dunia Industri', '', '', '', '', '']],
                    ['cells' => ['Dunia Kerja', '', '', '', '', '']],
                ],
            ],
            '3.A.1' => [
                'leaf_headers' => ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
                'has_total' => false,
                'note' => '● Prasarana diisi nama laboratorium. ● Perangkat yang dimiliki diisi perangkat keras, perangkat lunak, bandwidth, device, tool dan lain-lain.',
                'default_rows' => [
                    ['cells' => ['', '', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '', '']],
                ],
            ],
            '3.A.3' => [
                'header_rows' => [
                    [
                        ['label' => 'Jenis Pengembangan DTPR', 'rowspan' => 2, 'colIdx' => 0, 'width' => '220px'],
                        ['label' => 'Nama DTPR', 'rowspan' => 2, 'colIdx' => 1, 'width' => '200px'],
                        ['label' => 'Jumlah', 'colspan' => 3, 'colIdx' => 2],
                        ['label' => 'Link Bukti', 'rowspan' => 2, 'colIdx' => 5, 'width' => '200px'],
                    ],
                    [
                        ['label' => 'TS-2', 'colIdx' => 2, 'width' => '90px'],
                        ['label' => 'TS-1', 'colIdx' => 3, 'width' => '90px'],
                        ['label' => 'TS', 'colIdx' => 4, 'width' => '90px'],
                    ],
                ],
                'leaf_headers' => ['Jenis Pengembangan DTPR', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['', '', '', '', '', '']],
                ],
            ],
            '4.A.1' => [
                'leaf_headers' => ['Nama Prasarana PkM', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
                'has_total' => false,
                'note' => '● Prasarana diisi nama laboratorium, nama bengkel dan lain-lain.',
                'default_rows' => [
                    ['cells' => ['', '', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '', '']],
                ],
            ],
            '5.1' => [
                'leaf_headers' => ['No', 'Jenis Tata Kelola', 'Nama Sistem Informasi', 'Akses (Lokal/Internet)', 'Unit Pengelola', 'Link Bukti'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['1', 'Pendidikan', '', '', '', '']],
                    ['cells' => ['2', 'Keuangan', '', '', '', '']],
                    ['cells' => ['3', 'SDM', '', '', '', '']],
                    ['cells' => ['4', 'Sarana Prasarana', '', '', '', '']],
                    ['cells' => ['5', 'Sistem Penjaminan Mutu', '', '', '', '']],
                    ['cells' => ['6', 'Kerjasama', '', '', '', '']],
                    ['cells' => ['7', 'Kemahasiswaan', '', '', '', '']],
                ],
            ],
            '5.2' => [
                'leaf_headers' => ['Nama Prasarana Pendidikan', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['', '', '', '', '', '', '']],
                    ['cells' => ['', '', '', '', '', '', '']],
                ],
            ],
            '6' => [
                'leaf_headers' => ['Visi PT', 'Visi UPPS', 'Visi Keilmuan PS', 'Misi PT', 'Misi UPPS'],
                'has_total' => false,
                'default_rows' => [
                    ['cells' => ['', '', '', '', '']],
                ],
            ],
        ];

        return $schemas[$tableCode] ?? null;
    }

    /**
     * Parse uploaded Excel file for a specific sheet.
     */
    public function parseSheet(UploadedFile $file, string $tableCode): array
    {
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(false); // Enable merge cells detection
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
            '1.A.4' => $this->parseTable1A4($allData, $sheet),
            '1.A.5' => $this->parseTable1A5($allData, $sheet),
            '1.B'   => $this->parseTable1B($allData, $sheet),
            '2.A.1' => $this->parseTable2A1($allData, $sheet),
            '2.B.5' => $this->parseTable2B5($allData, $sheet),
            '2.B.6' => $this->parseTable2B6($allData, $sheet),
            '3.A.1' => $this->parseTable3A1($allData),
            '3.A.2', '4.A.2' => $this->parseTable3A2($allData, $sheet, $tableCode),
            '3.C.1', '4.C.1' => $this->parseTable3C1($allData, $sheet, $tableCode),
            '3.C.2', '4.C.2' => $this->parseTable3C2($allData, $sheet, $tableCode),
            '3.C.3', '4.C.3' => $this->parseTable3C3($allData, $sheet, $tableCode),
            '5.1'   => $this->parseTable51($allData),
            '5.2'   => $this->parseTable52($allData),
            '6'     => $this->parseTable6($allData),
            default => $this->parseGenericTable($allData, $tableCode, $sheet),
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
        $schema = self::getTableSchema('1.A.1');
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $unitKerja = trim((string)($row['A'] ?? ''));
            $namaKetua = trim((string)($row['B'] ?? ''));
            $c = trim((string)($row['C'] ?? ''));
            $d = trim((string)($row['D'] ?? ''));
            $e = trim((string)($row['E'] ?? ''));
            $f = trim((string)($row['F'] ?? ''));

            if ($unitKerja === '' && $namaKetua === '' && $c === '' && $d === '' && $e === '' && $f === '') {
                continue;
            }
            if (str_starts_with(strtolower($unitKerja), 'keterangan') || str_starts_with(strtolower($unitKerja), 'catatan')) {
                continue;
            }

            $rows[] = [
                'cells' => [
                    $unitKerja,
                    $namaKetua,
                    $c,
                    $d,
                    $e,
                    $f,
                ],
                'unit_kerja' => $unitKerja,
                'nama_ketua' => $namaKetua,
                'periode_jabatan' => $c,
                'pendidikan_terakhir' => $d,
                'jabatan_fungsional' => $e,
                'tupoksi' => $f,
            ];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'] ?? ['Unit Kerja', 'Nama Ketua', 'Periode Jabatan', 'Pendidikan Terakhir', 'Jabatan Fungsional', 'Tugas Pokok & Fungsi'],
            'table_rows' => $rows,
            'summary_data' => [
                'total' => count($rows),
                'note' => $schema['note'] ?? 'Data yang ditulis dalam tabel ini termasuk Unit Penjaminan Mutu.',
                'has_total' => false,
            ],
        ];
    }

    /**
     * Tabel 1.A.4: EWMP Beban DTPR (Multi-level Merged Header)
     */
    private function parseTable1A4(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('1.A.4');
        $rows = [];
        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $nama = trim((string)($row['B'] ?? ''));
            if ($nama === '' || str_contains(strtolower($no), 'jumlah') || str_contains(strtolower($no), 'rata')) {
                continue;
            }

            $cells = [
                $no ?: (string)(count($rows) + 1),
                $nama,
                (string)($row['C'] ?? '0'),
                (string)($row['D'] ?? '0'),
                (string)($row['E'] ?? '0'),
                (string)($row['F'] ?? '0'),
                (string)($row['G'] ?? '0'),
                (string)($row['H'] ?? '0'),
                (string)($row['I'] ?? '0'),
                (string)($row['J'] ?? '0'),
            ];

            $rows[] = [
                'cells' => $cells,
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
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_dosen' => count($rows),
                'rata_rata_sks' => count($rows) > 0 ? round(array_sum(array_column($rows, 'total_sks')) / count($rows), 2) : 0,
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
                'note' => $schema['note'],
            ],
        ];
    }

    /**
     * Tabel 1.A.5: Kualifikasi Tenaga Kependidikan (Multi-level Merged Header)
     */
    private function parseTable1A5(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('1.A.5');
        $rows = [];

        // Row 4..7: Categories (Pustakawan, Laboran, Administrasi, Lainnya)
        $standardTypes = ['Pustakawan *)', 'Laboran/Teknisi', 'Administrasi', 'Lainnya'];
        for ($r = 4; $r <= 7; $r++) {
            $row = $data[$r] ?? [];
            $typeName = trim((string)($row['B'] ?? ($standardTypes[$r - 4] ?? '')));
            $noStr = trim((string)($row['A'] ?? (($r - 3) . '.')));

            $cells = [
                $noStr,
                $typeName,
                trim((string)($row['C'] ?? '0')),
                trim((string)($row['D'] ?? '0')),
                trim((string)($row['E'] ?? '0')),
                trim((string)($row['F'] ?? '0')),
                trim((string)($row['G'] ?? '0')),
                trim((string)($row['H'] ?? '0')),
                trim((string)($row['I'] ?? '0')),
                trim((string)($row['J'] ?? '0')),
                trim((string)($row['K'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        // Check if there are additional rows before Total or Keterangan
        for ($r = 8; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $bVal = trim((string)($row['B'] ?? ''));
            $aVal = trim((string)($row['A'] ?? ''));

            if (str_starts_with(strtolower($aVal), 'keterangan') || str_starts_with(strtolower($bVal), 'total')) {
                break;
            }
            if ($bVal !== '' && !in_array($bVal, $standardTypes)) {
                $cells = [
                    $aVal ?: ((count($rows) + 1) . '.'),
                    $bVal,
                    trim((string)($row['C'] ?? '0')),
                    trim((string)($row['D'] ?? '0')),
                    trim((string)($row['E'] ?? '0')),
                    trim((string)($row['F'] ?? '0')),
                    trim((string)($row['G'] ?? '0')),
                    trim((string)($row['H'] ?? '0')),
                    trim((string)($row['I'] ?? '0')),
                    trim((string)($row['J'] ?? '0')),
                    trim((string)($row['K'] ?? '')),
                ];
                $rows[] = ['cells' => $cells];
            }
        }

        // Extract note
        $note = $schema['note'];
        for ($r = 8; $r <= count($data); $r++) {
            $val = trim((string)($data[$r]['A'] ?? ''));
            if (str_starts_with($val, '*)')) {
                $note = $val;
                break;
            }
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
                'note' => $note,
            ],
        ];
    }

    /**
     * Tabel 1.B: Unit SPMI dan SDM (Multi-level Merged Header)
     */
    private function parseTable1B(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('1.B');
        $rows = [];

        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $unit = trim((string)($row['A'] ?? ''));
            $nama = trim((string)($row['B'] ?? ''));

            if ($unit === '' && $nama === '') {
                continue;
            }
            if ($unit === '…' || $unit === '...') {
                continue;
            }

            $cells = [
                $unit,
                $nama,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['G'] ?? '')),
                trim((string)($row['H'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        if (empty($rows)) {
            $rows = $schema['default_rows'];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
            ],
        ];
    }

    /**
     * Tabel 2.A.1: Data Mahasiswa
     */
    private function parseTable2A1(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('2.A.1');
        $rows = [];
        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $ts = trim((string)($row['A'] ?? ''));
            if ($ts === '' || str_contains(strtolower($ts), 'jumlah')) {
                continue;
            }

            $cells = [
                $ts,
                (string)($row['B'] ?? '0'),
                (string)($row['C'] ?? '0'),
                (string)($row['D'] ?? '0'),
                (string)($row['E'] ?? '0'),
                (string)($row['F'] ?? '0'),
                (string)($row['I'] ?? '0'),
                (string)($row['L'] ?? '0'),
                (string)($row['O'] ?? '0'),
            ];

            $rows[] = [
                'cells' => $cells,
                'ts' => $ts,
                'daya_tampung' => (int)($row['B'] ?? 0),
                'calon_pendaftar' => (int)($row['C'] ?? 0),
                'calon_afirmasi' => (int)($row['D'] ?? 0),
                'calon_disabilitas' => (int)($row['E'] ?? 0),
                'baru_reguler_diterima' => (int)($row['F'] ?? 0),
                'baru_rpl_diterima' => (int)($row['I'] ?? 0),
                'aktif_reguler_diterima' => (int)($row['L'] ?? 0),
                'aktif_rpl_diterima' => (int)($row['O'] ?? 0),
            ];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_tahun' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
            ],
        ];
    }

    /**
     * Tabel 2.B.5: Kesesuaian Bidang Kerja Lulusan
     */
    private function parseTable2B5(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('2.B.5');
        $rows = [];

        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $ts = trim((string)($row['A'] ?? ''));

            if ($ts === '' || str_contains(strtolower($ts), 'jumlah')) {
                continue;
            }

            $cells = [
                $ts,
                trim((string)($row['B'] ?? '0')),
                trim((string)($row['C'] ?? '0')),
                trim((string)($row['D'] ?? '0')),
                trim((string)($row['E'] ?? '0')),
                trim((string)($row['F'] ?? '0')),
                trim((string)($row['G'] ?? '0')),
                trim((string)($row['H'] ?? '0')),
            ];

            $rows[] = ['cells' => $cells];
        }

        if (empty($rows)) {
            $rows = $schema['default_rows'];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
            ],
        ];
    }

    /**
     * Tabel 2.B.6: Kepuasan Pengguna Lulusan
     */
    private function parseTable2B6(array $data, ?Worksheet $sheet = null): array
    {
        $schema = self::getTableSchema('2.B.6');
        $rows = [];

        for ($r = 4; $r <= 10; $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ($r - 3)));
            $jenis = trim((string)($row['B'] ?? ''));

            if ($jenis === '') {
                continue;
            }

            $cells = [
                $no,
                $jenis,
                trim((string)($row['C'] ?? '0')),
                trim((string)($row['D'] ?? '0')),
                trim((string)($row['E'] ?? '0')),
                trim((string)($row['F'] ?? '0')),
                trim((string)($row['G'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        if (empty($rows)) {
            $rows = $schema['default_rows'];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
            ],
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

            $cells = [
                $prasarana,
                trim((string)($row['B'] ?? '')),
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['H'] ?? '')),
            ];

            $rows[] = [
                'cells' => $cells,
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
     * Tabel 3.A.2 & 4.A.2: Penelitian / PkM DTPR, Hibah dan Pembiayaan
     */
    private function parseTable3A2(array $data, ?Worksheet $sheet = null, string $tableCode = '3.A.2'): array
    {
        $schema = self::getTableSchema($tableCode) ?? self::getTableSchema('3.A.2');
        $rows = [];

        for ($r = 5; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $nama = trim((string)($row['B'] ?? ''));

            if ($nama === '' || str_contains(strtolower($no), 'jumlah')) {
                continue;
            }
            if ($no === '…' || $no === '...') {
                continue;
            }

            $cells = [
                $no ?: (string)(count($rows) + 1),
                $nama,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '0')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['G'] ?? '1')),
                trim((string)($row['H'] ?? '0')),
                trim((string)($row['I'] ?? '0')),
                trim((string)($row['J'] ?? '0')),
                trim((string)($row['K'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
            ],
        ];
    }

    /**
     * Tabel 3.C.1 & 4.C.1: Kerjasama Penelitian / PkM
     */
    private function parseTable3C1(array $data, ?Worksheet $sheet = null, string $tableCode = '3.C.1'): array
    {
        $schema = self::getTableSchema($tableCode) ?? self::getTableSchema('3.C.1');
        $rows = [];

        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $judul = trim((string)($row['B'] ?? ''));

            if ($judul === '' || str_contains(strtolower($no), 'jumlah')) {
                continue;
            }

            $cells = [
                $no ?: (string)(count($rows) + 1),
                $judul,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '1')),
                trim((string)($row['F'] ?? '0')),
                trim((string)($row['G'] ?? '0')),
                trim((string)($row['H'] ?? '0')),
                trim((string)($row['I'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
                'has_total' => $schema['has_total'],
                'total_label_col' => $schema['total_label_col'],
                'sum_cols' => $schema['sum_cols'],
            ],
        ];
    }

    /**
     * Tabel 3.C.2 & 4.C.2: Publikasi Penelitian / Diseminasi PkM
     */
    private function parseTable3C2(array $data, ?Worksheet $sheet = null, string $tableCode = '3.C.2'): array
    {
        $schema = self::getTableSchema($tableCode) ?? self::getTableSchema('3.C.2');
        $rows = [];

        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $nama = trim((string)($row['B'] ?? ''));

            if ($nama === '' || str_contains(strtolower($no), 'jumlah') || $no === '…') {
                continue;
            }

            $cells = [
                $no ?: (string)(count($rows) + 1),
                $nama,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['G'] ?? '')),
                trim((string)($row['H'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
            ],
        ];
    }

    /**
     * Tabel 3.C.3 & 4.C.3: Perolehan HKI
     */
    private function parseTable3C3(array $data, ?Worksheet $sheet = null, string $tableCode = '3.C.3'): array
    {
        $schema = self::getTableSchema($tableCode) ?? self::getTableSchema('3.C.3');
        $rows = [];

        for ($r = 4; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $no = trim((string)($row['A'] ?? ''));
            $judul = trim((string)($row['B'] ?? ''));

            if ($judul === '' || str_contains(strtolower($no), 'jumlah') || $no === '…') {
                continue;
            }

            $cells = [
                $no ?: (string)(count($rows) + 1),
                $judul,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['G'] ?? '')),
                trim((string)($row['H'] ?? '')),
            ];

            $rows[] = ['cells' => $cells];
        }

        return [
            'dynamic_headers' => $schema['leaf_headers'],
            'table_rows' => $rows,
            'summary_data' => [
                'total_rows' => count($rows),
                'header_structure' => $schema['header_rows'],
            ],
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

            $cells = [
                trim((string)($row['A'] ?? (count($rows) + 1))),
                $jenis,
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
            ];

            $rows[] = [
                'cells' => $cells,
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

            $cells = [
                $prasarana,
                trim((string)($row['B'] ?? '')),
                trim((string)($row['C'] ?? '')),
                trim((string)($row['D'] ?? '')),
                trim((string)($row['E'] ?? '')),
                trim((string)($row['F'] ?? '')),
                trim((string)($row['H'] ?? '')),
            ];

            $rows[] = [
                'cells' => $cells,
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
                    'cells' => [
                        trim((string)($data[3]['A'] ?? '')),
                        trim((string)($data[3]['B'] ?? '')),
                        trim((string)($data[3]['C'] ?? '')),
                        trim((string)($data[5]['A'] ?? '')),
                        trim((string)($data[5]['B'] ?? '')),
                    ],
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
     * Generic parser for any other sheets with automatic merged header detection.
     */
    private function parseGenericTable(array $data, string $tableCode, ?Worksheet $sheet = null): array
    {
        // Check schema registry first
        $schema = self::getTableSchema($tableCode);

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

        if (empty($headers) && $schema) {
            $headers = $schema['leaf_headers'];
        }

        $rows = [];
        for ($r = $headerRow + 1; $r <= count($data); $r++) {
            $row = $data[$r] ?? [];
            $nonEmpty = array_filter($row, fn($v) => $v !== null && trim((string)$v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $cells = [];
            foreach ($headers as $col => $hName) {
                $cells[] = trim((string)($row[$col] ?? ''));
            }

            $rowData = ['cells' => $cells];
            foreach ($headers as $col => $hName) {
                $rowData[$hName] = trim((string)($row[$col] ?? ''));
            }
            $rows[] = $rowData;
        }

        $summary = ['total' => count($rows)];
        if ($schema) {
            $summary['header_structure'] = $schema['header_rows'] ?? null;
            $summary['has_total'] = $schema['has_total'] ?? false;
            $summary['total_label_col'] = $schema['total_label_col'] ?? null;
            $summary['sum_cols'] = $schema['sum_cols'] ?? [];
            $summary['note'] = $schema['note'] ?? null;
        }

        return [
            'dynamic_headers' => array_values($headers),
            'table_rows' => $rows,
            'summary_data' => $summary,
        ];
    }

    /**
     * Export LKPS submission data to a clean Excel file with merged headers support.
     */
    public function exportToExcel(string $tableCode, string $tableName, array $headers, array $rows, ?array $summaryData = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($tableCode);

        // Title in A1
        $sheet->setCellValue('A1', "Tabel {$tableCode} {$tableName}");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $schema = self::getTableSchema($tableCode);
        $headerStructure = $summaryData['header_structure'] ?? ($schema['header_rows'] ?? null);

        $dataStartRow = 4;

        if ($headerStructure && is_array($headerStructure) && count($headerStructure) >= 2) {
            // MULTI-LEVEL MERGED HEADERS (2 Tiers)
            $tier1 = $headerStructure[0] ?? [];
            $tier2 = $headerStructure[1] ?? [];

            // Write Tier 1 (Row 2)
            $colCursor = 1;
            foreach ($tier1 as $th) {
                $label = $th['label'] ?? '';
                $colspan = (int)($th['colspan'] ?? 1);
                $rowspan = (int)($th['rowspan'] ?? 1);

                $startColLetter = Coordinate::stringFromColumnIndex($colCursor);
                $endColLetter = Coordinate::stringFromColumnIndex($colCursor + $colspan - 1);
                $startRow = 2;
                $endRow = $startRow + $rowspan - 1;

                $sheet->setCellValue($startColLetter . $startRow, $label);

                if ($colspan > 1 || $rowspan > 1) {
                    $sheet->mergeCells("{$startColLetter}{$startRow}:{$endColLetter}{$endRow}");
                }

                $colCursor += $colspan;
            }

            // Write Tier 2 (Row 3)
            foreach ($tier2 as $th) {
                $label = $th['label'] ?? '';
                $colIdx = (int)($th['colIdx'] ?? 0);
                $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($colLetter . '3', $label);
            }

            // Style headers Row 2 and 3
            $maxColLetter = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle("A2:{$maxColLetter}3")->getFont()->setBold(true);
            $sheet->getStyle("A2:{$maxColLetter}3")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle("A2:{$maxColLetter}3")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("A2:{$maxColLetter}3")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $dataStartRow = 4;
        } else {
            // SINGLE LEVEL HEADERS (Row 3)
            $colIdx = 1;
            foreach ($headers as $header) {
                $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . '3';
                $sheet->setCellValue($cellCoord, $header);
                $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }
            $maxColLetter = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle("A3:{$maxColLetter}3")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("A3:{$maxColLetter}3")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $dataStartRow = 4;
        }

        // Data Rows
        $rowNum = $dataStartRow;
        foreach ($rows as $row) {
            $colIdx = 1;
            foreach ($headers as $headerKey => $headerName) {
                $val = '';
                if (isset($row['cells']) && is_array($row['cells'])) {
                    $val = $row['cells'][$colIdx - 1] ?? '';
                } elseif (isset($row[$headerName])) {
                    $val = $row[$headerName];
                } elseif (isset($row[$headerKey])) {
                    $val = $row[$headerKey];
                } elseif (is_array($row)) {
                    $val = array_values($row)[$colIdx - 1] ?? '';
                }
                if (is_array($val)) {
                    $val = json_encode($val);
                }

                $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . $rowNum;
                $sheet->setCellValue($cellCoord, $val);
                $colIdx++;
            }
            $sheet->getStyle("A{$rowNum}:" . Coordinate::stringFromColumnIndex(count($headers)) . "{$rowNum}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR);
            $rowNum++;
        }

        // Apply custom row merges (vertical cell merges spanning > 1 row)
        $rowMerges = $summaryData['row_merges'] ?? [];
        if (!empty($rowMerges) && is_array($rowMerges)) {
            foreach ($rowMerges as $rm) {
                if (isset($rm['colIdx'], $rm['startRow'], $rm['endRow'])) {
                    $colLetter = Coordinate::stringFromColumnIndex((int)$rm['colIdx'] + 1);
                    $startR = $dataStartRow + (int)$rm['startRow'];
                    $endR = $dataStartRow + (int)$rm['endRow'];
                    if ($startR < $endR && $endR < $rowNum) {
                        $sheet->mergeCells("{$colLetter}{$startR}:{$colLetter}{$endR}");
                        if (isset($rm['value']) && $rm['value'] !== '') {
                            $sheet->setCellValue("{$colLetter}{$startR}", $rm['value']);
                        }
                        $sheet->getStyle("{$colLetter}{$startR}:{$colLetter}{$endR}")
                            ->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }
        }

        // Total row if schema or summary defines it
        $hasTotal = $summaryData['has_total'] ?? ($schema['has_total'] ?? false);
        $sumCols = $summaryData['sum_cols'] ?? ($schema['sum_cols'] ?? []);
        $totalLabelCol = $summaryData['total_label_col'] ?? ($schema['total_label_col'] ?? 1);

        if ($hasTotal && !empty($sumCols) && count($rows) > 0) {
            $totalColLetter = Coordinate::stringFromColumnIndex($totalLabelCol + 1);
            $sheet->setCellValue($totalColLetter . $rowNum, 'Total');
            $sheet->getStyle($totalColLetter . $rowNum)->getFont()->setBold(true);

            foreach ($sumCols as $cIdx) {
                $colLetter = Coordinate::stringFromColumnIndex($cIdx + 1);
                $startCell = $colLetter . $dataStartRow;
                $endCell = $colLetter . ($rowNum - 1);
                $sheet->setCellValue($colLetter . $rowNum, "=SUM({$startCell}:{$endCell})");
                $sheet->getStyle($colLetter . $rowNum)->getFont()->setBold(true);
            }

            $maxColLetter = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle("A{$rowNum}:{$maxColLetter}{$rowNum}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE2E8F0');
            $sheet->getStyle("A{$rowNum}:{$maxColLetter}{$rowNum}")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $rowNum++;
        }

        // Note / Keterangan
        $note = $summaryData['note'] ?? ($schema['note'] ?? null);
        if ($note) {
            $rowNum++;
            $sheet->setCellValue("A{$rowNum}", "Keterangan :");
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(10);
            $rowNum++;
            $sheet->setCellValue("A{$rowNum}", $note);
            $sheet->getStyle("A{$rowNum}")->getFont()->setItalic(true)->setSize(9);
        }

        // Auto size columns
        for ($i = 1; $i <= max(count($headers), 5); $i++) {
            $colString = Coordinate::stringFromColumnIndex($i);
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
