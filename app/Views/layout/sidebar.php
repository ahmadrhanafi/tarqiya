<aside class="sidebar d-flex flex-column transition-all duration-300" id="mainSidebar"
    style="width: 260px; position: fixed; top: 0; left: 0; height: 100vh; z-index: 1050; background-color: #111827; color: #f8fafc;">

    <!-- Logo & Brand Header + Tombol Toggle -->
    <div class="sidebar-header-box d-flex align-items-center justify-between px-3 pt-4 pb-3">
        <div class="d-flex align-items-center gap-3 overflow-hidden sidebar-brand-wrapper">
            <img src="<?= base_url('assets/img/mainicon.png') ?>" alt="Tarqiya"
                style="width: 40px; height: auto; min-width: 40px;">
            <div class="sidebar-text text-truncate">
                <h5 class="m-0 fw-bold tracking-wide text-white" style="font-size: 1.3rem; letter-spacing: 0.5px;">
                    TARQIYA</h5>
                <small style="font-size: 0.65rem; font-weight: 700; color: #097969; letter-spacing: 1.5px;">Tahfidz
                    Analytics</small>
            </div>
        </div>
        <!-- Tombol Buka/Tutup Sidebar -->
        <button id="sidebarToggle" class="btn btn-sm text-white-50 p-1 ms-auto shadow-none" title="Buka/Tutup Sidebar"
            style="border: none; background: transparent;">
            <i class="fa-solid fa-bars-staggered fs-5"></i>
        </button>
    </div>

    <!-- Garis Pemisah Custom -->
    <div class="custom-divider mx-3 my-1"></div>

    <!-- Navigasi Menu Utama -->
    <nav class="nav flex-column my-2 flex-grow-1 px-2 overflow-y-auto overflow-x-hidden" style="scrollbar-width: thin;">
        <?php
        $role = session()->get('role');
        $dashboardUrl = base_url($role . '/dashboard');
        ?>

        <!-- Link Dashboard Umum -->
        <div class="text-uppercase small px-3 mt-3 mb-1 text-sidebar-label sidebar-text"
            style="font-size: 0.65rem; color: rgba(10, 233, 203, 0.34); letter-spacing: 1px;">Overview</div>
        <a href="<?= $dashboardUrl ?>"
            class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (current_url() == $dashboardUrl) ? 'active' : '' ?>"
            title="Dashboard">
            <i class="fa-solid fa-gauge-high fa-fw fs-5"></i> <span class="sidebar-text">Dashboard</span>
        </a>

        <!-- MENU KHUSUS SUPERADMIN (PUSAT) -->
        <?php if ($role === 'superadmin'): ?>
            <div class="text-uppercase small px-3 mt-3 mb-1 text-sidebar-label sidebar-text"
                style="font-size: 0.65rem; color: rgba(10, 233, 203, 0.34); letter-spacing: 1px;">Menu Pusat</div>
            <a href="<?= base_url('superadmin/tenants') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('superadmin/tenants*')) ? 'active' : '' ?>"
                title="Data Tenants">
                <i class="fa-solid fa-school fa-fw fs-5"></i> <span class="sidebar-text">Data Tenants</span>
            </a>
            <a href="<?= base_url('superadmin/users') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('superadmin/users*')) ? 'active' : '' ?>"
                title="Kelola Pengguna">
                <i class="fa-solid fa-users-gear fa-fw fs-5"></i> <span class="sidebar-text">Kelola Pengguna</span>
            </a>
            <a href="<?= base_url('superadmin/subscription') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('superadmin/subscription*')) ? 'active' : '' ?>"
                title="Langganan & Tagihan">
                <i class="fa-solid fa-file-invoice-dollar fa-fw fs-5"></i> <span class="sidebar-text">Langganan &
                    Tagihan</span>
            </a>
            <a href="<?= base_url('superadmin/settings') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('superadmin/settings*')) ? 'active' : '' ?>"
                title="Pengaturan">
                <i class="fa-solid fa-gears fa-fw fs-5"></i> <span class="sidebar-text">Pengaturan</span>
            </a>
        <?php endif; ?>

        <!-- MENU KHUSUS ADMIN -->
        <?php if ($role === 'admin'): ?>
            <div class="text-uppercase small px-3 mt-3 mb-1 text-sidebar-label sidebar-text"
                style="font-size: 0.65rem; color: rgba(10, 233, 203, 0.34); letter-spacing: 1px;">Menu Admin</div>
            <a href="<?= base_url('admin/kelas') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/kelas*')) ? 'active' : '' ?>"
                title="Data Kelas">
                <i class="fa-solid fa-school fa-fw fs-5"></i> <span class="sidebar-text">Data Kelas</span>
            </a>
            <a href="<?= base_url('admin/santri') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/santri*')) ? 'active' : '' ?>"
                title="Data Santri">
                <i class="fa-solid fa-user-graduate fa-fw fs-5"></i> <span class="sidebar-text">Data Santri</span>
            </a>
            <a href="<?= base_url('admin/ustadz') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/ustadz*')) ? 'active' : '' ?>"
                title="Data Ustadz">
                <i class="fa-solid fa-chalkboard-user fa-fw fs-5"></i> <span class="sidebar-text">Data Ustadz</span>
            </a>
            <a href="<?= base_url('admin/wali-santri') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/wali-santri*')) ? 'active' : '' ?>"
                title="Data Wali Santri">
                <i class="fa-solid fa-users fa-fw fs-5"></i> <span class="sidebar-text">Data Wali Santri</span>
            </a>
            <a href="<?= base_url('admin/hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/hafalan*')) ? 'active' : '' ?>"
                title="Data Hafalan">
                <i class="fa-solid fa-book-quran fa-fw fs-5"></i> <span class="sidebar-text">Data Hafalan</span>
            </a>
            <a href="<?= base_url('admin/statistik-hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/statistik-hafalan*')) ? 'active' : '' ?>"
                title="Statistik Hafalan">
                <i class="fa-solid fa-chart-line fa-fw fs-5"></i> <span class="sidebar-text">Statistik Hafalan</span>
            </a>
            <a href="<?= base_url('admin/administrasi') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('admin/administrasi*')) ? 'active' : '' ?>"
                title="Administrasi">
                <i class="fa-solid fa-file-invoice-dollar fa-fw fs-5"></i> <span class="sidebar-text">Administrasi</span>
            </a>
        <?php endif; ?>

        <!-- MENU KHUSUS GURU/PENGAJAR -->
        <?php if ($role === 'guru'): ?>
            <div class="text-uppercase small px-3 mt-3 mb-1 text-sidebar-label sidebar-text"
                style="font-size: 0.65rem; color: rgba(10, 233, 203, 0.34); letter-spacing: 1px;">Menu Pengajar</div>
            <a href="<?= base_url('guru/santri') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('guru/santri*')) ? 'active' : '' ?>"
                title="Data Santri">
                <i class="fa-solid fa-people-group fa-fw fs-5"></i> <span class="sidebar-text">Data Santri</span>
            </a>
            <a href="<?= base_url('guru/hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('guru/hafalan*')) ? 'active' : '' ?>"
                title="Data Hafalan">
                <i class="fa-solid fa-book-open fa-fw fs-5"></i> <span class="sidebar-text">Data Hafalan</span>
            </a>
            <a href="<?= base_url('guru/statistik-hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('guru/statistik-hafalan*')) ? 'active' : '' ?>"
                title="Statistik Hafalan">
                <i class="fa-solid fa-chart-bar fa-fw fs-5"></i> <span class="sidebar-text">Statistik Hafalan</span>
            </a>
            <a href="<?= base_url('guru/riwayat-hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('guru/riwayat-hafalan*')) ? 'active' : '' ?>"
                title="Riwayat Hafalan">
                <i class="fa-solid fa-history fa-fw fs-5"></i> <span class="sidebar-text">Riwayat Hafalan</span>
            </a>
        <?php endif; ?>

        <!-- MENU KHUSUS WALI SANTRI -->
        <?php if ($role === 'wali'): ?>
            <div class="text-uppercase small px-3 mt-3 mb-1 text-sidebar-label sidebar-text"
                style="font-size: 0.65rem; color: rgba(10, 233, 203, 0.34); letter-spacing: 1px;">Menu Wali</div>
            <a href="<?= base_url('wali/statistik-hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('wali/statistik-hafalan*')) ? 'active' : '' ?>"
                title="Statistik Hafalan">
                <i class="fa-solid fa-chart-bar fa-fw fs-5"></i> <span class="sidebar-text">Statistik Hafalan</span>
            </a>
            <a href="<?= base_url('wali/riwayat-hafalan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('wali/riwayat-hafalan*')) ? 'active' : '' ?>"
                title="Riwayat Hafalan">
                <i class="fa-solid fa-history fa-fw fs-5"></i> <span class="sidebar-text">Riwayat Hafalan</span>
            </a>
            <a href="<?= base_url('wali/riwayat-tagihan') ?>"
                class="nav-link px-3 py-2 rounded-3 mb-1 d-flex align-items-center gap-3 <?= (url_is('wali/riwayat-tagihan*')) ? 'active' : '' ?>"
                title="Riwayat Tagihan">
                <i class="fa-solid fa-receipt fa-fw fs-5"></i> <span class="sidebar-text">Riwayat Tagihan</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Menu Bagian Bawah (Logout) -->
    <div class="nav flex-column mt-auto pb-3 px-2">
        <div class="custom-divider mx-3 mb-2"></div>
        <a href="<?= base_url('logout') ?>"
            class="nav-link px-3 py-2 rounded-3 text-danger d-flex align-items-center gap-3 hover-danger-bg"
            onclick="return confirm('Apakah Anda yakin ingin mengakhiri sesi saat ini?');" title="Logout">
            <i class="fa-solid fa-right-from-bracket fa-fw fs-5"></i> <span class="sidebar-text">Logout</span>
        </a>
    </div>
</aside>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('mainSidebar');
        sidebar.classList.toggle('collapsed');

        // Wajib ada agar CSS body.sidebar-collapsed aktif
        document.body.classList.toggle('sidebar-collapsed');

        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebar_collapsed', isCollapsed);
    });

    // Load state saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('mainSidebar');
        const savedState = localStorage.getItem('sidebar_collapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    });
</script>