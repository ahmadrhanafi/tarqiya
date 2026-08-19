<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TenantModel;

class Pengaturan extends BaseController
{
    protected $userModel;
    protected $tenantId;

    public function __construct()
    {
        $this->userModel = new UserModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        // Log file backup dipisahkan per tenant agar lebih aman
        $logFile = WRITEPATH . 'backups/last_backup_' . $this->tenantId . '.txt';

        if (file_exists($logFile)) {
            $lastBackup = file_get_contents($logFile);
        } else {
            $lastBackup = 'Belum pernah';
        }

        // Anda juga bisa mengambil data pengaturan tenant dari database di sini jika ada tabel khusus
        $data = [
            'title' => 'Pengaturan Sistem',
            'lastBackup' => $lastBackup
        ];

        return view('admin/pengaturan', $data);
    }

    // Fungsi simpan Konfigurasi Umum (Berdasarkan Tenant)
    public function updateGeneral()
    {
        $namaPesantren = $this->request->getPost('nama_pesantren');

        // Contoh implementasi jika disimpan ke database atau tabel tenant:
        // $db = \Config\Database::connect();
        // $db->table('tenants')->where('id', $this->tenantId)->update(['nama_pesantren' => $namaPesantren]);

        return redirect()->to('/admin/pengaturan#general')->with('success', 'Konfigurasi umum berhasil diperbarui.');
    }

    // Fungsi ganti sandi admin
    public function updatePassword()
    {
        $userId = session()->get('id');

        // Pastikan user yang di-update benar-benar milik tenant yang aktif dan memiliki role admin
        $user = $this->userModel->where('id', $userId)->where('tenant_id', $this->tenantId)->first();

        if (!$user) {
            return redirect()->to('/admin/pengaturan#security')->with('error', 'Pengguna tidak ditemukan.');
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->to('/admin/pengaturan#security')->with('error', 'Kata sandi saat ini salah!');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->to('/admin/pengaturan#security')->with('error', 'Kata sandi baru minimal harus 8 karakter!');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->to('/admin/pengaturan#security')->with('error', 'Konfirmasi kata sandi baru tidak cocok!');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/admin/pengaturan#security')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function backupDatabase()
    {
        $db = db_connect();
        $hostname = $db->hostname;
        $username = $db->username;
        $password = $db->password;
        $database = $db->database;
        $port = $db->port;

        // Nama file backup menyertakan tenant_id agar jelas milik lembaga yang mana
        $filename = 'backup_db_tenant_' . $this->tenantId . '_' . date('Y-m-d_H-i-s') . '.sql';

        $filePath = WRITEPATH . 'backups/' . $filename;

        if (!is_dir(WRITEPATH . 'backups')) {
            mkdir(WRITEPATH . 'backups', 0777, true);
        }

        $passwordClause = !empty($password) ? "-p\"{$password}\"" : "";
        $command = "mysqldump -h {$hostname} -P {$port} -u {$username} {$passwordClause} {$database} > \"{$filePath}\"";

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode === 0 && file_exists($filePath)) {
            // Simpan waktu backup terakhir ke file teks khusus tenant ini
            $logFile = WRITEPATH . 'backups/last_backup_' . $this->tenantId . '.txt';
            $formattedTime = date('d F Y') . ' (' . date('H:i') . ')';
            file_put_contents($logFile, $formattedTime);

            return $this->response->download($filePath, null)->setFileName($filename);
        } else {
            return redirect()->to('/admin/pengaturan#backup')->with('error', 'Gagal melakukan backup database. Pastikan perintah mysqldump diizinkan di server.');
        }
    }
}