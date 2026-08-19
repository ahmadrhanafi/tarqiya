<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\HafalanModel;
use App\Models\SantriModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class RiwayatHafalan extends BaseController
{
    protected $hafalanModel;
    protected $santriModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tenantId; // Tambahkan properti tenantId

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

        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $idGuru = session()->get('ref_id');

        // Pastikan pencarian guru berdasarkan tenant_id
        $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)->find($idGuru);
        $idKelasGuru = $guru['id_kelas_diampu'] ?? session()->get('id_kelas');

        $namaKelasString = '-';
        if (!empty($idKelasGuru)) {
            $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($idKelasGuru);
            $namaKelasString = $kelas['nama_kelas'] ?? '-';
        }

        // Query santri dengan tenant_id
        $builder = $this->santriModel->select('santri.*, kelas.nama_kelas, users.name as nama_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('users', 'users.ref_id = santri.id_wali AND users.role = "wali"', 'left')
            ->where('santri.tenant_id', $this->tenantId);

        if (!empty($idKelasGuru)) {
            $builder->where('santri.id_kelas', $idKelasGuru);
        } else {
            $builder->where('santri.id_kelas', 0);
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('santri.nama_santri', $keyword)
                ->orLike('santri.nis', $keyword)
                ->groupEnd();
        }

        $santri = $builder->orderBy('santri.nama_santri', 'ASC')->findAll();
        $idSantriKelas = array_column($santri, 'id');

        $totalSetoranBulanIni = 0;
        $santriAktifBulanIni = 0;
        $predikatUmum = 'Belum Ada';

        if (!empty($idSantriKelas)) {
            $bulanIni = date('m');
            $tahunIni = date('Y');

            // Tambahkan filter tenant_id pada setiap query statistik
            $totalSetoranBulanIni = $this->hafalanModel->where('hafalan.tenant_id', $this->tenantId)
                ->whereIn('id_santri', $idSantriKelas)
                ->where('MONTH(created_at)', $bulanIni)
                ->where('YEAR(created_at)', $tahunIni)
                ->countAllResults();

            $santriAktifBulanIni = $this->hafalanModel->select('id_santri')
                ->where('hafalan.tenant_id', $this->tenantId)
                ->whereIn('id_santri', $idSantriKelas)
                ->where('MONTH(created_at)', $bulanIni)
                ->where('YEAR(created_at)', $tahunIni)
                ->groupBy('id_santri')
                ->countAllResults();

            $dominantPredikat = $this->hafalanModel->select('predikat, COUNT(predikat) as jumlah')
                ->where('hafalan.tenant_id', $this->tenantId)
                ->whereIn('id_santri', $idSantriKelas)
                ->where('MONTH(created_at)', $bulanIni)
                ->where('YEAR(created_at)', $tahunIni)
                ->groupBy('predikat')
                ->orderBy('jumlah', 'DESC')
                ->first();

            if (!empty($dominantPredikat)) {
                $predikatUmum = ucwords($dominantPredikat['predikat']);
            }
        }

        return view('guru/riwayat_hafalan', [
            'title' => 'Riwayat Hafalan',
            'santri' => $santri,
            'keyword' => $keyword,
            'nama_kelas' => $namaKelasString,
            'total_setoran_bulan_ini' => $totalSetoranBulanIni,
            'santri_aktif' => $santriAktifBulanIni,
            'total_santri' => count($santri),
            'predikat_umum' => $predikatUmum
        ]);
    }

    public function detail($id_santri)
    {
        $santri = $this->santriModel->where('tenant_id', $this->tenantId)
            ->where('id', $id_santri)
            ->first();

        if (!$santri) {
            return redirect()->back()->with('error', 'Data santri tidak ditemukan.');
        }

        $riwayat = $this->hafalanModel->where('tenant_id', $this->tenantId)
            ->where('id_santri', $id_santri)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('guru/_detail_riwayat_hafalan', [
            'title' => 'Detail Riwayat Hafalan - ' . $santri['nama_santri'],
            'icon' => 'fa-solid fa-book-quran',
            'santri' => $santri,
            'riwayat' => $riwayat
        ]);
    }

    public function ekspor()
    {
        // Pastikan akses ekspor hanya untuk tenant aktif
        $idGuru = session()->get('ref_id');
        $guru = $this->guruModel->where('tenant_id', $this->tenantId)->find($idGuru);
        $idKelasGuru = $guru['id_kelas_diampu'] ?? session()->get('id_kelas');

        if (empty($idKelasGuru)) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $kelas = $this->kelasModel->where('tenant_id', $this->tenantId)->find($idKelasGuru);
        $namaKelas = $kelas['nama_kelas'] ?? 'Kelas';

        $santri = $this->santriModel->where('tenant_id', $this->tenantId)
            ->where('id_kelas', $idKelasGuru)
            ->findAll();

        $filename = 'Rekap_Hafalan_' . str_replace(' ', '_', $namaKelas) . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'NIS', 'Nama Santri', 'Jenis Kelamin', 'Kelas', 'Total Setoran Bulan Ini']);

        $no = 1;
        $bulanIni = date('m');
        $tahunIni = date('Y');

        foreach ($santri as $s) {
            $totalSetoran = $this->hafalanModel->where('id_santri', $s['id'])
                ->where('MONTH(created_at)', $bulanIni)
                ->where('YEAR(created_at)', $tahunIni)
                ->countAllResults();

            $jk = ($s['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan';

            fputcsv($output, [
                $no++,
                $s['nis'],
                $s['nama_santri'],
                $jk,
                $s['nama_kelas'],
                $totalSetoran . ' Setoran'
            ]);
        }

        fclose($output);
        exit();
    }
}
