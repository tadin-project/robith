<?php

namespace Tests\Feature\Services;

use App\Services\AsesmenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AsesmenServiceTest extends TestCase
{
    private AsesmenService $asesmenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->asesmenService = $this->app->make(AsesmenService::class);
    }

    public function testGetKriteriaSuccess()
    {
        $res = $this->asesmenService->getKriteria();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetTenantByUserSuccess()
    {
        $res = $this->asesmenService->getTenantByUser(3);
        self::assertTrue($res["status"]);
        self::assertEquals(1, $res["data"]->tenant_id);
        self::assertEquals("Tenant User 01", $res["data"]->tenant_nama);
    }

    public function testGetTenantByUserNotFound()
    {
        $res = $this->asesmenService->getTenantByUser(0);
        self::assertTrue($res["status"]);
        self::assertEquals("", $res["msg"]);
        self::assertEquals(0, $res["data"]);
    }
}
