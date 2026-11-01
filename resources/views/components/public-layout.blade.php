<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    @php
        $pageTitle = isset($title) ? $title . ' - SILADATA | Sistem Layanan Dokumen Akreditasi LAM Infokom' : 'SILADATA - Sistem Layanan Dokumen Akreditasi Perguruan Tinggi & LAM Infokom';
        $pageDesc = $description ?? 'SILADATA (Sistem Layanan Dokumen Akreditasi) adalah platform sistem layanan dokumen akreditasi perguruan tinggi sesuai standar Lembaga Akreditasi Mandiri (LAM Infokom, LAMEMBA, LAM-PTKes, LAMDIK). Memudahkan pengunggahan data, manajemen berkas prodi, dan pemantauan mutu akreditasi.';
        $pageKeywords = $keywords ?? 'SILADATA, Sistem Layanan Dokumen Akreditasi, Sistem Layanan Dokumen Akreditasi berdasarkan LAM Infokom, LAM Infokom, akreditasi perguruan tinggi, akreditasi LAM, dokumen akreditasi prodi, upload data akreditasi, monitoring akreditasi, siladata.my.id, mutu pendidikan tinggi Indonesia';
        $pageUrl = $canonical ?? url()->current();
        $pageImage = $metaImage ?? asset('images/logoname.png');
    @endphp

    <title>{{ $pageTitle }}</title>
    <link rel="canonical" href="{{ $pageUrl }}">

    <!-- Favicons & Apple Touch Icons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}?v=4">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-192.png') }}?v=4">

    <!-- Meta SEO & Keywords -->
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="SILADATA">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SILADATA">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $pageImage }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <!-- Structured Data / Schema.org (JSON-LD) for Search Engines -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}/#website",
          "url": "{{ url('/') }}",
          "name": "SILADATA",
          "alternateName": [
            "Sistem Layanan Dokumen Akreditasi",
            "Sistem Layanan Dokumen Akreditasi berdasarkan LAM Infokom",
            "SILADATA LAM Infokom"
          ],
          "description": "SILADATA adalah Sistem Layanan Dokumen Akreditasi Perguruan Tinggi yang dirancang untuk mengelola dan memantau dokumen akreditasi sesuai standar LAM Infokom dan Lembaga Akreditasi Mandiri lainnya.",
          "inLanguage": "id-ID"
        },
        {
          "@type": "Organization",
          "@id": "{{ url('/') }}/#organization",
          "name": "SILADATA - Sistem Layanan Dokumen Akreditasi",
          "url": "{{ url('/') }}",
          "logo": "{{ asset('images/logoname.png') }}",
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+62881023300457",
            "contactType": "customer service",
            "areaServed": "ID",
            "availableLanguage": "Indonesian"
          }
        },
        {
          "@type": "SoftwareApplication",
          "name": "SILADATA (Sistem Layanan Dokumen Akreditasi)",
          "operatingSystem": "Web Browser",
          "applicationCategory": "EducationalApplication",
          "offers": {
            "@type": "Offer",
            "price": "499000",
            "priceCurrency": "IDR"
          },
          "description": "Sistem Layanan Dokumen Akreditasi Perguruan Tinggi terintegrasi untuk pengunggahan, validasi, dan monitoring kelengkapan dokumen akreditasi prodi sesuai LAM Infokom."
        }
      ]
    }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="font-sans text-slate-900 antialiased" style="background-color: #ECE8DD; margin: 0; padding: 0;">
    {{-- Floating Pill Navbar (Sticky on scroll for home, always on other pages) --}}
    @if(request()->routeIs('home'))
        {{-- Sticky Nav for Home: appears smoothly when user scrolls down past hero banner --}}
        <header x-data="{ scrolled: false }"
                x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 150 })"
                x-show="scrolled"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="fixed top-4 left-0 right-0 z-50 mx-auto px-4 pointer-events-none"
                style="max-width: 1180px; width: 100%; display: none;">
            <div class="pointer-events-auto flex items-center justify-between"
                 style="background-color: rgba(255, 255, 255, 0.98); border-radius: 9999px; padding: 12px 32px; border: 1px solid rgba(229, 223, 209, 0.85); box-shadow: 0 10px 35px rgba(17, 28, 46, 0.08); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);">
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group" style="text-decoration: none;">
                    <span style="font-size: 23px; font-weight: 900; letter-spacing: -0.03em; color: #111C2E; font-family: 'Plus Jakarta Sans', sans-serif;">SILADATA</span>
                    <span class="hidden sm:inline" style="font-size: 13.5px; color: #475467; font-weight: 500;">Sistem Layanan Dokumen Akreditasi</span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="{{ route('harga') }}" style="font-size: 14.5px; font-weight: 600; color: #111C2E; text-decoration: none;">Harga</a>
                    @auth
                        <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; background-color: #111C2E; color: #FFFFFF; font-size: 14px; font-weight: 700; border-radius: 9999px; padding: 9px 26px; text-decoration: none; box-shadow: 0 4px 12px rgba(17, 28, 46, 0.15);">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; justify-content: center; background-color: #111C2E; color: #FFFFFF; font-size: 14px; font-weight: 700; border-radius: 9999px; padding: 9px 26px; text-decoration: none; box-shadow: 0 4px 12px rgba(17, 28, 46, 0.15);">Masuk</a>
                    @endauth
                </div>
            </div>
        </header>
    @else
        {{-- Floating Nav for Other Pages (Harga, Discussion, etc.) --}}
        <header class="sticky top-4 z-50 mx-auto w-full px-4 mb-4" style="max-width: 1180px;">
            <div class="flex items-center justify-between"
                 style="background-color: rgba(255, 255, 255, 0.98); border-radius: 9999px; padding: 12px 32px; border: 1px solid rgba(229, 223, 209, 0.85); box-shadow: 0 8px 30px rgba(17, 28, 46, 0.06); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);">
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group" style="text-decoration: none;">
                    <span style="font-size: 23px; font-weight: 900; letter-spacing: -0.03em; color: #111C2E; font-family: 'Plus Jakarta Sans', sans-serif;">SILADATA</span>
                    <span class="hidden sm:inline" style="font-size: 13.5px; color: #475467; font-weight: 500;">Sistem Layanan Dokumen Akreditasi</span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="{{ route('harga') }}" style="font-size: 14.5px; font-weight: 600; color: #111C2E; text-decoration: none;">Harga</a>
                    @auth
                        <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; background-color: #111C2E; color: #FFFFFF; font-size: 14px; font-weight: 700; border-radius: 9999px; padding: 9px 26px; text-decoration: none; box-shadow: 0 4px 12px rgba(17, 28, 46, 0.15);">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; justify-content: center; background-color: #111C2E; color: #FFFFFF; font-size: 14px; font-weight: 700; border-radius: 9999px; padding: 9px 26px; text-decoration: none; box-shadow: 0 4px 12px rgba(17, 28, 46, 0.15);">Masuk</a>
                    @endauth
                </div>
            </div>
        </header>
    @endif

    <div class="relative min-h-screen flex flex-col" style="background-color: #ECE8DD;">
        <main class="relative flex-grow">
            {{ $slot }}
        </main>

        {{-- Footer Gelap (Matching Bottom Accent) --}}
        <footer class="bg-[#101A2B] text-slate-300" style="background-color: #101A2B;">
            <div class="mx-auto max-w-7xl px-6 py-16">
                <div class="grid gap-12 md:grid-cols-12">
                    {{-- Logo & Deskripsi --}}
                    <div class="md:col-span-5 flex flex-col justify-between">
                        <div>
                            <span class="text-3xl font-black tracking-wider text-white">SILADATA</span>
                            <p class="mt-1 text-xs font-medium text-[#E47A34] tracking-wider uppercase">Sistem Layanan Dokumen Akreditasi</p>
                            <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">
                                Platform digital yang membantu perguruan tinggi dalam mengelola, menyimpan, dan mempersiapkan dokumen akreditasi sesuai standar LAM Infokom, LAMEMBA, LAM-PTKes, dan LAMDIK secara efisien dan transparan.
                            </p>
                        </div>

                        {{-- Hubungi Kami --}}
                        <div class="mt-8">
                            <p class="text-xs font-semibold uppercase tracking-wider text-white mb-3">Hubungi Kami</p>
                            <a href="https://wa.me/62881023300457" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-3 text-slate-300 hover:text-white transition group">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#25d366]/15 text-[#25d366] group-hover:bg-[#25d366]/25 transition">
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.031 2c-5.524 0-10 4.48-10 10 0 2.16.69 4.19 1.94 5.86L2.9 22l4.3-1.13c1.57.87 3.15 1.34 4.83 1.34 5.52 0 10-4.48 10-10s-4.48-10-10-10zm.07 17.5c-1.63 0-3.15-.45-4.48-1.24l-.32-.19-2.58.68.69-2.52-.21-.34c-.87-1.39-1.34-3.01-1.34-4.7 0-4.85 3.95-8.8 8.8-8.8s8.8 3.95 8.8 8.8-3.95 8.8-8.8 8.82z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">+62 881-0233-00457</p>
                                    <p class="text-xs text-slate-400">Chat via WhatsApp</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Navigasi Grid --}}
                    <div class="grid grid-cols-3 gap-8 md:col-span-7">
                        {{-- Navigasi --}}
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-white mb-4">Navigasi</h3>
                            <ul class="space-y-2.5 text-sm">
                                <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-[#E47A34] transition">Beranda</a></li>
                                <li><a href="{{ route('harga') }}" class="text-slate-400 hover:text-[#E47A34] transition">Harga Paket</a></li>
                                <li><a href="{{ route('discussion') }}" class="text-slate-400 hover:text-[#E47A34] transition">Diskusi</a></li>
                                <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-[#E47A34] transition">Masuk</a></li>
                            </ul>
                        </div>

                        {{-- Fitur Platform --}}
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-white mb-4">Fitur Platform</h3>
                            <ul class="space-y-2.5 text-sm">
                                <li><span class="text-slate-400">Dashboard Akreditasi</span></li>
                                <li><span class="text-slate-400">Monitoring Progress</span></li>
                                <li><span class="text-slate-400">Upload Dokumen</span></li>
                                <li><span class="text-slate-400">Cetak Laporan PDF</span></li>
                            </ul>
                        </div>

                        {{-- Legal --}}
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-white mb-4">Legal</h3>
                            <ul class="space-y-2.5 text-sm">
                                <li><a href="#" class="text-slate-400 hover:text-white transition">Kebijakan Privasi</a></li>
                                <li><a href="#" class="text-slate-400 hover:text-white transition">Syarat & Ketentuan</a></li>
                                <li><a href="#" class="text-slate-400 hover:text-white transition">Keamanan Data</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Bottom Footer --}}
                <div class="mt-12 border-t border-slate-800 pt-8">
                    <div class="flex flex-col items-center justify-between gap-3 text-center md:flex-row">
                        <p class="text-sm text-slate-500">
                            © {{ date('Y') }} SILADATA. Seluruh hak cipta dilindungi.
                        </p>
                        <p class="text-xs text-slate-600">
                            Sistem Layanan Dokumen Akreditasi · LAM Infokom
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>

