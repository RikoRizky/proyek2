<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 Halaman Tidak Ditemukan - SILADATA</title>

        <!-- Favicons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased h-full">
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-100 via-violet-50/50 to-indigo-50/40 px-4 py-10 sm:px-6">
            
            <!-- Background Decorations -->
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-violet-200/30 via-transparent to-transparent"></div>
            <div class="pointer-events-none absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-violet-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-indigo-400/20 blur-3xl"></div>

            <div class="relative w-full max-w-2xl text-center flex flex-col items-center">
                <!-- Illustration / Logo Area -->
                <div class="mb-8 relative">
                    <div class="absolute inset-0 bg-violet-500/10 blur-2xl rounded-full scale-150"></div>
                    <img
                        class="relative z-10 flex h-24 w-24 sm:h-28 sm:w-28 items-center justify-center rounded-[2rem] shadow-2xl shadow-violet-500/40 ring-8 ring-white/60 object-contain bg-white transform transition hover:scale-105 duration-300"
                        src="{{ asset('images/logoname.png') }}"
                        alt="SILADATA"
                    />
                </div>

                <!-- 404 Text -->
                <h1 class="text-7xl sm:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-indigo-600 drop-shadow-sm mb-4">
                    404
                </h1>
                
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-4">
                    Oops! Anda Sepertinya Tersesat
                </h2>
                
                <p class="text-slate-500 text-sm sm:text-base max-w-md mx-auto mb-10 leading-relaxed">
                    Halaman yang Anda cari tidak dapat ditemukan, mungkin telah dihapus, namanya diubah, atau sementara tidak tersedia.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
                    <button onclick="window.history.back()" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white border border-slate-200/80 text-slate-600 font-semibold shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-all focus:ring-4 focus:ring-slate-100 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </button>
                    
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold shadow-xl shadow-violet-500/30 hover:shadow-violet-500/40 hover:scale-[1.02] transition-all focus:ring-4 focus:ring-violet-500/20 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Ke Beranda
                    </a>
                </div>

                <div class="mt-16 text-center">
                    <p class="text-xs font-medium text-slate-400">
                        &copy; {{ date('Y') }} SILADATA - Sistem Layanan Dokumen Akreditasi
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
