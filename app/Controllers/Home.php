<?php

namespace App\Controllers;

class Home extends BaseController
{

    private array $items = [
        1 => ['id' => 1, 'name' => 'Item 1', 'price' => 10.00],
        2 => ['id' => 2, 'name' => 'Item 2', 'price' => 7.50],
        3 => ['id' => 3, 'name' => 'Item 3', 'price' => 5.00],
        4 => ['id' => 4, 'name' => 'Item 4', 'price' => 2.50],
        5 => ['id' => 5, 'name' => 'Item 5', 'price' => 3.00],
    ];

    public function index(): string
    {
        return view('cart_view', ['available_items' => $this->items]);
    }
}
