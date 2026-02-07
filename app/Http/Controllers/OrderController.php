<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $status  = $request->query('status');
        $perPage = (int) $request->query('per_page', 10);

        $result = $this->orderService->list($status, $perPage);

        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }


    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result  = $this->orderService->create($data);
        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }


    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $data = $request->validated();
        $result  = $this->orderService->update($order,$data);
        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }


    public function destroy(Order $order): JsonResponse
    {
        $result=$this->orderService->delete($order);

        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }



}
