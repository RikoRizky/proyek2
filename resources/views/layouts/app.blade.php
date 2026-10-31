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
    <body class="font-sans text-slate-900">
        <div
            x-data="{ sidebarOpen: false }"
            @keydown.window.escape="sidebarOpen = false"
            class="ui-page-shell flex min-h-screen flex-col lg:h-screen lg:flex-row lg:overflow-hidden"
        >
            @include('layouts.sidebar')

            {{-- In-flow gutter: fixed sidebar does not reserve width in flex layout; this matches lg:w-64 aside --}}
            <div class="hidden shrink-0 select-none lg:block lg:w-64" aria-hidden="true"></div>

            <div class="flex min-h-0 min-w-0 flex-1 flex-col relative">
                @if(auth()->check() && auth()->user()->role !== \App\Enums\UserRole::Admin && auth()->user()->effective_package_valid_until && auth()->user()->effective_package_valid_until < now())
                    <div class="bg-rose-600 px-4 py-2.5 text-white sm:px-6 lg:px-8 text-center flex flex-col sm:flex-row items-center justify-center gap-3 shadow-md z-[100] sticky top-0 relative">
                        <div class="font-bold flex items-center gap-1.5 text-sm">
                            <svg class="w-4 h-4 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Masa Berlaku Paket Anda Telah Habis!
                        </div>
                        <span class="text-xs sm:text-sm opacity-90 hidden md:inline">Akun ini beralih ke Mode Read-Only (Hanya Baca).</span>
                        <a href="{{ route('upgrade.packages') }}" class="bg-white text-rose-600 px-4 py-1.5 rounded-full text-xs font-black hover:bg-rose-50 transition-colors shadow-sm ml-auto sm:ml-0 mt-2 sm:mt-0 uppercase tracking-wide">Perpanjang Sekarang</a>
                    </div>
                @endif
                @isset($header)
                    <header class="relative lg:sticky lg:top-0 z-20 shrink-0 border-b border-slate-200/60 bg-white/85 shadow-sm backdrop-blur-lg">
                        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                            <div class="min-w-0 flex-1">
                                {{ $header }}
                            </div>
                            <style>
                                @media (max-width: 1024px) {
                                    .desktop-profile-only { display: none !important; }
                                }
                            </style>
                            <div class="flex desktop-profile-only shrink-0 items-center rounded-full border border-slate-200/80 bg-white/60 shadow-sm backdrop-blur-sm">
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

                <main class="mx-auto min-h-0 w-full max-w-7xl flex-1 overflow-y-auto px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                    @if (session('status'))
                        <div class="mb-4 ui-alert-success" role="status">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-bold">✓</span>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm" role="alert">
                            <svg class="h-5 w-5 shrink-0 text-rose-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                            <span class="font-medium text-rose-800">{{ session('error') }}</span>
                        </div>
                    @endif


                    {{ $slot }}
                </main>
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
