<?php

namespace App\Controllers\Wali;

use App\Controllers\BaseController;

class Esertifikat extends BaseController
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
        $data = [
            'title' => 'E-Sertifikat Santri',
            'icon' => 'fa-solid fa-certificate'
        ];

        return view('wali/esertifikat', $data);
    }
}