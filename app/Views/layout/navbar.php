<nav class="navbar navbar-expand-lg navbar-light shadow-sm px-3 px-md-4 py-2 border-bottom sticky-top transition-base"
    id="mainNavbar" style="background-color: #097969;">
    <?php
    $role = session()->get('role');
    $dashboardUrl = base_url($role . '/dashboard');
    ?>
    <div class="container-fluid d-flex justify-content-between align-items-center p-0">
        <!-- Brand / Title (Sisi Kiri) -->
        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Bars Mobile -->
            <a href="javascript:void(0);" id="sidebarToggle"
                class="text-dark-mode p-2 text-decoration-none mobile-menu-btn"
                style="z-index: 9999; position: relative; display: inline-block;">
                <i class="fa-solid fa-bars fa-lg"></i>
            </a>

            <div class="d-none d-md-flex align-items-center gap-2">
                <div class="text-dark-mode p-2 rounded-3 d-flex align-items-center justify-content-center"
                    style="width: 30px; height: 30px; font-size: 16px; background-color: #50C878;">
                    <i class="<?= $icon ?? 'fa-solid fa-gauge-high' ?>"></i>
                </div>
                <h5 class="m-0 fw-bold text-dark-mode" style="font-size: 16px;"><?= $title ?? 'Dashboard' ?></h5>
            </div>
        </div>

        <!-- Right Side: Dark Mode Toggle & User Dropdown (Sisi Kanan) -->
        <div class="d-flex align-items-center gap-3">
            <!-- User Dropdown Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle-no-caret"
                    id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">

                    <!-- Teks Nama & Role (Hidden di Mobile) -->
                    <div class="me-2 text-end d-none d-sm-block">
                        <div class="small fw-bold text-dark-mode"
                            style="font-size: 12px; line-height: 1.2; margin-bottom: -12px !important;">
                            <?= session()->get('name') ?>
                        </div>
                        <?php if (session()->get('role') == 'guru'): ?>
                            <div class="ket-user mt-1" style="font-size: 8px; margin-top: 17px !important;">
                                <span class="text-dark-mode">Pengampu:
                                </span>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0"
                                    style="font-size: 8px;">
                                    Kelas <?= session()->get('nama_kelas') ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="text-dark-mode small d-block"
                                style="font-size: 10px; margin-top: 17px !important;">
                                <?php
                                $roleLabel = session()->get('role');
                                if ($roleLabel === 'superadmin')
                                    echo 'Super Admin';
                                elseif ($roleLabel === 'admin')
                                    echo 'Admin';
                                elseif ($roleLabel === 'wali')
                                    echo 'Wali Santri';
                                else
                                    echo ucfirst($roleLabel ?? 'Anonymous');
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Foto Profil -->
                    <div class=" position-relative">
                        <?php
                        $fotoUser = session()->get('foto');
                        if (!empty($fotoUser) && file_exists(FCPATH . 'uploads/profile/' . $fotoUser)) {
                            $urlFoto = base_url('uploads/profile/' . $fotoUser);
                        } else {
                            $urlFoto = base_url('uploads/profile/default.png');
                        }
                        ?>
                        <img src="<?= $urlFoto; ?>" alt="User" width="38" height="38"
                            class="rounded-circle  shadow-sm object-fit-cover">
                    </div>
                </a>

                <!-- Dropdown Menu List -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-4 animate-fade-in"
                    aria-labelledby="dropdownUser">
                    <li class="px-3 py-2 d-sm-none mb-2">
                        <div class="fw-bold text-dark-mode"><?= session()->get('name') ?></div>
                        <small class="text-secondary">
                            <?php
                            $r = session()->get('role');
                            if ($r === 'guru')
                                echo 'Pengampu: ' . session()->get('nama_kelas');
                            elseif ($r === 'wali')
                                echo 'Wali Santri';
                            elseif ($r === 'superadmin')
                                echo 'Super Admin Pusat';
                            else
                                echo 'Admin Pesantren';
                            ?>
                        </small>
                    </li>
                    <li>
                        <hr class="dropdown-divider border border-secondary border-opacity-25">
                    </li>
                    <li>
                        <?php if ($role === 'admin' || $role === 'superadmin'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url($role . '/profile') ?>">
                                <i class="fa-solid fa-user text-success"></i> <span class="small fw-medium">Profile
                                    Saya</span>
                            </a>
                        <?php elseif ($role === 'guru'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url('guru/profile') ?>">
                                <i class="fa-solid fa-user text-success"></i> <span class="small fw-medium">Profile
                                    Saya</span>
                            </a>
                        <?php elseif ($role === 'wali'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url('wali/profile') ?>">
                                <i class="fa-solid fa-user text-success"></i> <span class="small fw-medium">Profile
                                    Saya</span>
                            </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($role === 'admin' || $role === 'superadmin'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url($role . '/pengaturan') ?>">
                                <i class="fa-solid fa-gear text-secondary"></i> <span
                                    class="small fw-medium">Pengaturan</span>
                            </a>
                        <?php elseif ($role === 'guru'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url('guru/pengaturan') ?>">
                                <i class="fa-solid fa-gear text-secondary"></i> <span
                                    class="small fw-medium">Pengaturan</span>
                            </a>
                        <?php elseif ($role === 'wali'): ?>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base"
                                href="<?= base_url('wali/pengaturan') ?>">
                                <i class="fa-solid fa-gear text-secondary"></i> <span
                                    class="small fw-medium">Pengaturan</span>
                            </a>
                        <?php endif; ?>
                    </li>
                    <!-- Tombol Toggle Dark Mode -->
                    <li>
                        <button
                            class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-dark hover-bg-light transition-base w-100 border-0 bg-transparent"
                            id="darkModeToggle" type="button">
                            <i class="fa-solid fa-moon text-warning" id="darkModeIcon"></i> <span
                                class="small fw-medium">Ubah Tema</span>
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider border border-secondary border-opacity-25">
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-2 text-danger custom-logout-hover transition-base"
                            href="<?= base_url('logout') ?>">
                            <i class="fa-solid fa-right-from-bracket"></i> <span class="small fw-semibold">Keluar
                                Akun</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Cek dan buat backdrop jika belum ada
        let backdrop = document.querySelector('.sidebar-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
        }

        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainSidebar = document.getElementById('mainSidebar');

        if (sidebarToggle && mainSidebar) {
            sidebarToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                mainSidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                mainSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            });
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const body = document.body;

        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            darkModeIcon.classList.remove('fa-moon', 'text-secondary');
            darkModeIcon.classList.add('fa-sun', 'text-warning');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                body.classList.toggle('dark-mode');

                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                    darkModeIcon.classList.remove('fa-moon', 'text-secondary');
                    darkModeIcon.classList.add('fa-sun', 'text-warning');
                } else {
                    localStorage.setItem('theme', 'light');
                    darkModeIcon.classList.remove('fa-sun', 'text-warning');
                    darkModeIcon.classList.add('fa-moon', 'text-secondary');
                }
            });
        }
    });
</script>