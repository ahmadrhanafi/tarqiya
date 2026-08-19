<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\HafalanModel;

class Dashboard extends BaseController
{
    protected $tenantId;

    public function __construct()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $refId = session()->get('ref_id');

        // Ambil data guru beserta nama kelasnya dengan filter tenant_id
        $guru = $db->table('guru')
            ->select('guru.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = guru.id_kelas_diampu', 'left')
            ->where('guru.tenant_id', $this->tenantId)
            ->where('guru.id', $refId)
            ->get()
            ->getRowArray();

        $setoranHafalan = [];
        $infoKelasBinaan = null;
        $totalSantriBinaan = 0;
        $totalSetoranHariIni = 0;

        if ($guru && !empty($guru['id_kelas_diampu'])) {
            $idKelas = $guru['id_kelas_diampu'];

            // Hitung total santri binaan berdasarkan kelas dan tenant_id
            $totalSantriBinaan = $db->table('santri')
                ->where('tenant_id', $this->tenantId)
                ->where('id_kelas', $idKelas)
                ->countAllResults();

            $tanggalHariIni = date('Y-m-d');

            // Hitung total setoran hari ini dengan filter tenant_id di tabel hafalan & santri
            $totalSetoranHariIni = $db->table('hafalan')
                ->join('santri', 'santri.id = hafalan.id_santri')
                ->where('hafalan.tenant_id', $this->tenantId)
                ->where('santri.id_kelas', $idKelas)
                ->where('DATE(hafalan.created_at)', $tanggalHariIni)
                ->countAllResults();

            // Ambil 5 setoran hafalan terakhir dengan filter tenant_id
            $setoranHafalan = $db->table('hafalan')
                ->select('hafalan.*, santri.nama_santri, kelas.nama_kelas')
                ->join('santri', 'santri.id = hafalan.id_santri')
                ->join('kelas', 'kelas.id = santri.id_kelas')
                ->where('hafalan.tenant_id', $this->tenantId)
                ->where('santri.id_kelas', $idKelas)
                ->orderBy('hafalan.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            // Ambil informasi kelas binaan dengan filter tenant_id
            $infoKelasBinaan = $db->table('kelas')
                ->where('tenant_id', $this->tenantId)
                ->where('id', $idKelas)
                ->get()
                ->getRowArray();
        }

        $data = [
            'title' => 'Dashboard Guru',
            'setoran' => $setoranHafalan,
            'kelas_binaan' => $infoKelasBinaan,
            'total_santri_binaan' => $totalSantriBinaan,
            'total_setoran_hari_ini' => $totalSetoranHariIni,
            'guru' => $guru
        ];

        return view('guru/dashboard', $data);
    }
}