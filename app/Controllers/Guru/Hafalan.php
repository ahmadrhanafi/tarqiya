<?php

namespace App\Controllers\Guru;

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

        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $idGuru = session()->get('ref_id');
        $idKelas = session()->get('id_kelas');

        // Cari data guru berdasarkan tenant_id dan id
        $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)->find($idGuru);
        $id_kelas_diampu = $guru['id_kelas_diampu'] ?? $idKelas;

        // Ambil data kelas berdasarkan tenant_id
        $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($id_kelas_diampu);

        // Menggunakan prefix tabel spesifik pada hafalanModel untuk mencegah error ambigu pada JOIN
        $data = [
            'title' => 'Data Hafalan',
            'icon' => 'fa-solid fa-book-quran',
            'nama_kelas' => $kelas['nama_kelas'] ?? '-',
            'hafalan' => $this->hafalanModel->where('hafalan.tenant_id', $this->tenantId)
                ->getHafalanByGuru($idGuru)
                ->paginate(6, 'hafalan'),
            'pager' => $this->hafalanModel->pager,
            'santri' => $this->santriModel->where('santri.tenant_id', $this->tenantId)
                ->where('santri.id_kelas', $id_kelas_diampu)
                ->findAll(),
            'guru' => $this->guruModel->where('guru.tenant_id', $this->tenantId)->findAll()
        ];

        return view('guru/data_hafalan', $data);
    }

    public function getSurahByJuz($juz)
    {
        $data = $this->hafalanModel->getSurahByJuz($juz);
        return $this->response->setJSON($data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'id_santri' => 'required|numeric',
                'jenis' => 'required',
                'juz' => 'required|numeric',
                'surah' => 'required',
                'ayat_mulai' => 'required|numeric',
                'ayat_selesai' => 'required|numeric',
                'predikat' => 'required'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi data dengan benar.');
        }

        $ayatMulai = $this->request->getVar('ayat_mulai');
        $ayatSelesai = $this->request->getVar('ayat_selesai');

        if ($ayatSelesai < $ayatMulai) {
            return redirect()->back()->withInput()->with('error', 'Ayat selesai tidak boleh lebih kecil dari ayat mulai.');
        }

        // Simpan data dengan menyertakan tenant_id
        $this->hafalanModel->save([
            'tenant_id' => $this->tenantId,
            'id_santri' => $this->request->getVar('id_santri'),
            'id_guru' => session()->get('ref_id'),
            'jenis' => $this->request->getVar('jenis'),
            'juz' => $this->request->getVar('juz'),
            'surah' => $this->request->getVar('surah'),
            'ayat_mulai' => $ayatMulai,
            'ayat_selesai' => $ayatSelesai,
            'predikat' => $this->request->getVar('predikat'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return redirect()->to(base_url('guru/hafalan'))->with('success', 'Data berhasil ditambahkan!');
    }

    public function update($id)
    {
        // Validasi kepemilikan berdasarkan ID, tenant_id, dan guru yang bersangkutan
        $hafalan = $this->hafalanModel->where('hafalan.tenant_id', $this->tenantId)->find($id);

        if (!$hafalan || $hafalan['id_guru'] != session()->get('ref_id')) {
            return redirect()->to(base_url('guru/hafalan'))->with('error', 'Akses ditolak! Data tidak ditemukan atau bukan milik Anda.');
        }

        if (
            !$this->validate([
                'id_santri' => 'required|numeric',
                'jenis' => 'required',
                'juz' => 'required|numeric',
                'surah' => 'required',
                'ayat_mulai' => 'required|numeric',
                'ayat_selesai' => 'required|numeric',
                'predikat' => 'required'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Mohon cek kembali inputan.');
        }

        $this->hafalanModel->update($id, [
            'tenant_id' => $this->tenantId,
            'id_santri' => $this->request->getVar('id_santri'),
            'id_guru' => session()->get('ref_id'),
            'jenis' => $this->request->getVar('jenis'),
            'juz' => $this->request->getVar('juz'),
            'surah' => $this->request->getVar('surah'),
            'ayat_mulai' => $this->request->getVar('ayat_mulai'),
            'ayat_selesai' => $this->request->getVar('ayat_selesai'),
            'predikat' => $this->request->getVar('predikat'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return redirect()->to(base_url('guru/hafalan'))->with('success', 'Data berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Validasi data berdasarkan tenant_id dan kepemilikan guru
        $hafalan = $this->hafalanModel->where('hafalan.tenant_id', $this->tenantId)->find($id);

        if (!$hafalan || $hafalan['id_guru'] != session()->get('ref_id')) {
            return redirect()->to(base_url('guru/hafalan'))->with('error', 'Akses ditolak! Anda tidak berhak menghapus data ini.');
        }

        $this->hafalanModel->delete($id);

        return redirect()->to(base_url('guru/hafalan'))->with('success', 'Data berhasil dihapus!');
    }
}