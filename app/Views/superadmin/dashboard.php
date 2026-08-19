<?= $this->extend('layout/main') ?>

<?php
/**
 * @var string $title
 * @var string $icon
 * @var int $total_tenant
 * @var int $tenant_aktif
 * @var int $total_semua_user
 * @var array<int, array{id: int, nama_lembaga: string, status_tenant: string, created_at: string}> $daftar_tenant
 */
?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <!-- Welcome Banner Modern -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 mt-4 me-5 d-none d-lg-block">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success d-flex align-items-center justify-content-center"
                        style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-network-wired fa-2x"></i>
                    </div>
                </div>
                <div style="z-index: 1;">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold mb-2"
                        style="text-transform: none !important;">
                        <i class="fa-solid fa-circle-check me-1"></i> Panel Super Administrator (Pusat)
                    </span>
                    <h3 class="fw-bold text-dark-mode mb-1" style="text-transform: none !important;">Selamat Datang,
                        <?= session()->get('name') ?>!
                    </h3>
                    <p class="text-secondary small mb-0" style="text-transform: none !important;">Ringkasan data global
                        seluruh tenant dan sistem SaaS Tarqiya pusat saat ini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total Tenant / Pesantren -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i class="fa-solid fa-school fa-lg"></i>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"
                            style="text-transform: none !important;">Lembaga</span>
                    </div>
                    <h6 class="text-secondary mb-1 font-monospace small" style="text-transform: none !important;">TOTAL
                        TENANT</h6>
                    <h2 class="fw-bold text-dark-mode mb-0"><?= number_format($total_tenant ?? 0, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>

        <!-- Card 2: Tenant Aktif -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i class="fa-solid fa-circle-check fa-lg"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"
                            style="text-transform: none !important;">Berlangganan</span>
                    </div>
                    <h6 class="text-secondary mb-1 font-monospace small" style="text-transform: none !important;">TENANT
                        AKTIF</h6>
                    <h2 class="fw-bold text-dark-mode mb-0"><?= number_format($tenant_aktif ?? 0, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Pengguna Sistem -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                            <i class="fa-solid fa-users fa-lg"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold"
                            style="text-transform: none !important;">Keseluruhan</span>
                    </div>
                    <h6 class="text-secondary mb-1 font-monospace small" style="text-transform: none !important;">TOTAL
                        PENGGUNA</h6>
                    <h2 class="fw-bold text-dark-mode mb-0"><?= number_format($total_semua_user ?? 0, 0, ',', '.'); ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Tenant Terbaru -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div
            class="card-header py-3 bg-white border-0 rounded-top-4 d-flex justify-content-between align-items-center px-4 pt-4">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-list text-success me-2"></i>Daftar Tenant /
                Pesantren Terbaru</h6>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 rounded-start-3">No</th>
                            <th class="py-3">Nama Lembaga</th>
                            <th class="py-3">Status Langganan</th>
                            <th class="py-3 rounded-end-3">Tanggal Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_tenant)): ?>
                            <?php $no = 1;
                            foreach ($daftar_tenant as $t): ?>
                                <tr>
                                    <td class="text-center fw-semibold text-secondary" width="5%"><?= $no++ ?></td>
                                    <td class="fw-bold text-dark"><?= esc($t['nama_lembaga']) ?></td>
                                    <td>
                                            <?php if ($t['status_tenant'] === 'Active'): ?>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">Active</span>
                                            <?php else: ?>
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-semibold"><?= esc($t['status_tenant']) ?></span>
                                            <?php endif; ?>
                                    </td>
                                    <td class="text-secondary"><?= date('d M Y, H:i', strtotime($t['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada tenant terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>