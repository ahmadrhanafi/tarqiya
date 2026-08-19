<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\TenantModel;

class TenantController extends BaseController
{
    protected $tenantModel;

    public function __construct()
    {
        $this->tenantModel = new TenantModel();
    }

    // Menampilkan daftar seluruh tenant
    public function index()
    {
        $data = [
            'title' => 'Data Tenant / Pesantren',
            'icon' => 'fa-solid fa-school',
            'daftar_tenant' => $this->tenantModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('superadmin/tenants/index', $data);
    }

    // Menampilkan form tambah tenant baru
    public function create()
    {
        $data = [
            'title' => 'Tambah Tenant Baru',
            'icon' => 'fa-solid fa-school-circle-plus'
        ];

        return view('superadmin/tenants/create', $data);
    }

    // Proses simpan data tenant baru
    public function store()
    {
        // Validasi input sederhana
        $rules = [
            'nama_lembaga' => 'required|min_length[3]',
            'slug' => 'required|is_unique[tenants.slug]',
            'status_tenant' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tenantModel->save([
            'nama_lembaga' => $this->request->getPost('nama_lembaga'),
            'slug' => $this->request->getPost('slug'),
            'domain_custom' => $this->request->getPost('domain_custom'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp'),
            'status_tenant' => $this->request->getPost('status_tenant'),
        ]);

        return redirect()->to(base_url('superadmin/tenants'))->with('success', 'Tenant berhasil ditambahkan.');
    }

    // Proses hapus tenant
    public function delete($id)
    {
        $tenant = $this->tenantModel->find($id);

        if (!$tenant) {
            return redirect()->to(base_url('superadmin/tenants'))->with('error', 'Tenant tidak ditemukan.');
        }

        $this->tenantModel->delete($id);

        return redirect()->to(base_url('superadmin/tenants'))->with('success', 'Tenant berhasil dihapus.');
    }
}