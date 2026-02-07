<?php
namespace App\Services\Payment;
use App\Models\Order;
use App\Repositories\Contracts\PaymentStrategyInterface;

class PayPalStrategy implements PaymentStrategyInterface {
    public function pay(Order $order, array $data): array {
        return ['status' => 'successful', 'transaction_id' => 'PP_' . uniqid()];
    }
}
