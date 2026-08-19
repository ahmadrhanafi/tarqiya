<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\SantriModel;
use App\Models\KelasModel;

class Administrasi extends BaseController
{
    protected $pembayaranModel;
    protected $santriModel;
    protected $kelasModel;
    protected $tenantId;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->santriModel = new SantriModel();
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
        $perPage = 10;

        $selectedMonth = $this->request->getGet('month') ?? date('m');
        $selectedYear = $this->request->getGet('year') ?? date('Y');
        $selectedStatus = $this->request->getGet('status') ?? '';
        $keyword = $this->request->getGet('keyword') ?? '';

        // Base query pembayaran dengan batasan tenant_id
        // Pastikan method getPembayaranWithSantriByTenant() sudah memfilter berdasarkan pembayaran.tenant_id
        $builder = $this->pembayaranModel->getPembayaranWithSantriByTenant($this->tenantId);

        if (!empty($selectedMonth)) {
            $builder->where('MONTH(pembayaran.tanggal)', $selectedMonth);
            $builder->where('YEAR(pembayaran.tanggal)', $selectedYear);
        }

        if (!empty($selectedStatus)) {
            $builder->where('pembayaran.status', $selectedStatus);
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('santri.nama_santri', $keyword)
                ->orLike('wali.nama_wali', $keyword)
                ->orLike('pembayaran.keterangan', $keyword)
                ->groupEnd();
        }

        // Hitung Total Pembayaran Masuk (Status Lunas) dengan filter tenant
        $totalBulanIni = $this->pembayaranModel->db->table('pembayaran')
            ->selectSum('jumlah')
            ->where('tenant_id', $this->tenantId)
            ->where('status', 'Lunas')
            ->where('MONTH(tanggal)', $selectedMonth)
            ->where('YEAR(tanggal)', $selectedYear)
            ->get()
            ->getRow()
            ->jumlah ?? 0;

        // Hitung Jumlah Transaksi Lunas dengan filter tenant
        $countLunasBulanIni = $this->pembayaranModel->db->table('pembayaran')
            ->where('tenant_id', $this->tenantId)
            ->where('status', 'Lunas')
            ->where('MONTH(tanggal)', $selectedMonth)
            ->where('YEAR(tanggal)', $selectedYear)
            ->countAllResults();

        // Hitung Jumlah Pending / Belum Lunas dengan filter tenant
        $countPending = $this->pembayaranModel->db->table('pembayaran')
            ->where('tenant_id', $this->tenantId)
            ->whereIn('status', ['Pending', 'Menunggu Verifikasi', 'tertunda'])
            ->where('MONTH(tanggal)', $selectedMonth)
            ->where('YEAR(tanggal)', $selectedYear)
            ->countAllResults();

        $data = [
            'title' => 'Administrasi',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'administrasi' => $builder->paginate($perPage, 'administrasi'),
            'pager' => $this->pembayaranModel->pager,
            'listKelas' => $this->kelasModel->where('tenant_id', $this->tenantId)->findAll(),
            'listSantri' => $this->santriModel->select('santri.id, santri.nama_santri, santri.id_kelas, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
                ->where('santri.tenant_id', $this->tenantId)
                ->where('santri.status_aktif', 'Aktif')
                ->findAll(),
            'role' => session()->get('role') ?? 'admin',
            'totalBulanIni' => $totalBulanIni,
            'countLunasBulanIni' => $countLunasBulanIni,
            'countPending' => $countPending,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedStatus' => $selectedStatus,
            'keyword' => $keyword
        ];

        return view('admin/administrasi', $data);
    }

    public function store()
    {
        $targetType = $this->request->getPost('target_type') ?? 'satuan';

        $rules = [
            'target_type' => 'required|in_list[satuan,kelas,semua]',
            'tanggal' => 'required|valid_date',
            'jenis_pembayaran' => 'required|string|max_length[100]',
            'jumlah' => 'required|numeric',
            'status' => 'required|in_list[Lunas,Pending,Gagal,Menunggu Verifikasi]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $targetSantriIds = [];

        if ($targetType === 'satuan') {
            $inputSantriId = $this->request->getPost('id_santri');
            // Validasi santri milik tenant aktif
            $cekSantri = $this->santriModel->where('id', $inputSantriId)->where('tenant_id', $this->tenantId)->first();
            if ($cekSantri) {
                $targetSantriIds = [$inputSantriId];
            }
        } elseif ($targetType === 'kelas') {
            $idKelas = $this->request->getPost('id_kelas');
            // Validasi kelas milik tenant aktif
            $cekKelas = $this->kelasModel->where('id', $idKelas)->where('tenant_id', $this->tenantId)->first();
            if ($cekKelas) {
                $santriKelas = $this->santriModel->where('id_kelas', $idKelas)
                    ->where('tenant_id', $this->tenantId)
                    ->where('status_aktif', 'Aktif')
                    ->findAll();
                $targetSantriIds = array_column($santriKelas, 'id');
            }
        } else {
            $semuaSantri = $this->santriModel->where('tenant_id', $this->tenantId)
                ->where('status_aktif', 'Aktif')
                ->findAll();
            $targetSantriIds = array_column($semuaSantri, 'id');
        }

        if (empty($targetSantriIds) || empty($targetSantriIds[0])) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada santri aktif yang ditemukan pada target tersebut.');
        }

        $dataForm = [
            'tenant_id' => $this->tenantId,
            'tanggal' => $this->request->getPost('tanggal'),
            'jenis_pembayaran' => $this->request->getPost('jenis_pembayaran'),
            'jumlah' => $this->request->getPost('jumlah'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $batchData = [];
        foreach ($targetSantriIds as $idSantri) {
            $tmp = $dataForm;
            $tmp['id_santri'] = $idSantri;
            $batchData[] = $tmp;
        }

        $this->pembayaranModel->insertBatch($batchData);

        return redirect()->to(base_url('admin/administrasi'))->with('success', 'Berhasil! Data tagihan ditambahkan ke ' . count($targetSantriIds) . ' santri.');
    }

    public function update($id)
    {
        // Pastikan data pembayaran yang di-update milik tenant aktif
        $pembayaran = $this->pembayaranModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$pembayaran) {
            return redirect()->to(base_url('admin/administrasi'))->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $rules = [
            'id_santri' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'jenis_pembayaran' => 'required|string|max_length[100]',
            'jumlah' => 'required|numeric',
            'status' => 'required|in_list[Lunas,Pending,Gagal,Menunggu Verifikasi]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi santri tujuan baru apakah milik tenant aktif
        $idSantriBaru = $this->request->getPost('id_santri');
        $cekSantri = $this->santriModel->where('id', $idSantriBaru)->where('tenant_id', $this->tenantId)->first();
        if (!$cekSantri) {
            return redirect()->back()->withInput()->with('error', 'Santri yang dipilih tidak valid untuk lembaga ini.');
        }

        $this->pembayaranModel->update($id, [
            'id_santri' => $idSantriBaru,
            'tanggal' => $this->request->getPost('tanggal'),
            'jenis_pembayaran' => $this->request->getPost('jenis_pembayaran'),
            'jumlah' => $this->request->getPost('jumlah'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(base_url('admin/administrasi'))->with('success', 'Data pembayaran berhasil diperbarui!');
    }

    public function verifikasi($id)
    {
        // Pastikan data pembayaran milik tenant aktif
        $pembayaran = $this->pembayaranModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();

        if (!$pembayaran) {
            return redirect()->to(base_url('admin/administrasi'))->with('error', 'Data pembayaran tidak ditemukan!');
        }

        $this->pembayaranModel->update($id, [
            'status' => 'Lunas'
        ]);

        return redirect()->to(base_url('admin/administrasi'))->with('success', 'Pembayaran berhasil diverifikasi menjadi Lunas!');
    }

    public function delete($id)
    {
        // Pastikan data pembayaran milik tenant aktif sebelum dihapus
        $pembayaran = $this->pembayaranModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();

        if (!$pembayaran) {
            return redirect()->to(base_url('admin/administrasi'))->with('error', 'Data pembayaran tidak ditemukan!');
        }

        $this->pembayaranModel->delete($id);

        return redirect()->to(base_url('admin/administrasi'))->with('success', 'Data pembayaran berhasil dihapus!');
    }

    public function exportExcel()
    {
        $selectedMonth = $this->request->getGet('month') ?? date('m');
        $selectedYear = $this->request->getGet('year') ?? date('Y');
        $selectedStatus = $this->request->getGet('status') ?? '';

        // Ambil data sesuai filter dan pastikan dibatasi tenant_id aktif
        $builder = $this->pembayaranModel->getPembayaranWithSantriByTenant($this->tenantId);

        if (!empty($selectedMonth)) {
            $builder->where('MONTH(pembayaran.tanggal)', $selectedMonth);
            $builder->where('YEAR(pembayaran.tanggal)', $selectedYear);
        }

        if (!empty($selectedStatus)) {
            $builder->where('pembayaran.status', $selectedStatus);
        }

        $dataPembayaran = $builder->findAll();

        $filename = "Rekap-Keuangan-Bulan-" . $selectedMonth . "-" . $selectedYear . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background-color: #d1e7dd;">';
        echo '<th>No</th>';
        echo '<th>Nama Santri</th>';
        echo '<th>Kelas</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Jenis Pembayaran</th>';
        echo '<th>Jumlah (Rp)</th>';
        echo '<th>Status</th>';
        echo '<th>Keterangan</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $no = 1;
        foreach ($dataPembayaran as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . $row['nama_santri'] . '</td>';
            echo '<td>' . ($row['nama_kelas'] ?? '-') . '</td>';
            echo '<td>' . $row['tanggal'] . '</td>';
            echo '<td>' . $row['jenis_pembayaran'] . '</td>';
            echo '<td>' . $row['jumlah'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '<td>' . ($row['keterangan'] ?? '-') . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        exit();
    }
}