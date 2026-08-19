<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Kolom yang diizinkan untuk di-insert/update
    protected $allowedFields = ['tenant_id', 'foto', 'name', 'username', 'password', 'role', 'ref_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Mengambil data user berdasarkan username dan tenant_id (berguna saat proses login multi-tenant)
     */
    public function getUserByUsernameAndTenant($username, $tenantId)
    {
        return $this->where('username', $username)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    /**
     * Mengambil data user berdasarkan relasi referensi (ref_id) dan role di tenant tertentu
     */
    public function getUserByRefAndRoleByTenant($refId, $role, $tenantId)
    {
        return $this->where('ref_id', $refId)
            ->where('role', $role)
            ->where('tenant_id', $tenantId)
            ->first();
    }
}