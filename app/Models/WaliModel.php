<?php

namespace App\Models;

use CodeIgniter\Model;

class WaliModel extends Model
{
    protected $table = 'wali';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tenant_id', 'nama_wali', 'no_hp', 'alamat'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getWaliWithSantri()
    {
        $db = \Config\Database::connect();

        $wali = $this->findAll();

        foreach ($wali as &$w) {
            $w['santri'] = $db->table('santri')
                ->select('santri.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
                ->where('santri.id_wali', $w['id'])
                ->get()
                ->getResultArray();

            $user = $db->table('users')
                ->select('foto')
                ->where('ref_id', $w['id'])
                ->where('role', 'wali')
                ->get()
                ->getRowArray();

            $w['foto'] = $user['foto'] ?? null;
        }

        return $wali;
    }
}
