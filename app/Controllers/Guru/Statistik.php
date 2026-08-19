<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\HafalanModel;
use App\Models\UserModel;
use Dompdf\Dompdf;

class Statistik extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $hafalanModel;
    protected $userModel;
    protected $tenantId;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->hafalanModel = new HafalanModel();
        $this->userModel = new UserModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
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

        $id_guru = $user['ref_id'] ?? session()->get('ref_id');

        // Cari data guru dengan filter tenant_id
        $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)->find($id_guru);
        $id_kelas_diampu = $guru['id_kelas_diampu'] ?? session()->get('id_kelas');

        // Cari data kelas dengan filter tenant_id
        $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($id_kelas_diampu);
        $nama_kelas = $kelas['nama_kelas'] ?? 'Belum Ada Kelas';

        $periode = $this->request->getGet('periode') ?? 'bulan_ini';

        $data = [
            'title' => 'Statistik Hafalan',
            'icon' => 'fa-solid fa-chart-line',
            'nama_kelas' => $nama_kelas,
            'rata_setoran' => $this->hafalanModel->getRataRataKelas($id_guru, $periode, $this->tenantId),
            'juz_dominan' => $this->hafalanModel->getJuzDominanKelas($id_guru, $periode, $this->tenantId),
            'predikat_umum' => $this->hafalanModel->getPredikatTerbanyakKelas($id_guru, $periode, $this->tenantId),
            'capaian_juz' => $this->hafalanModel->getProgressJuzKelas($id_guru, $periode, $this->tenantId),
            'grafik_setoran' => $this->hafalanModel->getGrafikSetoranKelas($id_guru, $periode, $this->tenantId),
            'rekap_santri' => $this->hafalanModel->getRekapSantriKelas($id_guru, $periode, $this->tenantId),
        ];

        return view('guru/statistik_hafalan', $data);
    }

    public function export()
    {
        $userId = session()->get('id');
        $user = $this->userModel->where('tenant_id', $this->tenantId)->find($userId);
        $id_guru = $user['ref_id'] ?? session()->get('ref_id');

        $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)->find($id_guru);
        $id_kelas_diampu = $guru['id_kelas_diampu'] ?? session()->get('id_kelas');
        $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($id_kelas_diampu);

        $periode = $this->request->getGet('periode') ?? 'bulan_ini';

        $data = [
            'nama_guru' => $guru['nama_guru'] ?? '-',
            'nama_kelas' => $kelas['nama_kelas'] ?? '-',
            'periode' => $periode,
            'rata_setoran' => $this->hafalanModel->getRataRataKelas($id_guru, $periode, $this->tenantId),
            'juz_dominan' => $this->hafalanModel->getJuzDominanKelas($id_guru, $periode, $this->tenantId),
            'predikat_umum' => $this->hafalanModel->getPredikatTerbanyakKelas($id_guru, $periode, $this->tenantId),
            'capaian_juz' => $this->hafalanModel->getProgressJuzKelas($id_guru, $periode, $this->tenantId),
            'detail_hafalan' => $this->hafalanModel->getDetailHafalanByPeriode($id_guru, $periode, $this->tenantId),
        ];

        // FITUR DEBUG: Jika diakses dengan /export?print=1 di URL, tampilkan isi datanya
        if ($this->request->getGet('print') == 1) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            exit;
        }

        $html = view('guru/cetak_laporan_statistik', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nama_kelas_bersih = preg_replace('/[^A-Za-z0-9\-_]/', '_', $kelas['nama_kelas'] ?? 'Kelas');
        $nama_file = 'Laporan_Statistik_' . $nama_kelas_bersih . '_' . strtoupper($periode) . '.pdf';

        $dompdf->stream($nama_file, ['Attachment' => true]);
        exit;
    }
}