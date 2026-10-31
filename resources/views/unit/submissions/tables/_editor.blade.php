@php
    $rowsJson = json_encode($lkps?->table_rows ?? []);
    $headersJson = json_encode($lkps?->dynamic_headers ?? []);
    $summaryJson = json_encode($lkps?->summary_data ?? []);
@endphp

<div x-data="lkpsTableEditor('{{ $tableCode }}', {{ $headersJson }}, {{ $rowsJson }}, {{ $summaryJson }})" class="space-y-4">

    @if ($tableCode === '2.B.1')
        {{-- Custom Interface for Tabel 2.B.1 (Tabel Isi Pembelajaran) --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel 2.B.1: Isi Pembelajaran</h4>
                    <p class="text-xs text-slate-500">Mata kuliah, SKS, semester, dan pemetaan Profil Lulusan (PL). Klik judul kolom PL untuk mengubah nama.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="addPlColumn()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Tambah Kolom PL
                    </button>
                    <button type="button" @click="addRow2B1()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
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
                            <th class="py-2.5 px-3 min-w-[220px]">Mata Kuliah</th>
                            <th class="py-2.5 px-3 w-20 text-center">SKS</th>
                            <th class="py-2.5 px-3 w-24 text-center">Semester</th>
                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                <th class="py-2.5 px-2 text-center min-w-[100px] bg-violet-50/40 border-l border-violet-100/80">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <input type="text" x-model="headers[plIdx]"
                                            class="w-full text-center bg-transparent border border-transparent hover:border-slate-300 focus:border-violet-500 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                            title="Klik untuk mengubah nama Profil Lulusan">
                                        <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1" title="Hapus kolom PL">×</button>
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
                                    <td class="py-2 px-2 text-center bg-violet-50/20 border-l border-violet-100/50">
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
        <div class="rounded-2xl border border-violet-100 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel 2.B.2: Pemetaan CPL dan Profil Lulusan</h4>
                    <p class="text-xs text-slate-500">Beri centang (✓) pada Profil Lulusan yang dicapai oleh tiap CPL. Klik judul kolom PL untuk mengubah nama.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="addPlColumn()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Tambah Kolom PL
                    </button>
                    <button type="button" @click="addRow2B2()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Tambah CPL
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3 min-w-[240px]">Capaian Pembelajaran Lulusan (CPL)</th>
                            <template x-for="(pl, plIdx) in headers" :key="plIdx">
                                <th class="py-2.5 px-2 text-center min-w-[100px] bg-violet-50/40 border-l border-violet-100/80">
                                    <div class="flex items-center justify-center gap-1">
                                        <input type="text" x-model="headers[plIdx]"
                                            class="w-full text-center bg-transparent border border-transparent hover:border-slate-300 focus:border-violet-500 focus:bg-white text-xs font-bold text-violet-900 py-1 px-1 rounded transition"
                                            title="Klik untuk mengubah nama Profil Lulusan">
                                        <button type="button" @click="removePlColumn(plIdx)" class="text-slate-300 hover:text-rose-500 font-bold px-1">×</button>
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
                                    <td class="py-2 px-2 text-center bg-violet-50/20 border-l border-violet-100/50">
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

    @else
        {{-- Generic Interactive Spreadsheet Grid for ALL OTHER 28 TABLES --}}
        <div class="rounded-2xl border border-violet-100 bg-white p-4 sm:p-5 shadow-sm">
            {{-- Toolbar: Tambah Kolom, Tambah Baris, Reset --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Editor Tabel {{ $tableCode }}</h4>
                    <p class="text-xs text-slate-500">Anda dapat mengubah judul kolom langsung di header, menambah kolom baru, atau menambah baris data.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="resetToDefaultColumns()" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition" title="Kembalikan nama-nama kolom ke template standar LAM INFOKOM">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        Format Standar
                    </button>
                    <button type="button" @click="addColumn()" class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50/80 px-3 py-1.5 text-xs font-bold text-violet-700 hover:bg-violet-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Tambah Kolom
                    </button>
                    <button type="button" @click="addRow()" class="inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-violet-700 transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Tambah Baris
                    </button>
                </div>
            </div>

            {{-- Table Grid --}}
            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center font-bold text-slate-400">No</th>
                            <template x-for="(col, cIdx) in headers" :key="cIdx">
                                <th class="py-2 px-2.5 min-w-[150px] border-r border-slate-200/60 group">
                                    <div class="flex items-center justify-between gap-1.5 rounded-lg border border-transparent hover:border-slate-300 focus-within:border-violet-500 focus-within:bg-white bg-slate-100/70 p-1 transition">
                                        <input type="text" x-model="headers[cIdx]"
                                            class="w-full bg-transparent border-0 text-xs font-bold text-slate-800 p-0 focus:ring-0 focus:outline-none"
                                            title="Klik untuk mengubah nama judul kolom ini">
                                        <button type="button" @click="removeColumn(cIdx)"
                                            class="text-slate-300 hover:text-rose-600 p-0.5 rounded transition shrink-0"
                                            title="Hapus kolom ini">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </th>
                            </template>
                            <th class="py-2 px-2 w-10 text-center">
                                <button type="button" @click="addColumn()" class="flex h-6 w-6 items-center justify-center rounded-lg bg-violet-100 text-violet-700 hover:bg-violet-200 transition font-bold" title="Tambah kolom di akhir">+</button>
                            </th>
                            <th class="py-2.5 px-3 w-12 text-center text-slate-400">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, rIdx) in rows" :key="rIdx">
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-2 px-3 text-center text-slate-400 font-bold" x-text="rIdx + 1"></td>
                                <template x-for="(col, cIdx) in headers" :key="cIdx">
                                    <td class="py-1.5 px-2">
                                        <input type="text"
                                            :value="row.cells ? row.cells[cIdx] : ''"
                                            @input="setCellVal(row, cIdx, $event.target.value)"
                                            :placeholder="'Isi ' + col + '...'"
                                            class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                    </td>
                                </template>
                                <td class="py-2 px-2 text-center text-slate-300">·</td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(rIdx)" class="text-rose-400 hover:text-rose-600 p-1" title="Hapus baris ini">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td :colspan="headers.length + 3" class="text-center py-6 text-slate-400">
                                Belum ada baris data. Klik tombol <span class="font-bold text-violet-600">+ Tambah Baris</span> untuk mengisi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-2.5 flex items-center justify-between text-xs text-slate-400">
                <p>💡 Tip: Anda bisa klik judul kolom di atas untuk mengganti nama kolom kapan saja.</p>
                <p><span x-text="rows.length"></span> baris · <span x-text="headers.length"></span> kolom</p>
            </div>
        </div>
    @endif

    {{-- Bottom Action: Simpan Data ke Server --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
        <p class="text-xs text-slate-500 font-medium" x-text="saveStatusMessage"></p>
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
