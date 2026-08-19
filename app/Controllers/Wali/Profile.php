<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WaliModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $waliModel;
    protected $tenantId;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->waliModel = new WaliModel();

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
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);

        $wali = null;
        if (!empty($user['ref_id'])) {
            $wali = $this->waliModel->where('tenant_id', $this->tenantId)->find($user['ref_id']);
        }

        $data = [
            'title' => 'Profil Pengguna',
            'icon' => 'fa-solid fa-user',
            'user' => $user,
            'wali' => $wali
        ];

        return view('wali/profile', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'username' => "required|min_length[3]|is_unique[users.username,id,{$userId}]",
        ];

        if (!empty($user['ref_id'])) {
            $rules['nama_wali'] = 'required';
            $rules['no_hp'] = 'required';
            $rules['alamat'] = 'required';
        }

        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[6]';
            $rules['pass_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUserUpdate = [
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
        ];

        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('uploads/profile', $namaFoto);

            if (!empty($user['foto']) && file_exists('uploads/profile/' . $user['foto'])) {
                @unlink('uploads/profile/' . $user['foto']);
            }

            $dataUserUpdate['foto'] = $namaFoto;
            session()->set('foto', $namaFoto);
        }

        if ($this->request->getPost('password')) {
            $dataUserUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $dataUserUpdate);
        session()->set('name', $dataUserUpdate['name']);

        if (!empty($user['ref_id'])) {
            $dataWaliUpdate = [
                'nama_wali' => $this->request->getPost('nama_wali'),
                'no_hp' => $this->request->getPost('no_hp'),
                'alamat' => $this->request->getPost('alamat'),
            ];

            // Pastikan pembaruan wali sesuai dengan tenant_id
            $this->waliModel->where('tenant_id', $this->tenantId)->update($user['ref_id'], $dataWaliUpdate);

            session()->set('nama_wali', $dataWaliUpdate['nama_wali']);
        }

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}