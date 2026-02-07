<?php


use App\Services\Payment\CreditCardStrategy;
use App\Services\Payment\PayPalStrategy;

return [
    'gateways' => [
        'credit_card' => CreditCardStrategy::class,
        'paypal'      => PayPalStrategy::class,
        // add new methods here later (e.g., 'vodafone_cash' => VodafoneCashStrategy::class)
    ],
];
