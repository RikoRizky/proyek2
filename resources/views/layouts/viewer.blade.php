<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - SILADATA (Sistem Layanan Dokumen Akreditasi)' : 'SILADATA (Sistem Layanan Dokumen Akreditasi)' }}</title>

        <!-- Favicons & Apple Touch Icons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192.png') }}?v=4">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-192.png') }}?v=4">

        <!-- Meta SEO & Keywords -->
        <meta name="description" content="SILADATA (Sistem Layanan Dokumen Akreditasi) adalah sistem layanan dokumen akreditasi perguruan tinggi untuk Lembaga Akreditasi Mandiri (LAM) yang menilai mutu pendidikan tinggi di Indonesia. Memudahkan pengunggahan data, manajemen, dan monitoring kelengkapan dokumen akreditasi secara terstruktur.">
        <meta name="keywords" content="SILADATA, Sistem Layanan Dokumen Akreditasi, akreditasi perguruan tinggi, upload data akreditasi, LAM, Lembaga Akreditasi Mandiri, mutu pendidikan tinggi Indonesia, akreditasi LAM, dokumen akreditasi prodi, monitoring akreditasi, unggah data, perguruan tinggi">
        <meta name="author" content="SILADATA">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="SILADATA (Sistem Layanan Dokumen Akreditasi)">
        <meta property="og:description" content="SILADATA membantu perguruan tinggi mengelola dan mempersiapkan dokumen akreditasi sesuai kebutuhan Lembaga Akreditasi Mandiri (LAM).">
        <meta property="og:image" content="{{ asset('images/logoname.png') }}?v=2">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen h-full font-sans text-slate-900 antialiased">
        <div
            x-data="{ sidebarOpen: false }"
            @keydown.window.escape="sidebarOpen = false"
            class="flex h-full min-h-screen w-full flex-col overflow-hidden bg-gradient-to-br from-slate-100 via-violet-50/40 to-slate-100 lg:h-screen lg:min-h-0 lg:flex-row"
        >
            @include('layouts.sidebar')

            <div class="hidden shrink-0 select-none lg:block lg:w-64" aria-hidden="true"></div>

            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
                @isset($header)
                    <header class="z-20 shrink-0 border-b border-slate-200/60 bg-white/85 shadow-sm backdrop-blur-lg">
                        <div class="flex w-full items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                            <div class="min-w-0 flex-1">
                                {{ $header }}
                            </div>
                            <div class="flex shrink-0 items-center rounded-full border border-slate-200/80 bg-white/60 shadow-sm backdrop-blur-sm">
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold text-slate-600 transition-all duration-200 hover:bg-violet-50 hover:text-violet-700">
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="h-6 w-6 rounded-full object-cover ring-2 ring-violet-500/10">
                                    <span class="hidden sm:inline">Profil</span>
                                </a>
                                <div class="h-5 w-px bg-slate-200/80"></div>
                                <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-slate-500 transition-all duration-200 hover:bg-rose-50 hover:text-rose-600">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                        <span class="hidden sm:inline">Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </header>
                @endisset

                <div class="flex min-h-0 flex-1 flex-col overflow-hidden bg-slate-200/80">
                    {{ $slot }}
                </div>
        </div>
        <script>
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    document.querySelectorAll('[id^="upload-modal-"]').forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const reqId = modal.id.replace('upload-modal-', '');
                            if (typeof window.closeUploadModal === 'function') {
                                window.closeUploadModal(reqId);
                            } else {
                                modal.classList.add('hidden');
                            }
                        }
                    });

                    if (typeof window.closeViewerRevisionModal === 'function') window.closeViewerRevisionModal();
                    if (typeof window.closeRevisionModal === 'function') window.closeRevisionModal();
                    if (typeof window.closeDetailValidationModal === 'function') window.closeDetailValidationModal();

                    document.querySelectorAll('[id$="Modal"]:not(.hidden)').forEach(modal => {
                        modal.classList.add('hidden');
                    });
                }
            });
        </script>
    </body>
</html>
