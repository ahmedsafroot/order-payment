<?php
namespace App\Repositories\Contracts;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{

    public function paginate(?int $orderId, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Payment;

    public function update(Payment $payment, array $data): Payment;
}
