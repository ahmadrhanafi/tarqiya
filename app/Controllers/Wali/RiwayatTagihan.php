<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\PembayaranModel;

class RiwayatTagihan extends BaseController
{
    protected $tenantId;

    public function __construct()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'wali') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $santriModel = new SantriModel();
        $pembayaranModel = new PembayaranModel();

        $idWali = session()->get('ref_id') ?? session()->get('id');

        // Ambil data anak berdasarkan tenant_id dan id_wali
        $anak = $santriModel->select('santri.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->where('santri.tenant_id', $this->tenantId)
            ->where('santri.id_wali', $idWali)
            ->findAll();

        $idsAnak = array_column($anak, 'id');

        $selectedSantri = $this->request->getGet('id_santri');
        $selectedStatus = $this->request->getGet('status');
        $keyword = $this->request->getGet('keyword');

        $tagihan = [];
        $santriAktif = null;

        $totalLunas = 0;
        $totalTagihanAktif = 0;
        $jumlahPending = 0;

        if (!empty($idsAnak)) {
            $targetIdsAnak = (!empty($selectedSantri) && in_array($selectedSantri, $idsAnak))
                ? [$selectedSantri]
                : $idsAnak;

            $totalLunas = $pembayaranModel->db->table('pembayaran')
                ->selectSum('jumlah')
                ->where('tenant_id', $this->tenantId)
                ->whereIn('id_santri', $targetIdsAnak)
                ->where('status', 'Lunas')
                ->get()->getRow()->jumlah ?? 0;

            $totalTagihanAktif = $pembayaranModel->db->table('pembayaran')
                ->selectSum('jumlah')
                ->where('tenant_id', $this->tenantId)
                ->whereIn('id_santri', $targetIdsAnak)
                ->whereIn('status', ['Pending', 'Menunggu Verifikasi', 'tertunda'])
                ->get()->getRow()->jumlah ?? 0;

            $jumlahPending = $pembayaranModel->db->table('pembayaran')
                ->where('tenant_id', $this->tenantId)
                ->whereIn('id_santri', $targetIdsAnak)
                ->whereIn('status', ['Pending', 'Menunggu Verifikasi', 'tertunda'])
                ->countAllResults();

            $builder = $pembayaranModel->getPembayaranWithSantriByTenant($this->tenantId);

            if (!empty($selectedSantri) && in_array($selectedSantri, $idsAnak)) {
                $builder->where('pembayaran.id_santri', $selectedSantri);
                foreach ($anak as $a) {
                    if ($a['id'] == $selectedSantri) {
                        $santriAktif = $a;
                        break;
                    }
                }
            } else {
                $builder->whereIn('pembayaran.id_santri', $idsAnak);
                $santriAktif = $anak[0] ?? null;
            }

            if (!empty($selectedStatus)) {
                $builder->where('pembayaran.status', $selectedStatus);
            }

            if (!empty($keyword)) {
                $builder->groupStart()
                    ->like('pembayaran.jenis_pembayaran', $keyword)
                    ->orLike('pembayaran.keterangan', $keyword)
                    ->groupEnd();
            }

            $tagihan = $builder->findAll();
        }

        $data = [
            'title' => 'Riwayat Tagihan & Pembayaran',
            'tagihan' => $tagihan,
            'santri_list' => $anak,
            'santri_aktif' => $santriAktif,
            'selectedStatus' => $selectedStatus,
            'keyword' => $keyword,
            'totalLunas' => $totalLunas,
            'totalTagihanAktif' => $totalTagihanAktif,
            'jumlahPending' => $jumlahPending
        ];

        return view('wali/riwayat_tagihan', $data);
    }

    public function konfirmasi($id)
    {
        $pembayaranModel = new PembayaranModel();

        // Validasi tagihan berdasarkan tenant_id aktif
        $tagihan = $pembayaranModel->where('tenant_id', $this->tenantId)->find($id);
        if (!$tagihan) {
            return redirect()->back()->with('error', 'Data tagihan tidak ditemukan.');
        }

        $fileBukti = $this->request->getFile('bukti_pembayaran');

        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $namaFileBaru = $fileBukti->getRandomName();

            $fileBukti->move('uploads/bukti', $namaFileBaru);

            $pembayaranModel->update($id, [
                'tanggal_konfirmasi' => $this->request->getPost('tanggal_konfirmasi'),
                'bank_tujuan' => $this->request->getPost('bank_tujuan'),
                'bukti_pembayaran' => $namaFileBaru,
                'status' => 'Menunggu Verifikasi'
            ]);

            return redirect()->to(base_url('wali/riwayat-tagihan'))->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload bukti pembayaran. Pastikan file valid.');
    }

    public function unduhKuitansi($id)
    {
        $pembayaranModel = new PembayaranModel();

        $tagihan = $pembayaranModel->getPembayaranWithSantriByTenant($this->tenantId)
            ->where('pembayaran.id', $id)
            ->first();

        if (!$tagihan) {
            return redirect()->back()->with('error', 'Data tagihan tidak ditemukan.');
        }

        if ($tagihan['status'] !== 'Lunas') {
            return redirect()->back()->with('error', 'Kuitansi hanya dapat diunduh untuk tagihan yang sudah lunas.');
        }

        $data = [
            'tagihan' => $tagihan
        ];

        $html = view('wali/kuitansi_pdf', $data);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('kuitansi_tagihan_' . $tagihan['id'] . '.pdf', ['Attachment' => true]);
    }

    public function exportExcel()
    {
        $santriModel = new SantriModel();
        $pembayaranModel = new PembayaranModel();

        $idWali = session()->get('ref_id') ?? session()->get('id');

        $anak = $santriModel->where('tenant_id', $this->tenantId)
            ->where('id_wali', $idWali)
            ->findAll();
        $idsAnak = array_column($anak, 'id');

        $selectedSantri = $this->request->getGet('id_santri');
        $selectedStatus = $this->request->getGet('status');

        $dataPembayaran = [];

        if (!empty($idsAnak)) {
            $builder = $pembayaranModel->getPembayaranWithSantriByTenant($this->tenantId);

            if (!empty($selectedSantri) && in_array($selectedSantri, $idsAnak)) {
                $builder->where('pembayaran.id_santri', $selectedSantri);
            } else {
                $builder->whereIn('pembayaran.id_santri', $idsAnak);
            }

            if (!empty($selectedStatus)) {
                $builder->where('pembayaran.status', $selectedStatus);
            }

            $dataPembayaran = $builder->findAll();
        }

        $filename = "Riwayat-Tagihan-Wali-" . date('Y-m-d') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background-color: #d1e7dd;">';
        echo '<th>No</th>';
        echo '<th>ID Transaksi</th>';
        echo '<th>Nama Santri</th>';
        echo '<th>Kelas</th>';
        echo '<th>Jenis Pembayaran</th>';
        echo '<th>Nominal (Rp)</th>';
        echo '<th>Status</th>';
        echo '<th>Tanggal</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $no = 1;
        foreach ($dataPembayaran as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . ($row['id_transaksi'] ?? '-') . '</td>';
            echo '<td>' . $row['nama_santri'] . '</td>';
            echo '<td>' . ($row['nama_kelas'] ?? '-') . '</td>';
            echo '<td>' . $row['jenis_pembayaran'] . '</td>';
            echo '<td>' . $row['jumlah'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '<td>' . $row['tanggal'] . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        exit();
    }
}