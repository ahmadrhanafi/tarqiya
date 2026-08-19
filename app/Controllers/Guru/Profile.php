<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $tenantId;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $userId = session()->get('id');

        // Ambil data user dengan filter tenant_id
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);

        $guru = null;
        if (!empty($user['ref_id'])) {
            // Ambil data guru dengan filter tenant_id
            $guru = $this->guruModel->where('tenant_id', $this->tenantId)->find($user['ref_id']);
        }

        $data = [
            'user' => $user,
            'guru' => $guru
        ];

        return view('guru/profile', $data);
    }

    public function update()
    {
        $userId = session()->get('id');

        // Ambil data user dengan validasi tenant_id
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'username' => "required|min_length[3]",
        ];

        if (!empty($user['ref_id'])) {
            $rules['nip'] = "permit_empty";
            $rules['nama_guru'] = 'required';
            $rules['no_hp'] = 'required';
        }

        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[6]';
            $rules['pass_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUserUpdate = [
            'tenant_id' => $this->tenantId,
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
        ];

        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move(FCPATH . 'uploads/profile', $namaFoto);

            if (!empty($user['foto']) && file_exists(FCPATH . 'uploads/profile/' . $user['foto'])) {
                @unlink(FCPATH . 'uploads/profile/' . $user['foto']);
            }

            $dataUserUpdate['foto'] = $namaFoto;
            session()->set('foto', $namaFoto);
        }

        if ($this->request->getPost('password')) {
            $dataUserUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Update user dengan memastikan tenant_id tetap terjaga
        $this->userModel->update($userId, $dataUserUpdate);
        session()->set('name', $dataUserUpdate['name']);

        if (!empty($user['ref_id'])) {
            $dataGuruUpdate = [
                'tenant_id' => $this->tenantId,
                'nama_guru' => $this->request->getPost('nama_guru'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'no_hp' => $this->request->getPost('no_hp'),
            ];

            // Update data guru sesuai dengan ref_id dan tenant_id miliknya
            $this->guruModel->where('tenant_id', $this->tenantId)->update($user['ref_id'], $dataGuruUpdate);
        }

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}