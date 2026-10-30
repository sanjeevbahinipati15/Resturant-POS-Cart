<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;

class Orders extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $limit  = (int) ($this->request->getGet('limit') ?? 20);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        // Max limit cap to safeguard server performance
        $limit = min(max($limit, 1), 100);

        $orderModel = new OrderModel();
        $data = $orderModel->getOrdersFullDetails($limit, $offset);

        return $this->respond([
            'status'  => 200,
            'message' => 'Orders retrieved successfully',
            'count'   => count($data),
            'data'    => $data
        ]);
    }
}