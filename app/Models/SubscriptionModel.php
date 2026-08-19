<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table = 'subscriptions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tenant_id', 'package_id', 'tanggal_mulai', 'tanggal_berakhir', 'status', 'bukti_bayar'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Fungsi untuk mengecek langganan aktif suatu tenant
    public function getActiveSubscription($tenantId)
    {
        return $this->select('subscriptions.*, packages.nama_paket, packages.max_santri')
            ->join('packages', 'packages.id = subscriptions.package_id')
            ->where('subscriptions.tenant_id', $tenantId)
            ->where('subscriptions.status', 'Active')
            ->where('subscriptions.tanggal_berakhir >=', date('Y-m-d H:i:s'))
            ->first();
    }
}