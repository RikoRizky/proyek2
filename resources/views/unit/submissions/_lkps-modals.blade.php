{{-- Modal Import Excel untuk LKPS --}}
<div id="lkps-import-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 transition-opacity" style="background: rgba(10,10,20,0.82)" onclick="closeLkpsImportModal()"></div>

    {{-- Modal Box --}}
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="relative overflow-hidden transform transition-all duration-300 scale-95 opacity-0 rounded-2xl shadow-2xl ring-1 ring-white/10 w-full"
            style="width: 500px; max-width: calc(100vw - 2rem); background: #fff;" id="lkps-import-box">

            {{-- Header --}}
            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e1b4b 100%); padding: 1.25rem 1.5rem 1.5rem;">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-md" style="background:rgba(16,185,129,0.3)">
                                <svg class="h-3 w-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                </svg>
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-400" id="lkps-modal-code">LKPS TABLE</span>
                        </div>
                        <h3 id="lkps-modal-title" class="text-base font-bold leading-snug text-white">Import Data dari Excel</h3>
                        <p class="mt-1 text-xs text-slate-300">Unggah file Excel LAM INFOKOM Anda, data akan langsung diekstrak ke dalam sistem.</p>
                    </div>
                    <button type="button" onclick="closeLkpsImportModal()"
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:text-white transition"
                        style="background:rgba(255,255,255,0.08);">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Body Form --}}
            <form id="lkps-import-form" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/80 p-5 text-center transition hover:border-violet-400 hover:bg-violet-50/20">
                    <svg class="mx-auto h-10 w-10 text-emerald-500 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <label for="excel_file_input" class="cursor-pointer">
                        <span class="block text-sm font-semibold text-slate-700">Pilih berkas Excel (.xlsx / .xls)</span>
                        <span class="mt-1 block text-xs text-slate-400">File LKPS LAM INFOKOM (Status Unggul S1Tr)</span>
                        <input id="excel_file_input" name="excel_file" type="file" accept=".xlsx,.xls" class="sr-only" required onchange="handleExcelFileSelect(this)">
                    </label>
                    <p id="selected-excel-name" class="mt-2 text-xs font-bold text-violet-600 hidden"></p>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200/80 p-3 text-[11px] text-amber-900 flex items-start gap-2">
                    <svg class="h-4 w-4 shrink-0 text-amber-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <span>Sistem akan membaca sheet dengan nama/kode tabel ini secara otomatis. Data yang diimport akan menggantikan draft data pada tabel ini.</span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeLkpsImportModal()" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-import" class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:from-emerald-700 hover:to-teal-700 transition">
                        Mulai Import Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openLkpsImportModal(tableCode, tableTitle) {
        const modal = document.getElementById('lkps-import-modal');
        const box = document.getElementById('lkps-import-box');
        const form = document.getElementById('lkps-import-form');
        const codeLabel = document.getElementById('lkps-modal-code');
        const titleLabel = document.getElementById('lkps-modal-title');
        const fileNameLabel = document.getElementById('selected-excel-name');

        codeLabel.textContent = 'TABEL ' + tableCode;
        titleLabel.textContent = 'Import Data ' + tableTitle;
        form.action = '/unit/lkps/' + encodeURIComponent(tableCode) + '/import';
        fileNameLabel.classList.add('hidden');
        fileNameLabel.textContent = '';
        form.reset();

        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeLkpsImportModal() {
        const modal = document.getElementById('lkps-import-modal');
        const box = document.getElementById('lkps-import-box');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function handleExcelFileSelect(input) {
        const label = document.getElementById('selected-excel-name');
        if (input.files && input.files[0]) {
            label.textContent = '✓ Terpilih: ' + input.files[0].name;
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
        }
    }

    // Official LAM INFOKOM 2.1 Default Table Column Templates (31 Sheets)
    const DEFAULT_TABLE_TEMPLATES = {
        '1.A.1': ['Unit Kerja', 'Nama Ketua', 'Periode Jabatan', 'Pendidikan Terakhir', 'Jabatan Fungsional', 'Tugas Pokok & Fungsi'],
        '1.A.2': ['Sumber Pendanaan', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '1.A.3': ['Penggunaan Dana', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '1.A.4': ['No', 'Nama DTPR', 'PS Sendiri', 'PS Lain PT Sendiri', 'PT Lain', 'SKS Penelitian', 'SKS PkM', 'SKS Manajemen PT Sendiri', 'SKS Manajemen PT Lain', 'Total SKS'],
        '1.A.5': ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'],
        '1.B':   ['Unit SPMI', 'Nama Unit SPMI', 'Dokumen SPMI', 'Auditor Certified', 'Auditor Non-Certified', 'Frekuensi Audit/Tahun', 'Bukti Certified', 'Laporan Audit'],
        '2.A.1': ['TS', 'Daya Tampung', 'Pendaftar', 'Afirmasi', 'Kebutuhan Khusus', 'Mhs Baru Reguler', 'Mhs Baru RPL', 'Mhs Aktif Reguler', 'Mhs Aktif RPL'],
        '2.A.2': ['Asal Mahasiswa', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '2.A.3': ['Kategori Mahasiswa Asing', 'TS-2', 'TS-1', 'TS', 'Jumlah'],
        '2.B.1': ['PL 1', 'PL 2', 'PL 3', 'PL 4'],
        '2.B.2': ['PL 1', 'PL 2', 'PL 3', 'PL 4'],
        '2.B.3': ['CPL', 'CPMK', 'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8'],
        '2.B.4': ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Rata-rata Waktu Tunggu (Bulan)'],
        '2.B.5': ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Profesi Infokom', 'Profesi Non-Infokom', 'Multinasional', 'Nasional', 'Wirausaha'],
        '2.B.6': ['No', 'Jenis Kemampuan', 'Sangat Baik (%)', 'Baik (%)', 'Cukup (%)', 'Kurang (%)', 'Rencana Tindak Lanjut'],
        '2.C':   ['Tahun Akademik', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '2.D':   ['Sumber Rekognisi', 'Jenis Pengakuan (Rekognisi)', 'Tahun Akademik', 'Link Bukti'],
        '3.A.1': ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '3.A.2': ['No', 'Nama DTPR (Ketua)', 'Judul Penelitian', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '3.A.3': ['Tahun Akademik', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '3.C.1': ['No', 'Judul Kerjasama', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '3.C.2': ['No', 'Nama DTPR', 'Judul Publikasi', 'Jenis Publikasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '3.C.3': ['No', 'Judul', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '4.A.1': ['Nama Prasarana PkM', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '4.A.2': ['No', 'Nama DTPR (Ketua PkM)', 'Judul PkM', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '4.C.1': ['No', 'Judul Kerjasama PkM', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '4.C.2': ['No', 'Nama DTPR (Ketua)', 'Judul', 'Diseminasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '4.C.3': ['No', 'Judul PkM', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '5.1':   ['Jenis Tata Kelola', 'Nama Sistem Informasi', 'Akses (Lokal/Internet)', 'Unit Pengelola', 'Link Bukti'],
        '5.2':   ['Nama Prasarana Pendidikan', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '6':     ['Visi PT', 'Visi UPPS', 'Visi Keilmuan PS', 'Misi PT', 'Misi UPPS']
    };

    // Official LAM INFOKOM Table Schemas with Merged Headers & Rows
    const TABLE_SCHEMAS = {
        '1.A.1': {
            leafHeaders: ['Unit Kerja', 'Nama Ketua', 'Periode Jabatan', 'Pendidikan Terakhir', 'Jabatan Fungsional', 'Tugas Pokok & Fungsi'],
            hasTotal: false,
            note: 'Data yang ditulis dalam tabel ini termasuk Unit Penjaminan Mutu.',
            defaultRows: [
                { cells: ['', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', ''] }
            ]
        },
        '1.A.2': {
            leafHeaders: ['Sumber Pendanaan', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3],
            defaultRows: [
                { cells: ['Mahasiswa (SPP)', '0', '0', '0', ''] },
                { cells: ['Yayasan', '0', '0', '0', ''] },
                { cells: ['PTN/PTS Lain', '0', '0', '0', ''] },
                { cells: ['Pemerintah (Pusat/Daerah)', '0', '0', '0', ''] },
                { cells: ['Industri/Masyarakat', '0', '0', '0', ''] },
                { cells: ['Lainnya', '0', '0', '0', ''] }
            ]
        },
        '1.A.3': {
            leafHeaders: ['Penggunaan Dana', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3],
            defaultRows: [
                { cells: ['Pendidikan', '0', '0', '0', ''] },
                { cells: ['Penelitian', '0', '0', '0', ''] },
                { cells: ['PKM', '0', '0', '0', ''] },
                { cells: ['Investasi Prasarana', '0', '0', '0', ''] },
                { cells: ['Investasi Sarana', '0', '0', '0', ''] },
                { cells: ['Investasi SDM', '0', '0', '0', ''] },
                { cells: ['Lainnya', '0', '0', '0', ''] }
            ]
        },
        '1.A.4': {
            headerRows: [
                [
                    { label: 'No.', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Nama DTPR', rowspan: 2, colIdx: 1, width: '220px' },
                    { label: 'SKS Pengajaran¹) Pada', colspan: 3, colIdx: 2 },
                    { label: 'SKS Penelitian²)', rowspan: 2, colIdx: 5, width: '130px' },
                    { label: 'SKS Pengabdian kepada Masyarakat²)', rowspan: 2, colIdx: 6, width: '150px' },
                    { label: 'SKS Manajemen³)', colspan: 2, colIdx: 7 },
                    { label: 'Total SKS', rowspan: 2, colIdx: 9, width: '110px' }
                ],
                [
                    { label: 'PS Sendiri', colIdx: 2, width: '110px' },
                    { label: 'PS Lain, PT Sendiri', colIdx: 3, width: '140px' },
                    { label: 'PT Lain', colIdx: 4, width: '110px' },
                    { label: 'PT Sendiri', colIdx: 7, width: '110px' },
                    { label: 'PT Lain', colIdx: 8, width: '110px' }
                ]
            ],
            leafHeaders: ['No', 'Nama DTPR', 'PS Sendiri', 'PS Lain PT Sendiri', 'PT Lain', 'SKS Penelitian', 'SKS PkM', 'SKS Manajemen PT Sendiri', 'SKS Manajemen PT Lain', 'Total SKS'],
            hasTotal: true,
            totalLabelCol: 1,
            sumCols: [2, 3, 4, 5, 6, 7, 8, 9],
            note: '1) SKS pengajaran sama dengan sks mata kuliah yang diajarkan. 2) SKS penelitian dan pengabdian kepada masyarakat dihitung sesuai dengan peraturan yang berlaku di PT. 3) SKS manajemen dihitung sesuai jabatan.'
        },
        '1.A.5': {
            headerRows: [
                [
                    { label: 'No.', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Jenis Tenaga Kependidikan', rowspan: 2, colIdx: 1, width: '220px' },
                    { label: 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', colspan: 8, colIdx: 2 },
                    { label: 'Unit Kerja', rowspan: 2, colIdx: 10, width: '200px' }
                ],
                [
                    { label: 'S3', colIdx: 2, width: '75px' },
                    { label: 'S2', colIdx: 3, width: '75px' },
                    { label: 'S1', colIdx: 4, width: '75px' },
                    { label: 'D4', colIdx: 5, width: '75px' },
                    { label: 'D3', colIdx: 6, width: '75px' },
                    { label: 'D2', colIdx: 7, width: '75px' },
                    { label: 'D1', colIdx: 8, width: '75px' },
                    { label: 'SMA/SMK/MA', colIdx: 9, width: '110px' }
                ]
            ],
            leafHeaders: ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'],
            defaultRows: [
                { cells: ['1.', 'Pustakawan *)', '0', '0', '0', '0', '0', '0', '0', '0', ''] },
                { cells: ['2.', 'Laboran/Teknisi', '0', '0', '0', '0', '0', '0', '0', '0', ''] },
                { cells: ['3.', 'Administrasi', '0', '0', '0', '0', '0', '0', '0', '0', ''] },
                { cells: ['4.', 'Lainnya', '0', '0', '0', '0', '0', '0', '0', '0', ''] }
            ],
            hasTotal: true,
            totalLabelCol: 1,
            sumCols: [2, 3, 4, 5, 6, 7, 8, 9],
            note: '*) Pustakawan adalah staf perpustakaan yang memiliki ijazah atau sertifikat kompetensi pada bidang ilmu perpustakaan.'
        },
        '1.B': {
            headerRows: [
                [
                    { label: 'Unit SPMI', rowspan: 2, colIdx: 0, width: '120px' },
                    { label: 'Nama Unit SPMI', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Dokumen SPMI', rowspan: 2, colIdx: 2, width: '220px' },
                    { label: 'Jumlah Auditor Mutu Internal', colspan: 2, colIdx: 3 },
                    { label: 'Frekuensi audit/monev per tahun', rowspan: 2, colIdx: 5, width: '160px' },
                    { label: 'Bukti Certified Auditor', rowspan: 2, colIdx: 6, width: '180px' },
                    { label: 'Laporan Audit', rowspan: 2, colIdx: 7, width: '180px' }
                ],
                [
                    { label: 'Certified', colIdx: 3, width: '110px' },
                    { label: 'Non Certified', colIdx: 4, width: '130px' }
                ]
            ],
            leafHeaders: ['Unit SPMI', 'Nama Unit SPMI', 'Dokumen SPMI', 'Auditor Certified', 'Auditor Non-Certified', 'Frekuensi Audit/Tahun', 'Bukti Certified', 'Laporan Audit'],
            defaultRows: [
                { cells: ['PT', '', '', '', '', '', '', ''] },
                { cells: ['UPPS', '', '', '', '', '', '', ''] }
            ]
        },
        '2.A.1': {
            headerRows: [
                [
                    { label: 'TS', rowspan: 2, colIdx: 0, width: '90px' },
                    { label: 'Daya Tampung', rowspan: 2, colIdx: 1, width: '120px' },
                    { label: 'Jumlah Calon Mahasiswa', colspan: 3, colIdx: 2 },
                    { label: 'Jumlah Mahasiswa Baru', colspan: 2, colIdx: 5 },
                    { label: 'Jumlah Mahasiswa Aktif', colspan: 2, colIdx: 7 }
                ],
                [
                    { label: 'Pendaftar', colIdx: 2, width: '110px' },
                    { label: 'Afirmasi', colIdx: 3, width: '110px' },
                    { label: 'Kebutuhan Khusus', colIdx: 4, width: '140px' },
                    { label: 'Reguler', colIdx: 5, width: '110px' },
                    { label: 'RPL', colIdx: 6, width: '110px' },
                    { label: 'Reguler', colIdx: 7, width: '110px' },
                    { label: 'RPL', colIdx: 8, width: '110px' }
                ]
            ],
            leafHeaders: ['TS', 'Daya Tampung', 'Pendaftar', 'Afirmasi', 'Kebutuhan Khusus', 'Mhs Baru Reguler', 'Mhs Baru RPL', 'Mhs Aktif Reguler', 'Mhs Aktif RPL'],
            defaultRows: [
                { cells: ['TS-3', '', '', '', '', '', '', '', ''] },
                { cells: ['TS-2', '', '', '', '', '', '', '', ''] },
                { cells: ['TS-1', '', '', '', '', '', '', '', ''] },
                { cells: ['TS', '', '', '', '', '', '', '', ''] }
            ],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3, 4, 5, 6, 7, 8]
        },
        '2.B.5': {
            headerRows: [
                [
                    { label: 'Tahun Lulus', rowspan: 2, colIdx: 0, width: '110px' },
                    { label: 'Jumlah Lulusan', rowspan: 2, colIdx: 1, width: '120px' },
                    { label: 'Jumlah Lulusan yang Terlacak', rowspan: 2, colIdx: 2, width: '160px' },
                    { label: 'Profesi Kerja Bidang Infokom', rowspan: 2, colIdx: 3, width: '160px' },
                    { label: 'Profesi Kerja Bidang NON Infokom', rowspan: 2, colIdx: 4, width: '170px' },
                    { label: 'Lingkup Tempat Kerja', colspan: 3, colIdx: 5 }
                ],
                [
                    { label: 'Multinasional/Internasional', colIdx: 5, width: '180px' },
                    { label: 'Nasional', colIdx: 6, width: '120px' },
                    { label: 'Wirausaha', colIdx: 7, width: '120px' }
                ]
            ],
            leafHeaders: ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Profesi Infokom', 'Profesi Non-Infokom', 'Multinasional', 'Nasional', 'Wirausaha'],
            defaultRows: [
                { cells: ['TS-2', '', '', '', '', '', '', ''] },
                { cells: ['TS-1', '', '', '', '', '', '', ''] },
                { cells: ['TS', '', '', '', '', '', '', ''] }
            ],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3, 4, 5, 6, 7]
        },
        '2.B.6': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Jenis Kemampuan', rowspan: 2, colIdx: 1, width: '260px' },
                    { label: 'Tingkat Kepuasan Pengguna (%)', colspan: 4, colIdx: 2 },
                    { label: 'Rencana Tindak Lanjut oleh UPPS/PS', rowspan: 2, colIdx: 6, width: '260px' }
                ],
                [
                    { label: 'Sangat Baik', colIdx: 2, width: '110px' },
                    { label: 'Baik', colIdx: 3, width: '110px' },
                    { label: 'Cukup', colIdx: 4, width: '110px' },
                    { label: 'Kurang', colIdx: 5, width: '110px' }
                ]
            ],
            leafHeaders: ['No', 'Jenis Kemampuan', 'Sangat Baik (%)', 'Baik (%)', 'Cukup (%)', 'Kurang (%)', 'Rencana Tindak Lanjut'],
            defaultRows: [
                { cells: ['1', 'Kerjasama Tim', '', '', '', '', ''] },
                { cells: ['2', 'Keahlian di Bidang Prodi', '', '', '', '', ''] },
                { cells: ['3', 'Kemampuan Berbahasa Asing (Inggris)', '', '', '', '', ''] },
                { cells: ['4', 'Kemampuan Berkomunikasi', '', '', '', '', ''] },
                { cells: ['5', 'Pengembangan Diri', '', '', '', '', ''] },
                { cells: ['6', 'Kepemimpinan', '', '', '', '', ''] },
                { cells: ['7', 'Etos Kerja', '', '', '', '', ''] }
            ],
            hasTotal: true,
            totalLabelCol: 1,
            sumCols: [2, 3, 4, 5]
        },
        '3.A.2': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Nama DTPR (Ketua)', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Judul Penelitian', rowspan: 2, colIdx: 2, width: '260px' },
                    { label: 'Jumlah Mhs Terlibat', rowspan: 2, colIdx: 3, width: '140px' },
                    { label: 'Jenis Hibah', rowspan: 2, colIdx: 4, width: '150px' },
                    { label: 'Sumber (L/N/I)', rowspan: 2, colIdx: 5, width: '120px' },
                    { label: 'Durasi (Tahun)', rowspan: 2, colIdx: 6, width: '110px' },
                    { label: 'Pendanaan (Rp Juta)', colspan: 3, colIdx: 7 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 10, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 7, width: '100px' },
                    { label: 'TS-1', colIdx: 8, width: '100px' },
                    { label: 'TS', colIdx: 9, width: '100px' }
                ]
            ],
            leafHeaders: ['No', 'Nama DTPR (Ketua)', 'Judul Penelitian', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 2,
            sumCols: [3, 6, 7, 8, 9]
        },
        '4.A.2': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Nama DTPR (Ketua PkM)', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Judul PkM', rowspan: 2, colIdx: 2, width: '260px' },
                    { label: 'Jumlah Mhs Terlibat', rowspan: 2, colIdx: 3, width: '140px' },
                    { label: 'Jenis Hibah', rowspan: 2, colIdx: 4, width: '150px' },
                    { label: 'Sumber (L/N/I)', rowspan: 2, colIdx: 5, width: '120px' },
                    { label: 'Durasi (Tahun)', rowspan: 2, colIdx: 6, width: '110px' },
                    { label: 'Pendanaan (Rp Juta)', colspan: 3, colIdx: 7 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 10, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 7, width: '100px' },
                    { label: 'TS-1', colIdx: 8, width: '100px' },
                    { label: 'TS', colIdx: 9, width: '100px' }
                ]
            ],
            leafHeaders: ['No', 'Nama DTPR (Ketua PkM)', 'Judul PkM', 'Mhs Terlibat', 'Jenis Hibah', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 2,
            sumCols: [3, 6, 7, 8, 9]
        },
        '3.C.1': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Judul Kerjasama', rowspan: 2, colIdx: 1, width: '220px' },
                    { label: 'Mitra Kerja Sama', rowspan: 2, colIdx: 2, width: '200px' },
                    { label: 'Sumber L/N/I', rowspan: 2, colIdx: 3, width: '120px' },
                    { label: 'Durasi (Tahun)', rowspan: 2, colIdx: 4, width: '110px' },
                    { label: 'Pendanaan (Rp Juta)', colspan: 3, colIdx: 5 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 8, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 5, width: '100px' },
                    { label: 'TS-1', colIdx: 6, width: '100px' },
                    { label: 'TS', colIdx: 7, width: '100px' }
                ]
            ],
            leafHeaders: ['No', 'Judul Kerjasama', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 1,
            sumCols: [4, 5, 6, 7]
        },
        '4.C.1': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Judul Kerjasama PkM', rowspan: 2, colIdx: 1, width: '220px' },
                    { label: 'Mitra Kerja Sama', rowspan: 2, colIdx: 2, width: '200px' },
                    { label: 'Sumber (L/N/I)', rowspan: 2, colIdx: 3, width: '120px' },
                    { label: 'Durasi (Tahun)', rowspan: 2, colIdx: 4, width: '110px' },
                    { label: 'Pendanaan (Rp Juta)', colspan: 3, colIdx: 5 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 8, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 5, width: '100px' },
                    { label: 'TS-1', colIdx: 6, width: '100px' },
                    { label: 'TS', colIdx: 7, width: '100px' }
                ]
            ],
            leafHeaders: ['No', 'Judul Kerjasama PkM', 'Mitra Kerja Sama', 'Sumber (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 1,
            sumCols: [4, 5, 6, 7]
        },
        '2.A.2': {
            headerRows: [
                [
                    { label: 'Asal Mahasiswa', rowspan: 2, colIdx: 0, width: '220px' },
                    { label: 'Jumlah Mahasiswa Baru', colspan: 4, colIdx: 1 }
                ],
                [
                    { label: 'TS-2', colIdx: 1, width: '100px' },
                    { label: 'TS-1', colIdx: 2, width: '100px' },
                    { label: 'TS', colIdx: 3, width: '100px' },
                    { label: 'Link Bukti', colIdx: 4, width: '200px' }
                ]
            ],
            leafHeaders: ['Asal Mahasiswa', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3],
            defaultRows: [
                { cells: ['Kota/Kab sama dengan PS', '0', '0', '0', ''] },
                { cells: ['Kota/Kabupaten Lain', '0', '0', '0', ''] },
                { cells: ['Provinsi Lain', '0', '0', '0', ''] },
                { cells: ['Negara Lain', '0', '0', '0', ''] }
            ]
        },
        '2.A.3': {
            leafHeaders: ['Kategori Mahasiswa Asing', 'TS-2', 'TS-1', 'TS', 'Jumlah'],
            hasTotal: true,
            totalLabelCol: 0,
            sumCols: [1, 2, 3, 4],
            defaultRows: [
                { cells: ['Mahasiswa Baru', '0', '0', '0', '0'] },
                { cells: ['Mahasiswa Aktif pada saat TS', '0', '0', '0', '0'] },
                { cells: ['Lulus pada saat TS', '0', '0', '0', '0'] },
                { cells: ['Mengundurkan Diri/DO pada saat TS', '0', '0', '0', '0'] }
            ]
        },
        '2.B.4': {
            leafHeaders: ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Rata-rata Waktu Tunggu (Bulan)'],
            hasTotal: false,
            defaultRows: [
                { cells: ['TS-2', '0', '0', '0'] },
                { cells: ['TS-1', '0', '0', '0'] },
                { cells: ['TS', '0', '0', '0'] }
            ]
        },
        '2.C': {
            leafHeaders: ['Tahun Akademik / Bentuk Pembelajaran', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: false,
            defaultRows: [
                { cells: ['Jumlah Mahasiswa Aktif', '', '', '', ''] },
                { cells: ['Micro-credensial', '', '', '', ''] },
                { cells: ['RPL tipe A-2', '', '', '', ''] },
                { cells: ['Pembelajaran di PS lain', '', '', '', ''] },
                { cells: ['Magang/Praktik Kerja', '', '', '', ''] },
                { cells: ['Lainnya', '', '', '', ''] }
            ]
        },
        '2.D': {
            headerRows: [
                [
                    { label: 'Sumber Rekognisi', rowspan: 2, colIdx: 0, width: '180px' },
                    { label: 'Jenis Pengakuan Lulusan (Rekognisi)', rowspan: 2, colIdx: 1, width: '240px' },
                    { label: 'Tahun Akademik', colspan: 3, colIdx: 2 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 5, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 2, width: '90px' },
                    { label: 'TS-1', colIdx: 3, width: '90px' },
                    { label: 'TS', colIdx: 4, width: '90px' }
                ]
            ],
            leafHeaders: ['Sumber Rekognisi', 'Jenis Pengakuan (Rekognisi)', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: false,
            defaultRows: [
                { cells: ['Masyarakat', '', '', '', '', ''] },
                { cells: ['Dunia Usaha', '', '', '', '', ''] },
                { cells: ['Dunia Industri', '', '', '', '', ''] },
                { cells: ['Dunia Kerja', '', '', '', '', ''] }
            ]
        },
        '3.A.1': {
            leafHeaders: ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
            hasTotal: false,
            note: '● Prasarana diisi nama laboratorium. ● Perangkat yang dimiliki diisi perangkat keras, perangkat lunak, bandwidth, device, tool dan lain-lain.',
            defaultRows: [
                { cells: ['', '', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', '', ''] }
            ]
        },
        '3.A.3': {
            headerRows: [
                [
                    { label: 'Jenis Pengembangan DTPR', rowspan: 2, colIdx: 0, width: '220px' },
                    { label: 'Nama DTPR', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Jumlah', colspan: 3, colIdx: 2 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 5, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 2, width: '90px' },
                    { label: 'TS-1', colIdx: 3, width: '90px' },
                    { label: 'TS', colIdx: 4, width: '90px' }
                ]
            ],
            leafHeaders: ['Jenis Pengembangan DTPR', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
            hasTotal: false,
            defaultRows: [
                { cells: ['', '', '', '', '', ''] }
            ]
        },
        '3.C.2': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Nama DTPR', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Judul Publikasi', rowspan: 2, colIdx: 2, width: '260px' },
                    { label: 'Jenis Publikasi', rowspan: 2, colIdx: 3, width: '150px' },
                    { label: 'Tahun Terbit (Beri tanda √)', colspan: 3, colIdx: 4 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 7, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 4, width: '90px' },
                    { label: 'TS-1', colIdx: 5, width: '90px' },
                    { label: 'TS', colIdx: 6, width: '90px' }
                ]
            ],
            leafHeaders: ['No', 'Nama DTPR', 'Judul Publikasi', 'Jenis Publikasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti']
        },
        '3.C.3': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Judul', rowspan: 2, colIdx: 1, width: '240px' },
                    { label: 'Jenis HKI', rowspan: 2, colIdx: 2, width: '150px' },
                    { label: 'Nama DTPR', rowspan: 2, colIdx: 3, width: '200px' },
                    { label: 'Tahun Perolehan (Beri Tanda √)', colspan: 3, colIdx: 4 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 7, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 4, width: '90px' },
                    { label: 'TS-1', colIdx: 5, width: '90px' },
                    { label: 'TS', colIdx: 6, width: '90px' }
                ]
            ],
            leafHeaders: ['No', 'Judul', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti']
        },
        '4.A.1': {
            leafHeaders: ['Nama Prasarana PkM', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
            hasTotal: false,
            note: '● Prasarana diisi nama laboratorium, nama bengkel dan lain-lain.',
            defaultRows: [
                { cells: ['', '', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', '', ''] }
            ]
        },
        '4.C.2': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Nama DTPR (Ketua)', rowspan: 2, colIdx: 1, width: '200px' },
                    { label: 'Judul', rowspan: 2, colIdx: 2, width: '260px' },
                    { label: 'Diseminasi (L/N/I)', rowspan: 2, colIdx: 3, width: '150px' },
                    { label: 'Tahun Terbit', colspan: 3, colIdx: 4 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 7, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 4, width: '90px' },
                    { label: 'TS-1', colIdx: 5, width: '90px' },
                    { label: 'TS', colIdx: 6, width: '90px' }
                ]
            ],
            leafHeaders: ['No', 'Nama DTPR (Ketua)', 'Judul', 'Diseminasi', 'TS-2', 'TS-1', 'TS', 'Link Bukti']
        },
        '4.C.3': {
            headerRows: [
                [
                    { label: 'No', rowspan: 2, colIdx: 0, width: '50px' },
                    { label: 'Judul PkM', rowspan: 2, colIdx: 1, width: '240px' },
                    { label: 'Jenis HKI', rowspan: 2, colIdx: 2, width: '150px' },
                    { label: 'Nama DTPR', rowspan: 2, colIdx: 3, width: '200px' },
                    { label: 'Tahun Perolehan (Beri Tanda √)', colspan: 3, colIdx: 4 },
                    { label: 'Link Bukti', rowspan: 2, colIdx: 7, width: '200px' }
                ],
                [
                    { label: 'TS-2', colIdx: 4, width: '90px' },
                    { label: 'TS-1', colIdx: 5, width: '90px' },
                    { label: 'TS', colIdx: 6, width: '90px' }
                ]
            ],
            leafHeaders: ['No', 'Judul PkM', 'Jenis HKI', 'Nama DTPR', 'TS-2', 'TS-1', 'TS', 'Link Bukti']
        },
        '5.1': {
            leafHeaders: ['No', 'Jenis Tata Kelola', 'Nama Sistem Informasi', 'Akses (Lokal/Internet)', 'Unit Pengelola', 'Link Bukti'],
            hasTotal: false,
            defaultRows: [
                { cells: ['1', 'Pendidikan', '', '', '', ''] },
                { cells: ['2', 'Keuangan', '', '', '', ''] },
                { cells: ['3', 'SDM', '', '', '', ''] },
                { cells: ['4', 'Sarana Prasarana', '', '', '', ''] },
                { cells: ['5', 'Sistem Penjaminan Mutu', '', '', '', ''] },
                { cells: ['6', 'Kerjasama', '', '', '', ''] },
                { cells: ['7', 'Kemahasiswaan', '', '', '', ''] }
            ]
        },
        '5.2': {
            leafHeaders: ['Nama Prasarana Pendidikan', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
            hasTotal: false,
            defaultRows: [
                { cells: ['', '', '', '', '', '', ''] },
                { cells: ['', '', '', '', '', '', ''] }
            ]
        },
        '6': {
            leafHeaders: ['Visi PT', 'Visi UPPS', 'Visi Keilmuan PS', 'Misi PT', 'Misi UPPS'],
            hasTotal: false,
            defaultRows: [
                { cells: ['', '', '', '', ''] }
            ]
        }
    };

    // Alpine component definition for LKPS Table Editor
    function lkpsTableEditor(tableCode, initialHeaders, initialRows, initialSummary) {
        const schema = TABLE_SCHEMAS[tableCode] || null;
        const defaultHeaders = (schema && schema.leafHeaders) ? schema.leafHeaders : (DEFAULT_TABLE_TEMPLATES[tableCode] || ['Kolom 1', 'Kolom 2', 'Kolom 3']);
        let startHeaders = [];
        if (initialHeaders && Array.isArray(initialHeaders) && initialHeaders.length > 0) {
            startHeaders = [...initialHeaders];
        } else {
            startHeaders = [...defaultHeaders];
        }

        // Header structure (multi-level merged headers)
        let initHeaderStructure = null;
        if (initialSummary && initialSummary.header_structure) {
            initHeaderStructure = JSON.parse(JSON.stringify(initialSummary.header_structure));
        } else if ((!initialHeaders || initialHeaders.length === 0 || (schema && schema.leafHeaders && schema.leafHeaders.length === initialHeaders.length)) && schema && schema.headerRows) {
            initHeaderStructure = JSON.parse(JSON.stringify(schema.headerRows));
        }

        // Extract initial column merge groups from header structure if any
        const initialGroups = [];
        if (initHeaderStructure && Array.isArray(initHeaderStructure[0])) {
            initHeaderStructure[0].forEach(col => {
                if (col.colspan && col.colspan > 1) {
                    initialGroups.push({
                        title: col.label,
                        startCol: col.colIdx,
                        endCol: col.colIdx + col.colspan - 1
                    });
                }
            });
        }

        // Normalize initial rows
        let startRows = [];
        if (initialRows && Array.isArray(initialRows) && initialRows.length > 0) {
            startRows = initialRows.map(r => {
                if (tableCode === '2.B.1' || tableCode === '2.B.2') {
                    return JSON.parse(JSON.stringify(r));
                }
                // Generic or tabular rows
                if (r && Array.isArray(r.cells)) {
                    const cells = [...r.cells];
                    while (cells.length < startHeaders.length) cells.push('');
                    return { ...r, cells };
                }
                if (Array.isArray(r)) {
                    const cells = [...r];
                    while (cells.length < startHeaders.length) cells.push('');
                    return { cells };
                }
                if (r && typeof r === 'object') {
                    const cells = startHeaders.map((h, i) => {
                        if (r[h] !== undefined) return r[h];
                        const key = Object.keys(r)[i];
                        return key !== undefined ? r[key] : '';
                    });
                    return { ...r, cells };
                }
                return { cells: startHeaders.map(() => '') };
            });
        } else {
            // Empty state: provide standard rows if available in schema
            if (schema && schema.defaultRows) {
                startRows = JSON.parse(JSON.stringify(schema.defaultRows));
            } else if (tableCode === '2.B.1') {
                const plMap = {};
                startHeaders.forEach(h => plMap[h] = 0);
                startRows = [{ no: 1, mata_kuliah: '', sks: 3, semester: 1, profil_lulusan: plMap }];
            } else if (tableCode === '2.B.2') {
                const plMap = {};
                startHeaders.forEach(h => plMap[h] = 0);
                startRows = [{ cpl: 'CPL 01', profil_lulusan: plMap }];
            } else {
                // Default 5 empty spreadsheet rows
                startRows = Array.from({ length: 5 }, () => ({ cells: startHeaders.map(() => '') }));
            }
        }

        return {
            tableCode: tableCode,
            headers: startHeaders,
            rows: startRows,
            headerStructure: initHeaderStructure,
            columnGroups: initialGroups,
            hasTotal: initialSummary && initialSummary.has_total !== undefined ? initialSummary.has_total : (schema && schema.hasTotal ? true : false),
            totalLabelCol: initialSummary && initialSummary.total_label_col !== undefined ? initialSummary.total_label_col : (schema && schema.totalLabelCol !== undefined ? schema.totalLabelCol : 1),
            sumCols: initialSummary && initialSummary.sum_cols ? initialSummary.sum_cols : (schema && schema.sumCols ? schema.sumCols : []),
            note: initialSummary && initialSummary.note ? initialSummary.note : (schema && schema.note ? schema.note : ''),
            summary: initialSummary ? {...initialSummary} : {},
            saving: false,
            saveStatusMessage: '',

            // Excel Active Cell & Formula Bar tracking
            activeRow: 0,
            activeCol: 0,
            activeCellVal: '',

            // Paste Assistant Modal State
            showPasteModal: false,
            pasteRawText: '',
            pasteMode: 'replace', // 'replace' | 'append'
            pasteHasHeaders: false,
            pastePreviewRows: [],
            pastePreviewCols: [],

            // Merge Columns & Rows Modal State
            showMergeModal: false,
            mergeTab: 'rows', // 'rows' | 'columns' | 'active'
            mergeGroupTitle: '',
            selectedColumnsToMerge: [],
            rowMergeColIdx: 0,
            selectedRowsToMerge: [],
            rowMergeValue: '',
            rowMerges: (initialSummary && Array.isArray(initialSummary.row_merges)) ? JSON.parse(JSON.stringify(initialSummary.row_merges)) : [],

            init() {
                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
                if (this.rows.length > 0 && this.rows[0].cells) {
                    this.activeCellVal = this.rows[0].cells[0] || '';
                }
            },

            // ── Active Cell Tracking & Formula Bar ──
            onCellFocus(r, c) {
                this.activeRow = r;
                this.activeCol = c;
                if (this.rows[r] && this.rows[r].cells) {
                    this.activeCellVal = this.rows[r].cells[c] || '';
                } else {
                    this.activeCellVal = '';
                }
            },

            updateActiveCellValue(val) {
                this.activeCellVal = val;
                if (this.rows[this.activeRow]) {
                    if (!this.rows[this.activeRow].cells) {
                        this.rows[this.activeRow].cells = this.headers.map(() => '');
                    }
                    this.rows[this.activeRow].cells[this.activeCol] = val;
                }
            },

            getActiveCellCoord() {
                const colLetter = String.fromCharCode(65 + (this.activeCol % 26));
                const rowNum = this.activeRow + 1;
                return colLetter + rowNum;
            },

            getActiveColName() {
                return this.headers[this.activeCol] || ('Kolom ' + (this.activeCol + 1));
            },

            focusCell(r, c) {
                if (r < 0) r = 0;
                if (r >= this.rows.length) r = this.rows.length - 1;
                if (c < 0) c = 0;
                if (c >= this.headers.length) c = this.headers.length - 1;

                this.activeRow = r;
                this.activeCol = c;
                if (this.rows[r] && this.rows[r].cells) {
                    this.activeCellVal = this.rows[r].cells[c] || '';
                }

                this.$nextTick(() => {
                    const el = document.getElementById(`cell-${r}-${c}`);
                    if (el) {
                        el.focus();
                        el.select();
                        el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                    }
                });
            },

            // ── Full Excel Keyboard Navigation ──
            handleKeyNavigation(e, rIdx, cIdx) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (e.shiftKey) {
                        // Shift + Enter: Move Up
                        if (rIdx > 0) this.focusCell(rIdx - 1, cIdx);
                    } else {
                        // Enter: Move Down (Auto-add row if at bottom, just like Excel!)
                        const nextR = rIdx + 1;
                        if (nextR >= this.rows.length) {
                            this.addRow();
                        }
                        this.focusCell(nextR, cIdx);
                    }
                } else if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        // Shift + Tab: Move Left
                        if (cIdx > 0) {
                            e.preventDefault();
                            this.focusCell(rIdx, cIdx - 1);
                        } else if (rIdx > 0) {
                            e.preventDefault();
                            this.focusCell(rIdx - 1, this.headers.length - 1);
                        }
                    } else {
                        // Tab: Move Right (Wrap to next row if at end)
                        if (cIdx + 1 < this.headers.length) {
                            e.preventDefault();
                            this.focusCell(rIdx, cIdx + 1);
                        } else {
                            e.preventDefault();
                            const nextR = rIdx + 1;
                            if (nextR >= this.rows.length) {
                                this.addRow();
                            }
                            this.focusCell(nextR, 0);
                        }
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextR = rIdx + 1;
                    if (nextR < this.rows.length) {
                        this.focusCell(nextR, cIdx);
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (rIdx > 0) {
                        this.focusCell(rIdx - 1, cIdx);
                    }
                } else if (e.key === 'ArrowRight') {
                    const input = e.target;
                    if (input && (input.selectionStart === input.value.length && input.selectionEnd === input.value.length)) {
                        if (cIdx + 1 < this.headers.length) {
                            e.preventDefault();
                            this.focusCell(rIdx, cIdx + 1);
                        }
                    }
                } else if (e.key === 'ArrowLeft') {
                    const input = e.target;
                    if (input && (input.selectionStart === 0 && input.selectionEnd === 0)) {
                        if (cIdx > 0) {
                            e.preventDefault();
                            this.focusCell(rIdx, cIdx - 1);
                        }
                    }
                } else if (e.key === 'Escape') {
                    if (e.target) e.target.blur();
                }
            },

            // ── Robust TSV / CSV Parser for Excel Clipboard ──
            parseTSV(text) {
                const rows = [];
                let currentRow = [];
                let currentCell = '';
                let insideQuote = false;

                for (let i = 0; i < text.length; i++) {
                    const char = text[i];
                    const nextChar = text[i + 1];

                    if (char === '"') {
                        if (insideQuote && nextChar === '"') {
                            currentCell += '"';
                            i++;
                        } else {
                            insideQuote = !insideQuote;
                        }
                    } else if (char === '\t' && !insideQuote) {
                        currentRow.push(currentCell.trim());
                        currentCell = '';
                    } else if ((char === '\r' || char === '\n') && !insideQuote) {
                        if (char === '\r' && nextChar === '\n') {
                            i++;
                        }
                        currentRow.push(currentCell.trim());
                        if (currentRow.some(c => c !== '')) {
                            rows.push(currentRow);
                        }
                        currentRow = [];
                        currentCell = '';
                    } else {
                        currentCell += char;
                    }
                }
                if (currentCell !== '' || currentRow.length > 0) {
                    currentRow.push(currentCell.trim());
                    if (currentRow.some(c => c !== '')) {
                        rows.push(currentRow);
                    }
                }
                return rows;
            },

            // ── Direct In-Cell Paste (Cmd+V / Ctrl+V) ──
            handleExcelPaste(e, startR, startC) {
                const clipboardData = (e.clipboardData || window.clipboardData);
                if (!clipboardData) return;
                const text = clipboardData.getData('text');
                if (!text || (!text.includes('\t') && !text.includes('\n'))) {
                    // Single string paste, let default input behavior happen
                    return;
                }
                e.preventDefault();

                const parsedRows = this.parseTSV(text);
                if (parsedRows.length === 0) return;

                for (let r = 0; r < parsedRows.length; r++) {
                    const targetR = startR + r;
                    while (targetR >= this.rows.length) {
                        this.addRow();
                    }

                    const cols = parsedRows[r];
                    for (let c = 0; c < cols.length; c++) {
                        const targetC = startC + c;
                        if (targetC < this.headers.length) {
                            if (!this.rows[targetR].cells) {
                                this.rows[targetR].cells = this.headers.map(() => '');
                            }
                            this.rows[targetR].cells[targetC] = cols[c];
                        }
                    }
                }

                // Update active cell val if affected
                if (this.rows[startR] && this.rows[startR].cells) {
                    this.activeCellVal = this.rows[startR].cells[startC] || '';
                }

                this.saveStatusMessage = `✓ Berhasil menempel ${parsedRows.length} baris data dari Excel!`;
                setTimeout(() => {
                    if (this.saveStatusMessage.startsWith('✓ Berhasil menempel')) {
                        this.saveStatusMessage = '';
                    }
                }, 4000);
            },

            // ── Dedicated Paste Assistant Modal ──
            openPasteModal() {
                this.pasteRawText = '';
                this.pastePreviewRows = [];
                this.pastePreviewCols = [];
                this.pasteHasHeaders = false;
                this.pasteMode = 'replace';
                this.showPasteModal = true;
            },

            closePasteModal() {
                this.showPasteModal = false;
            },

            onPasteInputChange() {
                if (!this.pasteRawText.trim()) {
                    this.pastePreviewRows = [];
                    this.pastePreviewCols = [];
                    return;
                }
                const parsed = this.parseTSV(this.pasteRawText);
                if (parsed.length > 0) {
                    this.pastePreviewRows = parsed;
                    const maxCols = Math.max(...parsed.map(r => r.length));
                    this.pastePreviewCols = Array.from({ length: maxCols }, (_, i) => this.headers[i] || `Kolom ${i + 1}`);
                }
            },

            applyPasteModal() {
                if (this.pastePreviewRows.length === 0) {
                    alert('Silakan tempel (Paste) data dari berkas Excel Anda terlebih dahulu.');
                    return;
                }
                let dataRows = [...this.pastePreviewRows];
                if (this.pasteHasHeaders && dataRows.length > 0) {
                    dataRows = dataRows.slice(1);
                }
                if (dataRows.length === 0) {
                    alert('Tidak ada baris data setelah melewati baris judul.');
                    return;
                }

                if (this.pasteMode === 'replace') {
                    this.rows = [];
                    this.rowMerges = [];
                }

                dataRows.forEach(rowCols => {
                    const cells = this.headers.map((_, i) => rowCols[i] !== undefined ? rowCols[i] : '');
                    this.rows.push({ cells });
                });

                if (this.rows.length > 0 && this.rows[0].cells) {
                    this.activeRow = 0;
                    this.activeCol = 0;
                    this.activeCellVal = this.rows[0].cells[0] || '';
                }

                this.showPasteModal = false;
                this.saveStatusMessage = `✓ Berhasil menerapkan ${dataRows.length} baris data dari Excel!`;
                setTimeout(() => {
                    if (this.saveStatusMessage.startsWith('✓ Berhasil menerapkan')) {
                        this.saveStatusMessage = '';
                    }
                }, 4000);
            },

            // ── Quick Row Operations ──
            addRowsBatch(count) {
                for (let i = 0; i < count; i++) {
                    this.addRow();
                }
                this.saveStatusMessage = `✓ Ditambahkan ${count} baris baru.`;
                setTimeout(() => { if (this.saveStatusMessage.includes('baris baru')) this.saveStatusMessage = ''; }, 3000);
            },

            removeEmptyRows() {
                const initialLen = this.rows.length;
                this.rows = this.rows.filter(r => {
                    if (r.is_total) return true;
                    if (!r.cells) return false;
                    return r.cells.some(c => String(c).trim() !== '');
                });
                if (this.rows.length === 0) {
                    this.addRow();
                }
                const removed = initialLen - this.rows.length;
                this.saveStatusMessage = removed > 0 ? `✓ ${removed} baris kosong berhasil dibersihkan.` : 'Tidak ada baris kosong.';
                setTimeout(() => { this.saveStatusMessage = ''; }, 3000);
            },

            clearAllRows() {
                if (!confirm('Kosongkan semua data baris pada tabel ini?')) return;
                this.rows = Array.from({ length: 5 }, () => ({ cells: this.headers.map(() => '') }));
                this.rowMerges = [];
                this.activeRow = 0;
                this.activeCol = 0;
                this.activeCellVal = '';
                this.saveStatusMessage = '✓ Tabel telah dikosongkan.';
                setTimeout(() => { this.saveStatusMessage = ''; }, 3000);
            },

            // ── Reset to Official LAM INFOKOM Template ──
            resetToDefaultColumns() {
                const sc = TABLE_SCHEMAS[this.tableCode];
                if (sc) {
                    if (!confirm(`Kembalikan Tabel ${this.tableCode} ke format standar resmi LAM INFOKOM? Data kustom dan gabungan kolom/baris yang belum disimpan akan dipulihkan ke template asli.`)) return;
                    this.headers = [...sc.leafHeaders];
                    this.headerStructure = sc.headerRows ? JSON.parse(JSON.stringify(sc.headerRows)) : null;
                    this.columnGroups = [];
                    this.rowMerges = [];
                    this.hasTotal = sc.hasTotal || false;
                    this.totalLabelCol = sc.totalLabelCol !== undefined ? sc.totalLabelCol : 1;
                    this.sumCols = sc.sumCols || [];
                    this.note = sc.note || '';
                    if (sc.defaultRows) {
                        this.rows = JSON.parse(JSON.stringify(sc.defaultRows));
                    } else {
                        this.rows = Array.from({ length: 5 }, () => ({ cells: this.headers.map(() => '') }));
                    }
                    this.activeRow = 0;
                    this.activeCol = 0;
                    this.activeCellVal = '';
                    this.saveStatusMessage = '✓ Format standar resmi LAM INFOKOM berhasil dipulihkan!';
                    setTimeout(() => { this.saveStatusMessage = ''; }, 4000);
                    return;
                }

                const def = DEFAULT_TABLE_TEMPLATES[this.tableCode];
                if (!def || def.length === 0) return;
                if (!confirm('Kembalikan judul kolom ke template standar LAM INFOKOM?')) return;
                this.headers = [...def];
                this.headerStructure = null;
                this.columnGroups = [];
                this.rows.forEach(r => {
                    if (!r.cells) r.cells = [];
                    while (r.cells.length < this.headers.length) {
                        r.cells.push('');
                    }
                });
            },

            // ── Generic Row Manipulation ──
            addRow() {
                const newCells = this.headers.map(() => '');
                this.rows.push({ cells: newCells });
            },

            insertRow(index, position = 'below') {
                const targetIdx = position === 'above' ? index : index + 1;
                const newCells = this.headers.map(() => '');
                this.rows.splice(targetIdx, 0, { cells: newCells });

                // Update rowMerges index
                this.rowMerges.forEach(m => {
                    if (m.startRow >= targetIdx) {
                        m.startRow += 1;
                        m.endRow += 1;
                    } else if (m.startRow < targetIdx && m.endRow >= targetIdx) {
                        m.endRow += 1;
                        m.rowspan += 1;
                    }
                });

                this.$nextTick(() => {
                    this.focusCell(targetIdx, 0);
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
                // Adjust row merges
                this.rowMerges = this.rowMerges.filter(m => !(m.startRow <= index && m.endRow >= index && m.rowspan <= 2));
                this.rowMerges.forEach(m => {
                    if (m.startRow > index) {
                        m.startRow -= 1;
                        m.endRow -= 1;
                    } else if (m.startRow <= index && m.endRow >= index) {
                        m.endRow -= 1;
                        m.rowspan -= 1;
                    }
                });

                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
            },

            setCellVal(row, cIdx, val) {
                if (!row.cells) row.cells = [];
                row.cells[cIdx] = val;
                if (this.activeRow === this.rows.indexOf(row) && this.activeCol === cIdx) {
                    this.activeCellVal = val;
                }
            },

            // ── Live Calculation & Total Row ──
            calcColumnSum(colIdx) {
                return this.rows.reduce((sum, r) => {
                    if (!r.cells || r.is_total) return sum;
                    const val = parseFloat(r.cells[colIdx]);
                    return sum + (isNaN(val) ? 0 : val);
                }, 0);
            },

            addTotalRow() {
                this.hasTotal = true;
                if (!Array.isArray(this.sumCols)) this.sumCols = [];
                const newCells = this.headers.map((_, i) => {
                    if (i === this.totalLabelCol) return 'Total';
                    if (this.sumCols.includes(i)) return String(this.calcColumnSum(i));
                    // Auto-detect numeric columns if sumCols not configured
                    const hasNumbers = this.rows.some(r => r.cells && !r.is_total && !isNaN(parseFloat(r.cells[i])) && String(r.cells[i]).trim() !== '');
                    if (hasNumbers && !this.sumCols.includes(i) && i !== 0 && i !== this.totalLabelCol) {
                        this.sumCols.push(i);
                        return String(this.calcColumnSum(i));
                    }
                    return '';
                });
                this.rows.push({ cells: newCells, is_total: true });
            },

            removeTotalRows() {
                this.rows = this.rows.filter(r => {
                    if (r.is_total) return false;
                    if (r.cells && r.cells.some(c => String(c).trim().toLowerCase() === 'total')) return false;
                    return true;
                });
                this.hasTotal = false;
                this.saveStatusMessage = '✓ Baris total telah dihapus.';
                setTimeout(() => { this.saveStatusMessage = ''; }, 3000);
            },

            // ── Generic & Dynamic Column Manipulation ──
            addColumn() {
                const colName = prompt('Masukkan judul kolom baru:', 'Kolom ' + (this.headers.length + 1));
                if (colName === null || colName.trim() === '') return;
                const finalName = colName.trim();
                this.headers.push(finalName);

                this.rows.forEach(r => {
                    if (!r.cells) r.cells = [];
                    r.cells.push('');
                });
            },

            removeColumn(index) {
                if (this.headers.length <= 1) {
                    alert('Tabel minimal harus memiliki 1 kolom.');
                    return;
                }
                const colName = this.headers[index];
                if (!confirm(`Hapus kolom "${colName}"? Data dalam kolom ini akan dihapus.`)) {
                    return;
                }
                this.headers.splice(index, 1);
                this.rows.forEach(r => {
                    if (r.cells) {
                        r.cells.splice(index, 1);
                    }
                });
                this.rowMerges = this.rowMerges.filter(m => m.colIdx !== index);
                this.rowMerges.forEach(m => {
                    if (m.colIdx > index) {
                        m.colIdx -= 1;
                    }
                });

                if (this.headerStructure) {
                    this.headerStructure = null;
                    this.columnGroups = [];
                }
            },

            // ── Interactive Merge Columns & Rows Feature ──
            openMergeModal(defaultTab = 'rows') {
                this.mergeTab = defaultTab;
                this.mergeGroupTitle = '';
                this.selectedColumnsToMerge = [];
                this.rowMergeColIdx = 0;
                this.selectedRowsToMerge = [];
                this.rowMergeValue = '';
                this.showMergeModal = true;
            },

            closeMergeModal() {
                this.showMergeModal = false;
            },

            // Row Selection & Merge (Rowspan)
            toggleRowToMerge(rIdx) {
                rIdx = parseInt(rIdx);
                const pos = this.selectedRowsToMerge.indexOf(rIdx);
                if (pos > -1) {
                    this.selectedRowsToMerge.splice(pos, 1);
                } else {
                    this.selectedRowsToMerge.push(rIdx);
                }
                this.selectedRowsToMerge.sort((a, b) => a - b);
                if (this.selectedRowsToMerge.length > 0 && !this.rowMergeValue) {
                    const firstRow = this.rows[this.selectedRowsToMerge[0]];
                    if (firstRow && firstRow.cells) {
                        this.rowMergeValue = firstRow.cells[this.rowMergeColIdx] || '';
                    }
                }
            },

            selectAllRowsToMerge() {
                this.selectedRowsToMerge = this.rows.map((_, i) => i);
            },

            clearRowsToMerge() {
                this.selectedRowsToMerge = [];
                this.rowMergeValue = '';
            },

            applyMultiRowMerge() {
                if (this.selectedRowsToMerge.length < 2) {
                    alert('Pilih minimal 2 baris yang ingin digabungkan.');
                    return;
                }
                const sorted = [...this.selectedRowsToMerge].sort((a, b) => a - b);
                const startRow = sorted[0];
                const endRow = sorted[sorted.length - 1];
                const colIdx = parseInt(this.rowMergeColIdx);
                const val = (this.rowMergeValue || '').trim();

                if (this.rows[startRow] && this.rows[startRow].cells) {
                    this.rows[startRow].cells[colIdx] = val;
                }

                this.rowMerges = this.rowMerges.filter(m => !(m.colIdx === colIdx && ((m.startRow >= startRow && m.startRow <= endRow) || (m.endRow >= startRow && m.endRow <= endRow))));

                this.rowMerges.push({
                    colIdx: colIdx,
                    colName: this.headers[colIdx] || ('Kolom ' + (colIdx + 1)),
                    startRow: startRow,
                    endRow: endRow,
                    rowspan: (endRow - startRow + 1),
                    value: val
                });

                this.showMergeModal = false;
                this.saveStatusMessage = `✓ Kolom "${this.headers[colIdx]}" baris ${startRow + 1} s/d ${endRow + 1} berhasil digabungkan!`;
                setTimeout(() => { this.saveStatusMessage = ''; }, 3000);
            },

            removeRowMerge(index) {
                this.rowMerges.splice(index, 1);
            },

            unmergeCell(rIdx, cIdx) {
                const idx = this.rowMerges.findIndex(m => m.colIdx === cIdx && m.startRow === rIdx);
                if (idx > -1) {
                    this.rowMerges.splice(idx, 1);
                    this.saveStatusMessage = '✓ Sel berhasil dipisahkan kembali.';
                    setTimeout(() => { this.saveStatusMessage = ''; }, 2500);
                }
            },

            clearAllRowMerges() {
                if (!confirm('Hapus semua penggabungan baris?')) return;
                this.rowMerges = [];
            },

            getCellRowMerge(rIdx, cIdx) {
                return this.rowMerges.find(m => m.colIdx === cIdx && m.startRow === rIdx) || null;
            },

            isCellRowHidden(rIdx, cIdx) {
                return this.rowMerges.some(m => m.colIdx === cIdx && rIdx > m.startRow && rIdx <= m.endRow);
            },

            // Column Selection & Merge (Colspan)
            toggleColumnToMerge(colIdx) {
                colIdx = parseInt(colIdx);
                const pos = this.selectedColumnsToMerge.indexOf(colIdx);
                if (pos > -1) {
                    this.selectedColumnsToMerge.splice(pos, 1);
                } else {
                    this.selectedColumnsToMerge.push(colIdx);
                }
                this.selectedColumnsToMerge.sort((a, b) => a - b);
            },

            selectAllColumnsToMerge() {
                this.selectedColumnsToMerge = this.headers.map((_, i) => i);
            },

            clearColumnsToMerge() {
                this.selectedColumnsToMerge = [];
            },

            rebuildHeaderStructure() {
                if (!this.columnGroups || this.columnGroups.length === 0) {
                    this.headerStructure = null;
                    return;
                }

                const tier0 = [];
                const tier1 = [];
                const sortedGroups = [...this.columnGroups].sort((a, b) => a.startCol - b.startCol);

                let colIdx = 0;
                while (colIdx < this.headers.length) {
                    const group = sortedGroups.find(g => g.startCol === colIdx);
                    if (group) {
                        const count = (group.endCol - group.startCol + 1);
                        tier0.push({
                            label: group.title,
                            colspan: count,
                            colIdx: group.startCol
                        });
                        for (let i = group.startCol; i <= group.endCol; i++) {
                            tier1.push({
                                label: this.headers[i] || ('Kolom ' + (i + 1)),
                                colIdx: i
                            });
                        }
                        colIdx = group.endCol + 1;
                    } else {
                        tier0.push({
                            label: this.headers[colIdx] || ('Kolom ' + (colIdx + 1)),
                            rowspan: 2,
                            colIdx: colIdx
                        });
                        colIdx++;
                    }
                }

                this.headerStructure = [tier0, tier1];
            },

            applyMultiColumnMerge() {
                if (this.selectedColumnsToMerge.length < 2) {
                    alert('Pilih minimal 2 kolom yang ingin digabungkan.');
                    return;
                }
                if (!this.mergeGroupTitle || this.mergeGroupTitle.trim() === '') {
                    alert('Silakan masukkan judul grup / header induk kolom yang digabung.');
                    return;
                }

                const sorted = [...this.selectedColumnsToMerge].sort((a, b) => a - b);
                const startIdx = sorted[0];
                const endIdx = sorted[sorted.length - 1];
                const title = this.mergeGroupTitle.trim();

                if (!Array.isArray(this.columnGroups)) this.columnGroups = [];
                this.columnGroups = this.columnGroups.filter(g => !(g.startCol <= endIdx && g.endCol >= startIdx));

                this.columnGroups.push({
                    title: title,
                    startCol: startIdx,
                    endCol: endIdx
                });

                this.rebuildHeaderStructure();
                this.showMergeModal = false;
            },

            removeColumnGroup(index) {
                this.columnGroups.splice(index, 1);
                this.rebuildHeaderStructure();
            },

            unmergeColumns() {
                if (!confirm('Pisahkan semua gabungan kolom dan kembalikan ke judul kolom satu tingkat?')) return;
                this.columnGroups = [];
                this.headerStructure = null;
            },

            // ── Tabel 2.B.1 Helpers ──
            addRow2B1() {
                const plMap = {};
                this.headers.forEach(h => plMap[h] = 0);
                this.rows.push({
                    no: this.rows.length + 1,
                    mata_kuliah: '',
                    sks: 3,
                    semester: 1,
                    profil_lulusan: plMap
                });
                this.calcSummary2B1();
            },

            addPlColumn() {
                const nextNum = this.headers.length + 1;
                const newPl = 'PL ' + nextNum;
                this.headers.push(newPl);
                this.rows.forEach(r => {
                    if (!r.profil_lulusan) r.profil_lulusan = {};
                    r.profil_lulusan[newPl] = 0;
                });
            },

            removePlColumn(index) {
                const plName = this.headers[index];
                this.headers.splice(index, 1);
                this.rows.forEach(r => {
                    if (r.profil_lulusan && r.profil_lulusan[plName] !== undefined) {
                        delete r.profil_lulusan[plName];
                    }
                });
            },

            togglePlCheck(row, plName, isChecked) {
                if (!row.profil_lulusan) row.profil_lulusan = {};
                row.profil_lulusan[plName] = isChecked ? 1 : 0;
            },

            calcSummary2B1() {
                const totalSks = this.rows.reduce((sum, r) => sum + (parseFloat(r.sks) || 0), 0);
                this.summary.total_sks = totalSks;
                this.summary.total_mk = this.rows.length;
            },

            // ── Tabel 2.B.2 Helpers ──
            addRow2B2() {
                const plMap = {};
                this.headers.forEach(h => plMap[h] = 0);
                const nextNum = this.rows.length + 1;
                this.rows.push({
                    cpl: 'CPL ' + (nextNum < 10 ? '0' + nextNum : nextNum),
                    profil_lulusan: plMap
                });
            },

            // ── Save to Server ──
            async saveTableData() {
                this.saving = true;
                this.saveStatusMessage = 'Menyimpan data...';

                let payloadRows = [];
                if (this.tableCode === '2.B.1' || this.tableCode === '2.B.2') {
                    payloadRows = this.rows;
                } else {
                    payloadRows = this.rows.map(r => {
                        const rowObj = { cells: r.cells || [] };
                        if (r.is_total) rowObj.is_total = true;
                        this.headers.forEach((h, idx) => {
                            rowObj[h] = (r.cells && r.cells[idx] !== undefined) ? r.cells[idx] : '';
                        });
                        return rowObj;
                    });
                }

                const summaryPayload = {
                    ...this.summary,
                    header_structure: this.headerStructure,
                    row_merges: this.rowMerges,
                    has_total: this.hasTotal,
                    total_label_col: this.totalLabelCol,
                    sum_cols: this.sumCols,
                    note: this.note,
                };

                try {
                    const response = await fetch('/unit/lkps/' + encodeURIComponent(this.tableCode) + '/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            table_code: this.tableCode,
                            dynamic_headers: this.headers,
                            rows: payloadRows,
                            summary_data: summaryPayload,
                        })
                    });

                    const res = await response.json();
                    if (res.success) {
                        this.saveStatusMessage = '✓ Data berhasil disimpan (' + new Date().toLocaleTimeString('id-ID') + ')';
                        setTimeout(() => {
                            window.location.reload();
                        }, 700);
                    } else {
                        this.saveStatusMessage = 'Gagal: ' + (res.message || 'Terjadi kesalahan');
                    }
                } catch (err) {
                    this.saveStatusMessage = 'Kesalahan jaringan: ' + err.message;
                } finally {
                    this.saving = false;
                }
            }
        };
    }
</script>
