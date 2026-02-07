<?php
namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{

    public function paginate(?int $orderId, int $perPage = 10): LengthAwarePaginator
    {
        return Payment::query()
            ->when($orderId, fn($q) => $q->where('order_id', $orderId))
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest('id')
            ->paginate($perPage);
    }


    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);
        return $payment;
    }
}
