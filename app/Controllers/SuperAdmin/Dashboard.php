<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\TenantModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $tenantModel;
    protected $userModel;

    public function __construct()
    {
        $this->tenantModel = new TenantModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Keamanan: pastikan hanya superadmin
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to(base_url('unauthorized'));
        }

        $data = [
            'title' => 'Dashboard Super Admin Pusat',
            'icon' => 'fa-solid fa-gauge',
            'total_tenant' => $this->tenantModel->countAll(),
            'tenant_aktif' => $this->tenantModel->where('status_tenant', 'Active')->countAllResults(),
            'total_semua_user' => $this->userModel->countAll(),
            'daftar_tenant' => $this->tenantModel->orderBy('created_at', 'DESC')->limit(5)->findAll()
        ];

        return view('superadmin/dashboard', $data);
    }
}