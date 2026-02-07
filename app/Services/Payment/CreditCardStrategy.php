<?php
namespace App\Services\Payment;
use App\Models\Order;
use App\Repositories\Contracts\PaymentStrategyInterface;

class CreditCardStrategy implements  PaymentStrategyInterface {
    public function pay(Order $order, array $data): array {
        return ['status' => 'successful', 'transaction_id' => 'CC_' . uniqid()];
    }
}
