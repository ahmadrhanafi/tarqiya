<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarqiya Apps | <?= $title ?? 'Dashboard' ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/mainicon.png') ?>" type="image/png">

    <!-- Bootstrap 5.3 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- jQuery & Select2 JS (Pastikan jQuery diload duluan) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --sidebar-bg: #002127;
            --main-bg: #f4f7f6;
            --emerald-green: #50C878;
            --dark-emerald: #097969;
            --teal-secondary: #008080;
            --deep-teal: #005F73;
            --off-white: #F8F9FA;
            --light-gray: #E9ECEF;
            --dark-card: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--main-bg);
            color: #334155;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        /* Navbar */
        /* Styling tambahan untuk memperhalus interaksi */
        .dropdown-toggle-no-caret::after {
            display: none !important;
        }

        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }

        .hover-bg-danger-subtle:hover {
            background-color: #f8d7da !important;
            color: #842029 !important;
        }

        /* Dark Mode Global Support (Bisa disesuaikan dengan root body class) */
        /* Warna default (Light Mode) */
        .text-dark-mode {
            color: #212529 !important;
        }

        /* Warna saat Dark Mode aktif */
        body.dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .text-dark-mode {
            color: #ffffff !important;
        }

        body.dark-mode .navbar {
            background-color: #1e1e1e !important;
            border-color: #2c2c2c !important;
        }

        body.dark-mode .navbar h5 {
            color: #ffffff !important;
        }

        body.dark-mode .dropdown-menu {
            background-color: #1e1e1e !important;
            border: 1px solid #2c2c2c !important;
        }

        .dropdown-menu {
            max-height: none !important;
            overflow: visible !important;
            height: auto !important;
        }

        body.dark-mode .dropdown-item {
            color: #e0e0e0 !important;
        }

        body.dark-mode .hover-bg-light:hover {
            background-color: #2c2c2c !important;
        }

        body.dark-mode .btn-light {
            background-color: #2c2c2c !important;
            border-color: #3d3d3d !important;
            color: #e0e0e0 !important;
        }

        /* --- Sidebar Styling Profesional --- */
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            width: 270px !important;
            min-width: 270px !important;
            max-width: 270px !important;
            box-sizing: border-box !important;
            transition: all 0.3s ease-in-out;
            z-index: 1050;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
        }

        .sidebar .logo-box {
            padding: 24px 20px 16px 20px;
        }

        /* Styling Dasar Nav Link di Sidebar */
        #mainSidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.2s ease-in-out;
            font-size: 0.9rem;
        }

        #mainSidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(3px);
        }

        #mainSidebar .nav-link.active {
            color: #ffffff !important;
            background-color: #198754 !important;
            /* Warna aksen hijau utama */
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        .hover-danger-bg:hover {
            background-color: rgba(220, 53, 69, 0.15) !important;
            color: #ff6b6b !important;
        }

        .custom-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Sinkronisasi saat Dark Mode Global Aktif */
        body.dark-mode #mainSidebar {
            background-color: #1a1a1a !important;
            border-right: 1px solid #2c2c2c;
        }

        .nav-link {
            color: #94a3b8 !important;
            padding: 12px 18px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 10px;
            margin: 4px 12px;
            transition: all 0.2s ease;
        }

        /* --- Pengaturan Ikon FontAwesome agar Lebih Mantap --- */
        .nav-link i {
            font-size: 1.1rem;
            width: 28px;
            /* Memberikan ruang tetap agar teks menu sejajar rapi */
            text-align: center;
            margin-right: 12px;
            /* Jarak pas antara ikon dan teks */
            transition: transform 0.2s ease, color 0.2s ease;
            color: var(--emerald-green);
            /* Memberikan warna aksen hijau khas pada ikon */
        }

        .nav-link:hover {
            background: rgba(10, 233, 203, 0.34);
            color: var(--emerald-green) !important;
        }

        .nav-link:hover i {
            transform: scale(1.15) translateX(2px);
            /* Efek membesar sedikit saat di-hover */
            color: var(--emerald-green);
        }

        .nav-link.active {
            background: var(--emerald-green);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(10, 233, 203, 0.34);
        }

        /* Ikon pada menu yang sedang aktif otomatis berubah jadi putih */
        .nav-link.active i {
            color: #ffffff !important;
        }

        .custom-divider {
            width: 75%;
            height: 2px;
            background: rgba(10, 233, 203, 0.34);
            margin: 12px auto;
            border-radius: 2px;
        }

        /* --- Navbar Atas (Header) --- */
        .navbar-main {
            background: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            height: 70px;
            padding: 0 24px;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        /* Styling khusus hover tombol Keluar Akun agar aman di Dark Mode */
        .custom-logout-hover:hover {
            background-color: rgba(220, 53, 69, 0.15) !important;
            /* Background merah transparan lembut */
            color: #ff6b6b !important;
            /* Warna teks merah sedikit lebih terang agar kontras di dark mode */
        }

        .custom-logout-hover:hover i,
        .custom-logout-hover:hover span {
            color: #ff6b6b !important;
            /* Memastikan ikon dan teks ikut berubah kontras */
        }

        /* --- Main Content Area --- */
        main {
            padding: 28px;
            min-height: calc(100vh - 70px);
        }

        /* --- Penyesuaian Dark Mode untuk Tabel & Card --- */
        body.dark-mode .card,
        body.dark-mode .table-responsive,
        body.dark-mode table.table {
            background-color: #1a1a1a !important;
            color: #e0e0e0 !important;
            border-color: #2c2c2c !important;
        }

        /* Warna latar belakang baris tabel saat dark mode */
        body.dark-mode .table {
            --bs-table-bg: #1a1a1a;
            --bs-table-color: #e0e0e0;
            --bs-table-border-color: #2c2c2c;
            color: #e0e0e0 !important;
        }

        /* Header tabel jadi sedikit lebih gelap elegan */
        body.dark-mode .table thead th {
            background-color: #222222 !important;
            color: #ffffff !important;
            border-bottom: 2px solid #333333 !important;
        }

        /* Sel / baris tabel saat dark mode */
        body.dark-mode .table td,
        body.dark-mode .table th {
            background-color: #1a1a1a !important;
            color: #d1d5db !important;
            border-color: #2c2c2c !important;
        }

        /* Efek hover baris tabel saat kursor diarahkan */
        body.dark-mode .table-hover tbody tr:hover td {
            background-color: #252525 !important;
            color: #ffffff !important;
        }

        /* --- Styling Footer Dashboard Profesional --- */
        .footer-dashboard {
            background-color: #ffffff;
            border-color: #e2e8f0 !important;
            font-size: 0.85rem;
        }

        /* Sinkronisasi Footer dengan Dark Mode Global */
        body.dark-mode .footer-dashboard {
            background-color: #1a1a1a !important;
            border-color: #2c2c2c !important;
        }

        /* Efek Animasi Titik Hijau Status Server */
        @keyframes custom-ping {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            70% {
                transform: scale(2.2);
                opacity: 0;
            }

            100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .animate-ping {
            animation: custom-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* Responsive Mobile Handling */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -270px;
            }

            .sidebar.active {
                left: 0;
            }
        }

        /* ============================================ */
        /* PRATINJAU/PREVIEW E-KARTU PADA DETAIL SANTRI */
        /* ============================================ */
        /* Container Utama Kartu Pratinjau */
        .web-card-preview {
            position: relative;
            width: 100%;
            max-width: 100%;
            aspect-ratio: 1.58 / 1;
            /* Mengikuti rasio kartu ID */
            border-radius: 10px;
            overflow: hidden;
            background-color: #198754;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Gambar Background Template */
        .web-card-preview img.bg-template {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        /* Kotak Foto Pratinjau */
        .web-photo-box {
            position: absolute;
            top: 26%;
            left: 6%;
            width: 22%;
            height: 52%;
            border-radius: 4px;
            overflow: hidden;
            z-index: 2;
            background: #eee;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Posisi Teks Menggunakan Persentase (%) agar Fleksibel di Web */
        .w-text {
            position: absolute;
            z-index: 2;
            font-family: sans-serif;
            color: #333;
            /* Sesuaikan warna teks jika background terang/gelap */
            font-size: 11px;
            /* Ukuran aman untuk preview web */
            white-space: nowrap;
        }

        /* Sesuaikan titik koordinat (top & left) ini dengan letak kolom kosong pada gambar kartumu */
        .pos-w-nama {
            top: 27%;
            left: 32%;
            font-weight: bold;
            font-size: 13px;
        }

        .pos-w-nis {
            top: 40%;
            left: 32%;
            font-family: monospace;
        }

        .pos-w-ttl {
            top: 52%;
            left: 32%;
        }

        .pos-w-hp {
            top: 64%;
            left: 32%;
        }

        .pos-w-alamat {
            top: 76%;
            left: 32%;
            font-size: 10px;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* --- Pengaturan Responsive Mobile Sidebar --- */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed !important;
                top: 0;
                left: -270px !important;
                height: 100vh;
                transition: left 0.3s ease-in-out;
                z-index: 1060 !important;
                /* Di atas elemen lain */
            }

            .sidebar.active {
                left: 0 !important;
            }

            /* Backdrop / Lapisan hitam transparan di belakang sidebar saat aktif di mobile */
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(2px);
                z-index: 1040;
            }

            .modal {
                z-index: 1060 !important;
            }

            .modal-backdrop {
                z-index: 1050 !important;
            }

            .sidebar-backdrop.active {
                display: block;
            }
        }

        @media (max-width: 768px) {

            .container,
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
                overflow: visible !important;
                /* Ubah dari hidden ke visible */
            }

            .navbar,
            .navbar-main,
            header {
                overflow: visible !important;
            }

            .dropdown {
                position: relative !important;
                overflow: visible !important;
            }

            .dropdown-menu.show {
                display: block !important;
                position: absolute !important;
                top: 100% !important;
                right: 0 !important;
                left: auto !important;
                float: none !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                /* Pastikan tidak ada scrollbar lokal */
                transform: none !important;
                z-index: 99999 !important;
            }

            .web-card-preview,
            div[style*="aspect-ratio"] {
                max-width: 100% !important;
                height: auto !important;
            }

            .d-flex:not(.flex-row) {
                max-width: 100%;
            }

            .input-group {
                flex-wrap: nowrap !important;
            }

            .card-body .d-grid {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {

            /* Penyesuaian ekstra untuk layar HP sangat kecil (di bawah 480px) */
            body {
                font-size: 13px !important;
            }

            /* Pengecilan ukuran font spesifik pada pratinjau kartu di HP sempit agar tidak keluar jalur */
            .web-card-preview div[style*="position: absolute"],
            .position-relative div[style*="position: absolute"] {
                /* Sedikit menyesuaikan skala font absolut jika diperlukan */
                transform-origin: left center;
            }
        }
    </style>
</head>