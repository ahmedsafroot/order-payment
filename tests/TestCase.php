<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function actingAsApi(User $user = null, array $headers = []): TestCase
    {
        $user ??= User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $headers = array_merge([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ], $headers);

        return $this->withHeaders($headers);
    }
}
