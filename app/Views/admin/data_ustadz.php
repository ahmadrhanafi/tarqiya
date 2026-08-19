<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
$guru = $guru ?? [];
$kelas = $kelas ?? [];
?>

<div class="container-fluid px-0">

    <!-- Flash Message Floating -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080; max-width: 400px;">
        <!-- Alert Success -->
        <?php if (session()->getFlashdata('success')): ?>
            <div id="flash-alert-success"
                class="alert alert-success fade show rounded-4 shadow-lg border-0 d-flex align-items-center p-3 mb-2 position-relative"
                role="alert">
                <div class="d-flex align-items-center flex-grow-1 pe-4">
                    <div class="text-success fs-5 me-3 flex-shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <span class="fw-bold d-block text-success mb-0">Berhasil!</span>
                        <span class="text-secondary small"
                            style="font-size: 12px;"><?= session()->getFlashdata('success'); ?></span>
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3 shadow-none"
                    style="font-size: 10px; width: 20px; height: 20px;" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Error -->
        <?php if (session()->getFlashdata('error')): ?>
            <div id="flash-alert-error"
                class="alert alert-danger fade show rounded-4 shadow-lg border-0 d-flex align-items-center p-3 mb-2 position-relative"
                role="alert">
                <div class="d-flex align-items-center flex-grow-1 pe-4">
                    <div class="text-danger fs-5 me-3 flex-shrink-0">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <span class="fw-bold d-block text-danger mb-0">Gagal!</span>
                        <span class="text-secondary small"
                            style="font-size: 12px;"><?= session()->getFlashdata('error'); ?></span>
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2 shadow-none"
                    style="font-size: 8px; width: 16px; height: 16px;" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark-mode mb-1" style="text-transform: none !important;">Manajemen Data Pengajar
            </h3>
            <p class="text-secondary mb-0 small" style="text-transform: none !important;">Kelola informasi data pengajar
                dan penugasan kelas di pesantren.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm px-3 rounded-pill bg-white shadow-sm"
                style="text-transform: none !important;">
                <i class="fa-solid fa-file-excel text-success me-1"></i> Ekspor Data
            </button>
            <button type="button" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalTambah" style="text-transform: none !important;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Pengajar
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3 text-muted">
                            <i class="fa-solid fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control bg-light border-0 py-2"
                            placeholder="Cari berdasarkan nama pengajar, NIP, atau kelas yang diampu...">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="genderFilter" class="form-select form-select-sm bg-light border-0 py-2">
                        <option value="semua" selected>Gender: Semua</option>
                        <option value="l">Laki-laki</option>
                        <option value="p">Perempuan</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="statusFilter" class="form-select form-select-sm bg-light border-0 py-2">
                        <option value="semua" selected>Status: Semua</option>
                        <option value="aktif">Aktif Mengajar</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase"
                        style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 ps-4" style="width: 5%;">No</th>
                            <th class="py-3" style="width: 30%;">NIP & Nama Pengajar</th>
                            <th class="py-3" style="width: 25%;">Chat Whatsapp</th>
                            <th class="py-3" style="width: 15%;">Jenis Kelamin</th>
                            <th class="py-3" style="width: 25%;">Kelas Diampu</th>
                            <th class="py-3" style="width: 25%;">Status</th>
                            <th class="py-3 text-end pe-4" style="width: 25%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyUstadz">
                        <?php if (!empty($guru)): ?>
                            <?php $no = 1;
                            foreach ($guru as $g): ?>
                                <?php
                                $words = explode(' ', $g['nama_guru']);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                ?>
                                <!-- Ubah data-status agar mengambil $g['status_aktif'] -->
                                <tr class="guru-row" data-gender="<?= strtolower($g['jenis_kelamin']); ?>"
                                    data-status="<?= strtolower($g['status_aktif'] ?? 'aktif'); ?>">
                                    <td class="ps-4 fw-medium text-muted"><?= $no++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($g['foto_user']) && file_exists('uploads/profile/' . $g['foto_user'])): ?>
                                                <img src="<?= base_url('uploads/profile/' . $g['foto_user']); ?>"
                                                    alt="Foto <?= esc($g['nama_guru']); ?>"
                                                    class="rounded-circle object-fit-cover shadow-sm"
                                                    style="width: 38px; height: 38px;">
                                            <?php else: ?>
                                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                    style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                    <?= $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark-mode" style="font-size: 0.9rem;">
                                                    <?= esc($g['nama_guru']); ?>
                                                </h6>
                                                <small class="text-secondary" style="font-size: 0.75rem;"><i
                                                        class="fa-solid fa-id-card text-secondary me-1"></i> NIP:
                                                    <?= esc($g['nip']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/<?= esc($g['no_hp'] ?? ''); ?>" target="_blank"
                                            rel="noopener noreferrer">
                                            <span class="badge bg-success text-light border">
                                                <i class="fa-brands fa-whatsapp text-light me-1"></i> Click to Chat
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text-secondary small fw-medium">
                                            <?= ($g['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 text-secondary small" style="font-size: 0.65rem;">Kelas:</h6>
                                            <?php
                                            $namaKelas = $g['nama_kelas'] ?? 'Unassigned';
                                            $isBelumAda = ($namaKelas === 'Unassigned');
                                            ?>
                                            <small class="fw-semibold <?= $isBelumAda ? 'text-danger' : 'text-dark-mode'; ?>"
                                                style="font-size: 0.75rem;">
                                                <i
                                                    class="fa-solid fa-chalkboard-user <?= $isBelumAda ? 'text-secondary' : 'text-secondary'; ?> me-1"></i>
                                                <?= esc($namaKelas); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($g['status_aktif'] == 'Aktif'): ?>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small fw-semibold">Aktif</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill small fw-semibold">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <!-- detail -->
                                            <button type="button" class="btn btn-sm btn-light text-primary border-0 rounded-2"
                                                title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetail"
                                                data-id="<?= $g['id']; ?>" data-nip="<?= esc($g['nip']); ?>"
                                                data-namaguru="<?= esc($g['nama_guru']); ?>"
                                                data-jeniskelamin="<?= esc($g['jenis_kelamin']); ?>"
                                                data-nohp="<?= esc($g['no_hp'] ?? ''); ?>"
                                                data-foto="<?= $g['foto_user'] ?? ''; ?>"
                                                data-idkelas="<?= esc($g['id_kelas_diampu']); ?>"
                                                data-status="<?= $g['status_aktif']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <!-- edit -->
                                            <button type="button"
                                                class="btn btn-sm btn-light text-warning border-0 rounded-2 btn-edit"
                                                title="Edit" data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                data-id="<?= $g['id']; ?>" data-nip="<?= esc($g['nip']); ?>"
                                                data-namaguru="<?= esc($g['nama_guru']); ?>"
                                                data-nohp="<?= esc($g['no_hp'] ?? ''); ?>"
                                                data-jeniskelamin="<?= esc($g['jenis_kelamin']); ?>"
                                                data-idkelas="<?= esc($g['id_kelas_diampu']); ?>"
                                                data-status="<?= $g['status_aktif']; ?>">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <!-- hapus -->
                                            <a href="<?= base_url('admin/ustadz/delete/' . $g['id']); ?>"
                                                class="btn btn-sm btn-light text-danger border-0 rounded-2" title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus data ustadz ini?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- BARIS KOSONG JIKA TIDAK DITEMUKAN -->
                        <tr id="emptyRowUstadz" class="d-none">
                            <td colspan="6" class="text-center py-4 text-secondary small">
                                <i class="fa-solid fa-folder-open me-1"></i> Tidak ada data pengajar yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Card Footer / Pagination -->
        <div
            class="card card-footer bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <span class="text-secondary small" id="totalDataTextUstadz">Menampilkan total <?= count($guru); ?> data
                pengajar</span>
        </div>
    </div>

</div>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Tambah Pengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/ustadz/store'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body py-4">

                    <div class="mb-3 p-2 bg-light rounded-3">
                        <small class="text-muted">
                            <i class="fa-solid fa-circle-info me-1"></i> NIP akan dibuat otomatis oleh sistem setelah
                            data disimpan.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Nama Pengajar</label>
                        <input type="text" name="nama_guru" class="form-control"
                            placeholder="Contoh: Ust. Abdul Somad, S.Pd." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">No. WhatsApp</label>
                        <input type="tel" name="no_hp" class="form-control" placeholder="Contoh: 08123456789"
                            minlength="10" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Kelas Diampu</label>
                        <select name="id_kelas_diampu" class="form-select">
                            <option value="">-- Pilih Kelas (Opsional) --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id']; ?>"><?= esc($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Tambahan Input Foto -->
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg"
                            required>
                        <div class="form-text text-muted">Format: JPG, JPEG, PNG (Maks. 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Edit Data Pengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">NIP</label>
                        <input type="text" name="nip" id="editNip" class="form-control bg-secondary bg-opacity-25"
                            readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Nama Pengajar</label>
                        <input type="text" name="nama_guru" id="editNamaGuru" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">No. WhatsApp</label>
                        <input type="tel" name="no_hp" id="editNoHp" class="form-control"
                            placeholder="Contoh: 08123456789" minlength="10" maxlength="15"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="editJenisKelamin" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Kelas Diampu</label>
                        <select name="id_kelas_diampu" id="editIdKelas" class="form-select">
                            <option value="">Bebaskan dari kelas yang diampu</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id']; ?>"><?= esc($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Status Kepegawaian</label>
                        <select name="status_aktif" id="editStatus" class="form-select" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Ganti Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-tie text-primary me-2"></i> Detail
                    Pengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block mb-3">
                        <!-- Foto Profil -->
                        <img id="detailFoto" src="" alt="Foto Profil"
                            class="rounded-circle object-fit-cover shadow-sm border border-3 border-white"
                            style="width: 90px; height: 90px; display: none;">

                        <!-- Inisial (d-flex dihapus agar tidak bentrok) -->
                        <div id="detailInitials"
                            class="bg-success bg-opacity-10 text-success rounded-circle align-items-center justify-content-center fw-bold shadow-sm mx-auto border border-3 border-white"
                            style="width: 90px; height: 90px; font-size: 2rem; display: none;">
                        </div>
                    </div>
                    <h4 id="detailNamaGuru" class="fw-bold text-dark mb-1">-</h4>
                    <p class="text-muted small mb-2"><i class="fa-solid fa-id-card me-1"></i> NIP: <span
                            id="detailNip">-</span></p>
                    <div id="detailStatusBadge"></div>
                </div>

                <div class="bg-light rounded-3 p-3 mb-2">
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom border-light-subtle">
                            <span class="text-muted"><i class="fa-solid fa-venus-mars me-2 text-secondary"></i> Jenis
                                Kelamin</span>
                            <span id="detailGender" class="fw-semibold text-dark">-</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom border-light-subtle">
                            <span class="text-muted"><i class="fa-brands fa-whatsapp me-2 text-success"></i>
                                WhatsApp</span>
                            <a id="detailNoHpLink" href="#" target="_blank"
                                class="fw-semibold text-success text-decoration-none">-</a>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted"><i class="fa-solid fa-chalkboard-user me-2 text-primary"></i> Kelas
                                Diampu</span>
                            <span id="detailKelas" class="fw-semibold text-dark">-</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Modal Footer (Posisi Reset Password & Tutup yang lebih nyaman) -->
            <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-between">
                <a href="#" id="btnResetPassword" class="btn btn-danger btn-sm rounded-pill px-3 fw-medium"
                    onclick="return confirm('Yakin ingin mereset password pengajar ini ke default?')">
                    <i class="fa-solid fa-lock-open me-1"></i> Reset Password
                </a>
                <button type="button" class="btn btn-dark btn-sm rounded-pill px-4"
                    data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const genderFilter = document.getElementById('genderFilter');
        const statusFilter = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('#tableBodyUstadz .guru-row');
        const totalDataText = document.getElementById('totalDataTextUstadz');
        const emptyRow = document.getElementById('emptyRowUstadz');

        function filterUstadz() {
            const keyword = searchInput ? searchInput.value.toLowerCase() : '';
            const genderVal = genderFilter ? genderFilter.value.toLowerCase() : 'semua';
            const statusVal = statusFilter ? statusFilter.value.toLowerCase() : 'semua';

            let visibleCount = 0;

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowGender = row.getAttribute('data-gender');
                const rowStatus = row.getAttribute('data-status');

                const matchesKeyword = rowText.includes(keyword);
                const matchesGender = (genderVal === "" || genderVal === "semua" || rowGender === genderVal);
                const matchesStatus = (statusVal === "" || statusVal === "semua" || rowStatus === statusVal);

                if (matchesKeyword && matchesGender && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptyRow) {
                if (visibleCount === 0) {
                    emptyRow.classList.remove('d-none');
                } else {
                    emptyRow.classList.add('d-none');
                }
            }

            if (totalDataText) {
                totalDataText.textContent = `Menampilkan total ${visibleCount} data pengajar`;
            }
        }

        if (searchInput) searchInput.addEventListener('keyup', filterUstadz);
        if (genderFilter) genderFilter.addEventListener('change', filterUstadz);
        if (statusFilter) statusFilter.addEventListener('change', filterUstadz);
    });

    // --JavaScript untuk Lempar Data ke Modal Edit--
    document.addEventListener('DOMContentLoaded', function () {
        const modalEdit = document.getElementById('modalEdit');
        if (modalEdit) {
            modalEdit.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');
                const nip = button.getAttribute('data-nip');
                const namaGuru = button.getAttribute('data-namaguru');
                const noHp = button.getAttribute('data-nohp');
                const jenisKelamin = button.getAttribute('data-jeniskelamin');
                const idKelas = button.getAttribute('data-idkelas');
                const status = button.getAttribute('data-status');

                modalEdit.querySelector('#editNip').value = nip;
                modalEdit.querySelector('#editNamaGuru').value = namaGuru;
                modalEdit.querySelector('#editNoHp').value = noHp;
                modalEdit.querySelector('#editJenisKelamin').value = jenisKelamin;
                modalEdit.querySelector('#editIdKelas').value = idKelas;
                modalEdit.querySelector('#editStatus').value = status;

                modalEdit.querySelector('#formEdit').action = '<?= base_url('admin/ustadz/update/'); ?>' + id;
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const modalDetail = document.getElementById('modalDetail');
        if (modalDetail) {
            modalDetail.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                // Ambil data dari atribut tombol
                const id = button.getAttribute('data-id');
                const nip = button.getAttribute('data-nip') || '-';
                const nama = button.getAttribute('data-namaguru') || '-';
                const gender = button.getAttribute('data-jeniskelamin') || '-';
                const noHp = button.getAttribute('data-nohp') || '';
                const foto = button.getAttribute('data-foto');
                const kelas = button.getAttribute('data-namakelas') || button.getAttribute('data-idkelas') || 'Unassigned'; // Sesuaikan jika ada teks nama kelas
                const status = button.getAttribute('data-status') || 'Aktif';

                // Set URL tombol Reset Password
                const btnReset = modalDetail.querySelector('#btnResetPassword');
                if (btnReset && id) {
                    // Sesuaikan base_url route reset password ustadz kamu
                    btnReset.href = "<?= base_url('admin/ustadz/reset-password/'); ?>" + id;
                }

                // Isi teks ke elemen modal
                modalDetail.querySelector('#detailNamaGuru').textContent = nama;
                modalDetail.querySelector('#detailNip').textContent = nip;

                // Format Jenis Kelamin
                const genderText = (gender.toUpperCase() === 'L') ? 'Laki-laki' : ((gender.toUpperCase() === 'P') ? 'Perempuan' : gender);
                modalDetail.querySelector('#detailGender').textContent = genderText;

                // Format WhatsApp
                const hpLink = modalDetail.querySelector('#detailNoHpLink');
                if (noHp) {
                    hpLink.href = "https://wa.me/" + noHp;
                    hpLink.textContent = noHp;
                } else {
                    hpLink.href = "#";
                    hpLink.textContent = "Tidak ada nomor";
                }

                // Kelas Diampu
                modalDetail.querySelector('#detailKelas').textContent = kelas;

                // Status Badge
                const statusContainer = modalDetail.querySelector('#detailStatusBadge');
                if (status.toLowerCase() === 'aktif') {
                    statusContainer.innerHTML = `<span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small fw-semibold">Aktif</span>`;
                } else {
                    statusContainer.innerHTML = `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill small fw-semibold">Non-Aktif</span>`;
                }

                // Handle Foto Profil vs Inisial
                const imgElement = modalDetail.querySelector('#detailFoto');
                const initialsElement = modalDetail.querySelector('#detailInitials');

                // Bersihkan string foto dari spasi kosong
                const cleanFoto = foto ? foto.trim() : '';

                // Cek apakah foto ada, tidak kosong, dan bukan string 'null' / 'undefined'
                if (cleanFoto !== '' && cleanFoto !== 'null' && cleanFoto !== 'undefined') {
                    imgElement.src = "<?= base_url('uploads/profile/'); ?>" + cleanFoto;
                    imgElement.style.display = 'block';
                    initialsElement.style.display = 'none';
                } else {
                    // Buat inisial otomatis dari nama
                    const words = nama.trim().split(/\s+/);
                    const initials = (words[0] ? words[0][0] : '') + (words[1] ? words[1][0] : '');
                    initialsElement.textContent = initials.toUpperCase();

                    imgElement.style.display = 'none';
                    initialsElement.style.display = 'flex';
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>