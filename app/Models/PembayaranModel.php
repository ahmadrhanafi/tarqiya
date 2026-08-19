<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'tenant_id',
        'id_santri',
        'tanggal',
        'jenis_pembayaran',
        'jumlah',
        'status',
        'keterangan',
        'created_at',
        'updated_at',
        'bukti_pembayaran',
        'bank_tujuan',
        'tanggal_konfirmasi'
    ];

    /**
     * Method untuk mengambil data pembayaran beserta relasi ke tabel santri, kelas, dan wali berdasarkan tenant_id
     */
    public function getPembayaranWithSantriByTenant($tenantId)
    {
        return $this->select('pembayaran.*, santri.nama_santri, santri.foto as foto_santri, kelas.nama_kelas, wali.nama_wali, wali.no_hp as no_hp_wali')
            ->join('santri', 'santri.id = pembayaran.id_santri', 'left')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left')
            ->join('wali', 'wali.id = santri.id_wali', 'left')
            ->where('pembayaran.tenant_id', $tenantId)
            ->orderBy('pembayaran.created_at', 'DESC');
    }
}