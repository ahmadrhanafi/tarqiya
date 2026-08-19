<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            $role = session()->get('role');

            if ($role === 'superadmin') {
                return redirect()->to(base_url('superadmin/dashboard'));
            } elseif ($role === 'admin') {
                return redirect()->to(base_url('admin/dashboard'));
            } elseif ($role === 'guru') {
                return redirect()->to(base_url('guru/dashboard'));
            } elseif ($role === 'wali') {
                return redirect()->to(base_url('wali/dashboard'));
            }
        }

        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $model = $this->userModel;
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $data = $model->where('username', $username)->first();

        if ($data && password_verify($password, $data['password'])) {

            $namaKelas = null;
            $idKelas = null;
            $namaWali = null;

            if ($data['role'] == 'guru' && !empty($data['ref_id'])) {
                $db = \Config\Database::connect();
                $guru = $db->table('guru')
                    ->select('guru.id_kelas_diampu, guru.status_aktif, kelas.nama_kelas')
                    ->join('kelas', 'kelas.id = guru.id_kelas_diampu', 'left')
                    ->where('guru.id', $data['ref_id'])
                    ->get()
                    ->getRowArray();

                if ($guru) {
                    if (strtolower($guru['status_aktif']) == 'non-aktif') {
                        return redirect()->back()->with('error', 'Akun Anda sudah non-aktif. Silakan hubungi admin.');
                    }
                    $idKelas = $guru['id_kelas_diampu'];
                    $namaKelas = $guru['nama_kelas'];
                }
            } elseif ($data['role'] == 'wali' && !empty($data['ref_id'])) {
                $db = \Config\Database::connect();
                $wali = $db->table('wali')
                    ->select('nama_wali')
                    ->where('id', $data['ref_id'])
                    ->get()
                    ->getRowArray();

                if ($wali) {
                    $namaWali = $wali['nama_wali'];
                }
            }

            // SIMPAN DATA KE SESSION
            $session->set([
                'id' => $data['id'],
                'tenant_id' => $data['tenant_id'],
                'role' => $data['role'],
                'name' => $data['name'],
                'nama_wali' => $namaWali ? $namaWali : $data['name'],
                'foto' => !empty($data['foto']) ? $data['foto'] : 'default.png',
                'ref_id' => $data['ref_id'],
                'id_kelas' => $idKelas,
                'nama_kelas' => $namaKelas ? $namaKelas : 'Belum Ada Kelas',
                'logged_in' => TRUE
            ]);

            return redirect()->to('/loading');
        }

        return redirect()->back()->with('error', 'Username atau Password salah');
    }

    public function loading()
    {
        return view('auth/loading');
    }

    public function resetPasswordGuru($id)
    {
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
        $user = $this->userModel->where(['ref_id' => $id, 'role' => 'guru'])->first();

        if ($user) {
            $this->userModel->update($user['id'], ['password' => $defaultPassword]);
            return redirect()->back()->with('success', 'Password ' . $user['name'] . ' berhasil direset menjadi 123456');
        }
        return redirect()->back()->with('error', 'Gagal menemukan akun guru tersebut.');
    }

    public function resetPasswordWali($id)
    {
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
        $user = $this->userModel->where(['ref_id' => $id, 'role' => 'wali'])->first();

        if ($user) {
            $this->userModel->update($user['id'], ['password' => $defaultPassword]);
            return redirect()->back()->with('success', 'Password ' . $user['name'] . ' berhasil direset menjadi 123456');
        }
        return redirect()->back()->with('error', 'Gagal menemukan akun wali tersebut.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}