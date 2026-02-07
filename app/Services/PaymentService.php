<?php

namespace App\Services;

use App\Http\Resources\PaymentResource;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PaymentStrategyInterface;
use App\Repositories\OrderRepository;
use App\Services\Payment\CreditCardStrategy;
use App\Services\Payment\PayPalStrategy;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private OrderRepository $orderRepository
    ) {}

    protected array $gateways = [
        'credit_card' => CreditCardStrategy::class,
        'paypal'      => PayPalStrategy::class,
    ];

    public function list(?int $orderId, int $perPage = 10): array
    {
        try {
            $paginator = $this->paymentRepository->paginate($orderId, $perPage);
            return [
                'status'  => true,
                'code'    => 200,
                'data'    => [
                    'items'      => PaymentResource::collection($paginator->items()),
                    'pagination' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page'     => $paginator->perPage(),
                        'total'        => $paginator->total(),
                        'last_page'    => $paginator->lastPage(),
                    ],
                ],
                'message' => 'Payments fetched successfully',
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to fetch payments',
            ];
        }
    }

    public function process(int $orderId, array $data): array
    {
        try {
            $payment_method  = $data['payment_method'] ?? null;
            $details = $data['details'] ?? [];
            $order=$this->orderRepository->findById($orderId);

            if($order->user_id !== auth()->id()){
                return [
                    'status'  => false,
                    'code'    => 403,
                    'data'    => null,
                    'message' => 'You are not allowed to perform this action.',
                ];
            }

            if ($order->status !== 'confirmed') {
                return [
                    'status' => false,
                    'code' => 422,
                    'data' => null,
                    'message' => 'Payments can only be processed for confirmed orders',
                ];
            }

            $payment = $this->paymentRepository->create([
                    'order_id'       => $orderId,
                    'payment_method'         => $payment_method,
                    'status'         => 'pending',
                    'transaction_id' => null,
            ]);

            $gateway = $this->resolveGateway($payment_method);
            $result  = $gateway->pay($order, $details);
            $status  = $result['status'];
            $transactionId  = $result['transaction_id'];

            $payment = $this->paymentRepository->update($payment, [
                    'status'         => $status,
                    'transaction_id' => $transactionId,
            ]);

            $message = $status === 'successful' ? 'Payment done successfully' : 'Payment failed';

            return [
                'status'  => $status === 'successful',
                'code'    => $status === 'successful' ? 201 : 400,
                'data'    => ['payment'=>new PaymentResource($payment)],
                'message' => $message,
            ];
        } catch (Throwable $e) {
            return [
                'status'  => false,
                'code'    => 500,
                'data'    => null,
                'message' => 'Failed to process payment',
            ];
        }
    }

    protected function resolveGateway(string $payment_method): PaymentStrategyInterface
    {
        $gatewayClass = $this->gateways[$payment_method];

        return app($gatewayClass);
    }
}

