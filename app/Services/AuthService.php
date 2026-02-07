<?php
namespace App\Services;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthService
{
    protected string $guard = 'api';

    public function __construct(protected UserRepositoryInterface $userRepository) {}
    public function register(array $data): array
    {
        try {
            $user = $this->userRepository->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);;
            $accessToken = auth($this->guard)->login($user);
            $refreshToken = auth($this->guard)
                ->setTTL((int) config('jwt.refresh_ttl', 43200))
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);

            return [
                'status' => true,
                'code' => 201,
                'data'=>[
                    'token_type' => 'bearer',
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'user' => new UserResource($user),
                ],
                'message'=>'Registered Successfully'
            ];

        }
        catch (JWTException $e) {
            return ['status' => false, 'code' => 500, 'message' => 'could not create token','data' => null];
        }

    }
    public function login(string $email, string $password): array
    {
        $credentials = ['email' => $email, 'password' => $password];

        try {
            if (!$accessToken = Auth::guard($this->guard)->attempt($credentials)) {
                return ['status' => false, 'code' => 401, 'message' => 'Unauthorized'];
            }

            $user = Auth::guard($this->guard)->user();
            $refreshTTL = (int)config('jwt.refresh_ttl', 43200);
            $refreshToken = Auth::guard($this->guard)
                ->setTTL($refreshTTL)
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);

            return [
                'status' => true,
                'code' => 200,
                'data'=>[
                    'token_type' => 'bearer',
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'user' => new UserResource($user),
                ],
                'message'=>'Logged in Successfully'
            ];
        } catch (JWTException $e) {
            return ['status' => false, 'code' => 500, 'message' => 'could not create token','data' => null];
        }
    }
    public function logout(): array
    {
        try {
            Auth::guard($this->guard)->logout();
            return ['status' => true, 'code' => 200, 'message' => 'logout successfully','data' => []];
        } catch (\Throwable $e) {
            return ['status' => false, 'code' => 500, 'message' => 'failed to logout','data' => null];
        }
    }
    public function refresh(): array
    {
        try {
            Auth::guard($this->guard)->invalidate();
            $user = Auth::guard($this->guard)->user();
            $newAccessToken = Auth::guard($this->guard)->login($user);
            $refreshTTL = (int)config('jwt.refresh_ttl', 43200);
            $newRefreshToken = Auth::guard($this->guard)
                ->setTTL($refreshTTL)
                ->claims(['token_type' => 'refresh'])
                ->fromUser($user);
            return [
                'ok' => true,
                'status' => 200,
                'data'=>[
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                ],
                'message'=>'Refresh Token Successfully'
            ];
        }
        catch (TokenExpiredException $e) {
            return ['status' => false, 'code' => 401, 'message' => 'refresh token expired','data'=> null];
        } catch (\Throwable $e) {
            return ['status' => false, 'code' => 401, 'message' => 'Unauthorized', 'data' => null];
        }
    }

}
