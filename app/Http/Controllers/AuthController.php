<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function __construct(protected AuthService $authService) {}
    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $result  = $this->authService->register($data);
        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $result  = $this->authService->login($data['email'], $data['password']);

        if (!$result['status']) {
            return $this->errorResponse($result['data'], $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }

    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();
        if (!$result['status']) {
            return $this->errorResponse(null, $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);
    }

    public function refresh_token(): JsonResponse
    {
        $result = $this->authService->refresh();
        if (!$result['status']) {
            return $this->errorResponse(null, $result['message'], $result['code']);
        }
        return $this->successResponse($result['data'], $result['message'],$result['code']);

    }
}
