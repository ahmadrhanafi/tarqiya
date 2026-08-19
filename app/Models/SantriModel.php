<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    protected $table = 'santri';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Field yang diizinkan untuk di-insert/update
    protected $allowedFields = [
        'tenant_id',
        'foto',
        'nis',
        'nama_santri',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'id_kelas',
        'id_wali',
        'status_aktif'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Fungsi untuk mengambil data santri beserta relasi berdasarkan tenant_id
     */
    public function getSantriWithRelationsByTenant($tenantId, $id = null)
    {
        $builder = $this->select('santri.*, kelas.nama_kelas, wali.nama_wali, wali.no_hp as no_hp_wali, wali.alamat AS alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('wali', 'wali.id = santri.id_wali', 'left')
            ->where('santri.tenant_id', $tenantId);

        if ($id === null) {
            return $builder->findAll();
        }

        return $builder->where('santri.id', $id)->first();
    }

    /**
     * Fungsi pencarian data santri dengan batasan tenant_id
     */
    public function searchSantriByTenant($tenantId, $keyword = null, $idKelas = null, $status = null)
    {
        $builder = $this->select('santri.*, kelas.nama_kelas, wali.nama_wali, wali.no_hp as no_hp_wali, wali.alamat as alamat_wali')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('wali', 'wali.id = santri.id_wali', 'left')
            ->where('santri.tenant_id', $tenantId);

        if (!empty($idKelas)) {
            $builder->where('santri.id_kelas', $idKelas);
        } else {
            // Jika yang login adalah guru dan tidak membawa idKelas, kunci ke 0 agar aman
            if (session()->get('role') == 'guru') {
                $builder->where('santri.id_kelas', 0);
            }
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('santri.nama_santri', $keyword)
                ->orLike('santri.nis', $keyword)
                ->orLike('wali.nama_wali', $keyword)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('santri.status_aktif', $status);
        }

        return $builder->findAll();
    }

    /**
     * Fungsi mengambil data santri berdasarkan kelas dan tenant_id
     */
    public function getSantriByKelasByTenant($tenantId, $idKelas)
    {
        return $this->select('santri.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'inner')
            ->where('santri.tenant_id', $tenantId)
            ->where('santri.id_kelas', $idKelas)
            ->findAll();
    }
}