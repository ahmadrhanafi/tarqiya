<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\UserModel;

class Pengaturan extends BaseController
{
    protected $guruModel;
    protected $userModel;
    protected $tenantId;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $userId = session()->get('id');
        $userNama = session()->get('name');

        // Cari data guru berdasarkan tenant_id dan nama
        $guru = $this->guruModel->select('guru.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = guru.id_kelas_diampu', 'left')
            ->where('guru.tenant_id', $this->tenantId)
            ->where('guru.nama_guru', $userNama)
            ->first();

        if (!$guru) {
            // Fallback cari berdasarkan tenant_id dan id/ref_id
            $guru = $this->guruModel->select('guru.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = guru.id_kelas_diampu', 'left')
                ->where('guru.tenant_id', $this->tenantId)
                ->find(session()->get('ref_id') ?? $userId);
        }

        $data = [
            'title' => 'Pengaturan Akun Pengajar',
            'guru' => $guru
        ];

        return view('guru/pengaturan', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('id');
        $userNama = session()->get('name');

        // Cari data guru dengan filter tenant_id
        $guru = $this->guruModel->where('tenant_id', $this->tenantId)
            ->where('nama_guru', $userNama)->first();
        if (!$guru) {
            $guru = $this->guruModel->where('tenant_id', $this->tenantId)
                ->find(session()->get('ref_id') ?? $userId);
        }

        if (!$guru) {
            return redirect()->to('/guru/pengaturan')->with('error', 'Data profil guru tidak ditemukan.');
        }

        // Update profil guru (pastikan tenant_id juga terjaga)
        $this->guruModel->update($guru['id'], [
            'nama_guru' => $this->request->getPost('nama_guru'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/guru/pengaturan')->with('success', 'Profil pengajar berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = session()->get('id');

        // Ambil data user dengan validasi tenant_id agar aman
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);

        if (!$user) {
            return redirect()->to('/guru/pengaturan')->with('error', 'Akun pengguna tidak ditemukan.');
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->to('/guru/pengaturan')->with('error', 'Kata sandi saat ini salah!');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->to('/guru/pengaturan')->with('error', 'Kata sandi baru minimal harus 8 karakter!');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->to('/guru/pengaturan')->with('error', 'Konfirmasi kata sandi baru tidak cocok!');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/guru/pengaturan')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function updateNotification()
    {
        return redirect()->to('/guru/pengaturan')->with('success', 'Preferensi notifikasi berhasil disimpan.');
    }
}