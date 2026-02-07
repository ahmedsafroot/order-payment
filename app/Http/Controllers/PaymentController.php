<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected PaymentService $paymentService) {}

    public function index(Request $request): JsonResponse
    {
        $orderId  = $request->query('order_id');
        $perPage = (int) $request->query('per_page', 10);
        $result = $this->paymentService->list($orderId, $perPage);
        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }

    public function process(int $orderId, ProcessPaymentRequest $request): JsonResponse
    {

        $result = $this->paymentService->process($orderId, $request->all());
        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);    }



}
