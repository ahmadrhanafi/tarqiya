<?php

namespace App\Models;

use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

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
}