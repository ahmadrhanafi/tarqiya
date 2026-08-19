<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WaliModel;
use App\Models\SantriModel;

class Pengaturan extends BaseController
{
    protected $userModel;
    protected $waliModel;
    protected $santriModel;
    protected $tenantId;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->waliModel = new WaliModel();
        $this->santriModel = new SantriModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'wali') {
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

        $wali = null;

        // Cari data wali berdasarkan tenant_id dan nama atau ID
        if (!empty($userNama)) {
            $wali = $this->waliModel->where('tenant_id', $this->tenantId)
                ->where('nama_wali', $userNama)
                ->first();
        }

        if (!$wali) {
            $wali = $this->waliModel->where('tenant_id', $this->tenantId)->find($userId);
        }

        $list_santri = [];
        if ($wali) {
            $list_santri = $this->santriModel->select('santri.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
                ->where('santri.tenant_id', $this->tenantId)
                ->where('santri.id_wali', $wali['id'])
                ->findAll();
        }

        $data = [
            'title' => 'Pengaturan Akun Wali',
            'icon' => 'fa-solid fa-gear',
            'wali' => $wali,
            'list_santri' => $list_santri
        ];

        return view('wali/pengaturan', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('id');
        $userNama = session()->get('nama') ?? session()->get('name');

        // Cari data wali yang sedang login dengan validasi tenant_id
        $wali = $this->waliModel->where('tenant_id', $this->tenantId)
            ->where('nama_wali', $userNama)
            ->first();

        if (!$wali) {
            $wali = $this->waliModel->where('tenant_id', $this->tenantId)->find($userId);
        }

        if (!$wali) {
            return redirect()->to('/wali/pengaturan')->with('error', 'Data profil wali tidak ditemukan.');
        }

        // Update profil dengan validasi kepemilikan tenant
        $this->waliModel->update($wali['id'], [
            'nama_wali' => $this->request->getPost('nama_wali'),
            'no_hp' => $this->request->getPost('no_hp'),
            'alamat' => $this->request->getPost('alamat'),
        ]);

        return redirect()->to('/wali/pengaturan')->with('success', 'Informasi kontak wali berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = session()->get('id');

        // Ambil data user dengan batasan tenant_id
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);
        if (!$user) {
            return redirect()->to('/wali/pengaturan')->with('error', 'Data pengguna tidak ditemukan.');
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->to('/wali/pengaturan')->with('error', 'Kata sandi saat ini salah!');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->to('/wali/pengaturan')->with('error', 'Kata sandi baru minimal harus 8 karakter!');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->to('/wali/pengaturan')->with('error', 'Konfirmasi kata sandi baru tidak cocok!');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/wali/pengaturan')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}