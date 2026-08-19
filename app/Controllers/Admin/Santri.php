<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\KelasModel;
use App\Models\WaliModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Santri extends BaseController
{
    protected $santriModel;
    protected $kelasModel;
    protected $waliModel;
    protected $tenantId;

    public function __construct()
    {
        $this->santriModel = new SantriModel();
        $this->kelasModel = new KelasModel();
        $this->waliModel = new WaliModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $selectedKelas = $this->request->getGet('id_kelas');
        $selectedStatus = $this->request->getGet('status');

        // Modifikasi pencarian santri dengan menyertakan filter tenant_id di dalam model atau builder
        // Pastikan SantriModel Anda mendukung filter tenant_id
        $santriList = $this->santriModel->searchSantriByTenant($this->tenantId, $keyword, $selectedKelas, $selectedStatus);

        $data = [
            'title' => 'Data Santri',
            'icon' => 'fa-solid fa-user-graduate',
            'santri' => $santriList,
            'kelas' => $this->kelasModel->where('tenant_id', $this->tenantId)->findAll(),
            'wali' => $this->waliModel->where('tenant_id', $this->tenantId)->findAll(),
            'keyword' => $keyword,
            'selectedKelas' => $selectedKelas,
            'selectedStatus' => $selectedStatus,
            'role' => session()->get('role') ?? 'admin'
        ];

        return view('admin/data_santri', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'nama_santri' => 'required|min_length[3]',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|valid_date',
                'jenis_kelamin' => 'required|in_list[L,P]',
                'id_kelas' => 'required|numeric',
                'id_wali' => 'required|numeric',
                'foto' => 'uploaded[foto]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]'
            ])
        ) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data santri atau format foto salah (Maks. 2MB, format JPG/JPEG/PNG).');
        }

        // Validasi tambahan: Pastikan kelas dan wali benar-benar milik tenant aktif
        $cekKelas = $this->kelasModel->where('id', $this->request->getVar('id_kelas'))->where('tenant_id', $this->tenantId)->first();
        $cekWali = $this->waliModel->where('id', $this->request->getVar('id_wali'))->where('tenant_id', $this->tenantId)->first();

        if (!$cekKelas || !$cekWali) {
            return redirect()->back()->withInput()->with('error', 'Data kelas atau wali tidak valid untuk lembaga ini.');
        }

        $tahun = date('Y');
        $idKelas = str_pad($this->request->getVar('id_kelas'), 2, '0', STR_PAD_LEFT);

        // Generate NIS unik berdasarkan tenant
        $lastSantri = $this->santriModel
            ->where('tenant_id', $this->tenantId)
            ->like('nis', $tahun . $idKelas, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastSantri) {
            $noUrut = (int) substr($lastSantri['nis'], -3) + 1;
        } else {
            $noUrut = 1;
        }

        $nis = $tahun . $idKelas . str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        // Handle Upload Foto
        $fileFoto = $this->request->getFile('foto');
        $namaFoto = null;

        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $folderTujuan = 'uploads/santri/';
            if (!is_dir($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }
            $fileFoto->move($folderTujuan, $namaFoto);
        }

        $this->santriModel->save([
            'tenant_id' => $this->tenantId,
            'nis' => $nis,
            'nama_santri' => $this->request->getVar('nama_santri'),
            'tempat_lahir' => $this->request->getVar('tempat_lahir'),
            'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'id_kelas' => $this->request->getVar('id_kelas'),
            'id_wali' => $this->request->getVar('id_wali'),
            'status_aktif' => 'Aktif',
            'foto' => $namaFoto,
        ]);

        return redirect()->to(base_url('admin/santri'))->with('success', 'Data santri berhasil ditambahkan! NIS: ' . $nis);
    }

    public function update($id)
    {
        // Pastikan santri yang akan di-update milik tenant aktif
        $santriLama = $this->santriModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$santriLama) {
            return redirect()->to(base_url('admin/santri'))->with('error', 'Data santri tidak ditemukan.');
        }

        $rules = [
            'nama_santri' => 'required|min_length[3]',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'id_kelas' => 'required|numeric',
            'id_wali' => 'required|numeric',
        ];

        $fileFoto = $this->request->getFile('foto');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $rules['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi data santri. Periksa kembali inputan Anda.');
        }

        // Validasi relasi kelas & wali milik tenant aktif
        $cekKelas = $this->kelasModel->where('id', $this->request->getVar('id_kelas'))->where('tenant_id', $this->tenantId)->first();
        $cekWali = $this->waliModel->where('id', $this->request->getVar('id_wali'))->where('tenant_id', $this->tenantId)->first();

        if (!$cekKelas || !$cekWali) {
            return redirect()->back()->withInput()->with('error', 'Data kelas atau wali tidak valid untuk lembaga ini.');
        }

        $nis = $this->request->getVar('nis');
        if (empty($nis)) {
            $nis = $santriLama['nis'] ?? '';
        }

        $namaFoto = $santriLama['foto'];

        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('uploads/santri', $namaFoto);

            if (!empty($santriLama['foto']) && file_exists('uploads/santri/' . $santriLama['foto'])) {
                unlink('uploads/santri/' . $santriLama['foto']);
            }
        }

        $this->santriModel->update($id, [
            'nis' => $nis,
            'nama_santri' => $this->request->getVar('nama_santri'),
            'tempat_lahir' => $this->request->getVar('tempat_lahir'),
            'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'id_kelas' => $this->request->getVar('id_kelas'),
            'id_wali' => $this->request->getVar('id_wali'),
            'status_aktif' => $this->request->getVar('status_aktif') ?? 'Aktif',
            'foto' => $namaFoto,
        ]);

        return redirect()->to(base_url('admin/santri'))->with('success', 'Data santri berhasil diperbarui!');
    }

    public function delete($id)
    {
        $santri = $this->santriModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$santri) {
            return redirect()->to(base_url('admin/santri'))->with('error', 'Data santri tidak ditemukan.');
        }

        if (!empty($santri['foto']) && file_exists('uploads/santri/' . $santri['foto'])) {
            unlink('uploads/santri/' . $santri['foto']);
        }

        $this->santriModel->delete($id);
        return redirect()->to(base_url('admin/santri'))->with('success', 'Data santri berhasil dihapus!');
    }

    public function detail($id)
    {
        $santri = $this->santriModel->select('santri.*, kelas.nama_kelas, wali.nama_wali, wali.no_hp as no_hp_wali, wali.alamat as alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('wali', 'wali.id = santri.id_wali', 'left')
            ->where('santri.id', $id)
            ->where('santri.tenant_id', $this->tenantId)
            ->first();

        if (!$santri) {
            return redirect()->to(base_url('admin/santri'))->with('error', 'Data santri tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Santri',
            'santri' => $santri
        ];

        return view('admin/santri-detail', $data);
    }

    public function cetakKartu($id)
    {
        $santri = $this->santriModel->select('santri.*, kelas.nama_kelas, wali.no_hp as no_hp_wali, wali.alamat as alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('wali', 'wali.id = santri.id_wali', 'left')
            ->where('santri.id', $id)
            ->where('santri.tenant_id', $this->tenantId)
            ->first();

        if (!$santri)
            return redirect()->back();

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

        $html = view('admin/cetak_ekartu_santri', [
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