<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Santri extends BaseController
{
    protected $santriModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tenantId;

    public function __construct()
    {
        $this->santriModel = new SantriModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'guru') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $role = session()->get('role');
        $namaGuru = session()->get('name');

        if ($role == 'guru') {
            // Cari data guru berdasarkan tenant_id dan namanya (gunakan 'guru.tenant_id')
            $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)
                ->where('nama_guru', $namaGuru)->first();

            if (!$guru) {
                $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)
                    ->like('nama_guru', str_replace(['Ust.', 'Ustz.'], '', $namaGuru))->first();
            }

            $idKelasDiampu = $guru ? $guru['id_kelas_diampu'] : null;

            $namaKelasString = '-';
            if ($idKelasDiampu) {
                // Perbaikan: gunakan 'kelas.tenant_id' agar tidak ambigu
                $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($idKelasDiampu);
                $namaKelasString = $kelas['nama_kelas'] ?? '-';
            }

            $keyword = $this->request->getGet('keyword');
            $status = $this->request->getGet('status');

            // Gunakan method berbasis tenant di Model
            $santri = $this->santriModel->searchSantriByTenant($this->tenantId, $keyword, $idKelasDiampu, $status);

            $data = [
                'title' => 'Data Santri',
                'icon' => 'fa-solid fa-user-graduate',
                'santri' => $santri,
                'nama_kelas' => $namaKelasString,
                'role' => $role
            ];

            return view('guru/data_santri', $data);
        } elseif ($role == 'admin') {
            return redirect()->to('/admin/santri');
        } else {
            return redirect()->to('/login');
        }
    }

    public function detail($id)
    {
        // Ambil detail santri dengan validasi tenant_id
        $santri = $this->santriModel->getSantriWithRelationsByTenant($this->tenantId, $id);

        if (!$santri) {
            return redirect()->to(base_url('guru/santri'))->with('error', 'Data santri tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Santri',
            'santri' => $santri
        ];

        return view('guru/santri-detail', $data);
    }

    public function cetakKartu($id)
    {
        $santri = $this->santriModel->getSantriWithRelationsByTenant($this->tenantId, $id);

        if (!$santri) {
            return redirect()->back();
        }

        $base64FotoSantri = null;

        if (!empty($santri['foto'])) {
            $pathFoto = FCPATH . 'uploads/santri/' . $santri['foto'];
            if (file_exists($pathFoto)) {
                $type = pathinfo($pathFoto, PATHINFO_EXTENSION);
                $data = file_get_contents($pathFoto);
                $base64FotoSantri = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', FCPATH);

        $dompdf = new \Dompdf\Dompdf($options);

        $html = view('guru/cetak_ekartu_santri', [
            'santri' => $santri,
            'base64FotoSantri' => $base64FotoSantri
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('a7', 'landscape');
        $dompdf->render();

        if (ob_get_length()) {
            ob_end_clean();
        }

        $namaFile = "Kartu_Santri_" . preg_replace('/[^A-Za-z0-9_]/', '_', $santri['nama_santri']) . ".pdf";

        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"" . $namaFile . "\"");

        echo $dompdf->output();
        exit();
    }

    public function cetak()
    {
        $role = session()->get('role');
        if ($role !== 'guru') {
            return redirect()->to('/login');
        }

        $namaGuru = session()->get('name');
        $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)
            ->where('nama_guru', $namaGuru)->first();

        if (!$guru) {
            $guru = $this->guruModel->where('guru.tenant_id', $this->tenantId)
                ->like('nama_guru', str_replace(['Ust.', 'Ustz.'], '', $namaGuru))->first();
        }

        $idKelasDiampu = $guru ? $guru['id_kelas_diampu'] : null;

        // Ambil data santri dengan searchSantriByTenant
        $santri = $this->santriModel->searchSantriByTenant($this->tenantId, null, $idKelasDiampu, null);

        // Perbaikan: gunakan 'kelas.tenant_id'
        $kelas = $this->kelasModel->where('kelas.tenant_id', $this->tenantId)->find($idKelasDiampu);

        $data = [
            'nama_guru' => $guru['nama_guru'] ?? '-',
            'nama_kelas' => $kelas['nama_kelas'] ?? 'Semua Kelas',
            'santri' => $santri
        ];

        if ($this->request->getGet('print') == 1) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            exit;
        }

        $html = view('guru/cetak_data_santri', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nama_kelas_bersih = preg_replace('/[^A-Za-z0-9\-_]/', '_', $kelas['nama_kelas'] ?? 'Binaan');
        $nama_file = 'Laporan_Santri_' . $nama_kelas_bersih . '.pdf';

        $dompdf->stream($nama_file, ['Attachment' => true]);
        exit;
    }
}