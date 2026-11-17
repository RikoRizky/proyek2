<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SILADATA - Sistem Layanan Dokumen Akreditasi Perguruan Tinggi & LAM Infokom</title>

        <!-- Favicons & Apple Touch Icons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}?v=4">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-192.png') }}?v=4">

        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

        <!-- Meta SEO & Keywords -->
        <meta name="description" content="SILADATA (Sistem Layanan Dokumen Akreditasi) adalah sistem layanan dokumen akreditasi perguruan tinggi untuk Lembaga Akreditasi Mandiri (LAM Infokom, LAMEMBA, LAM-PTKes, LAMDIK). Memudahkan pengunggahan data, manajemen, dan monitoring kelengkapan dokumen akreditasi secara terstruktur.">
        <meta name="keywords" content="SILADATA, Sistem Layanan Dokumen Akreditasi, Sistem Layanan Dokumen Akreditasi berdasarkan LAM Infokom, LAM Infokom, akreditasi perguruan tinggi, upload data akreditasi, LAM, Lembaga Akreditasi Mandiri, mutu pendidikan tinggi Indonesia, akreditasi LAM, dokumen akreditasi prodi, monitoring akreditasi, unggah data, perguruan tinggi">
        <meta name="author" content="SILADATA">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="SILADATA">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="SILADATA - Sistem Layanan Dokumen Akreditasi Perguruan Tinggi & LAM Infokom">
        <meta property="og:description" content="SILADATA membantu perguruan tinggi mengelola dan mempersiapkan dokumen akreditasi sesuai kebutuhan Lembaga Akreditasi Mandiri (LAM Infokom).">
        <meta property="og:image" content="{{ asset('images/logoname.png') }}?v=2">

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="SILADATA - Sistem Layanan Dokumen Akreditasi Perguruan Tinggi & LAM Infokom">
        <meta name="twitter:description" content="SILADATA membantu perguruan tinggi mengelola dan mempersiapkan dokumen akreditasi sesuai kebutuhan Lembaga Akreditasi Mandiri (LAM Infokom).">
        <meta name="twitter:image" content="{{ asset('images/logoname.png') }}?v=2">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="relative min-h-screen overflow-hidden bg-[#EEE9DF]/30">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#FFB162]/15 via-transparent to-transparent"></div>
            <div class="pointer-events-none absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-[#A35139]/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-[#2C3B4D]/10 blur-3xl"></div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
                <a href="{{ url('/') }}" class="mb-8 flex flex-col items-center gap-3 transition hover:opacity-90">
                    <img
                        class="flex h-14 w-14 items-center justify-center rounded-2xl shadow-xl shadow-[#A35139]/20 ring-4 ring-white/60 object-contain bg-white"
                        src="{{ asset('images/logoname.png') }}"
                        alt="SILADATA"
                    />
                    <span class="text-center text-sm font-bold text-[#1B2632]">SILADATA (Sistem Layanan Dokumen Akreditasi)</span>
                </a>

                <div class="w-full max-w-md">
                    <div class="rounded-3xl border border-[#C9C1B1]/60 bg-white/90 p-8 shadow-2xl shadow-[#1B2632]/10 ring-1 ring-[#C9C1B1]/40 backdrop-blur-xl sm:p-10">
                        {{ $slot }}
                    </div>
                    <p class="mt-6 text-center text-xs text-[#2C3B4D]/60">© {{ date('Y') }} — SILADATA (Sistem Layanan Dokumen Akreditasi)</p>
                </div>
            </div>
        </div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '{{ session('error_title', 'Perhatian') }}',
                    text: "{!! session('error') !!}",
                    confirmButtonColor: '#A35139',
                });
            });
        </script>
        @endif

        @if(session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{!! session('status') !!}",
                    confirmButtonColor: '#A35139',
                });
            });
        </script>
        @endif

        <script>
            // Safari / Browser BFCache: otomatis reload jika kembali ke halaman lama agar CSRF token selalu segar
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>

    </body>
</html>