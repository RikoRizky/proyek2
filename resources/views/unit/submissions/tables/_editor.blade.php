@php
    $rowsJson = json_encode($lkps?->table_rows ?? []);
    $headersJson = json_encode($lkps?->dynamic_headers ?? []);
    $summaryJson = json_encode($lkps?->summary_data ?? []);
@endphp

<div x-data="lkpsTableEditor('{{ $tableCode }}', {{ $headersJson }}, {{ $rowsJson }}, {{ $summaryJson }})" class="space-y-4">

    @if ($tableCode === '2.B.1')
        {{-- Custom Interface for Tabel 2.B.1 (Tabel Isi Pembelajaran) --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel 2.B.1: Isi Pembelajaran</h4>
                    <p class="text-xs text-slate-500">Mata kuliah, SKS, semester, dan pemenuhan Profil Lulusan (PL).</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="addPlColumn()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                        + Tambah Kolom PL
                    </button>
                    <button type="button" @click="addRow2B1()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                        + Tambah Mata Kuliah
                    </button>
                </div>
            </div>

            {{-- Table Grid --}}
            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3 w-12 text-center">No</th>
                            <th class="py-2.5 px-3 min-w-[200px]">Mata Kuliah</th>
                            <th class="py-2.5 px-3 w-20 text-center">SKS</th>
                            <th class="py-2.5 px-3 w-24 text-center">Semester</th>
                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                <th class="py-2.5 px-3 text-center min-w-[70px]">
                                    <div class="flex items-center justify-center gap-1">
                                        <span x-text="pl"></span>
                                        <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold" title="Hapus kolom PL">×</button>
                                    </div>
                                </th>
                            </template>
                            <th class="py-2.5 px-3 w-12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-2 px-3 text-center font-bold text-slate-400" x-text="idx + 1"></td>
                                <td class="py-2 px-3">
                                    <input type="text" x-model="row.mata_kuliah" placeholder="Nama mata kuliah..."
                                        class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" min="0" max="10" x-model.number="row.sks" @input="calcSummary2B1()"
                                        class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" min="1" max="8" x-model.number="row.semester"
                                        class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                    <td class="py-2 px-3 text-center">
                                        <input type="checkbox"
                                            :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                            @change="togglePlCheck(row, pl, $event.target.checked)"
                                            class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                    </td>
                                </template>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td :colspan="5 + headers.length" class="text-center py-6 text-slate-400">
                                Belum ada mata kuliah. Klik tombol <span class="font-bold text-violet-600">+ Tambah Mata Kuliah</span> atau <span class="font-bold text-emerald-600">Import Excel</span> di atas.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200 bg-slate-50/80 font-bold text-slate-800">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-right">Total:</td>
                            <td class="py-2.5 px-3 text-center text-violet-700" x-text="summary.total_sks || 0"></td>
                            <td :colspan="2 + headers.length" class="py-2.5 px-3 text-slate-500 font-normal">
                                <span x-text="rows.length"></span> mata kuliah terdaftar
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @elseif ($tableCode === '2.B.2')
        {{-- Custom Interface for Tabel 2.B.2 (Pemetaan CPL dan Profil Lulusan) --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel 2.B.2: Pemetaan CPL dan Profil Lulusan</h4>
                    <p class="text-xs text-slate-500">Beri centang (✓) pada Profil Lulusan yang dicapai oleh tiap CPL.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="addPlColumn()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                        + Tambah Kolom PL
                    </button>
                    <button type="button" @click="addRow2B2()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                        + Tambah CPL
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3 min-w-[200px]">Capaian Pembelajaran Lulusan (CPL)</th>
                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                <th class="py-2.5 px-3 text-center min-w-[70px]">
                                    <div class="flex items-center justify-center gap-1">
                                        <span x-text="pl"></span>
                                        <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold" title="Hapus kolom PL">×</button>
                                    </div>
                                </th>
                            </template>
                            <th class="py-2.5 px-3 w-12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-2 px-3">
                                    <input type="text" x-model="row.cpl" placeholder="Contoh: CPL 01 - Mampu merancang sistem..."
                                        class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                    <td class="py-2 px-3 text-center">
                                        <input type="checkbox"
                                            :checked="row.profil_lulusan && (row.profil_lulusan[pl] == 1 || row.profil_lulusan[pl] === true)"
                                            @change="togglePlCheck(row, pl, $event.target.checked)"
                                            class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                    </td>
                                </template>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td :colspan="2 + headers.length" class="text-center py-6 text-slate-400">
                                Belum ada baris CPL. Klik <span class="font-bold text-violet-600">+ Tambah CPL</span>.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    @elseif ($tableCode === '2.B.3')
        {{-- Custom Interface for Tabel 2.B.3 (Peta Pemenuhan CPL) --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel 2.B.3: Peta Pemenuhan CPL</h4>
                    <p class="text-xs text-slate-500">Pemetaan CPL, CPMK, dan kode mata kuliah per semester (Semester 1 s/d 8).</p>
                </div>
                <button type="button" @click="addRow2B3()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                    + Tambah Baris CPL/CPMK
                </button>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-2.5 w-24">CPL</th>
                            <th class="py-2.5 px-2.5 w-28">CPMK</th>
                            <template x-for="i in 8" :key="i">
                                <th class="py-2.5 px-2 text-center min-w-[70px]" x-text="'Smt ' + i"></th>
                            </template>
                            <th class="py-2.5 px-2 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-1.5 px-2">
                                    <input type="text" x-model="row.cpl" placeholder="CPL 01"
                                        class="w-full rounded-md border-slate-200 text-xs py-1 px-1.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <td class="py-1.5 px-2">
                                    <input type="text" x-model="row.cpmk" placeholder="CPMK 01"
                                        class="w-full rounded-md border-slate-200 text-xs py-1 px-1.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                </td>
                                <template x-for="i in 8" :key="i">
                                    <td class="py-1.5 px-1.5">
                                        <input type="text"
                                            :value="row.semesters ? (row.semesters['semester_' + i] || '') : ''"
                                            @input="setSemesterVal(row, i, $event.target.value)"
                                            placeholder="MK..."
                                            class="w-full rounded-md border-slate-200 text-xs py-1 px-1 text-center focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                    </td>
                                </template>
                                <td class="py-1.5 px-2 text-center">
                                    <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600">×</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td colspan="11" class="text-center py-6 text-slate-400">
                                Belum ada baris. Klik <span class="font-bold text-violet-600">+ Tambah Baris CPL/CPMK</span>.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- Generic Interactive Table Grid for other tables (1.A.1, 1.A.4, 3.A.1, etc.) --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel {{ $tableCode }}</h4>
                    <p class="text-xs text-slate-500">Isi data langsung pada baris-baris di bawah.</p>
                </div>
                <button type="button" @click="addGenericRow()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                    + Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <template x-for="(col, cIdx) in headers" :key="cIdx">
                                <th class="py-2.5 px-3 min-w-[120px]" x-text="col"></th>
                            </template>
                            <th class="py-2.5 px-3 w-12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-2 px-3 text-center text-slate-400 font-bold" x-text="idx + 1"></td>
                                <template x-for="(col, cIdx) in headers" :key="cIdx">
                                    <td class="py-2 px-3">
                                        <input type="text"
                                            :value="row[col] !== undefined ? row[col] : (Object.values(row)[cIdx] || '')"
                                            @input="setGenericColVal(row, col, cIdx, $event.target.value)"
                                            class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                    </td>
                                </template>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(idx)" class="text-rose-400 hover:text-rose-600 p-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td :colspan="headers.length + 2" class="text-center py-6 text-slate-400">
                                Belum ada baris data. Klik <span class="font-bold text-violet-600">+ Tambah Baris</span>.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Bottom Action: Simpan Data ke Server --}}
    <div class="flex items-center justify-between gap-3 pt-2">
        <p class="text-xs text-slate-500" x-text="saveStatusMessage"></p>
        <button type="button" @click="saveTableData()" :disabled="saving"
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-violet-500/20 hover:from-violet-700 hover:to-indigo-700 transition disabled:opacity-50">
            <svg x-show="!saving" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <svg x-show="saving" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan & Ajukan Data Tabel'"></span>
        </button>
    </div>
</div>

