# Order & Payment Management API

Order Payment Task

***

## Requirements

*   PHP 8.1+
*   Composer
*   Laravel 12
*   MySQL (or SQLite for testing)
*   JWT Auth package (`tymon/jwt-auth`)

***

## Installation & Setup

1.  Clone the repository:
    ```bash
    git clone https://github.com/ahmedsafroot/order-payment.git
    cd order-payment
    ```

2.  Install dependencies:
    ```bash
    composer install
    ```

3.  Create the `.env` file:
    ```bash
    cp .env.example .env
    ```

4.  Update database credentials in `.env`.

5.  Generate app key:
    ```bash
    php artisan key:generate
    ```

6.  Generate JWT secret:
    ```bash
    php artisan jwt:secret
    ```

7.  Run migrations:
    ```bash
    php artisan migrate
    ```

8.  Start the server:
    ```bash
    php artisan serve
    ```

***

## Authentication

The API uses JWT for authentication.  
Available endpoints:

*   `POST /api/auth/register`
*   `POST /api/auth/login`
*   `POST /api/auth/logout`
*   `POST /api/auth/refresh`

After login, include the token in the header:

    Authorization: Bearer <token>

Password rules include uppercase, lowercase, number, special character, and length requirements.

***

## Orders

Order endpoints:

*   `GET /api/orders`
*   `POST /api/orders`
*   `PUT /api/orders/{id}`
*   `DELETE /api/orders/{id}`


### Example: Create Order

```json
{
  "items": [
    { "product_name": "Keyboard", "quantity": 2, "price": 450 },
    { "product_name": "Mouse", "quantity": 1, "price": 250 }
  ]
}
```

***

## Payments

*   `GET /api/payments` (lists all payments for authenticated user)
*   `GET /api/orders/{orderId}/payments`
*   `GET /api/orders/{orderId}/payments/{paymentId}`
*   `POST /api/orders/{orderId}/payments`

Notes:

*   Payments can **only** be processed when the order status is **confirmed**.
*   Supported payment methods:
    *   `credit_card`
    *   `paypal`

### Example: Process Payment

```json
{
  "payment_method": "credit_card",
  "details": {
    "card_number": "4111111111111111",
    "cvv": "123"
  }
}
```

***

## Extending Payment Gateways

Gateways are defined in `config/payments.php`.

To add a new gateway:

1.  Create a class implementing `PaymentStrategyInterface`.
2.  Register it in the config:
    ```php
    'vodafone_cash' => \App\Services\Payment\VodafoneCashStrategy::class
    ```
3.  That’s it — no changes needed in the service.

***

## Testing

A separate environment file is used for automated tests:

Create `.env.testing`:

    APP_ENV=testing
    DB_CONNECTION=sqlite
    DB_DATABASE=:memory:
    JWT_SECRET=testing_secret_key

Run the tests:

```bash
php artisan test
```

***

## Postman Collection

A Postman collection named ***Order Payment.postman_collection.json*** is included in the project for testing all endpoints manually.

***

