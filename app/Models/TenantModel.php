<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Kolom yang diizinkan untuk diisi/diubah
    protected $allowedFields = [
        'nama_lembaga',
        'slug',
        'domain_custom',
        'alamat',
        'no_telp',
        'logo',
        'status_tenant'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Mengambil data tenant berdasarkan slug atau domain custom untuk keperluan routing/identifikasi
     */
    public function getTenantByIdentifier($identifier)
    {
        return $this->groupStart()
            ->where('slug', $identifier)
            ->orWhere('domain_custom', $identifier)
            ->groupEnd()
            ->where('status_tenant', 'Aktif')
            ->first();
    }
}