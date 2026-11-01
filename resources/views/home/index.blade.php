@php
    /** @var array<string, mixed> $progress */
    $summary = $progress['summary'] ?? [];
    $units = $progress['units'] ?? [];
@endphp

<x-public-layout title="Sistem Dokumen Akreditasi Perguruan Tinggi">

    {{-- Pure CSS untuk Hero & Navbar agar 100% presisi dan tajam sesuai desain referensi --}}
    <style>
        .siladata-hero-section {
            position: relative;
            overflow: hidden;
            background-color: #F5EEE4;
            padding-top: 24px;
            padding-bottom: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        .siladata-hero-container {
            position: relative;
            z-index: 10;
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Floating Pill Navbar */
        #siladata-hero-nav {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            background-color: #FFFFFF;
            border: 1px solid rgba(225, 218, 204, 0.75);
            border-radius: 9999px;
            padding: 12px 28px;
            box-shadow: 0 8px 30px rgba(17, 28, 46, 0.06);
            max-width: 980px;
            margin: 0 auto;
        }

        .siladata-nav-left {
            display: flex;
            align-items: baseline;
            gap: 12px;
        }

        .siladata-nav-brand {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.025em;
            color: #111C2E !important;
            text-decoration: none;
        }

        .siladata-nav-subtitle {
            display: none;
            font-size: 14px;
            font-weight: 500;
            color: #475467;
        }

        .siladata-nav-right {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .siladata-nav-link {
            font-size: 14.5px;
            font-weight: 600;
            color: #111C2E !important;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .siladata-nav-link:hover {
            color: #E87A36 !important;
        }

        .siladata-nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #111C2E;
            color: #FFFFFF !important;
            font-size: 14px;
            font-weight: 700;
            padding: 9px 26px;
            border-radius: 9999px;
            text-decoration: none;
            white-space: nowrap;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .siladata-nav-btn:hover {
            background-color: #1B2B43;
            transform: translateY(-1px);
        }

        /* 2-Column Hero Grid */
        .siladata-hero-row {
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
            padding-top: 24px;
            width: 100%;
        }

        .siladata-hero-col-left {
            width: 52%;
            flex: 0 0 52%;
            max-width: 52%;
            align-self: center;
            padding-top: 20px;
            padding-bottom: 44px;
            position: relative;
            z-index: 20;
        }

        .siladata-hero-col-right {
            width: 48%;
            flex: 0 0 48%;
            max-width: 48%;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            align-self: flex-end;
            position: relative;
            z-index: 10;
        }

        /* Badge */
        .siladata-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 9999px;
            border: 1.5px solid #D8986B;
            background-color: #F8ECE1;
            padding: 6px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: #9A4E23;
            margin-bottom: 18px;
        }

        /* Headline */
        .siladata-h1 {
            font-size: 34px;
            font-weight: 900;
            line-height: 1.14;
            letter-spacing: -0.025em;
            color: #111C2E;
            margin: 0;
        }

        .siladata-h1-accent {
            display: block;
            margin-top: 4px;
            color: #E87A36;
        }

        /* Description */
        .siladata-desc {
            margin-top: 18px;
            font-size: 16px;
            line-height: 1.6;
            color: #232B38;
            font-weight: 400;
            max-width: 480px;
        }

        /* CTA Buttons */
        .siladata-cta-group {
            margin-top: 28px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
        }

        .siladata-btn-dark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background-color: #111C2E;
            color: #FFFFFF !important;
            font-size: 15px;
            font-weight: 700;
            padding: 13px 32px;
            box-shadow: 0 8px 20px rgba(17, 28, 46, 0.18);
            text-decoration: none;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .siladata-btn-dark:hover {
            background-color: #1B2B43;
            transform: translateY(-1px);
        }

        .siladata-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 9999px;
            border: 2px solid #B45E30;
            background-color: transparent;
            color: #994A1F !important;
            font-size: 15px;
            font-weight: 700;
            padding: 11px 26px;
            text-decoration: none;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .siladata-btn-outline:hover {
            background-color: rgba(232, 122, 54, 0.08);
            transform: translateY(-1px);
        }

        /* ═══════════════════════════════════════════
           STAGE VISUAL CODINGAN HERO (RIGHT COLUMN)
           ═══════════════════════════════════════════ */
        .siladata-stage-box {
            position: relative;
            width: 100%;
            max-width: 500px;
            height: 425px;
            margin: 0 auto;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        /* SVG Concentric Target Circles */
        .siladata-concentric-circles {
            position: absolute;
            left: 52%;
            top: 48%;
            transform: translate(-50%, -50%);
            width: 355px;
            height: 355px;
            z-index: 1;
            pointer-events: none;
            user-select: none;
        }

        /* Person / Woman Image (ONLY the person is an image) */
        .siladata-woman-img {
            position: absolute;
            left: 52%;
            bottom: 0;
            transform: translateX(-50%);
            height: 385px;
            width: auto;
            max-width: none;
            z-index: 10;
            pointer-events: none;
            user-select: none;
            filter: drop-shadow(0 4px 12px rgba(17, 28, 46, 0.08));
        }

        /* Top-Right Dashboard + Upload Mockup Cards (Layered Behind Shoulder) */
        .siladata-stage-top-right {
            position: absolute;
            right: 0px;
            top: 14px;
            z-index: 4;
        }

        .siladata-mini-dashboard {
            position: relative;
            width: 165px;
            background: #FFFFFF;
            border-radius: 10px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 10px 24px -4px rgba(17, 28, 46, 0.08);
            overflow: hidden;
            padding-bottom: 7px;
        }

        .siladata-dash-topbar {
            background: #F8FAFC;
            border-bottom: 1px solid #EEF2F6;
            padding: 5px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .siladata-dash-title {
            font-size: 8px;
            font-weight: 700;
            color: #1E293B;
        }

        .siladata-dash-track {
            width: 38px;
            height: 3.5px;
            background: #E2E8F0;
            border-radius: 9999px;
            overflow: hidden;
        }

        .siladata-dash-fill {
            width: 55%;
            height: 100%;
            background: #E87A36;
        }

        .siladata-dash-body {
            position: relative;
            padding: 5px 8px 0 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .siladata-dash-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: -7px;
            width: 14px;
            background: #111C2E;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 5px;
            gap: 4px;
        }

        .siladata-dash-dot {
            width: 4.5px;
            height: 4.5px;
            border-radius: 9999px;
        }

        .siladata-bar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
        }

        .siladata-bar-label {
            font-size: 7px;
            color: #64748B;
            font-weight: 500;
            white-space: nowrap;
        }

        .siladata-bar-track {
            flex-grow: 1;
            height: 3.5px;
            background: #EEF2F6;
            border-radius: 9999px;
            overflow: hidden;
        }

        .siladata-bar-fill {
            height: 100%;
            border-radius: 9999px;
        }

        /* Upload Documents Card */
        .siladata-mini-upload {
            position: absolute;
            right: -8px;
            top: 40px;
            width: 132px;
            background: #FFFFFF;
            border-radius: 10px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 12px 28px -4px rgba(17, 28, 46, 0.16);
            padding: 7px 9px;
            z-index: 5;
        }

        .siladata-upload-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #F1F5F9;
        }

        .siladata-upload-title {
            font-size: 9px;
            font-weight: 800;
            color: #0F172A;
        }

        .siladata-upload-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 3px;
        }

        .siladata-upload-item-text {
            font-size: 7.5px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }

        /* Left Floating Card: Rating Kepuasan */
        .siladata-card-rating {
            position: absolute;
            left: -4px;
            top: 165px;
            z-index: 20;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 14px;
            padding: 8px 14px 9px 14px;
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 12px 28px -4px rgba(17, 28, 46, 0.12), 0 4px 10px -2px rgba(17, 28, 46, 0.05);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .siladata-card-rating-title {
            font-size: 12.5px;
            font-weight: 800;
            color: #111C2E;
            margin-bottom: 3.5px;
            white-space: nowrap;
        }

        /* Left Floating Pill: Data Terenkripsi & Aman */
        .siladata-card-lock {
            position: absolute;
            left: 10px;
            bottom: 72px;
            z-index: 20;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 9999px;
            padding: 6.5px 16px 6.5px 11px;
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 24px -4px rgba(17, 28, 46, 0.12);
            display: inline-flex;
            align-items: center;
            gap: 7.5px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .siladata-card-lock-text {
            font-size: 11.5px;
            font-weight: 700;
            color: #111C2E;
            white-space: nowrap;
        }

        /* Right Floating Card: 1.300+ Kampus */
        .siladata-card-kampus {
            position: absolute;
            right: 4px;
            bottom: 112px;
            z-index: 20;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 14px;
            padding: 8px 13px 9px 13px;
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 12px 28px -4px rgba(17, 28, 46, 0.12), 0 4px 10px -2px rgba(17, 28, 46, 0.05);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .siladata-card-kampus-title {
            font-size: 12.5px;
            font-weight: 800;
            color: #111C2E;
            margin-bottom: 4px;
            white-space: nowrap;
        }

        /* Bottom Dark Navy Bar */
        .siladata-bottom-bar {
            position: relative;
            z-index: 25;
            width: 100%;
            height: 26px;
            background-color: #111C2E;
        }

        /* Subtle Gentle Floating Animation */
        @keyframes siladata-float-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .siladata-float {
            animation: siladata-float-gentle 4s ease-in-out infinite;
        }

        .siladata-float-delay-1 {
            animation: siladata-float-gentle 4.5s ease-in-out 1s infinite;
        }

        .siladata-float-delay-2 {
            animation: siladata-float-gentle 5s ease-in-out 2s infinite;
        }

        /* Responsive Breakpoints (Tablet & Desktop) */
        @media (min-width: 640px) {
            #siladata-hero-nav {
                padding: 14px 34px;
            }
            .siladata-nav-brand {
                font-size: 24px;
            }
            .siladata-nav-subtitle {
                display: inline-block;
            }
            .siladata-hero-row {
                flex-direction: row;
                align-items: flex-end;
                padding-top: 24px;
            }
            .siladata-hero-col-left {
                width: 53%;
                flex: 0 0 53%;
                align-self: center;
                padding-top: 20px;
                padding-bottom: 40px;
            }
            .siladata-hero-col-right {
                width: 49%;
                flex: 0 0 49%;
                margin-left: -2%;
                justify-content: flex-end;
            }
            .siladata-h1 {
                font-size: 38px;
            }
        }

        @media (min-width: 1024px) {
            .siladata-hero-col-left {
                width: 52%;
                flex: 0 0 52%;
                padding-top: 28px;
                padding-bottom: 50px;
            }
            .siladata-hero-col-right {
                width: 50%;
                flex: 0 0 50%;
                margin-left: -2%;
            }
            .siladata-stage-box {
                max-width: 520px;
                height: 460px;
            }
            .siladata-concentric-circles {
                width: 370px;
                height: 370px;
            }
            .siladata-woman-img {
                height: 405px;
            }
            .siladata-card-rating {
                left: 0px;
                top: 180px;
            }
            .siladata-card-lock {
                left: 18px;
                bottom: 84px;
            }
            .siladata-card-kampus {
                right: 12px;
                bottom: 130px;
            }
            .siladata-stage-top-right {
                right: 4px;
                top: 22px;
            }
            .siladata-h1 {
                font-size: 45px;
            }
            .siladata-h1-line1 {
                white-space: nowrap;
            }
            .siladata-desc {
                font-size: 17px;
                max-width: 485px;
            }
        }

        @media (max-width: 639px) {
            .siladata-stage-box {
                max-width: 360px;
                height: 370px;
            }
            .siladata-concentric-circles {
                width: 280px;
                height: 280px;
            }
            .siladata-woman-img {
                height: 320px;
            }
            .siladata-card-rating {
                transform: scale(0.85);
                transform-origin: top left;
                top: 140px;
                left: -8px;
            }
            .siladata-card-lock {
                transform: scale(0.85);
                transform-origin: bottom left;
                bottom: 60px;
                left: 0px;
            }
            .siladata-card-kampus {
                transform: scale(0.85);
                transform-origin: bottom right;
                bottom: 100px;
                right: -6px;
            }
            .siladata-stage-top-right {
                transform: scale(0.8);
                transform-origin: top right;
                top: 10px;
                right: -6px;
            }
        }
    </style>

    {{-- ═══════════════════════════════════════════
         HERO SECTION (PERSIS SESUAI DESAIN FOTO)
    ═══════════════════════════════════════════ --}}
    <section class="siladata-hero-section">
        {{-- Background Organic Waves in Soft Sand / Taupe --}}
        <div class="pointer-events-none absolute inset-0 z-0">
            <svg class="h-full w-full object-cover" viewBox="0 0 1440 640" preserveAspectRatio="none" fill="none">
                <!-- Warm Sand Base -->
                <rect width="1440" height="640" fill="#F5EEE4" />

                <!-- Top Right Organic Waves Swooshes -->
                <path d="M860 0C940 100 1140 195 1440 245V0H860Z" fill="#DDD2C4" fill-opacity="0.45" />
                <path d="M970 0C1050 75 1210 145 1440 180V0H970Z" fill="#D0C3B3" fill-opacity="0.35" />

                <!-- Bottom Left Organic Waves Swooshes -->
                <path d="M0 380C140 395 280 465 390 640H0V380Z" fill="#E5DCD0" fill-opacity="0.55" />
                <path d="M0 460C120 470 220 525 300 640H0V460Z" fill="#DDD0C2" fill-opacity="0.45" />
                <path d="M0 535C90 545 160 580 215 640H0V535Z" fill="#D0C0B0" fill-opacity="0.35" />
            </svg>
        </div>

        <div class="siladata-hero-container">
            {{-- Floating Pill Navbar --}}
            <nav id="siladata-hero-nav">
                <div class="siladata-nav-left">
                    <a href="{{ url('/') }}" class="siladata-nav-brand">SILADATA</a>
                    <span class="siladata-nav-subtitle">Sistem Layanan Dokumen Akreditasi</span>
                </div>

                <div class="siladata-nav-right">
                    <a href="{{ route('harga') }}" class="siladata-nav-link">Harga</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="siladata-nav-btn">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="siladata-nav-btn">Masuk</a>
                    @endauth
                </div>
            </nav>

            {{-- Hero 2-Column Content --}}
            <div class="siladata-hero-row">
                {{-- Left Column: Typography, Badge & CTAs --}}
                <div class="siladata-hero-col-left">
                    {{-- Badge --}}
                    <div class="siladata-badge">
                        <svg style="width: 15px; height: 15px; color: #E87A36; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 3L11.8 8.2L17 10L11.8 11.8L10 17L8.2 11.8L3 10L8.2 8.2L10 3Z" />
                            <path d="M18 2L18.9 4.6L21.5 5.5L18.9 6.4L18 9L17.1 6.4L14.5 5.5L17.1 4.6L18 2Z" />
                            <circle cx="18" cy="14" r="1.2" />
                        </svg>
                        <span>Sesuai Standar Instrumen Akreditasi Terbaru</span>
                    </div>

                    {{-- Headline --}}
                    <h1 class="siladata-h1">
                        <span class="siladata-h1-line1">Sistem Dokumen Akreditasi</span>
                        <span class="siladata-h1-accent">Perguruan Tinggi</span>
                    </h1>

                    {{-- Description --}}
                    <p class="siladata-desc">
                        SILADATA membantu perguruan tinggi mengelola, mengunggah, dan memantau berkas akreditasi sesuai standar LAM Infokom, LAMEMBA, LAM-PTKes, dan LAMDIK secara efisien, efektif, dan transparan.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="siladata-cta-group">
                        @auth
                            <a href="{{ route('dashboard') }}" class="siladata-btn-dark">
                                Masuk ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('harga') }}" class="siladata-btn-dark">
                                Lihat Harga
                            </a>
                            <a href="{{ route('discussion') }}" class="siladata-btn-outline">
                                <span>Coba Demo Gratis</span>
                                <svg style="width: 19px; height: 19px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none">
                                    <rect x="2" y="3" width="20" height="18" rx="4" fill="#B45E30" />
                                    <circle cx="6.5" cy="6.5" r="1" fill="#FAF4EE" />
                                    <circle cx="10" cy="6.5" r="1" fill="#FAF4EE" />
                                    <line x1="2" y1="9.5" x2="22" y2="9.5" stroke="#FAF4EE" stroke-width="1.2" stroke-opacity="0.6" />
                                    <polygon points="10,12 16,15.2 10,18.4" fill="#FFFFFF" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right Column: Pure Coded Elements & Clean Person Cutout --}}
                <div class="siladata-hero-col-right">
                    <div class="siladata-stage-box">

                        {{-- 1. SVG Concentric Target Graphic (Codingan SVG) --}}
                        <div class="siladata-concentric-circles">
                            <svg viewBox="0 0 380 380" class="w-full h-full" fill="none">
                                <!-- Lingkaran Terluar Terracotta -->
                                <circle cx="190" cy="190" r="180" stroke="#BF602E" stroke-width="12" />
                                <!-- Lingkaran Kedua Sand / Peach -->
                                <circle cx="190" cy="190" r="165" stroke="#DFCAAF" stroke-width="18" />
                                <!-- Lingkaran Ketiga Terracotta Tebal -->
                                <circle cx="190" cy="190" r="140" stroke="#B8592B" stroke-width="32" />
                                <!-- Disc Navy Tua Pusat -->
                                <circle cx="190" cy="190" r="112" fill="#141E30" />
                            </svg>
                        </div>

                        {{-- 2. Mock Dashboard & Mini Upload Card (Codingan HTML & CSS di Belakang Bahu) --}}
                        <div class="siladata-stage-top-right siladata-float">
                            {{-- Mini Dashboard Card --}}
                            <div class="siladata-mini-dashboard">
                                <div class="siladata-dash-topbar">
                                    <span class="siladata-dash-title">Kelengkapan Berkas 55%</span>
                                    <div class="siladata-dash-track">
                                        <div class="siladata-dash-fill"></div>
                                    </div>
                                </div>
                                <div class="siladata-dash-body">
                                    {{-- Mini Dark Sidebar --}}
                                    <div class="siladata-dash-sidebar">
                                        <div class="siladata-dash-dot" style="background-color: #E87A36;"></div>
                                        <div class="siladata-dash-dot" style="background-color: #38BDF8;"></div>
                                        <div class="siladata-dash-dot" style="background-color: #94A3B8;"></div>
                                        <div class="siladata-dash-dot" style="background-color: #64748B;"></div>
                                    </div>

                                    {{-- Progress Bar Kriteria --}}
                                    <div class="siladata-bar-row">
                                        <span class="siladata-bar-label">Kriteria 1</span>
                                        <div class="siladata-bar-track">
                                            <div class="siladata-bar-fill" style="width: 85%; background-color: #E87A36;"></div>
                                        </div>
                                    </div>
                                    <div class="siladata-bar-row">
                                        <span class="siladata-bar-label">Kriteria 2</span>
                                        <div class="siladata-bar-track">
                                            <div class="siladata-bar-fill" style="width: 70%; background-color: #E87A36;"></div>
                                        </div>
                                    </div>
                                    <div class="siladata-bar-row">
                                        <span class="siladata-bar-label">Kriteria 3</span>
                                        <div class="siladata-bar-track">
                                            <div class="siladata-bar-fill" style="width: 90%; background-color: #E87A36;"></div>
                                        </div>
                                    </div>
                                    <div class="siladata-bar-row">
                                        <span class="siladata-bar-label">Kriteria 4</span>
                                        <div class="siladata-bar-track">
                                            <div class="siladata-bar-fill" style="width: 45%; background-color: #CBD5E1;"></div>
                                        </div>
                                    </div>
                                    <div class="siladata-bar-row">
                                        <span class="siladata-bar-label">Kriteria 5</span>
                                        <div class="siladata-bar-track">
                                            <div class="siladata-bar-fill" style="width: 30%; background-color: #CBD5E1;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Documents Card --}}
                            <div class="siladata-mini-upload">
                                <div class="siladata-upload-header">
                                    <svg style="width: 13px; height: 13px; color: #334155; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                        <path d="M12 12v9" />
                                        <path d="m8 16 4-4 4 4" />
                                    </svg>
                                    <span class="siladata-upload-title">Upload Documents</span>
                                </div>
                                <div class="siladata-upload-item">
                                    <svg style="width: 11px; height: 11px; fill: #16A34A; flex-shrink: 0;" viewBox="0 0 20 20" fill="#16A34A">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="siladata-upload-item-text">Upload Document</span>
                                </div>
                                <div class="siladata-upload-item">
                                    <svg style="width: 11px; height: 11px; fill: #16A34A; flex-shrink: 0;" viewBox="0 0 20 20" fill="#16A34A">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="siladata-upload-item-text">Upload Checklist</span>
                                </div>
                                <div class="siladata-upload-item">
                                    <svg style="width: 11px; height: 11px; fill: #16A34A; flex-shrink: 0;" viewBox="0 0 20 20" fill="#16A34A">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="siladata-upload-item-text">Upload Checklist</span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Woman / Person Visual Cutout (HANYA INI YANG GAMBAR) --}}
                        <img
                            src="{{ asset('images/hero-woman-clean.png') }}"
                            alt="Sistem Dokumen Akreditasi Perguruan Tinggi SILADATA"
                            class="siladata-woman-img"
                        />

                        {{-- 4. Left Floating Card: 4.9/5 Rating Kepuasan (Codingan HTML & SVG) --}}
                        <div class="siladata-card-rating siladata-float">
                            <div class="siladata-card-rating-title">4.9/5 Rating Kepuasan</div>
                            <div style="display: flex; align-items: center; gap: 3px;">
                                @for($i = 0; $i < 5; $i++)
                                    <svg style="width: 13.5px; height: 13.5px; fill: #F59E0B; flex-shrink: 0;" viewBox="0 0 20 20" fill="#F59E0B">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        {{-- 5. Left Floating Pill: Data Terenkripsi & Aman (Codingan HTML & SVG) --}}
                        <div class="siladata-card-lock siladata-float-delay-1">
                            <div style="width: 16px; height: 16px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 14px; height: 14px; fill: #B84D2B;" viewBox="0 0 24 24" fill="#B84D2B">
                                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                </svg>
                            </div>
                            <span class="siladata-card-lock-text">Data Terenkripsi & Aman</span>
                        </div>

                        {{-- 6. Right Floating Card: 1.300+ Kampus (Codingan HTML & SVG) --}}
                        <div class="siladata-card-kampus siladata-float-delay-2">
                            <div class="siladata-card-kampus-title">1.300+ Kampus</div>
                            <div style="display: flex; align-items: center; margin-top: 4px;">
                                {{-- Logo Badge 1: Gold / Amber Laurel Seal --}}
                                <div style="width: 20px; height: 20px; border-radius: 9999px; border: 1.5px solid #FFFFFF; background: linear-gradient(135deg, #F59E0B, #D97706); display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.15); z-index: 5;">
                                    <svg style="width: 11px; height: 11px; fill: #FFFFFF;" viewBox="0 0 24 24" fill="#FFFFFF">
                                        <path d="M12 2L15 8L22 9L17 14L18 21L12 18L6 21L7 14L2 9L9 8L12 2Z"/>
                                    </svg>
                                </div>
                                {{-- Logo Badge 2: Navy / Blue Crest Seal --}}
                                <div style="width: 20px; height: 20px; border-radius: 9999px; border: 1.5px solid #FFFFFF; background: linear-gradient(135deg, #1D4ED8, #0C4A6E); display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.15); margin-left: -5px; z-index: 4;">
                                    <svg style="width: 11px; height: 11px; fill: #BAE6FD;" viewBox="0 0 24 24" fill="#BAE6FD">
                                        <path d="M12 3L2 8L12 13L22 8L12 3ZM5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z"/>
                                    </svg>
                                </div>
                                {{-- Logo Badge 3: Crimson / Red University Seal --}}
                                <div style="width: 20px; height: 20px; border-radius: 9999px; border: 1.5px solid #FFFFFF; background: linear-gradient(135deg, #DC2626, #881337); display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.15); margin-left: -5px; z-index: 3;">
                                    <svg style="width: 11px; height: 11px; fill: #FFE4E6;" viewBox="0 0 24 24" fill="#FFE4E6">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/>
                                    </svg>
                                </div>
                                {{-- Logo Badge 4: Orange / Terracotta Sunburst Seal --}}
                                <div style="width: 20px; height: 20px; border-radius: 9999px; border: 1.5px solid #FFFFFF; background: linear-gradient(135deg, #EA580C, #B45309); display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.15); margin-left: -5px; z-index: 2;">
                                    <svg style="width: 11px; height: 11px; fill: #FEF3C7;" viewBox="0 0 24 24" fill="#FEF3C7">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </div>
                                {{-- Logo Badge 5: Dark Navy Seal --}}
                                <div style="width: 20px; height: 20px; border-radius: 9999px; border: 1.5px solid #FFFFFF; background: linear-gradient(135deg, #334155, #0F172A); display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.15); margin-left: -5px; z-index: 1;">
                                    <svg style="width: 11px; height: 11px; fill: #CBD5E1;" viewBox="0 0 24 24" fill="#CBD5E1">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Dark Navy Bar (Presisi sesuai garis navy bawah pada foto referensi) --}}
        <div class="siladata-bottom-bar"></div>
    </section>

    {{-- ═══════════════════════════════════════════
         TENTANG SILADATA
    ═══════════════════════════════════════════ --}}
    <section id="about" class="py-20 sm:py-28 bg-[#FAF7F2]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-2 items-center">
                <div>
                    <span class="inline-flex rounded-full border border-[#E8CDBA] bg-[#FBF3EC] px-4 py-1.5 text-xs sm:text-sm font-semibold text-[#A65D33]"
                          style="background-color: #FBF3EC; border-color: #E8CDBA; color: #A65D33;">
                        Tentang SILADATA
                    </span>
                    <h2 class="mt-4 text-3xl font-bold text-[#111C2E] leading-tight sm:text-4xl" style="color: #111C2E;">
                        Platform Digital Akreditasi yang <span class="text-[#E47A34]" style="color: #E47A34;">Terintegrasi</span>
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
                    <div class="rounded-3xl bg-gradient-to-br from-[#111C2E] via-[#1A2C46] to-[#25395A] p-7 text-white shadow-xl shadow-[#111C2E]/20"
                         style="background: linear-gradient(135deg, #111C2E 0%, #1A2C46 50%, #25395A 100%);">
                        <p class="text-4xl font-black">1.300+</p>
                        <p class="mt-2 text-sm font-medium text-amber-200">Kampus bergabung di seluruh Indonesia</p>
                    </div>
                    <div class="rounded-3xl bg-white border border-stone-200/80 p-7 shadow-sm">
                        <p class="text-4xl font-black text-[#111C2E]">99.9%</p>
                        <p class="mt-2 text-sm font-medium text-slate-500">Uptime layanan server yang terjamin</p>
                    </div>
                    <div class="rounded-3xl bg-white border border-stone-200/80 p-7 shadow-sm">
                        <p class="text-4xl font-black text-[#111C2E]">4</p>
                        <p class="mt-2 text-sm font-medium text-slate-500">LAM didukung: Infokom, EMBA, PTKes, DIK</p>
                    </div>
                    <div class="rounded-3xl bg-gradient-to-br from-[#E47A34] to-[#C86423] p-7 text-white shadow-xl shadow-[#E47A34]/20"
                         style="background: linear-gradient(135deg, #E47A34 0%, #C86423 100%);">
                        <p class="text-4xl font-black">24/7</p>
                        <p class="mt-2 text-sm font-medium text-orange-100">Dukungan tim kami selalu siap membantu</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FITUR UTAMA
    ═══════════════════════════════════════════ --}}
    <section class="relative bg-white/70 py-20 sm:py-24 backdrop-blur-sm border-y border-stone-200/70">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex rounded-full border border-[#E8CDBA] bg-[#FBF3EC] px-4 py-1.5 text-xs sm:text-sm font-semibold text-[#A65D33]"
                      style="background-color: #FBF3EC; border-color: #E8CDBA; color: #A65D33;">
                    Fitur Unggulan
                </span>
                <h2 class="mt-4 text-3xl font-bold text-[#111C2E] sm:text-4xl" style="color: #111C2E;">
                    Semua yang Anda Butuhkan, <span class="text-[#E47A34]" style="color: #E47A34;">Dalam Satu Platform</span>
                </h2>
                <p class="mt-4 mx-auto max-w-2xl text-base text-slate-600">
                    Dirancang khusus untuk kebutuhan akreditasi perguruan tinggi Indonesia berdasarkan standar LAM Infokom dan lembaga akreditasi mandiri lainnya.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Kartu Fitur 1 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-[#FAF7F2] p-7 shadow-sm border border-stone-200/80 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#111C2E] shadow-lg shadow-[#111C2E]/20 text-white"
                         style="background-color: #111C2E;">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#111C2E]">Dokumen Akreditasi</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Kelola semua berkas LED & LKPS secara terpusat, terstruktur, dan sesuai standar LAM.</p>
                </div>

                {{-- Kartu Fitur 2 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-[#FAF7F2] p-7 shadow-sm border border-stone-200/80 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#E47A34] shadow-lg shadow-[#E47A34]/20 text-white"
                         style="background-color: #E47A34;">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#111C2E]">Cloud Storage</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Simpan dokumen akreditasi di cloud yang aman, dapat diakses kapan saja dan di mana saja.</p>
                </div>

                {{-- Kartu Fitur 3 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-[#FAF7F2] p-7 shadow-sm border border-stone-200/80 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1A2C46] shadow-lg shadow-[#1A2C46]/20 text-white"
                         style="background-color: #1A2C46;">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#111C2E]">Monitoring Progress</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Pantau kelengkapan dokumen setiap program studi secara real-time dengan grafik interaktif.</p>
                </div>

                {{-- Kartu Fitur 4 --}}
                <div class="group relative overflow-hidden rounded-3xl bg-[#FAF7F2] p-7 shadow-sm border border-stone-200/80 transition-all hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#C86423] shadow-lg shadow-[#C86423]/20 text-white"
                         style="background-color: #C86423;">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#111C2E]">Keamanan Data</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Data dokumen dilindungi dengan enkripsi end-to-end dan akses berbasis peran yang ketat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         CTA SECTION
    ═══════════════════════════════════════════ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:py-24 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#111C2E] via-[#17253D] to-[#25395A] px-8 py-16 text-center text-white sm:px-12 sm:py-20 shadow-2xl shadow-[#111C2E]/25"
             style="background: linear-gradient(135deg, #111C2E 0%, #17253D 50%, #25395A 100%);">
            {{-- Decorative subtle glow --}}
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-[#E47A34]/10 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 h-48 w-48 rounded-full bg-white/5 blur-2xl"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-3xl">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-[#E47A34] backdrop-blur-sm"
                      style="color: #E47A34;">
                    Bergabung Sekarang
                </span>
                <h2 class="mt-6 text-3xl font-black text-white sm:text-4xl lg:text-5xl leading-tight">
                    Siap Bertransformasi Bersama SILADATA?
                </h2>
                <p class="mt-4 text-base text-slate-300 sm:text-lg max-w-xl mx-auto">
                    Lebih dari 1.300 kampus sudah bergabung. Saatnya kampus Anda menjadi bagian dari ekosistem digital akreditasi Indonesia.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('discussion') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full border-2 border-white/80 bg-white/10 px-8 py-3.5 text-sm sm:text-base font-bold text-white backdrop-blur-sm transition-all duration-200 hover:bg-white hover:text-[#111C2E] hover:shadow-lg hover:scale-[1.03] active:scale-[0.97] w-full sm:w-auto">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Jadwalkan Diskusi
                    </a>
                    <a href="{{ route('harga') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-[#E47A34] hover:bg-[#D36C27] px-8 py-3.5 text-sm sm:text-base font-bold text-white shadow-lg shadow-[#E47A34]/25 transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] w-full sm:w-auto"
                       style="background-color: #E47A34;">
                        Lihat Paket & Harga
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>