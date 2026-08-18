<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
// $routes->get('/', 'Auth::login');

$routes->get('login', 'Auth::login');
$routes->post('auth/process', 'Auth::process');
$routes->get('loading', 'Auth::loading');
$routes->get('logout', 'Auth::logout');

$routes->get('kelas', 'KelasController::index');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Grouping agar akses lebih aman
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    // Manajemen Kelas
    $routes->get('kelas', 'Admin\Kelas::index');
    $routes->post('kelas/store', 'Admin\Kelas::store');
    $routes->post('kelas/update/(:num)', 'Admin\Kelas::update/$1');
    $routes->get('kelas/delete/(:num)', 'Admin\Kelas::delete/$1');

    //  Manajemen Santri
    $routes->get('santri', 'Admin\Santri::index');
    // $routes->get('santri/tambah', 'Admin\Santri::tambah');
    $routes->post('santri/store', 'Admin\Santri::store');
    $routes->get('santri-detail/(:num)', 'Admin\Santri::detail/$1');
    $routes->get('santri/cetakKartu/(:num)', 'Admin\Santri::cetakKartu/$1');
    $routes->get('santri/edit/(:num)', 'Admin\Santri::edit/$1');
    $routes->post('santri/update/(:num)', 'Admin\Santri::update/$1');
    $routes->get('santri/delete/(:num)', 'Admin\Santri::delete/$1');

    // Manajemen Ustadz/Guru/Pengajar
    $routes->get('ustadz', 'Admin\Ustadz::index');
    $routes->post('ustadz/store', 'Admin\Ustadz::store');
    $routes->post('ustadz/update/(:num)', 'Admin\Ustadz::update/$1');
    $routes->get('ustadz/delete/(:num)', 'Admin\Ustadz::delete/$1');
    $routes->get('ustadz/reset-password/(:num)', 'Auth::resetPasswordGuru/$1');

    // Manajemen Wali Santri
    $routes->get('wali-santri', 'Admin\Wali::index');
    $routes->post('wali-santri/store', 'Admin\Wali::store');
    $routes->post('wali-santri/update/(:num)', 'Admin\Wali::update/$1');
    $routes->get('wali-santri/delete/(:num)', 'Admin\Wali::delete/$1');
    $routes->get('wali-santri/reset-password/(:num)', 'Auth::resetPasswordWali/$1');

    // Manajemen Hafalan
    $routes->get('hafalan', 'Admin\Hafalan::index');
    $routes->get('hafalan/getSantriByGuru/(:num)', 'Admin\Hafalan::getSantriByGuru/$1');
    $routes->get('hafalan/getSurahByJuz/(:num)', 'Admin\Hafalan::getSurahByJuz/$1');
    $routes->post('hafalan/store', 'Admin\Hafalan::store');
    $routes->post('hafalan/update/(:num)', 'Admin\Hafalan::update/$1');
    $routes->get('hafalan/delete/(:num)', 'Admin\Hafalan::delete/$1');

    // Statistik Hafalan
    $routes->get('statistik-hafalan', 'Admin\Statistik::index');
    $routes->get('statistik-hafalan/export', 'Admin\Statistik::export');

    // Manajemen Administrasi
    $routes->get('administrasi', 'Admin\Administrasi::index');
    $routes->post('administrasi/store', 'Admin\Administrasi::store');
    $routes->post('administrasi/update/(:num)', 'Admin\Administrasi::update/$1');
    $routes->get('administrasi/verifikasi/(:num)', 'Admin\Administrasi::verifikasi/$1');
    $routes->get('administrasi/detail/(:num)', 'Admin\Administrasi::detail/$1');
    $routes->get('administrasi/export', 'Admin\Administrasi::exportExcel');
    $routes->get('administrasi/export/(:num)', 'Admin\Administrasi::export/$1');
    $routes->get('administrasi/delete/(:num)', 'Admin\Administrasi::delete/$1');

    $routes->get('esertifikat', 'Admin\Esertifikat::index');

    $routes->get('profile', 'Admin\Profile::index');
    $routes->post('profile/update', 'Admin\Profile::update');

    $routes->get('pengaturan', 'Admin\Pengaturan::index');
    $routes->post('pengaturan/update-general', 'Admin\Pengaturan::updateGeneral');
    $routes->post('pengaturan/update-password', 'Admin\Pengaturan::updatePassword');
    $routes->get('pengaturan/backup', 'Admin\Pengaturan::backupDatabase');
});

$routes->group('guru', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Guru\Dashboard::index');
    $routes->get('santri', 'Guru\Santri::index');
    $routes->get('santri-detail/(:num)', 'Guru\Santri::detail/$1');
    $routes->get('santri/cetak', 'Guru\Santri::cetak');
    $routes->get('santri/cetakKartu/(:num)', 'Guru\Santri::cetakKartu/$1');

    // Manajemen Hafalan
    $routes->get('hafalan', 'Guru\Hafalan::index');
    $routes->get('detail-riwayat-hafalan/(:num)', 'Guru\RiwayatHafalan::detail/$1');
    $routes->get('hafalan/getSurahByJuz/(:num)', 'Guru\Hafalan::getSurahByJuz/$1');
    $routes->post('hafalan/store', 'Guru\Hafalan::store');
    $routes->post('hafalan/update/(:num)', 'Guru\Hafalan::update/$1');
    $routes->get('hafalan/delete/(:num)', 'Guru\Hafalan::delete/$1');

    $routes->get('riwayat-hafalan', 'Guru\RiwayatHafalan::index');
    $routes->get('riwayat-hafalan/ekspor', 'Guru\RiwayatHafalan::ekspor');

    $routes->get('statistik-hafalan', 'Guru\Statistik::index');
    $routes->get('statistik-hafalan/export', 'Guru\Statistik::export');

    $routes->get('profile', 'Guru\Profile::index');
    $routes->post('profile/update', 'Guru\Profile::update');

    $routes->get('pengaturan', 'Guru\Pengaturan::index');
    $routes->post('pengaturan/update-profile', 'Guru\Pengaturan::updateProfile');
    $routes->post('pengaturan/update-password', 'Guru\Pengaturan::updatePassword');
});

$routes->group('wali', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Wali\Dashboard::index');
    $routes->get('santri-detail/(:num)', 'Wali\Dashboard::detailSantri/$1');
    $routes->get('santri/cetakKartu/(:num)', 'Wali\Dashboard::cetakKartu/$1');

    // Riwayat Hafalan
    $routes->get('riwayat-hafalan', 'Wali\RiwayatHafalan::index');
    $routes->get('detail-riwayat-hafalan/(:num)', 'Wali\RiwayatHafalan::detail/$1');

    // Riwayat Tagihan
    $routes->get('riwayat-tagihan', 'Wali\RiwayatTagihan::index');
    $routes->get('riwayat-tagihan/detail/(:num)', 'Wali\RiwayatTagihan::detail/$1');
    $routes->get('riwayat-tagihan/export', 'Wali\RiwayatTagihan::exportExcel');
    $routes->get('riwayat-tagihan/export/(:num)', 'Wali\RiwayatTagihan::export/$1');
    $routes->get('riwayat-tagihan/unduh-kuitansi/(:num)', 'Wali\RiwayatTagihan::unduhKuitansi/$1');
    $routes->post('riwayat-tagihan/konfirmasi/(:num)', 'Wali\RiwayatTagihan::konfirmasi/$1');

    $routes->get('esertifikat', 'Wali\Esertifikat::index');
    $routes->get('statistik-hafalan', 'Wali\Statistik::index');

    $routes->get('profile', 'Wali\Profile::index');
    $routes->post('profile/update', 'Wali\Profile::update');

    $routes->get('pengaturan', 'Wali\Pengaturan::index');
    $routes->post('pengaturan/update-profile', 'Wali\Pengaturan::updateProfile');
    $routes->post('pengaturan/update-password', 'Wali\Pengaturan::updatePassword');
});
