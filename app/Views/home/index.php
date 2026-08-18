<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarqiya - Platform Qur'an Insight via Analytics Monitoring</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/mainicon.png') ?>" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts Inter for modern SaaS look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.01em;
        }

        /* Modern Gradient & Glow Effects */
        .hero-section {
            background: radial-gradient(circle at top right, rgba(13, 110, 253, 0.05), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(13, 110, 253, 0.03), transparent 40%),
                        #fcfdff;
            position: relative;
        }

        .feature-card {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 1.2rem 2.5rem rgba(0, 0, 0, 0.06) !important;
            border-color: rgba(13, 110, 253, 0.15) !important;
        }

        .pricing-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .pricing-card.popular {
            border-color: #0d6efd;
            box-shadow: 0 1rem 3rem rgba(13, 110, 253, 0.12);
        }

        .store-badge {
            transition: transform 0.2s ease, opacity 0.2s;
        }

        .store-badge:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.85) !important;
        }

        .glass-mockup {
            background: linear-gradient(145deg, #1e2229 0%, #111318 100%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body class="bg-light text-dark">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm py-3 border-bottom border-light">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="#">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-graduation-cap fa-sm"></i>
                </div>
                <span class="fs-5 tracking-tight text-dark fw-bolder">Tarqiya</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#pricing">Harga</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#testimonials">Testimoni</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#download">Download</a></li>
                </ul>
                <div class="ms-lg-4 d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <a href="<?= base_url('login'); ?>" class="btn btn-link text-dark text-decoration-none fw-semibold btn-sm px-3">Masuk</a>
                    <a href="#pricing" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm">Coba Gratis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section py-5 overflow-hidden border-bottom">
        <div class="container py-lg-5 position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center gap-2 badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-4 border border-primary border-opacity-10">
                        <i class="fa-solid fa-bolt-lightning text-primary"></i> 
                        <span>Platform Tahfidz & Analytics #1</span>
                    </div>
                    <h1 class="display-4 fw-bold mb-4 text-dark tracking-tight lh-sm">
                        Kelola Sistem Tahfidz Jadi Lebih <span class="text-primary">Modern & Terukur</span>
                    </h1>
                    <p class="text-secondary lead mb-4 fw-normal" style="font-size: 1.125rem; line-height: 1.7;">
                        Platform terpadu untuk monitoring hafalan Al-Qur'an real-time, manajemen santri, ustadz, hingga administrasi keuangan pondok dalam satu ekosistem cerdas.
                    </p>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <a href="#pricing" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm fw-semibold">
                            Mulai Berlangganan <i class="fa-solid fa-arrow-right ms-2 fa-xs"></i>
                        </a>
                        <a href="<?= base_url('login'); ?>" class="btn btn-white btn-lg rounded-pill px-4 border shadow-sm fw-semibold text-dark bg-white">
                            Demo Sistem
                        </a>
                    </div>

                    <!-- Quick Stats / Trust Indicator -->
                    <div class="row mt-5 pt-4 border-top border-2 g-4">
                        <div class="col-4">
                            <h3 class="fw-bold text-dark mb-0">100%</h3>
                            <span class="text-muted small">Real-time Sync</span>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold text-dark mb-0">Multi</h3>
                            <span class="text-muted small">Role Akses</span>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold text-dark mb-0">24/7</h3>
                            <span class="text-muted small">Akses Sistem</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- Mockup Card Interaktif Elegan -->
                    <div class="card glass-mockup border-0 rounded-4 text-white p-4 position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 bg-primary opacity-20 rounded-circle blur-3xl" style="width: 250px; height: 250px; margin-right: -50px; margin-top: -50px;"></div>

                        <div class="card-body p-2 position-relative">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="bg-danger rounded-circle" style="width: 10px; height: 10px; display:inline-block;"></span>
                                    <span class="bg-warning rounded-circle" style="width: 10px; height: 10px; display:inline-block;"></span>
                                    <span class="bg-success rounded-circle" style="width: 10px; height: 10px; display:inline-block;"></span>
                                </div>
                                <span class="badge bg-white bg-opacity-10 px-3 py-1 rounded-pill small text-light fw-normal">Live Analytics Preview</span>
                            </div>

                            <div class="text-center py-4">
                                <div class="bg-primary bg-opacity-25 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 72px; height: 72px;">
                                    <i class="fa-solid fa-chart-pie fa-xl"></i>
                                </div>
                                <h4 class="fw-bold text-white mb-2">Dashboard Multi-Role</h4>
                                <p class="text-white-50 small mb-4 px-3 lh-base">Admin, Ustadz, dan Wali Santri terintegrasi dalam satu kendali cerdas berkecepatan tinggi.</p>

                                <div class="bg-white bg-opacity-10 rounded-3 p-3 text-start d-flex align-items-center gap-3 border border-white border-opacity-10">
                                    <div class="bg-success text-white rounded-2 p-2 px-3 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-arrow-trend-up"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-white small">Statistik Hafalan Qur'an</h6>
                                        <small class="text-white-50" style="font-size: 0.75rem;">Update setoran santri otomatis tercatat</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="text-primary fw-semibold small text-uppercase tracking-wider">Fitur Unggulan</span>
                <h2 class="fw-bold mt-2">Dirancang Khusus untuk Efisiensi Lembaga</h2>
                <p class="text-muted">Solusi terstruktur untuk menjawab tantangan operasional pondok pesantren di era digital.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 feature-card bg-light bg-opacity-50">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-4" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-book-quran fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Monitoring Hafalan Qur'an</h5>
                        <p class="text-secondary small mb-0 lh-base">Pencatatan setoran hafalan berdasarkan Juz & Surah lengkap dengan statistik perkembangan harian santri secara visual.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 feature-card bg-light bg-opacity-50">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-4" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-users-gear fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Manajemen Multi-Role</h5>
                        <p class="text-secondary small mb-0 lh-base">Pemisahan hak akses yang ketat dan aman antara Admin Pusat, Guru/Ustadz pengampu kelas, dan Wali Santri.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 feature-card bg-light bg-opacity-50">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-4" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-wallet fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Administrasi & Keuangan</h5>
                        <p class="text-secondary small mb-0 lh-base">Pencatatan riwayat tagihan, SPP, dan kuitansi pembayaran yang transparan serta mudah diakses wali santri.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="text-primary fw-semibold small text-uppercase tracking-wider">Investasi Terjangkau</span>
                <h2 class="fw-bold mt-2">Pilihan Paket Langganan</h2>
                <p class="text-muted">Pilih kapasitas yang paling sesuai dengan skala pertumbuhan lembaga Anda.</p>
            </div>
            <div class="row g-4 justify-content-center align-items-stretch">
                <!-- Paket Starter -->
                <div class="col-lg-4">
                    <div class="card pricing-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column">
                        <h4 class="fw-bold mb-1">Starter</h4>
                        <p class="text-muted small mb-4">Cocok untuk TPQ / Madrasah Diniyah</p>
                        <h3 class="fw-bold text-dark mb-4">Rp 150rb <span class="fs-6 text-muted fw-normal">/bulan</span></h3>
                        <ul class="list-unstyled mb-4 small text-secondary d-flex flex-column gap-2">
                            <li><i class="fa-solid fa-check text-success me-2"></i>Max 100 Santri</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Monitoring Hafalan Qur'an</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Akses Guru & Wali Santri</li>
                        </ul>
                        <button class="btn btn-outline-primary rounded-pill w-100 mt-auto fw-semibold py-2">Pilih Paket</button>
                    </div>
                </div>
                <!-- Paket Professional -->
                <div class="col-lg-4">
                    <div class="card pricing-card popular shadow-lg rounded-4 p-4 h-100 bg-white position-relative d-flex flex-column">
                        <span class="position-absolute top-0 start-50 translate-middle badge bg-primary px-3 py-1 rounded-pill fw-semibold shadow-sm">Terlaris</span>
                        <h4 class="fw-bold mb-1 mt-2">Professional</h4>
                        <p class="text-muted small mb-4">Untuk Pesantren Berkembang</p>
                        <h3 class="fw-bold text-primary mb-4">Rp 350rb <span class="fs-6 text-muted fw-normal">/bulan</span></h3>
                        <ul class="list-unstyled mb-4 small text-secondary d-flex flex-column gap-2">
                            <li><i class="fa-solid fa-check text-success me-2"></i>Unlimited Santri</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Modul Keuangan & Tagihan SPP</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Notifikasi WhatsApp Otomatis</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Backup Database Rutin</li>
                        </ul>
                        <button class="btn btn-primary rounded-pill w-100 mt-auto fw-semibold py-2 shadow-sm">Pilih Paket</button>
                    </div>
                </div>
                <!-- Paket Enterprise -->
                <div class="col-lg-4">
                    <div class="card pricing-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column">
                        <h4 class="fw-bold mb-1">Enterprise</h4>
                        <p class="text-muted small mb-4">Untuk Yayasan / Pesantren Besar</p>
                        <h3 class="fw-bold text-dark mb-4">Rp 750rb <span class="fs-6 text-muted fw-normal">/bulan</span></h3>
                        <ul class="list-unstyled mb-4 small text-secondary d-flex flex-column gap-2">
                            <li><i class="fa-solid fa-check text-success me-2"></i>Semua Fitur Professional</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Custom Domain (nama-pesantren.com)</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i>Prioritas Support 24/7</li>
                        </ul>
                        <button class="btn btn-outline-primary rounded-pill w-100 mt-auto fw-semibold py-2">Pilih Paket</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 bg-white border-top">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <span class="text-primary fw-semibold small text-uppercase tracking-wider">Testimoni</span>
                <h2 class="fw-bold mt-2">Apa Kata Pengasuh & Pengurus?</h2>
                <p class="text-muted">Kisah sukses lembaga yang telah bertransformasi menggunakan platform kami.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card bg-light border-0 p-4 rounded-4 h-100 d-flex flex-column justify-content-between">
                        <p class="text-secondary italic mb-4" style="line-height: 1.7;">"Semenjak pakai platform ini, rekapitulasi hafalan santri jadi jauh lebih transparan. Wali santri bisa langsung memantau perkembangan anak dari rumah secara real-time."</p>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">K.H. Ahmad Fauzi</h6>
                            <small class="text-muted">Pimpinan Pondok Pesantren</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0 p-4 rounded-4 h-100 d-flex flex-column justify-content-between">
                        <p class="text-secondary italic mb-4" style="line-height: 1.7;">"Pengelolaan administrasi keuangan dan pencatatan absensi ustadz sangat terbantu. Sangat direkomendasikan bagi pesantren yang ingin bertransformasi secara digital."</p>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Ustadz M. Rizki, M.Pd.</h6>
                            <small class="text-muted">Kepala Administrasi Pesantren</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download App Section -->
    <section id="download" class="py-5 bg-dark text-white position-relative overflow-hidden">
        <div class="position-absolute top-0 end-0 bg-primary opacity-10 rounded-circle blur-3xl" style="width: 400px; height: 400px; margin-right: -100px; margin-top: -100px;"></div>
        <div class="container py-4 text-center position-relative">
            <h2 class="fw-bold mb-3">Unduh Aplikasi Mobile Tarqiya</h2>
            <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 600px; font-size: 1.1rem;">Pantau perkembangan hafalan santri dan kelola administrasi kapan saja dan di mana saja melalui genggaman Anda.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="store-badge">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play" height="56">
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-secondary py-4 border-top small">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <p class="mb-0">&copy; 2026 Tarqiya. Product by @radicreative. All rights reserved.</p>
            <p class="mb-0 text-muted">Platform Digitalisasi Pesantren Modern Indonesia.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>