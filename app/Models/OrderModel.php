<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['order_date', 'status', 'total_amount'];

    /**
     * Retrieves full order history with associated items and split payment records.
     */
    public function getOrdersFullDetails(int $limit = 20, int $offset = 0): array
    {
        $orders = $this->select('orders.*')
                       ->orderBy('orders.id', 'ASC')
                       ->findAll($limit, $offset);

        if (empty($orders)) {
            return [];
        }

        $orderIds = array_column($orders, 'id');

        $db = \Config\Database::connect();

        // 1. Fetch Order Items
        $items = $db->table('order_items')
                    ->select('order_items.*, items.name as item_name')
                    ->join('items', 'items.id = order_items.item_id', 'left')
                    ->whereIn('order_items.order_id', $orderIds)
                    ->get()
                    ->getResultArray();

        // 2. Fetch Payments
        $payments = $db->table('payments')
                       ->whereIn('order_id', $orderIds)
                       ->get()
                       ->getResultArray();

        // Group items and payments by order_id
        $itemsByOrder = [];
        foreach ($items as $item) {
            $itemsByOrder[$item['order_id']][] = [
                'item_id'    => (int)$item['item_id'],
                'item_name'  => $item['item_name'],
                'size'       => $item['size'],
                'unit_price' => (float)$item['unit_price'],
                'quantity'   => (int)$item['qty'],
                'subtotal'   => (float)$item['subtotal']
            ];
        }

        $paymentsByOrder = [];
        foreach ($payments as $payment) {
            $paymentsByOrder[$payment['order_id']][] = [
                'payment_id'     => (int)$payment['id'],
                'payment_date'   => $payment['payment_date'],
                'amount_due'     => (float)$payment['amount_due'],
                'tips'           => (float)$payment['tips'],
                'discount'       => (float)$payment['discount'],
                'total_paid'     => (float)$payment['total_paid'],
                'payment_type'   => $payment['payment_type'],
                'payment_status' => $payment['payment_status']
            ];
        }

        // Assemble combined response
        $result = [];
        foreach ($orders as $order) {
            $oId = $order['id'];
            $result[] = [
                'order_id'     => (int)$order['id'],
                'order_date'   => $order['order_date'],
                'order_status' => $order['status'],
                'total_amount' => (float)$order['total_amount'],
                'items'        => $itemsByOrder[$oId] ?? [],
                'payments'     => $paymentsByOrder[$oId] ?? []
            ];
        }

        return $result;
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


}
