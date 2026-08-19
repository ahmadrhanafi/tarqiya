<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WaliModel;
use App\Models\UserModel;

class Wali extends BaseController
{
    protected $waliModel;
    protected $userModel;
    protected $tenantId;

    public function __construct()
    {
        $this->waliModel = new WaliModel();
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
        $perPage = 8;
        $currentPage = $this->request->getVar('page_wali') ? (int) $this->request->getVar('page_wali') : 1;

        // Pastikan method di WaliModel memfilter data berdasarkan tenant_id
        $allWali = $this->waliModel->getWaliWithSantriByTenant($this->tenantId);
        $total = count($allWali);

        $waliPaging = array_slice($allWali, ($currentPage - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('wali', $currentPage, $perPage, $total);

        $data = [
            'title' => 'Data Wali Santri',
            'icon' => 'fa-solid fa-users',
            'wali' => $waliPaging,
            'pager' => $pager,
            'role' => session()->get('role') ?? 'admin'
        ];

        return view('admin/wali', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'nama_wali' => 'required|min_length[3]',
                'no_hp' => 'required|numeric|min_length[10]',
                'alamat' => 'required',
                'foto' => 'uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data wali santri atau format foto tidak sesuai (Maks. 2MB).');
        }

        $namaWali = $this->request->getVar('nama_wali');
        $noHp = $this->request->getVar('no_hp');

        $fileFoto = $this->request->getFile('foto');
        $namaFoto = null;
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $folderTujuan = 'uploads/profile/';
            if (!is_dir($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }
            $fileFoto->move($folderTujuan, $namaFoto);
        }

        // Simpan data wali dengan menyertakan tenant_id
        $this->waliModel->save([
            'tenant_id' => $this->tenantId,
            'nama_wali' => $namaWali,
            'no_hp' => $noHp,
            'alamat' => $this->request->getVar('alamat'),
        ]);

        $waliId = $this->waliModel->insertID();

        // Simpan data akun user dengan menyertakan tenant_id
        $this->userModel->save([
            'tenant_id' => $this->tenantId,
            'name' => $namaWali,
            'username' => $noHp,
            'password' => password_hash($noHp, PASSWORD_DEFAULT),
            'role' => 'wali',
            'ref_id' => $waliId,
            'foto' => $namaFoto
        ]);

        return redirect()->to(base_url('admin/wali-santri'))->with('success', 'Data wali santri dan akun login berhasil ditambahkan!');
    }

    public function update($id)
    {
        // Pastikan data wali yang akan diupdate benar-benar milik tenant aktif
        $waliLama = $this->waliModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$waliLama) {
            return redirect()->to(base_url('admin/wali-santri'))->with('error', 'Data wali santri tidak ditemukan.');
        }

        if (
            !$this->validate([
                'nama_wali' => 'required|min_length[3]',
                'no_hp' => 'required|numeric|min_length[10]',
                'alamat' => 'required',
                'foto' => 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data wali santri.');
        }

        $namaWali = $this->request->getVar('nama_wali');
        $noHp = $this->request->getVar('no_hp');

        $this->waliModel->update($id, [
            'nama_wali' => $namaWali,
            'no_hp' => $noHp,
            'alamat' => $this->request->getVar('alamat'),
        ]);

        $user = $this->userModel->where('ref_id', $id)
            ->where('role', 'wali')
            ->where('tenant_id', $this->tenantId)
            ->first();

        if ($user) {
            $dataUpdateUser = [
                'name' => $namaWali,
                'username' => $noHp
            ];

            $fileFoto = $this->request->getFile('foto');
            if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
                $namaFotoBaru = $fileFoto->getRandomName();
                $fileFoto->move('uploads/profile', $namaFotoBaru);

                if (!empty($user['foto']) && file_exists('uploads/profile/' . $user['foto'])) {
                    unlink('uploads/profile/' . $user['foto']);
                }

                $dataUpdateUser['foto'] = $namaFotoBaru;
            }

            $this->userModel->update($user['id'], $dataUpdateUser);
        }

        return redirect()->to(base_url('admin/wali-santri'))->with('success', 'Data wali santri berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Pastikan data wali milik tenant aktif sebelum dihapus
        $wali = $this->waliModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$wali) {
            return redirect()->to(base_url('admin/wali-santri'))->with('error', 'Data wali santri tidak ditemukan.');
        }

        $user = $this->userModel->where('ref_id', $id)
            ->where('role', 'wali')
            ->where('tenant_id', $this->tenantId)
            ->first();

        if ($user) {
            if (!empty($user['foto']) && file_exists('uploads/profile/' . $user['foto'])) {
                unlink('uploads/profile/' . $user['foto']);
            }
            $this->userModel->delete($user['id']);
        }

        $this->waliModel->delete($id);

        return redirect()->to(base_url('admin/wali-santri'))->with('success', 'Data wali santri dan akun login berhasil dihapus!');
    }
}