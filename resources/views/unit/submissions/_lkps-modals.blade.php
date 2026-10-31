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
        '1.A.4': ['Nama DTPR', 'SKS Pengajaran PS Sendiri', 'SKS PS Lain PT Sendiri', 'SKS PT Lain', 'SKS Penelitian', 'SKS PkM', 'SKS Manajemen PT Sendiri', 'SKS Manajemen PT Lain', 'Total SKS'],
        '1.A.5': ['Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK', 'Unit Kerja'],
        '1.B':   ['Unit SPMI', 'Nama Unit SPMI', 'Dokumen SPMI', 'Auditor Certified', 'Auditor Non-Certified', 'Frekuensi Audit/Tahun', 'Bukti Certified', 'Laporan Audit'],
        '2.A.1': ['TS', 'Daya Tampung', 'Pendaftar Reguler', 'Pendaftar Afirmasi', 'Pendaftar Khusus', 'Mhs Baru Reguler', 'Mhs Baru RPL', 'Mhs Aktif Reguler', 'Mhs Aktif RPL'],
        '2.A.2': ['Asal Mahasiswa', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '2.A.3': ['Kategori Mahasiswa Asing', 'TS-2', 'TS-1', 'TS', 'Jumlah'],
        '2.B.1': ['PL 1', 'PL 2', 'PL 3', 'PL 4'],
        '2.B.2': ['PL 1', 'PL 2', 'PL 3', 'PL 4'],
        '2.B.3': ['CPL', 'CPMK', 'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8'],
        '2.B.4': ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Rata-rata Waktu Tunggu (Bulan)'],
        '2.B.5': ['Tahun Lulus', 'Jumlah Lulusan', 'Jumlah Terlacak', 'Profesi Infokom', 'Profesi Non-Infokom', 'Lingkup Tempat Kerja'],
        '2.B.6': ['Jenis Kemampuan', 'Sangat Baik (%)', 'Baik (%)', 'Cukup (%)', 'Kurang (%)', 'Rencana Tindak Lanjut'],
        '2.C':   ['Tahun Akademik', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '2.D':   ['Sumber Rekognisi', 'Jenis Pengakuan (Rekognisi)', 'Tahun Akademik', 'Link Bukti'],
        '3.A.1': ['Nama Prasarana', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '3.A.2': ['Nama DTPR (Ketua)', 'Judul Penelitian', 'Mhs Terlibat', 'Jenis Hibah', 'Tingkat (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '3.A.3': ['Tahun Akademik', 'TS-2', 'TS-1', 'TS', 'Link Bukti'],
        '3.C.1': ['Judul Kerjasama', 'Mitra Kerja Sama', 'Tingkat (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '3.C.2': ['Nama DTPR', 'Judul Publikasi', 'Jenis Publikasi', 'Tahun Terbit (TS-2/TS-1/TS)', 'Link Bukti'],
        '3.C.3': ['Judul HKI', 'Jenis HKI', 'Nama DTPR', 'Tahun Perolehan', 'Link Bukti'],
        '4.A.1': ['Nama Prasarana PkM', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '4.A.2': ['Nama DTPR (Ketua PkM)', 'Judul PkM', 'Mhs Terlibat', 'Jenis Hibah', 'Tingkat (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '4.C.1': ['Judul Kerjasama PkM', 'Mitra Kerja Sama', 'Tingkat (L/N/I)', 'Durasi (Tahun)', 'Dana TS-2', 'Dana TS-1', 'Dana TS', 'Link Bukti'],
        '4.C.2': ['Nama DTPR (Ketua)', 'Judul PkM', 'Diseminasi (L/N/I)', 'Tahun (TS-2/TS-1/TS)', 'Link Bukti'],
        '4.C.3': ['Judul HKI PkM', 'Jenis HKI', 'Nama DTPR', 'Tahun Perolehan', 'Link Bukti'],
        '5.1':   ['Jenis Tata Kelola', 'Nama Sistem Informasi', 'Akses (Lokal/Internet)', 'Unit Pengelola', 'Link Bukti'],
        '5.2':   ['Nama Prasarana Pendidikan', 'Daya Tampung', 'Luas Ruang (m²)', 'Kepemilikan (M/W)', 'Lisensi (L/P/T)', 'Perangkat', 'Link Bukti'],
        '6':     ['Visi PT', 'Visi UPPS', 'Visi Keilmuan PS', 'Misi PT', 'Misi UPPS']
    };

    // Alpine component definition for LKPS Table Editor
    function lkpsTableEditor(tableCode, initialHeaders, initialRows, initialSummary) {
        // Determine start headers
        const defaultHeaders = DEFAULT_TABLE_TEMPLATES[tableCode] || ['Kolom 1', 'Kolom 2', 'Kolom 3'];
        let startHeaders = [];
        if (initialHeaders && Array.isArray(initialHeaders) && initialHeaders.length > 0) {
            startHeaders = [...initialHeaders];
        } else {
            startHeaders = [...defaultHeaders];
        }

        // Normalize initial rows
        let startRows = [];
        if (initialRows && Array.isArray(initialRows) && initialRows.length > 0) {
            startRows = initialRows.map(r => {
                if (tableCode === '2.B.1' || tableCode === '2.B.2') {
                    return JSON.parse(JSON.stringify(r));
                }
                // Generic or 2.B.3
                if (r && Array.isArray(r.cells)) {
                    const cells = [...r.cells];
                    while (cells.length < startHeaders.length) cells.push('');
                    return { cells };
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
                    return { cells };
                }
                return { cells: startHeaders.map(() => '') };
            });
        } else {
            // Empty state: provide 1 row ready for input
            if (tableCode === '2.B.1') {
                const plMap = {};
                startHeaders.forEach(h => plMap[h] = 0);
                startRows = [{ no: 1, mata_kuliah: '', sks: 3, semester: 1, profil_lulusan: plMap }];
            } else if (tableCode === '2.B.2') {
                const plMap = {};
                startHeaders.forEach(h => plMap[h] = 0);
                startRows = [{ cpl: 'CPL 01', profil_lulusan: plMap }];
            } else {
                startRows = [{ cells: startHeaders.map(() => '') }];
            }
        }

        return {
            tableCode: tableCode,
            headers: startHeaders,
            rows: startRows,
            summary: initialSummary ? {...initialSummary} : {},
            saving: false,
            saveStatusMessage: '',

            init() {
                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
            },

            // ── Generic & Dynamic Column Manipulation ──
            addColumn() {
                const colName = prompt('Masukkan judul kolom baru:', 'Kolom ' + (this.headers.length + 1));
                if (colName === null || colName.trim() === '') return;
                const finalName = colName.trim();
                this.headers.push(finalName);

                // Add empty cell to every row
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
            },

            resetToDefaultColumns() {
                const def = DEFAULT_TABLE_TEMPLATES[this.tableCode];
                if (!def || def.length === 0) return;
                if (!confirm('Kembalikan judul kolom ke template standar LAM INFOKOM?')) return;
                this.headers = [...def];
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

            removeRow(index) {
                this.rows.splice(index, 1);
                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
            },

            setCellVal(row, cIdx, val) {
                if (!row.cells) row.cells = [];
                row.cells[cIdx] = val;
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

                // Format payload rows so both cells and header key/values exist
                let payloadRows = [];
                if (this.tableCode === '2.B.1' || this.tableCode === '2.B.2') {
                    payloadRows = this.rows;
                } else {
                    payloadRows = this.rows.map(r => {
                        const rowObj = { cells: r.cells || [] };
                        this.headers.forEach((h, idx) => {
                            rowObj[h] = (r.cells && r.cells[idx] !== undefined) ? r.cells[idx] : '';
                        });
                        return rowObj;
                    });
                }

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
                            summary_data: this.summary,
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
