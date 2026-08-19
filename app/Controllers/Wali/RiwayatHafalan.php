<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;
use App\Models\HafalanModel;
use App\Models\SantriModel;
use App\Models\GuruModel;

class RiwayatHafalan extends BaseController
{
    protected $hafalanModel;
    protected $santriModel;
    protected $guruModel;
    protected $tenantId;

    public function __construct()
    {
        $this->hafalanModel = new HafalanModel();
        $this->santriModel = new SantriModel();
        $this->guruModel = new GuruModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'wali') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $idWali = session()->get('ref_id') ?? session()->get('id');

        // Ambil semua anak wali berdasarkan tenant_id dan id_wali
        $santriList = $this->santriModel->select('santri.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->where('santri.tenant_id', $this->tenantId)
            ->where('santri.id_wali', $idWali)
            ->findAll();

        $idSantriDipilih = $this->request->getGet('id_santri');

        if (empty($idSantriDipilih) && !empty($santriList)) {
            $idSantriDipilih = $santriList[0]['id'];
        }

        $santriAktif = null;
        $riwayat = [];
        $periode = $this->request->getGet('periode') ?? 'bulan_ini';

        $statistik = [
            'juz_aktif' => '-',
            'total_setoran' => 0,
            'predikat_dominan' => '-'
        ];

        if (!empty($idSantriDipilih)) {
            // Ambil detail santri aktif dengan validasi tenant_id
            $santriAktif = $this->santriModel->select('santri.*, kelas.nama_kelas, guru.nama_guru, guru.no_hp as no_hp_guru')
                ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
                ->join('guru', 'guru.id_kelas_diampu = santri.id_kelas AND guru.tenant_id = santri.tenant_id', 'left')
                ->where('santri.tenant_id', $this->tenantId)
                ->where('santri.id', $idSantriDipilih)
                ->where('santri.id_wali', $idWali)
                ->first();

            if ($santriAktif) {
                // Sertakan parameter tenantId pada metode kustom model
                $riwayat = $this->hafalanModel->getRiwayatBySantri($idSantriDipilih, $periode, $this->tenantId);
                $statistik = $this->hafalanModel->getStatistikRingkasBySantri($idSantriDipilih, $periode, $this->tenantId);
            }
        }

        $data = [
            'title' => 'Riwayat Hafalan',
            'icon' => 'fa-solid fa-history',
            'santri_list' => $santriList,
            'santri_aktif' => $santriAktif,
            'riwayat' => $riwayat,
            'periodeVal' => $periode,
            'juz_aktif' => $statistik['juz_aktif'],
            'total_setoran' => $statistik['total_setoran'],
            'predikat_dominan' => $statistik['predikat_dominan']
        ];

        return view('wali/riwayat_hafalan', $data);
    }
}