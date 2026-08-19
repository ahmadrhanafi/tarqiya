<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\HafalanModel;
use App\Models\PembayaranModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Dashboard extends BaseController
{
    protected $santriModel;
    protected $hafalanModel;
    protected $pembayaranModel;
    protected $tenantId;

    public function __construct()
    {
        $this->santriModel = new SantriModel();
        $this->hafalanModel = new HafalanModel();
        $this->pembayaranModel = new PembayaranModel();

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

        // Filter pencarian anak berdasarkan tenant_id dan id_wali
        $anak = $this->santriModel->select('santri.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->where('santri.tenant_id', $this->tenantId)
            ->where('santri.id_wali', $idWali)
            ->findAll();

        if (!empty($anak)) {
            foreach ($anak as &$a) {
                $a['stat_total_setoran'] = $this->hafalanModel->where('hafalan.tenant_id', $this->tenantId)
                    ->where('id_santri', $a['id'])
                    ->countAllResults();

                $terakhir = $this->hafalanModel->select('juz, ayat_mulai, ayat_selesai')
                    ->where('hafalan.tenant_id', $this->tenantId)
                    ->where('id_santri', $a['id'])
                    ->orderBy('created_at', 'DESC')
                    ->first();

                if ($terakhir) {
                    $a['stat_juz'] = 'Juz ' . $terakhir['juz'] . ' (Ayat ' . $terakhir['ayat_mulai'] . '-' . $terakhir['ayat_selesai'] . ')';
                } else {
                    $a['stat_juz'] = 'Belum ada setoran';
                }
            }
            unset($a);
        }

        $idsAnak = array_column($anak, 'id');
        $setoranTerbaru = [];
        $tagihanTerbaru = [];

        if (!empty($idsAnak)) {
            $setoranTerbaru = $this->hafalanModel->select('hafalan.*, santri.nama_santri as nama_santri, santri.nis')
                ->join('santri', 'santri.id = hafalan.id_santri', 'inner')
                ->where('hafalan.tenant_id', $this->tenantId)
                ->whereIn('hafalan.id_santri', $idsAnak)
                ->orderBy('hafalan.created_at', 'DESC')
                ->limit(5)
                ->findAll();

            $tagihanTerbaru = $this->pembayaranModel->select('pembayaran.*, santri.nama_santri, kelas.nama_kelas')
                ->join('santri', 'santri.id = pembayaran.id_santri', 'inner')
                ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
                ->where('pembayaran.tenant_id', $this->tenantId)
                ->whereIn('pembayaran.id_santri', $idsAnak)
                ->orderBy('pembayaran.created_at', 'DESC')
                ->limit(4)
                ->findAll();
        }

        $data = [
            'title' => 'Dashboard Wali',
            'anak' => $anak,
            'setoran_terbaru' => $setoranTerbaru,
            'tagihan_terbaru' => $tagihanTerbaru,
            'stat_jumlah_anak' => count($anak)
        ];

        return view('wali/dashboard', $data);
    }

    public function detailSantri($id)
    {
        // Validasi relasi santri berdasarkan tenant_id dan kepemilikan wali
        $idWali = session()->get('ref_id') ?? session()->get('id');

        $santri = $this->santriModel->select('santri.*, kelas.nama_kelas, users.name as nama_wali, users.phone as no_hp_wali, users.address as alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('users', 'users.ref_id = santri.id_wali AND users.role = "wali"', 'left')
            ->where('santri.tenant_id', $this->tenantId)
            ->where('santri.id_wali', $idWali)
            ->find($id);

        if (!$santri) {
            return redirect()->to(base_url('wali/dashboard'))->with('error', 'Data santri tidak ditemukan atau bukan anak Anda.');
        }

        $data = [
            'title' => 'Detail Santri',
            'icon' => 'fa-solid fa-user-graduate',
            'santri' => $santri
        ];

        return view('wali/santri-detail', $data);
    }

    public function cetakKartu($id)
    {
        $idWali = session()->get('ref_id') ?? session()->get('id');

        $santri = $this->santriModel->select('santri.*, kelas.nama_kelas, users.phone as no_hp_wali, users.address as alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('users', 'users.ref_id = santri.id_wali AND users.role = "wali"', 'left')
            ->where('santri.tenant_id', $this->tenantId)
            ->where('santri.id_wali', $idWali)
            ->find($id);

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

        $html = view('wali/cetak_ekartu_santri', [
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
}