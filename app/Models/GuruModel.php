<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tenant_id', 'nip', 'nama_guru', 'jenis_kelamin', 'id_kelas_diampu', 'status_aktif', 'no_hp'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getGuruWithUserByTenant($tenantId)
    {
        return $this->select('guru.*, users.foto as foto_user, users.id as user_id')
            ->join('users', "users.ref_id = guru.id AND users.role = 'guru'", 'left')
            ->where('guru.tenant_id', $tenantId)
            ->findAll();
    }

    public function getGuruWithKelasByTenant($tenantId)
    {
        return $this->select('guru.*, kelas.nama_kelas, users.foto')
            ->join('kelas', 'kelas.id = guru.id_kelas_diampu', 'left')
            ->join('users', 'users.ref_id = guru.id AND users.role = "guru"', 'left')
            ->where('guru.tenant_id', $tenantId)
            ->findAll();
    }
}