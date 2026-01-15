<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SISPI - Sistem Informasi Supervisi dan Pengawasan Internal</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            overflow-x: hidden;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.15);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: #1a202c;
        }

        .nav-logo {
            width: 45px;
            height: 45px;
            /* background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); */
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-logo img {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        .nav-brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            align-items: center;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #1e40af;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #1e40af;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-auth {
            display: flex;
            gap: 15px;
        }

        .nav-btn {
            padding: 10px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-btn-login {
            color: #1e40af;
            border: 2px solid #1e40af;
        }

        .nav-btn-login:hover {
            background: #1e40af;
            color: white;
        }

        .nav-btn-register {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border: 2px solid transparent;
        }

        .nav-btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.4);
        }

        .nav-btn-dashboard {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border: 2px solid transparent;
        }

        .nav-btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.4);
        }

        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .mobile-toggle span {
            width: 25px;
            height: 3px;
            background: #667eea;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            position: relative;
            overflow: hidden;
            padding-top: 85px;
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.2) 0%, transparent 50%);
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: white;
            margin-bottom: 25px;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content h1 span {
            display: block;
            background: linear-gradient(90deg, #fff 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            line-height: 1.8;
            font-weight: 400;
        }

        .hero-cta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .hero-btn {
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .hero-btn-primary {
            background: white;
            color: #1e40af;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .hero-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid white;
            backdrop-filter: blur(10px);
        }

        .hero-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
        }

        .hero-visual {
            position: relative;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .hero-card-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .hero-card-icon svg {
            width: 45px;
            height: 45px;
        }

        .hero-card h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero-card p {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            font-size: 1rem;
        }

        /* About Section */
        .about-section {
            padding: 120px 40px;
            background: #f7fafc;
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-label {
            display: inline-block;
            color: #1e40af;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 2.75rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .section-description {
            font-size: 1.15rem;
            color: #718096;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-text h3 {
            font-size: 2rem;
            color: #1a202c;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .about-text p {
            font-size: 1.05rem;
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-features {
            list-style: none;
            margin-top: 30px;
        }

        .about-features li {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 1.05rem;
            color: #4a5568;
        }

        .about-features li svg {
            width: 24px;
            height: 24px;
            stroke: #1e40af;
            flex-shrink: 0;
        }

        .about-image {
            position: relative;
        }

        .about-image-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 15px;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        /* Features Section */
        .features-section {
            padding: 120px 40px;
            background: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }

        .feature-item {
            background: white;
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            border: 1px solid #e2e8f0;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(30, 64, 175, 0.15);
            border-color: #1e40af;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .feature-icon svg {
            width: 35px;
            height: 35px;
            stroke: white;
        }

        .feature-item h3 {
            font-size: 1.35rem;
            color: #1a202c;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .feature-item p {
            color: #718096;
            line-height: 1.7;
            font-size: 1rem;
        }

        /* Minutes Section */
        .minutes-section {
            padding: 120px 40px;
            background: #f7fafc;
        }

        .minutes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        @include('components.minute-card-styles') .minutes-empty {
            text-align: center;
            color: #4a5568;
            font-size: 1rem;
            margin-top: 40px;
        }

        .minutes-more {
            margin-top: 40px;
            text-align: center;
        }

        .minutes-more-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
            box-shadow: 0 12px 30px rgba(30, 64, 175, 0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .minutes-more-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(30, 64, 175, 0.3);
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 40px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .cta-container {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .cta-container h2 {
            font-size: 3rem;
            color: white;
            margin-bottom: 25px;
            font-weight: 800;
            line-height: 1.2;
        }

        .cta-container p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer */
        footer {
            background: #1a202c;
            color: #a0aec0;
            padding: 60px 40px 30px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 50px;
        }

        .footer-brand h3 {
            font-size: 1.75rem;
            color: white;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .footer-brand p {
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .footer-column h4 {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #a0aec0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #1e40af;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #2d3748;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .nav-links {
                display: none;
            }

            .mobile-toggle {
                display: flex;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 50px;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-cta {
                justify-content: center;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .minutes-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .cta-container h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 640px) {
            .nav-container {
                padding: 15px 20px;
            }

            .hero-section {
                padding-top: 75px;
            }

            .hero-container {
                padding: 40px 20px;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1.05rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .minutes-section {
                padding: 80px 20px;
            }

            .minute-card {
                padding: 25px;
            }

            .minute-gallery {
                grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="/" class="nav-brand">
                <div class="nav-logo">
                    <img src="/img/LogoPolinema.png" alt=""LogoPolinema>
                </div>
                <span class="nav-brand-text">SISPI</span>
            </a>

            <ul class="nav-links">
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="#berita-acara">Berita Acara</a></li>
                <li><a href="#fitur">Fitur</a></li>
            </ul>

            <div class="nav-auth">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-btn nav-btn-dashboard">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn nav-btn-login">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-btn nav-btn-register">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>

            <div class="mobile-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="hero-bg-pattern"></div>
        <div class="hero-container">
            <div class="hero-content">
                <h1>
                    Sistem Informasi
                    <span>Supervisi dan Pengawasan Internal</span>
                </h1>
                <p class="hero-subtitle">
                    Solusi digital terpadu untuk manajemen audit internal, penilaian risiko, dan monitoring tindak
                    lanjut yang efektif dan efisien.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('login') }}" class="hero-btn hero-btn-primary">
                        Mulai Sekarang
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="#tentang" class="hero-btn hero-btn-secondary">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                stroke="#1e40af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>Audit Management</h3>
                    <p>Kelola seluruh proses audit internal dengan sistematis, dari perencanaan hingga pelaporan hasil
                        audit.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="about-section">
        <div class="section-container">
            <div class="section-header">
                <span class="section-label">Tentang Kami</span>
                <h2 class="section-title">Transformasi Digital untuk Audit Internal</h2>
                <p class="section-description">
                    SISPI hadir sebagai solusi komprehensif untuk meningkatkan efektivitas pengawasan internal
                    organisasi Anda melalui digitalisasi proses audit.
                </p>
            </div>

            <div class="about-content">
                <div class="about-text">
                    <h3>Mengapa Memilih SISPI?</h3>
                    <p>
                        SISPI dirancang khusus untuk memenuhi kebutuhan audit internal yang modern dan efisien. Dengan
                        fitur-fitur lengkap dan interface yang user-friendly, kami membantu tim audit Anda bekerja lebih
                        produktif.
                    </p>
                    <p>
                        Sistem kami mengintegrasikan seluruh proses pengawasan internal, mulai dari penyusunan peta
                        risiko, pelaksanaan audit, hingga monitoring tindak lanjut rekomendasi.
                    </p>

                    <ul class="about-features">
                        <li>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Manajemen audit terintegrasi dan terstruktur
                        </li>
                        <li>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Peta risiko yang komprehensif dan real-time
                        </li>
                        <li>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Sistem kolaborasi tim yang efektif
                        </li>
                        <li>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Keamanan data tingkat enterprise
                        </li>
                    </ul>
                </div>

                <div class="about-image">
                    <div class="about-image-card">
                        <div class="stats-grid">
                            @forelse ($welcomeStats ?? [] as $stat)
                                <div class="stat-item">
                                    <div class="stat-number">{{ $stat['display'] ?? '-' }}</div>
                                    <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
                                </div>
                            @empty
                                <div class="stat-item">
                                    <div class="stat-number">-</div>
                                    <div class="stat-label">Data belum tersedia</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="features-section">
        <div class="section-container">
            <div class="section-header">
                <span class="section-label">Fitur Unggulan</span>
                <h2 class="section-title">Fitur Lengkap untuk Audit yang Efektif</h2>
                <p class="section-description">
                    Berbagai fitur canggih yang dirancang untuk mendukung setiap tahapan proses audit internal Anda.
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <h3>Manajemen Audit</h3>
                    <p>Kelola kegiatan audit internal dengan sistematis, termasuk perencanaan, pelaksanaan, dan
                        pelaporan hasil audit secara digital.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3>Peta Risiko</h3>
                    <p>Identifikasi dan analisis risiko organisasi dengan visualisasi matriks risiko yang mudah dipahami
                        dan dikelola.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3>Laporan & Dokumentasi</h3>
                    <p>Buat laporan audit yang profesional dan kelola seluruh dokumentasi dengan sistem penyimpanan
                        digital yang aman.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3>Kolaborasi Tim</h3>
                    <p>Koordinasi antar tim audit dengan sistem approval, komentar, dan notifikasi yang terintegrasi
                        untuk workflow yang efisien.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h3>Monitoring Tindak Lanjut</h3>
                    <p>Pantau dan evaluasi implementasi rekomendasi audit dengan sistem tracking yang efektif dan
                        real-time monitoring.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h3>Keamanan Data</h3>
                    <p>Proteksi data dengan sistem keamanan berlapis, verifikasi email, dan kontrol akses berbasis role
                        yang ketat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Minutes Section -->
    <section id="berita-acara" class="minutes-section">
        <div class="section-container">
            <div class="section-header">
                <span class="section-label">Berita Acara</span>
                <h2 class="section-title">Ringkasan Kegiatan Terbaru</h2>
                <p class="section-description">
                    Pantau berita acara terbaru lengkap dengan dokumentasi rapat dan bukti visual.
                </p>
            </div>

            @if (isset($beritaAcaras) && $beritaAcaras->isNotEmpty())
                <div class="minutes-grid">
                    @foreach ($beritaAcaras as $minute)
                        @include('components.minute-card', ['minute' => $minute])
                    @endforeach
                </div>
                @if (!empty($moreMinutesExist))
                    <div class="minutes-more">
                        <a href="{{ route('welcome.berita-acara') }}" class="minutes-more-link">
                            Lihat semua berita acara
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @endif
            @else
                <p class="minutes-empty">Belum ada berita acara yang dapat ditampilkan.</p>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container">
            <h2>Siap Meningkatkan Efektivitas Audit Internal Anda?</h2>
            <p>
                Bergabunglah dengan organisasi-organisasi yang telah mempercayai SISPI untuk transformasi digital audit
                internal mereka.
            </p>
            <div class="cta-buttons">
                <a href="{{ route('login') }}" class="hero-btn hero-btn-primary">
                    Mulai Sekarang
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="hero-btn hero-btn-secondary">
                        Daftar Gratis
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>SISPI</h3>
                    <p>
                        Sistem Informasi Supervisi dan Pengawasan Internal yang dirancang untuk meningkatkan efektivitas
                        audit internal organisasi Anda.
                    </p>
                </div>

                <div class="footer-column">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#tentang">Tentang</a></li>
                        <li><a href="#berita-acara">Berita Acara</a></li>
                        <li><a href="#fitur">Fitur</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Akses</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @endif
                        @auth
                            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Bantuan</h4>
                    <ul class="footer-links">
                        <li><a href="/feedback">Feedback</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} SISPI. Sistem Informasi Supervisi dan Pengawasan Internal. All rights
                    reserved.</p>
            </div>
        </div>
    </footer>

    @include('components.minute-card-script')

    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>

</html>
