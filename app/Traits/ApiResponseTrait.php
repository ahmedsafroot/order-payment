<?php

namespace App\Traits;


use Illuminate\Http\JsonResponse;


trait ApiResponseTrait
{

    protected function successResponse(array $data = [], string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(array $data = null,string $message = 'error', int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
