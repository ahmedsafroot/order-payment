<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'created_at'     => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
