<?php

namespace App\Repositories\Contracts;
use App\Models\Order;
use App\Models\PurchasedItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

interface OrderRepositoryInterface
{

    public function paginate(?string $status, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data, array $items): Order;

    public function update(Order $order, array $data, ?array $items = null): Order;

    public function delete(Order $order): void;

    public function hasAnyPayments(Order $order): bool;

    public function findById(int $orderId): Order;

}
