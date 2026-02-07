<?php

namespace App\Repositories\Contracts;

use App\Models\Order;

interface PaymentStrategyInterface
{
    public function pay(Order $order, array $data): array;
}
