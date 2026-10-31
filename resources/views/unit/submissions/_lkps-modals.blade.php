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

    // Alpine component definition for LKPS Table Editor
    function lkpsTableEditor(tableCode, initialHeaders, initialRows, initialSummary) {
        return {
            tableCode: tableCode,
            headers: initialHeaders && initialHeaders.length ? [...initialHeaders] : (tableCode === '2.B.1' || tableCode === '2.B.2' ? ['PL 1', 'PL 2', 'PL 3', 'PL 4'] : ['Kolom 1', 'Kolom 2', 'Kolom 3']),
            rows: initialRows && initialRows.length ? JSON.parse(JSON.stringify(initialRows)) : [],
            summary: initialSummary ? {...initialSummary} : {},
            saving: false,
            saveStatusMessage: '',

            init() {
                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
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

            // ── Tabel 2.B.3 Helpers ──
            addRow2B3() {
                const semMap = {};
                for (let i = 1; i <= 8; i++) {
                    semMap['semester_' + i] = '';
                }
                const nextNum = this.rows.length + 1;
                this.rows.push({
                    cpl: 'CPL ' + (nextNum < 10 ? '0' + nextNum : nextNum),
                    cpmk: 'CPMK ' + (nextNum < 10 ? '0' + nextNum : nextNum),
                    semesters: semMap
                });
            },

            setSemesterVal(row, semIndex, value) {
                if (!row.semesters) row.semesters = {};
                row.semesters['semester_' + semIndex] = value;
            },

            // ── Generic Table Helpers ──
            addGenericRow() {
                const rowObj = {};
                this.headers.forEach(h => rowObj[h] = '');
                this.rows.push(rowObj);
            },

            setGenericColVal(row, colName, colIdx, val) {
                row[colName] = val;
            },

            removeRow(index) {
                this.rows.splice(index, 1);
                if (this.tableCode === '2.B.1') {
                    this.calcSummary2B1();
                }
            },

            // ── Save to Server ──
            async saveTableData() {
                this.saving = true;
                this.saveStatusMessage = 'Menyimpan data...';

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
                            rows: this.rows,
                            summary_data: this.summary,
                        })
                    });

                    const res = await response.json();
                    if (res.success) {
                        this.saveStatusMessage = '✓ Data berhasil disimpan (' + new Date().toLocaleTimeString('id-ID') + ')';
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
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

