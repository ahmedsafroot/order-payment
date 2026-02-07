<?php
namespace App\Repositories;
use App\Models\Order;
use App\Models\PurchasedItem;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{

    public function paginate(?string $status, int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->with('items')
            ->orderByDesc('id')
            ->paginate($perPage);
    }


    public function create(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            $order = Order::create($data);
            $items_array=[];
            foreach ($items as $item){
                $items_array[]=[
                    'order_id'     => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity'          => $item['quantity'],
                    'price'        => $item['price'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            PurchasedItem::insert($items_array);
            return $order->load('items');
        });
    }

    public function update(Order $order, array $data, ?array $items = null): Order
    {
        return DB::transaction(function () use ($order, $data, $items) {
            $order->update($data);

            if (is_array($items)) {
                $order->items()->delete();

                $items_array=[];
                foreach ($items as $item){
                    $items_array[]=[
                        'order_id'     => $order->id,
                        'product_name' => $item['product_name'],
                        'quantity'          => $item['quantity'],
                        'price'        => $item['price'],
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
                PurchasedItem::insert($items_array);
            }
            return $order->load('items');
        });
    }

    public function delete(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->delete();
        });
    }

    public function hasAnyPayments(Order $order): bool
    {
        return $order->payments()->exists();
    }

}
