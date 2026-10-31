<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Admin</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Diskusi Masuk</h1>
                <p class="mt-1 text-sm text-slate-600">Semua formulir diskusi yang dikirim oleh calon pengguna</p>
            </div>
            <div class="shrink-0 text-right">
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-violet-50 px-4 py-2 text-sm font-semibold text-violet-700 ring-1 ring-violet-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                    {{ $discussions->total() }} total masuk
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Alpine Detail Modal --}}
    <div
        x-data="{
            open: false,
            item: null,
            show(data) {
                this.item = data;
                this.open = true;
            },
            getWhatsAppUrl() {
                if (!this.item || !this.item.whatsapp) return '#';
                
                // Ambil nomor whatsapp & bersihkan
                let phone = this.item.whatsapp.replace(/\D/g, '');
                if (phone.startsWith('0')) {
                    phone = '62' + phone.slice(1);
                } else if (!phone.startsWith('62')) {
                    phone = '62' + phone;
                }

                // Susun daftar kebutuhan utama
                let kebutuhanText = '';
                if (Array.isArray(this.item.kebutuhan) && this.item.kebutuhan.length > 0) {
                    kebutuhanText = this.item.kebutuhan.map((keb, i) => `  ${i + 1}. ${keb}`).join('\n');
                } else {
                    kebutuhanText = '  -';
                }

                if (this.item.kebutuhan_lainnya) {
                    kebutuhanText += `\n  Keterangan lainnya: ${this.item.kebutuhan_lainnya}`;
                }

                const message = `Halo *${this.item.nama}* dari *${this.item.perusahaan}*,\n\nKami dari *SILADATA* ingin menindaklanjuti terkait formulir diskusi yang Anda ajukan mengenai kebutuhan sistem di perguruan tinggi Anda.\n\nBerikut adalah ringkasan pengajuan diskusi Anda:\n\n*Kebutuhan Utama:*\n${kebutuhanText}\n\n*Sistem Saat Ini:* ${this.item.sistem}\n*Kesiapan Investasi:* ${this.item.investasi}\n\nApakah Anda memiliki waktu luang untuk berdiskusi lebih lanjut? Terima kasih.`;

                return 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
            }
        }"
        @keydown.escape.window="open = false"
    >
        {{-- Search Bar --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('admin.discussions.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, email, atau perguruan tinggi..."
                        class="w-full rounded-xl border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-slate-200 focus:border-violet-500 focus:ring-violet-500/30 placeholder:text-slate-400"
                    >
                </div>
                <button type="submit" class="ui-btn-primary px-5 py-2.5 text-sm">Cari</button>
                @if($search)
                    <a href="{{ route('admin.discussions.index') }}" class="ui-btn-secondary px-5 py-2.5 text-sm">Reset</a>
                @endif
            </form>
        </div>

        @if($discussions->isEmpty())
            {{-- Empty State --}}
            <div class="ui-card flex flex-col items-center justify-center py-20 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-violet-50 text-violet-400 mb-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum ada diskusi masuk</h3>
                <p class="mt-1 text-sm text-slate-500">
                    @if($search)
                        Tidak ditemukan hasil untuk "<span class="font-semibold">{{ $search }}</span>"
                    @else
                        Formulir diskusi yang dikirimkan akan muncul di sini.
                    @endif
                </p>
            </div>
        @else
            {{-- Table --}}
            <div class="ui-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Nama / Kontak</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Perguruan Tinggi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Kebutuhan</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Sistem & Investasi</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Tanggal</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($discussions as $i => $d)
                                @php
                                    $payload = json_encode([
                                        'nama'              => $d->nama,
                                        'email'             => $d->email,
                                        'whatsapp'          => $d->whatsapp,
                                        'perusahaan'        => $d->perusahaan,
                                        'jabatan'           => $d->jabatan,
                                        'kebutuhan'         => $d->kebutuhan,
                                        'kebutuhan_lainnya' => $d->kebutuhan_lainnya,
                                        'sistem'            => $d->sistemLabel(),
                                        'investasi'         => $d->investasiLabel(),
                                        'tanggal'           => $d->created_at->format('d M Y, H:i') . ' WIB',
                                    ], JSON_HEX_QUOT | JSON_HEX_APOS);
                                @endphp
                                <tr class="transition hover:bg-violet-50/20">
                                    {{-- No --}}
                                    <td class="px-5 py-4 text-xs text-slate-400 font-medium">
                                        {{ $discussions->firstItem() + $i }}
                                    </td>

                                    {{-- Nama & Kontak --}}
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $d->nama }}</p>
                                        <a href="mailto:{{ $d->email }}" class="text-xs text-violet-600 hover:text-violet-700 transition">{{ $d->email }}</a>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            <svg class="inline h-3 w-3 text-emerald-500 mr-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 2c-5.524 0-10 4.48-10 10 0 2.16.69 4.19 1.94 5.86L2.9 22l4.3-1.13c1.57.87 3.15 1.34 4.83 1.34 5.52 0 10-4.48 10-10s-4.48-10-10-10z"/></svg>
                                            +62 {{ ltrim($d->whatsapp, '0') }}
                                        </p>
                                    </td>

                                    {{-- Perguruan Tinggi & Jabatan --}}
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-800 max-w-[180px] truncate" title="{{ $d->perusahaan }}">{{ $d->perusahaan }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $d->jabatan }}</p>
                                    </td>

                                    {{-- Kebutuhan (ringkas) --}}
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @foreach ($d->kebutuhan as $keb)
                                                <span class="inline-flex items-center rounded-lg bg-violet-50 px-2 py-0.5 text-[11px] font-medium text-violet-700 ring-1 ring-violet-200/60">
                                                    {{ \Illuminate\Support\Str::limit($keb, 20) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Sistem & Investasi --}}
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-lg bg-sky-50 px-2 py-0.5 text-[11px] font-medium text-sky-700 ring-1 ring-sky-200/60">
                                            {{ $d->sistemLabel() }}
                                        </span>
                                        <span class="mt-1 block inline-flex items-center rounded-lg bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-amber-200/60">
                                            {{ $d->investasiLabel() }}
                                        </span>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                        <p class="font-medium text-slate-700">{{ $d->created_at->format('d M Y') }}</p>
                                        <p class="mt-0.5 text-slate-400">{{ $d->created_at->format('H:i') }} WIB</p>
                                    </td>

                                    {{-- Tombol Detail --}}
                                    <td class="px-5 py-4 text-center">
                                        <button
                                            type="button"
                                            @click="show({{ $payload }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:scale-[1.03] active:scale-[0.97] shadow-sm shadow-violet-500/30"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($discussions->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $discussions->links() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- ===== MODAL DETAIL ===== --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none;"
        >
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-950/75" @click="open = false"></div>

            {{-- Panel --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-slate-900/20 ring-1 ring-slate-200 overflow-hidden"
            >
                {{-- Modal Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-gradient-to-r from-violet-600 to-violet-700 px-6 py-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-violet-200">Detail Diskusi</p>
                        <h2 class="mt-1 text-lg font-bold text-white" x-text="item?.nama"></h2>
                        <p class="mt-0.5 text-sm text-violet-200" x-text="item?.tanggal"></p>
                    </div>
                    <button @click="open = false" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="max-h-[70vh] overflow-y-auto px-6 py-5 space-y-5">

                    {{-- Kontak --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Email</p>
                            <a :href="'mailto:' + item?.email" class="mt-1 block text-sm font-semibold text-violet-600 hover:text-violet-700 transition break-all" x-text="item?.email"></a>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">WhatsApp</p>
                            <a :href="getWhatsAppUrl()" target="_blank" class="mt-1 block text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition" x-text="'+62 ' + (item?.whatsapp?.replace(/^0/, '') ?? '')"></a>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- PT & Jabatan --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Perguruan Tinggi</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900" x-text="item?.perusahaan"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Jabatan</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900" x-text="item?.jabatan"></p>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- Kebutuhan --}}
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Kebutuhan Utama</p>
                        <div class="space-y-2">
                            <template x-for="(keb, idx) in item?.kebutuhan" :key="idx">
                                <div class="flex items-start gap-2.5 rounded-xl bg-violet-50 px-3.5 py-2.5 ring-1 ring-violet-200/60">
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-600 text-[10px] font-bold text-white" x-text="idx + 1"></span>
                                    <p class="text-sm text-violet-800 leading-relaxed" x-text="keb"></p>
                                </div>
                            </template>
                        </div>
                        {{-- Keterangan lainnya --}}
                        <template x-if="item?.kebutuhan_lainnya">
                            <div class="mt-2 rounded-xl bg-slate-50 px-3.5 py-2.5 ring-1 ring-slate-200">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">Keterangan Lainnya</p>
                                <p class="text-sm text-slate-700 leading-relaxed" x-text="item.kebutuhan_lainnya"></p>
                            </div>
                        </template>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- Sistem & Investasi --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Sistem Saat Ini</p>
                            <span class="inline-flex items-center rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 ring-1 ring-sky-200" x-text="item?.sistem"></span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Kesiapan Investasi</p>
                            <span class="inline-flex items-center rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200" x-text="item?.investasi"></span>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="border-t border-slate-100 bg-slate-50/60 px-6 py-4 flex justify-between items-center gap-3">
                    <a
                        :href="getWhatsAppUrl()"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:scale-[1.02] active:scale-[0.98] shadow-sm"
                    >
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 2c-5.524 0-10 4.48-10 10 0 2.16.69 4.19 1.94 5.86L2.9 22l4.3-1.13c1.57.87 3.15 1.34 4.83 1.34 5.52 0 10-4.48 10-10s-4.48-10-10-10z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                    <button @click="open = false" class="rounded-xl bg-slate-200 hover:bg-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
