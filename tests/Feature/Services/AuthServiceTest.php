<?php

namespace Tests\Feature\Services;

use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    public function setUp(): void
    {
        parent::setUp();
        $this->authService = $this->app->make(AuthService::class);
    }

    public function testGetKuSuccess()
    {
        $res = $this->authService->getKu();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }
}
