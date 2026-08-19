<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\GuruModel;

class Kelas extends BaseController
{
    protected $kelasModel;
    protected $guruModel;
    protected $tenantId;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('kelas');
        $builder->select('kelas.*, guru.nama_guru, guru.nip, guru.status_aktif, (SELECT COUNT(id) FROM santri WHERE santri.id_kelas = kelas.id AND santri.tenant_id = "' . $this->tenantId . '" AND santri.status_aktif = "Aktif") as total_santri');
        $builder->join('guru', 'guru.id_kelas_diampu = kelas.id AND guru.tenant_id = "' . $this->tenantId . '"', 'left');

        // Filter berdasarkan tenant_id
        $builder->where('kelas.tenant_id', $this->tenantId);

        $kelasWithTotal = $builder->get()->getResultArray();

        // Ambil guru yang AKTIF, sesuai tenant, dan (belum punya kelas atau id kelas kosong)
        $guruList = $this->guruModel->where('tenant_id', $this->tenantId)
            ->where('status_aktif', 'Aktif')
            ->groupStart()
            ->where('id_kelas_diampu IS NULL', null, false)
            ->orWhere('id_kelas_diampu', '')
            ->groupEnd()
            ->findAll();

        $data = [
            'title' => 'Data Kelas',
            'icon' => 'fa-solid fa-school',
            'kelas' => $kelasWithTotal,
            'guruList' => $guruList,
            'role' => session()->get('role') ?? 'admin'
        ];

        return view('admin/kelas', $data);
    }

    public function store()
    {
        // Validasi keunikan nama kelas dibatasi dalam tenant yang sama
        if (
            !$this->validate([
                'nama_kelas' => 'required|min_length[3]|is_unique[kelas.nama_kelas,tenant_id,' . $this->tenantId . ']'
            ])
        ) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan data kelas dengan menyertakan tenant_id
        $this->kelasModel->save([
            'tenant_id' => $this->tenantId,
            'nama_kelas' => $this->request->getVar('nama_kelas')
        ]);

        $kelasId = $this->kelasModel->insertID();
        $idGuru = $this->request->getVar('id_guru');

        if (!empty($idGuru)) {
            // Pastikan guru yang dipilih juga milik tenant yang sama
            $guru = $this->guruModel->where('id', $idGuru)->where('tenant_id', $this->tenantId)->first();
            if ($guru) {
                $this->guruModel->update($idGuru, [
                    'id_kelas_diampu' => $kelasId
                ]);
            }
        }

        return redirect()->to(base_url('admin/kelas'))->with('success', 'Data kelas berhasil ditambahkan!');
    }

    public function update($id)
    {
        // Pastikan kelas yang akan diedit benar-benar milik tenant aktif
        $kelas = $this->kelasModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Data kelas tidak ditemukan.');
        }

        if (
            !$this->validate([
                'nama_kelas' => 'required|min_length[3]|is_unique[kelas.nama_kelas,id,' . $id . ',tenant_id,' . $this->tenantId . ']'
            ])
        ) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->update($id, [
            'nama_kelas' => $this->request->getVar('nama_kelas')
        ]);

        $idGuruBaru = $this->request->getVar('id_guru');

        // Lepas relasi kelas dari guru lama yang berada di tenant ini
        $guruLama = $this->guruModel->where('id_kelas_diampu', $id)->where('tenant_id', $this->tenantId)->findAll();
        foreach ($guruLama as $g) {
            $this->guruModel->update($g['id'], ['id_kelas_diampu' => null]);
        }

        if (!empty($idGuruBaru)) {
            // Validasi guru baru milik tenant aktif
            $guruBaru = $this->guruModel->where('id', $idGuruBaru)->where('tenant_id', $this->tenantId)->first();
            if ($guruBaru) {
                $this->guruModel->update($idGuruBaru, [
                    'id_kelas_diampu' => $id
                ]);
            }
        }

        return redirect()->to(base_url('admin/kelas'))->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Validasi kepemilikan kelas berdasarkan tenant_id sebelum menghapus
        $kelas = $this->kelasModel->where('id', $id)->where('tenant_id', $this->tenantId)->first();
        if (!$kelas) {
            return redirect()->to(base_url('admin/kelas'))->with('error', 'Data kelas tidak ditemukan.');
        }

        // Kosongkan relasi kelas pada guru terkait terlebih dahulu
        $guruLama = $this->guruModel->where('id_kelas_diampu', $id)->where('tenant_id', $this->tenantId)->findAll();
        foreach ($guruLama as $g) {
            $this->guruModel->update($g['id'], ['id_kelas_diampu' => null]);
        }

        $this->kelasModel->delete($id);

        return redirect()->to(base_url('admin/kelas'))->with('success', 'Data kelas berhasil dihapus!');
    }
}