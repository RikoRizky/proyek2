@php
    /** @var array<string, mixed> $progress */
    $summary = $progress['summary'];
    $units = $progress['units'];
@endphp

<x-public-layout title="Dashboard Akreditasi">

    {{-- ═══════════════════════════════════════════
         HERO SECTION
    ═══════════════════════════════════════════ --}}
    <section class="relative overflow-hidden py-20 sm:py-28 lg:py-36">
        {{-- Background illustration --}}
        <div class="pointer-events-none absolute inset-0 z-0"
             style="background-image: url('{{ asset('images/bg-2.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
        {{-- Gradient color overlay --}}
        <div class="pointer-events-none absolute inset-0 z-[1]"
             style="background: linear-gradient(135deg, rgba(237,233,254,0.55) 0%, rgba(238,242,255,0.35) 50%, rgba(224,231,255,0.45) 100%);"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                {{-- Badge --}}
                <!-- <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-violet-200/60 bg-white/60 px-4 py-1.5 text-sm font-semibold text-violet-700 shadow-sm backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-violet-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-violet-500"></span>
                    </span>
                    Platform Digital Akreditasi LAM Infokom
                </div> -->

                {{-- Headline --}}
                <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    Sistem Dokumen Akreditasi
                    <span class="block bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent pb-2">
                        Perguruan Tinggi
                    </span>
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed">
                    SILADATA membantu perguruan tinggi mengelola, mengunggah, dan memantau berkas akreditasi sesuai standar <strong class="text-slate-800">LAM Infokom</strong>, LAMEMBA, LAM-PTKes, dan LAMDIK secara efisien, efektif, dan transparan.
                </p>

                {{-- CTA Buttons --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-8 py-4 text-sm font-bold text-white shadow-xl shadow-violet-500/30 transition-all hover:scale-[1.02] hover:shadow-violet-500/40 focus:ring-4 focus:ring-violet-500/20">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('harga') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-8 py-4 text-sm font-bold text-white shadow-xl shadow-violet-500/30 transition-all hover:scale-[1.02] hover:shadow-violet-500/40 focus:ring-4 focus:ring-violet-500/20">
                            Lihat Paket & Harga
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('discussion') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 px-8 py-4 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur-sm transition-all hover:bg-white hover:shadow-md">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Jadwalkan Diskusi
                        </a>
                    @endauth
                </div>

                {{-- Social Proof --}}
                <div class="mt-12 flex flex-wrap items-center justify-center gap-6 sm:gap-10">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-full border-2 border-white bg-violet-300 flex items-center justify-center text-xs font-bold text-white">A</div>
                            <div class="h-8 w-8 rounded-full border-2 border-white bg-indigo-400 flex items-center justify-center text-xs font-bold text-white">B</div>
                            <div class="h-8 w-8 rounded-full border-2 border-white bg-purple-500 flex items-center justify-center text-xs font-bold text-white">C</div>
                            <div class="h-8 w-8 rounded-full border-2 border-white bg-violet-600 flex items-center justify-center text-xs font-bold text-white">+</div>
                        </div>
                        <p class="text-sm text-slate-600"><strong class="text-slate-900">1.300+</strong> kampus bergabung</p>
                    </div>
                    <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        <div class="flex text-amber-400">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        <p class="text-sm text-slate-600"><strong class="text-slate-900">4.9/5</strong> rating kepuasan</p>
                    </div>
                    <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="text-sm text-slate-600">Data terenkripsi & aman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
{{-- ═══════════════════════════════════════════
         TENTANG SILADATA
    ═══════════════════════════════════════════ --}}
    <section id="about" class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-2 items-center">
                <div>
                    <span class="inline-flex rounded-full bg-violet-100 px-4 py-1.5 text-sm font-semibold text-violet-700">
                        Tentang SILADATA
                    </span>
                    <h2 class="mt-4 text-3xl font-bold text-slate-900 leading-tight sm:text-4xl">
                        Platform Digital Akreditasi yang <span class="text-violet-600">Terintegrasi</span>
                    </h2>
                    <p class="mt-5 text-slate-600 leading-relaxed">
                        <strong class="text-slate-800">SILADATA</strong> merupakan platform digital terintegrasi yang dirancang khusus untuk membantu perguruan tinggi dan program studi dalam mengelola, mengunggah, dan memantau berkas akreditasi sesuai kriteria <strong class="text-slate-800">LAM Infokom</strong> serta lembaga akreditasi mandiri lainnya (LAMEMBA, LAM-PTKes, LAMDIK, dll).
                    </p>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        Dengan sistem pengunggahan yang terstruktur, validasi bertingkat, dan pemantauan real-time, SILADATA mempermudah persiapan LED dan LKPS sehingga institusi siap menghadapi asesmen akreditasi secara efektif, efisien, dan transparan.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        @foreach(['Sesuai Kriteria LAM Infokom', 'Manajemen Dokumen Terpusat', 'Monitoring Progress per Prodi', 'Penyimpanan Cloud Terstruktur'] as $item)
                            <div class="flex items-center gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-5">
                    <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-600 p-7 text-white shadow-xl shadow-violet-500/20">
                        <p class="text-4xl font-black">1.300+</p>
                        <p class="mt-2 text-sm font-medium text-violet-200">Kampus bergabung di seluruh Indonesia</p>
                    </div>
                    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm">
                        <p class="text-4xl font-black text-slate-900">99.9%</p>
                        <p class="mt-2 text-sm font-medium text-slate-500">Uptime layanan server yang terjamin</p>
                    </div>
                    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm">
                        <p class="text-4xl font-black text-slate-900">4</p>
                        <p class="mt-2 text-sm font-medium text-slate-500">LAM didukung: Infokom, EMBA, PTKes, DIK</p>
                    </div>
                    <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-500 p-7 text-white shadow-xl shadow-emerald-500/20">
                        <p class="text-4xl font-black">24/7</p>
                        <p class="mt-2 text-sm font-medium text-emerald-100">Dukungan tim kami selalu siap membantu</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FITUR UTAMA
    ═══════════════════════════════════════════ --}}
    <section class="relative bg-white/60 py-20 sm:py-24 backdrop-blur-sm border-y border-slate-200/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex rounded-full bg-violet-100 px-4 py-1.5 text-sm font-semibold text-violet-700">
                    Fitur Unggulan
                </span>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 sm:text-4xl">
                    Semua yang Anda Butuhkan, <span class="text-violet-600">Dalam Satu Platform</span>
                </h2>
                <p class="mt-4 mx-auto max-w-2xl text-base text-slate-600">
                    Dirancang khusus untuk kebutuhan akreditasi perguruan tinggi Indonesia berdasarkan standar LAM Infokom dan lembaga akreditasi mandiri lainnya.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Kartu Fitur 1 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-50 to-violet-100/50 p-7 shadow-sm border border-violet-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-600 shadow-lg shadow-violet-500/30 text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Dokumen Akreditasi</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Kelola semua berkas LED & LKPS secara terpusat, terstruktur, dan sesuai standar LAM.</p>
                </div>

                {{-- Kartu Fitur 2 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-50 to-sky-100/50 p-7 shadow-sm border border-sky-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-500 shadow-lg shadow-sky-500/30 text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Cloud Storage</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Simpan dokumen akreditasi di cloud yang aman, dapat diakses kapan saja dan di mana saja.</p>
                </div>

                {{-- Kartu Fitur 3 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-7 shadow-sm border border-emerald-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 shadow-lg shadow-emerald-500/30 text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Monitoring Progress</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Pantau kelengkapan dokumen setiap program studi secara real-time dengan grafik interaktif.</p>
                </div>

                {{-- Kartu Fitur 4 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-50 to-amber-100/50 p-7 shadow-sm border border-amber-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 shadow-lg shadow-amber-500/30 text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Keamanan Data</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Data dokumen dilindungi dengan enkripsi end-to-end dan akses berbasis peran yang ketat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         CTA SECTION
    ═══════════════════════════════════════════ --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 px-8 py-16 text-center text-white sm:px-12 sm:py-20 shadow-2xl shadow-violet-500/30">
            {{-- Decorative elements --}}
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-white/5 blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 h-48 w-48 rounded-full bg-white/5 blur-2xl"></div>
                <div class="absolute top-1/2 left-1/4 h-32 w-32 rounded-full bg-violet-300/10 blur-xl"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-3xl">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white/80 backdrop-blur-sm">
                    Bergabung Sekarang
                </span>
                <h2 class="mt-6 text-3xl font-black text-white sm:text-4xl lg:text-5xl leading-tight">
                    Siap Bertransformasi Bersama SILADATA?
                </h2>
                <p class="mt-4 text-base text-violet-100 sm:text-lg max-w-xl mx-auto">
                    Lebih dari 1.300 kampus sudah bergabung. Saatnya kampus Anda menjadi bagian dari ekosistem digital akreditasi Indonesia.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('discussion') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-white/80 bg-white/10 px-8 py-3.5 text-sm font-bold text-white backdrop-blur-sm transition-all duration-200 hover:bg-white hover:text-violet-700 hover:shadow-lg hover:scale-[1.03] active:scale-[0.97] w-full sm:w-auto">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Jadwalkan Diskusi
                    </a>
                    <a href="{{ route('harga') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-8 py-3.5 text-sm font-bold text-violet-700 shadow-lg transition-all duration-200 hover:bg-violet-50 hover:scale-[1.03] active:scale-[0.97] w-full sm:w-auto">
                        Lihat Paket & Harga
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>