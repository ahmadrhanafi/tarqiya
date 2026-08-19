<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HafalanModel;

class Statistik extends BaseController
{
    protected $hafalanModel;
    protected $tenantId;

    public function __construct()
    {
        $this->hafalanModel = new HafalanModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $periode = $this->request->getGet('periode') ?? 'tahun_ini';

        // Memanggil method model dengan menyertakan parameter tenantId 
        // (Pastikan method pada HafalanModel Anda sudah disesuaikan untuk menerima tenantId)
        $data = [
            'title' => 'Statistik Hafalan',
            'icon' => 'fa-solid fa-chart-line',
            'periode' => $periode,
            'rata_setoran' => $this->hafalanModel->getRataRataByTenant($this->tenantId, $periode),
            'juz_dominan' => $this->hafalanModel->getJuzDominanByTenant($this->tenantId, $periode),
            'predikat_umum' => $this->hafalanModel->getPredikatTerbanyakByTenant($this->tenantId, $periode),
            'capaian_juz' => $this->hafalanModel->getProgressJuzByTenant($this->tenantId, $periode),
            'grafik_setoran' => $this->hafalanModel->getGrafikSetoranByTenant($this->tenantId, $periode),
        ];

        return view('admin/statistik_hafalan', $data);
    }

    public function export()
    {
        $periode = $this->request->getGet('periode') ?? 'tahun_ini';

        $data = [
            'periode' => $periode,
            'rata_setoran' => $this->hafalanModel->getRataRataByTenant($this->tenantId, $periode),
            'juz_dominan' => $this->hafalanModel->getJuzDominanByTenant($this->tenantId, $periode),
            'predikat_umum' => $this->hafalanModel->getPredikatTerbanyakByTenant($this->tenantId, $periode),
            'capaian_juz' => $this->hafalanModel->getProgressJuzByTenant($this->tenantId, $periode),
            'grafik_setoran' => $this->hafalanModel->getGrafikSetoranByTenant($this->tenantId, $periode),
        ];

        $html = view('admin/cetak_statistik_hafalan', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nama_file = 'Laporan_Statistik_Setoran_' . strtoupper($periode) . '.pdf';

        $dompdf->stream($nama_file, ['Attachment' => true]);
        exit;
    }
}