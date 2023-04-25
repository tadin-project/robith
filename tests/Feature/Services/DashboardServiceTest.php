<?php

namespace Tests\Feature\Services;

use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    private DashboardService $dashboardService;

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = $this->app->make(DashboardService::class);
    }

    public function test_example()
    {
        self::assertTrue(true);
    }
}
