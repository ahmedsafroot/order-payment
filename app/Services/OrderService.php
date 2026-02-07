<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Throwable;

class OrderService
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    public function list(?string $status, int $perPage = 10): array
    {
        try {
            $paginator = $this->orderRepository->paginate($status, $perPage);

            $data = [
                'orders'      => OrderResource::collection($paginator->items()),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
                'filters' => [
                    'status' => $status,
                ],
            ];

            return [
                'status'  => true,
                'code'    => 200,
                'data'    => $data,
                'message' => 'Orders fetched successfully',
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to fetch orders',
            ];
        }
    }

    public function create(array $data): array
    {
        try {
            $items = $data['items'];
            $totalPrice = $this->calculateTotal($items);
            $newOrder = [
                'user_id' => auth()->id(),
                'total_price'   => $totalPrice,
                'status'=>'pending'
            ];
            $order = $this->orderRepository->create($newOrder, $items);
            return [
                'status'  => true,
                'code'    => 201,
                'data'    => ['order'=>new OrderResource($order)],
                'message' => 'Order created successfully',
            ];
        }
        catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to create order',
            ];
        }
    }

    public function update(Order $order, array $data): array
    {
        try {
            $items = $data['items'] ?? null;
            $updatedOrder = [
                'status' => $data['status'] ?? $order->status,
                'total_price'  => $items ? $this->calculateTotal($items) : $order->total_price,
            ];

            $order = $this->orderRepository->update($order, $updatedOrder, $items);

            return [
                'status'  => true,
                'code'    => 200,
                'data'    => ['order'=>new OrderResource($order)],
                'message' => 'Order updated successfully',
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to update order',
            ];
        }
    }

    public function delete(Order $order): array
    {
        try {
            if($order->user_id !== auth()->id()){
                return [
                    'status'  => false,
                    'code'    => 403,
                    'data'    => null,
                    'message' => 'You are not allowed to perform this action.',
                ];
            }
            if ($this->orderRepository->hasAnyPayments($order)) {
                return [
                    'status'  => false,
                    'code'    => 422,
                    'data'    => null,
                    'message' => 'Order cannot be deleted because it has payments',
                ];
            }

            $this->orderRepository->delete($order);

            return [
                'status'  => true,
                'code'    => 200,
                'data'    => [],
                'message' => 'Order deleted successfully',
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to delete order',
            ];
        }
    }

    private function calculateTotal(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += ((int)$item['quantity'] * (float)$item['price']);
        }
        return round($total, 3);
    }
}
