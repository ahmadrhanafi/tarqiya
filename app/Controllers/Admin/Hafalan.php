<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HafalanModel;
use App\Models\SantriModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class Hafalan extends BaseController
{
    protected $hafalanModel;
    protected $santriModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tenantId;

    public function __construct()
    {
        $this->hafalanModel = new HafalanModel();
        $this->santriModel = new SantriModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();

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

        if ($role == 'admin') {
            // Filter data berdasarkan tenant_id masing-masing lembaga
            $data = [
                'title' => 'Data Hafalan',
                'icon' => 'fa-solid fa-book-quran',
                'hafalan' => $this->hafalanModel->getHafalanWithRelationsByTenant($this->tenantId)->paginate(6, 'hafalan'),
                'pager' => $this->hafalanModel->pager,
                'santri' => $this->santriModel->where('tenant_id', $this->tenantId)->findAll(),
                'guru' => $this->guruModel->where('tenant_id', $this->tenantId)->findAll(),
                'kelas' => $this->kelasModel->where('tenant_id', $this->tenantId)->findAll(),
                'role' => $role,
            ];

            return view('admin/data_hafalan', $data);
        } elseif ($role == 'guru') {
            return redirect()->to('/guru/hafalan');
        } else {
            return redirect()->to('/login');
        }
    }

    public function getSurahByJuz($juz)
    {
        if (CI_DEBUG) {
            service('toolbar')->respond();
        }

        $model = new \App\Models\HafalanModel();
        $data = $model->getSurahByJuz($juz);

        return $this->response->setJSON($data);
    }

    public function getSantriByGuru($idGuru)
    {
        // Pastikan pencarian guru juga dibatasi oleh tenant_id jika diperlukan
        $guru = $this->guruModel->where('id', $idGuru)->where('tenant_id', $this->tenantId)->first();

        if (!$guru || empty($guru['id_kelas_diampu'])) {
            return $this->response->setJSON([]);
        }

        $idKelas = $guru['id_kelas_diampu'];

        $santri = $this->santriModel->where('id_kelas', $idKelas)
            ->where('tenant_id', $this->tenantId)
            ->findAll();

        return $this->response->setJSON($santri);
    }

    public function store()
    {
        if (
            !$this->validate([
                'id_santri' => 'required|numeric',
                'id_guru' => 'required|numeric',
                'jenis' => 'required',
                'juz' => 'required|numeric',
                'surah' => 'required',
                'ayat_mulai' => 'required|numeric',
                'ayat_selesai' => 'required|numeric',
                'predikat' => 'required'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan! Mohon lengkapi data dengan benar.');
        }

        // Simpan data dengan menyertakan tenant_id aktif
        $this->hafalanModel->save([
            'tenant_id' => $this->tenantId,
            'id_santri' => $this->request->getVar('id_santri'),
            'id_guru' => $this->request->getVar('id_guru'),
            'jenis' => $this->request->getVar('jenis'),
            'juz' => $this->request->getVar('juz'),
            'surah' => $this->request->getVar('surah'),
            'ayat_mulai' => $this->request->getVar('ayat_mulai'),
            'ayat_selesai' => $this->request->getVar('ayat_selesai'),
            'predikat' => $this->request->getVar('predikat'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return redirect()->to(base_url('admin/hafalan'))->with('success', 'Data setoran hafalan berhasil ditambahkan!');
    }

    public function update($id)
    {
        if (
            !$this->validate([
                'id_santri' => 'required|numeric',
                'id_guru' => 'required|numeric',
                'jenis' => 'required',
                'juz' => 'required|numeric',
                'surah' => 'required',
                'ayat_mulai' => 'required|numeric',
                'ayat_selesai' => 'required|numeric',
                'predikat' => 'required'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui! Mohon cek kembali inputan anda.');
        }

        // Pastikan data yang diedit benar-benar milik tenant yang sedang aktif (keamanan tambahan)
        $hafalan = $this->hafalanModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$hafalan) {
            return redirect()->to(base_url('admin/hafalan'))->with('error', 'Data hafalan tidak ditemukan.');
        }

        $this->hafalanModel->update($id, [
            'id_santri' => $this->request->getVar('id_santri'),
            'id_guru' => $this->request->getVar('id_guru'),
            'jenis' => $this->request->getVar('jenis'),
            'juz' => $this->request->getVar('juz'),
            'surah' => $this->request->getVar('surah'),
            'ayat_mulai' => $this->request->getVar('ayat_mulai'),
            'ayat_selesai' => $this->request->getVar('ayat_selesai'),
            'predikat' => $this->request->getVar('predikat'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return redirect()->to(base_url('admin/hafalan'))->with('success', 'Data setoran hafalan berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Validasi kepemilikan data berdasarkan tenant_id
        $hafalan = $this->hafalanModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();

        if (!$hafalan) {
            return redirect()->to(base_url('admin/hafalan'))->with('error', 'Data hafalan tidak ditemukan.');
        }

        $this->hafalanModel->delete($id);

        return redirect()->to(base_url('admin/hafalan'))->with('success', 'Data hafalan berhasil dihapus!');
    }
}