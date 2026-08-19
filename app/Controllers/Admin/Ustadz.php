<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\UserModel;

class Ustadz extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $userModel;
    protected $tenantId;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
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
        $role = session()->get('role');

        // Pastikan method getGuruWithByTenant() atau sejenisnya di GuruModel memfilter berdasarkan tenant_id
        $data = [
            'title' => 'Data Ustadz',
            'icon' => 'fa-solid fa-chalkboard-user',
            'guru' => $this->guruModel->getGuruWithUserByTenant($this->tenantId),
            'kelas' => $this->kelasModel->where('tenant_id', $this->tenantId)->findAll(),
            'role' => session()->get('role') ?? 'admin'
        ];

        if ($role == 'admin') {
            return view('admin/data_ustadz', $data);
        } else {
            return redirect()->to('/login');
        }
    }

    public function store()
    {
        if (
            !$this->validate([
                'nama_guru' => 'required|min_length[3]',
                'no_hp' => 'required|numeric|min_length[10]',
                'jenis_kelamin' => 'required|in_list[L,P]',
                'foto' => 'uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data atau format foto tidak sesuai (Maks. 2MB).');
        }

        $tahun = date('Y');
        // Generate NIP berdasarkan tenant aktif agar tidak bentrok antar lembaga
        $lastGuru = $this->guruModel->where('tenant_id', $this->tenantId)->orderBy('id', 'DESC')->first();

        if ($lastGuru && !empty($lastGuru['nip'])) {
            $lastNoUrut = (int) substr($lastGuru['nip'], -3);
            $noUrut = $lastNoUrut + 1;
        } else {
            $noUrut = 1;
        }

        $nip = $tahun . str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        $namaGuru = $this->request->getVar('nama_guru');
        $noHp = $this->request->getVar('no_hp');

        $idKelasDiampu = $this->request->getVar('id_kelas_diampu');
        if (empty($idKelasDiampu)) {
            $idKelasDiampu = null;
        } else {
            // Validasi kelas milik tenant aktif
            $cekKelas = $this->kelasModel->where('id', $idKelasDiampu)->where('tenant_id', $this->tenantId)->first();
            if (!$cekKelas) {
                return redirect()->back()->withInput()->with('error', 'Kelas yang dipilih tidak valid untuk lembaga ini.');
            }
        }

        // Handle Upload Foto ke tabel users
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

        // Simpan data guru dengan menyertakan tenant_id
        $this->guruModel->save([
            'tenant_id' => $this->tenantId,
            'nip' => $nip,
            'nama_guru' => $namaGuru,
            'no_hp' => $noHp,
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'id_kelas_diampu' => $idKelasDiampu,
            'status_aktif' => 'Aktif',
        ]);

        $guruId = $this->guruModel->insertID();

        // Simpan data user beserta foto dan tenant_id
        $this->userModel->save([
            'tenant_id' => $this->tenantId,
            'name' => $namaGuru,
            'username' => $nip,
            'password' => password_hash($nip, PASSWORD_DEFAULT),
            'role' => 'guru',
            'ref_id' => $guruId,
            'foto' => $namaFoto
        ]);

        return redirect()->to(base_url('admin/ustadz'))->with('success', 'Data pengajar dan akun login berhasil ditambahkan! NIP: ' . $nip);
    }

    public function update($id)
    {
        // Pastikan guru yang akan diupdate benar-benar milik tenant aktif
        $guruLama = $this->guruModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$guruLama) {
            return redirect()->to(base_url('admin/ustadz'))->with('error', 'Data pengajar tidak ditemukan.');
        }

        if (
            !$this->validate([
                'nama_guru' => 'required|min_length[3]',
                'no_hp' => 'required|numeric|min_length[10]',
                'jenis_kelamin' => 'required|in_list[L,P]',
                'foto' => 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data pengajar.');
        }

        $idKelasDiampu = $this->request->getVar('id_kelas_diampu');
        if (empty($idKelasDiampu)) {
            $idKelasDiampu = null;
        } else {
            $cekKelas = $this->kelasModel->where('id', $idKelasDiampu)->where('tenant_id', $this->tenantId)->first();
            if (!$cekKelas) {
                return redirect()->back()->withInput()->with('error', 'Kelas yang dipilih tidak valid untuk lembaga ini.');
            }
        }

        $nip = $this->request->getVar('nip');
        if (empty($nip)) {
            $nip = $guruLama['nip'] ?? '';
        }

        $namaGuru = $this->request->getVar('nama_guru');
        $noHp = $this->request->getVar('no_hp');

        $this->guruModel->update($id, [
            'nip' => $nip,
            'nama_guru' => $namaGuru,
            'no_hp' => $noHp,
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'id_kelas_diampu' => $idKelasDiampu,
            'status_aktif' => $this->request->getVar('status_aktif') ?? 'Aktif',
        ]);

        $user = $this->userModel->where('ref_id', $id)
            ->where('role', 'guru')
            ->where('tenant_id', $this->tenantId)
            ->first();

        if ($user) {
            $dataUpdateUser = [
                'name' => $namaGuru,
                'username' => $nip
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

        return redirect()->to(base_url('admin/ustadz'))->with('success', 'Data pengajar berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Pastikan guru milik tenant aktif sebelum dihapus
        $guru = $this->guruModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$guru) {
            return redirect()->to(base_url('admin/ustadz'))->with('error', 'Data pengajar tidak ditemukan.');
        }

        $user = $this->userModel->where('ref_id', $id)
            ->where('role', 'guru')
            ->where('tenant_id', $this->tenantId)
            ->first();

        if ($user) {
            if (!empty($user['foto']) && file_exists('uploads/profile/' . $user['foto'])) {
                unlink('uploads/profile/' . $user['foto']);
            }
            $this->userModel->delete($user['id']);
        }

        $this->guruModel->delete($id);

        return redirect()->to(base_url('admin/ustadz'))->with('success', 'Data pengajar dan akun login berhasil dihapus!');
    }
}