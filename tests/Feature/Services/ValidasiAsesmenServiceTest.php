<?php

namespace Tests\Feature\Services;

use App\Services\ValidasiAsesmenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidasiAsesmenServiceTest extends TestCase
{
    private ValidasiAsesmenService $validasiAsesmenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->validasiAsesmenService = $this->app->make(ValidasiAsesmenService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->validasiAsesmenService->getTotal("");
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, $res["total"]);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->validasiAsesmenService->getTotal(" AND lower(t.tenant_nama) like lower('%as%') ");
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, $res["total"]);
    }

    public function testGetTotalByParamFailed()
    {
        $res = $this->validasiAsesmenService->getTotal(" AND lower(t.tenant_nama) like lower('%as ");
        self::assertFalse($res["status"]);
    }

    public function testGetDataSuccess()
    {
        $res = $this->validasiAsesmenService->getData("", "", "", []);
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->validasiAsesmenService->getData(" AND lower(t.tenant_nama) like lower('%as%') ", " order by a.created_at ", " limit 10 offset 0 ", ["a.as_id", "a.created_at", "t.tenant_nama"]);
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetDataByParamFailed()
    {
        $res = $this->validasiAsesmenService->getData(" AND lower(t.tenant_nama) like lower('%as ", "", "", []);
        self::assertFalse($res["status"]);
    }

    public function testGetKategoriUsahaSuccess()
    {
        $res = $this->validasiAsesmenService->getKategoriUsaha();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetKriteriaSuccess()
    {
        $res = $this->validasiAsesmenService->getKriteria();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }
}
