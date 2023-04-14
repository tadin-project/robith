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

    public function testGetSubKriteriaSuccess()
    {
        $res = $this->asesmenService->getSubKriteria(1);
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetSubKriteriaFailed()
    {
        $res = $this->asesmenService->getSubKriteria("");
        self::assertFalse($res["status"]);
    }
}
