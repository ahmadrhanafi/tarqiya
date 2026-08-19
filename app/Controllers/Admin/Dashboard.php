<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\SantriModel;
use App\Models\HafalanModel;

class Dashboard extends BaseController
{
    protected $guruModel;
    protected $santriModel;
    protected $hafalanModel;
    protected $tenantId;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->santriModel = new SantriModel();
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
        $periode = $this->request->getGet('periode') ?? 'bulan_ini';

        // Pastikan method grafik di model mendukung filter tenant_id (misal: getGrafikSetoranByTenant)
        $grafikData = $this->hafalanModel->getGrafikSetoranByTenant($this->tenantId, $periode);

        $data = [
            'title' => 'Dashboard Admin',
            'icon' => 'fa-solid fa-gauge',
            'periode' => $periode,
            'total_ustadz' => $this->guruModel->where('tenant_id', $this->tenantId)->where('status_aktif', 'Aktif')->countAllResults(),
            'total_santri' => $this->santriModel->where('tenant_id', $this->tenantId)->countAllResults(),
            'total_setoran' => $this->hafalanModel->where('tenant_id', $this->tenantId)->countAllResults(),
            'total_khatam' => $this->santriModel->where('tenant_id', $this->tenantId)->where('status_aktif', 'lulus')->countAllResults(),
            'grafik_data' => $grafikData,
        ];

        return view('admin/dashboard', $data);
    }
}