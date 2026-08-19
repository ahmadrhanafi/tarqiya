<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SantriModel;

class Esertifikat extends BaseController
{
    protected $santriModel;
    protected $tenantId;

    public function __construct()
    {
        $this->santriModel = new SantriModel();

        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session yang sedang aktif
        $this->tenantId = session()->get('tenant_id');
    }

    public function index()
    {
        // Pastikan method di SantriModel mendukung filter tenant_id (misal: getSantriWithRelationsByTenant)
        $data['sertifikat'] = $this->santriModel->getSantriWithRelationsByTenant($this->tenantId);

        return view('admin/esertifikat', $data);
    }
}