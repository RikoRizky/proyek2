<x-public-layout 
    title="Harga Paket SILADATA" 
    description="Daftar harga paket Sistem Layanan Dokumen Akreditasi (SILADATA) untuk perguruan tinggi dan program studi berdasarkan LAM Infokom. Pilih paket sesuai kebutuhan kampus Anda."
>

    {{-- ═══════════════════════════════════════════
         HERO SECTION
    ═══════════════════════════════════════════ --}}

    {{-- Wrapper dengan bg yang cover hero + cards --}}
    <div class="relative">
        {{-- Background illustration --}}
        <div class="pointer-events-none absolute inset-0 z-0"
             style="background-image: url('{{ asset('images/bg harga 1.png') }}'); background-size: cover; background-position: center top; background-repeat: no-repeat;"></div>
        {{-- Bottom fade → blend ke FAQ section --}}
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-48 z-[1]"
             style="background: linear-gradient(to bottom, transparent, #f5f3ff);"></div>

    <section class="relative overflow-hidden py-20 sm:py-24 text-center">
        <div class="relative z-10 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <span class="inline-flex rounded-full bg-violet-100 px-4 py-1.5 text-sm font-semibold text-violet-700">
                Harga Layanan
            </span>
            <h1 class="mt-5 text-4xl font-black text-slate-900 tracking-tight sm:text-5xl leading-tight">
                Pilih Paket Terbaik <br class="hidden sm:block">untuk Kampus Anda
            </h1>
            <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl mx-auto">
                Semua paket mencakup akses penuh ke platform SILADATA. Berlangganan per tahun dengan harga tetap dan tanpa biaya tersembunyi.
            </p>

            {{-- Cara Kerja Singkat --}}
            <div class="mt-10 w-full max-w-4xl mx-auto">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-6">Cara Kerja</p>

                @php
                    $steps = [
                        [
                            'num'   => '1',
                            'label' => 'Pilih Paket',
                            'sub'   => 'Sesuai kebutuhan kampus',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                            'ring'  => 'ring-violet-200',
                            'bg'    => 'bg-violet-600',
                            'text'  => 'text-violet-700',
                            'soft'  => 'bg-violet-50',
                        ],
                        [
                            'num'   => '2',
                            'label' => 'Isi Data',
                            'sub'   => 'Data institusi & email',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                            'ring'  => 'ring-purple-200',
                            'bg'    => 'bg-purple-600',
                            'text'  => 'text-purple-700',
                            'soft'  => 'bg-purple-50',
                        ],
                        [
                            'num'   => '3',
                            'label' => 'Bayar via Midtrans',
                            'sub'   => 'Transfer, VA, kartu kredit',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
                            'ring'  => 'ring-blue-200',
                            'bg'    => 'bg-blue-600',
                            'text'  => 'text-blue-700',
                            'soft'  => 'bg-blue-50',
                        ],
                        [
                            'num'   => '4',
                            'label' => 'Link via Email',
                            'sub'   => 'Link aktivasi otomatis',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                            'ring'  => 'ring-indigo-200',
                            'bg'    => 'bg-indigo-600',
                            'text'  => 'text-indigo-700',
                            'soft'  => 'bg-indigo-50',
                        ],
                        [
                            'num'   => '5',
                            'label' => 'Akun Aktif',
                            'sub'   => 'Siap pakai sekarang!',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
                            'ring'  => 'ring-emerald-200',
                            'bg'    => 'bg-emerald-500',
                            'text'  => 'text-emerald-700',
                            'soft'  => 'bg-emerald-50',
                        ],
                    ];
                @endphp

                {{-- Horizontal stepper (berlaku untuk semua ukuran layar) --}}
                <div class="flex items-start justify-between relative w-full overflow-x-auto pb-4 sm:pb-0 hide-scrollbar snap-x">
                    <div class="flex items-start justify-between relative w-full min-w-[500px] sm:min-w-0">
                        {{-- Connector line behind steps --}}
                        <div class="absolute top-5 sm:top-6 left-0 right-0 h-0.5 bg-gradient-to-r from-violet-200 via-blue-200 to-emerald-200 z-0 mx-[10%]"></div>

                        @foreach($steps as $step)
                        <div class="relative z-10 flex flex-1 flex-col items-center gap-2 px-1 sm:px-2 snap-center">
                            {{-- Circle badge --}}
                            <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full {{ $step['bg'] }} ring-4 {{ $step['ring'] }} shadow-md shrink-0">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $step['icon'] !!}
                                </svg>
                            </div>
                            {{-- Label --}}
                            <div class="text-center mt-1 sm:mt-0">
                                <p class="text-[10px] sm:text-sm font-bold text-slate-800 leading-tight">{{ $step['label'] }}</p>
                                <p class="mt-0.5 text-[9px] sm:text-xs text-slate-400 leading-snug max-w-[120px]">{{ $step['sub'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <style>
                    .hide-scrollbar::-webkit-scrollbar {
                        display: none;
                    }
                    .hide-scrollbar {
                        -ms-overflow-style: none;
                        scrollbar-width: none;
                    }
                </style>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         PAKET BERLANGGANAN
    ═══════════════════════════════════════════ --}}
    @php
    $packages = [
        [
            'name'        => 'Starter',
            'icon'        => '🌱',
            'price'       => 'Rp 1.500.000',
            'period'      => '/tahun',
            'description' => 'Cocok untuk institusi kecil yang baru memulai pengelolaan dokumen akreditasi.',
            'color'       => 'slate',
            'features'    => [
                ['text' => 'Maksimal 3 Akun Program Studi',      'check' => true],
                ['text' => 'Upload Dokumen via Link (Google Drive)', 'check' => true],
                ['text' => 'Dashboard Monitoring Progress',       'check' => true],
                ['text' => 'Cetak Laporan PDF',                   'check' => false],
                ['text' => 'Upload File Langsung (Storage)',      'check' => false],
            ]
        ],
        [
            'name'        => 'Pro',
            'icon'        => '🚀',
            'price'       => 'Rp 3.500.000',
            'period'      => '/tahun',
            'featured'    => true,
            'description' => 'Paling populer. Ideal untuk institusi menengah dengan banyak program studi aktif.',
            'color'       => 'violet',
            'features'    => [
                ['text' => 'Semua Fitur Starter',                'check' => true],
                ['text' => 'Maksimal 10 Akun Program Studi',     'check' => true],
                ['text' => 'Upload File Langsung (Storage 10GB)','check' => true],
                ['text' => 'Cetak Laporan PDF',                  'check' => true],
                ['text' => 'Dukungan Prioritas via WhatsApp',    'check' => true],
            ]
        ],
        [
            'name'        => 'Enterprise',
            'icon'        => '🏛️',
            'price'       => 'Rp 7.500.000',
            'period'      => '/tahun',
            'description' => 'Untuk universitas besar dengan kebutuhan prodi tidak terbatas dan storage besar.',
            'color'       => 'indigo',
            'features'    => [
                ['text' => 'Semua Fitur Pro',                        'check' => true],
                ['text' => 'Akun Program Studi Tidak Terbatas',      'check' => true],
                ['text' => 'Upload File Langsung (Storage 50GB)',     'check' => true],
                ['text' => 'Laporan & Analitik Lanjutan',            'check' => true],
                ['text' => 'Manajer Akun Khusus',                    'check' => true],
            ]
        ]
    ];
    @endphp

    <div class="relative z-10 mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-3 items-stretch">
            @foreach($packages as $package)
                <div class="group relative flex flex-col justify-between rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1
                    {{ isset($package['featured']) ? 'bg-gradient-to-b from-violet-600 to-indigo-700 text-white shadow-2xl shadow-violet-500/40 scale-[1.05] z-10 ring-4 ring-violet-400/30' : 'bg-white border border-slate-200 shadow-lg shadow-slate-200/60 hover:shadow-xl hover:shadow-slate-300/50' }}">

                    {{-- Badge Terbaik --}}
                    @if(isset($package['featured']))
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-400 to-orange-400 px-4 py-1.5 text-xs font-black text-slate-900 shadow-lg uppercase tracking-wider">
                                ⭐ Paling Populer
                            </span>
                        </div>
                    @endif

                    <div>
                        {{-- Icon & Nama --}}
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-2xl
                                {{ isset($package['featured']) ? 'bg-white/15' : 'bg-slate-50 border border-slate-100' }}">
                                {{ $package['icon'] }}
                            </div>
                            <div>
                                <h3 class="text-xl font-black {{ isset($package['featured']) ? 'text-white' : 'text-slate-900' }}">
                                    {{ $package['name'] }}
                                </h3>
                                <p class="text-xs {{ isset($package['featured']) ? 'text-violet-200' : 'text-slate-400' }}">
                                    Paket Berlangganan
                                </p>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <p class="mt-4 text-sm leading-relaxed {{ isset($package['featured']) ? 'text-violet-100' : 'text-slate-500' }}">
                            {{ $package['description'] }}
                        </p>

                        {{-- Harga --}}
                        <div class="mt-6 pb-6 border-b {{ isset($package['featured']) ? 'border-violet-500/40' : 'border-slate-100' }}">
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-black {{ isset($package['featured']) ? 'text-white' : 'text-slate-900' }}">
                                    {{ $package['price'] }}
                                </span>
                                <span class="mb-1 text-sm {{ isset($package['featured']) ? 'text-violet-200' : 'text-slate-400' }}">{{ $package['period'] }}</span>
                            </div>
                            <p class="mt-1 text-xs {{ isset($package['featured']) ? 'text-violet-200' : 'text-slate-400' }}">
                                Sudah termasuk pajak, tanpa biaya tersembunyi.
                            </p>
                        </div>

                        {{-- Fitur --}}
                        <ul class="mt-6 space-y-3">
                            @foreach($package['features'] as $feature)
                                <li class="flex items-start gap-3 text-sm">
                                    @if($feature['check'])
                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full
                                            {{ isset($package['featured']) ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-600' }}">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                        <span class="{{ isset($package['featured']) ? 'text-violet-50' : 'text-slate-700' }}">{{ $feature['text'] }}</span>
                                    @else
                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </span>
                                        <span class="text-slate-400 line-through">{{ $feature['text'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- CTA Tombol --}}
                    <div class="mt-8">
                        <a href="{{ route('checkout.form', ['package' => $package['name']]) }}"
                           class="block w-full text-center rounded-2xl py-4 text-sm font-bold transition-all duration-200 hover:scale-[1.02] hover:opacity-90"
                           style="
                            @if(isset($package['featured']))
                                background: #ffffff; color: #7c3aed; box-shadow: 0 4px 14px rgba(0,0,0,0.12);
                            @elseif($package['name'] === 'Enterprise')
                                background: #0f172a; color: #ffffff; box-shadow: 0 4px 12px rgba(15,23,42,0.3);
                            @else
                                background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #ffffff; box-shadow: 0 4px 14px rgba(124,58,237,0.4);
                            @endif
                           ">
                            Pesan Paket Ini →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>{{-- end bg wrapper --}}

    {{-- ═══════════════════════════════════════════
         FAQ SECTION
    ═══════════════════════════════════════════ --}}
    <section class="border-t border-slate-200/60 bg-white/60 py-20 sm:py-24 backdrop-blur-sm">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex rounded-full bg-violet-100 px-4 py-1.5 text-sm font-semibold text-violet-700">FAQ</span>
                <h2 class="mt-4 text-3xl font-black text-slate-900 sm:text-4xl">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-3 text-slate-500">Tidak menemukan jawaban? <a href="{{ route('discussion') }}" class="text-violet-600 font-semibold hover:underline">Hubungi kami langsung →</a></p>
            </div>

            @php
            $faqs = [
                [
                    'q' => 'Bagaimana alur pendaftaran ke SILADATA?',
                    'a' => 'Pilih paket yang sesuai → Isi data institusi dan email → Lakukan pembayaran via Midtrans → Setelah pembayaran berhasil, link pembuatan akun otomatis dikirim ke email Anda → Klik link tersebut untuk membuat akun Perguruan Tinggi Anda dan mulai gunakan SILADATA.'
                ],
                [
                    'q' => 'Apakah bisa mendaftar sendiri (self-register)?',
                    'a' => 'Tidak. Akun SILADATA hanya dapat dibuat melalui link aktivasi yang dikirim ke email Anda setelah pembayaran dikonfirmasi. Tidak ada tombol daftar langsung — ini untuk menjaga keamanan dan kualitas layanan.'
                ],
                [
                    'q' => 'Bagaimana proses pembayaran berlangganan?',
                    'a' => 'Pembayaran dilakukan secara online melalui platform Midtrans yang mendukung berbagai metode: transfer bank, virtual account, kartu kredit, dan lainnya. Setelah pembayaran dikonfirmasi oleh sistem, link aktivasi akun langsung dikirim ke email Anda secara otomatis.'
                ],
                [
                    'q' => 'Apa perbedaan upload via Link dan upload File langsung?',
                    'a' => 'Paket Starter hanya mendukung upload dokumen berupa tautan (misalnya Google Drive). Paket Pro dan Enterprise mendukung upload file langsung ke server SILADATA, dengan kuota storage masing-masing 10GB dan 50GB.'
                ],
                [
                    'q' => 'Apakah bisa upgrade paket di tengah masa berlangganan?',
                    'a' => 'Ya, Anda bisa upgrade kapan saja. Biaya akan dihitung secara prorata berdasarkan sisa masa aktif langganan, lalu Anda akan dikenakan selisih harga ke paket yang lebih tinggi.'
                ],
                [
                    'q' => 'Apakah data dokumen kami aman?',
                    'a' => 'Sangat aman. Seluruh dokumen disimpan di infrastruktur cloud terenkripsi dengan kontrol akses berbasis peran. Hanya akun yang berwenang yang bisa mengakses dokumen masing-masing program studi.'
                ],
            ];
            @endphp

            <div class="space-y-3" x-data="{ open: null }">
                @foreach($faqs as $i => $faq)
                    <div class="rounded-2xl border border-slate-200/70 bg-white shadow-sm overflow-hidden">
                        <button
                            @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                            id="faq-btn-{{ $i }}"
                        >
                            <span class="font-semibold text-slate-900 text-sm sm:text-base">{{ $faq['q'] }}</span>
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 transition-all duration-200"
                                  :class="open === {{ $i }} ? 'bg-violet-100 rotate-45' : ''">
                                <svg class="h-4 w-4 text-slate-500 transition-colors" :class="open === {{ $i }} ? 'text-violet-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="border-t border-slate-100 px-6 pb-5 pt-4">
                                <p class="text-sm leading-relaxed text-slate-600">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         CTA BOTTOM
    ═══════════════════════════════════════════ --}}
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 px-8 py-14 text-center text-white shadow-2xl shadow-violet-500/25">
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -top-16 -right-16 h-64 w-64 rounded-full bg-white/5 blur-2xl"></div>
                    <div class="absolute -bottom-10 -left-10 h-48 w-48 rounded-full bg-white/5 blur-2xl"></div>
                </div>
                <div class="relative z-10 mx-auto max-w-2xl">
                    <h2 class="text-2xl font-black text-white sm:text-3xl">Masih Bingung Pilih Paket?</h2>
                    <p class="mt-3 text-violet-100">Konsultasikan kebutuhan kampus Anda bersama tim kami. Kami akan bantu menemukan paket yang paling sesuai.</p>
                    <a href="{{ route('discussion') }}"
                       class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-bold text-violet-700 shadow-lg transition-all hover:bg-violet-50 hover:scale-[1.02]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Jadwalkan Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
