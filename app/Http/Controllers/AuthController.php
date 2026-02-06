<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(RegisterRequest $request) : JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            $accessToken = auth('api')->login($user);
            $refreshToken = auth('api')
                ->setTTL((int) config('jwt.refresh_ttl', 43200))
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);

        } catch (JWTException $e) {
            return $this->errorResponse(null,'could not create token',500);
        }

        return $this->successResponse([
            'token_type'         => 'bearer',
            'access_token'       => $accessToken,
            'refresh_token'      => $refreshToken,
            'user'               => $user,
        ], 'registered successfully', 201);

    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$accessToken = auth('api')->attempt($credentials)) {
                return $this->errorResponse(null,'Unauthorized',401);
            }

            $user = auth('api')->user();

            $refreshTTL = (int) config('jwt.refresh_ttl', 43200);
            $refreshToken = auth('api')
                ->setTTL($refreshTTL)
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);

        } catch (JWTException $e) {
            return $this->errorResponse(null,'could not create token',500);
        }

        return $this->successResponse([
            'token_type'    => 'bearer',
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user'          => $user,
        ], 'logged in successfully');
    }

    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();
            return $this->successResponse([],'logout successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(null,'failed to logout',500);
        }
    }

    public function refresh_token(): JsonResponse
    {
        try {
            auth('api')->invalidate();
            $user = auth('api')->user();
            $newAccessToken = auth('api')->login($user);
            $newRefreshToken = auth('api')
                ->setTTL((int) config('jwt.refresh_ttl', 43200))
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);
            return $this->successResponse([
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ],'new token generated');

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->errorResponse(null,'Refresh token expired',401);
        } catch (\Exception $e) {
            return $this->errorResponse(null,'Unauthorized',401);
        }
    }
}
