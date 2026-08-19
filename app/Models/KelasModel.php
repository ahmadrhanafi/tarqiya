<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tenant_id', 'nama_kelas'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Mengambil daftar kelas berdasarkan tenant_id aktif
     */
    public function getKelasByTenant($tenantId)
    {
        return $this->where('tenant_id', $tenantId)->findAll();
    }
}