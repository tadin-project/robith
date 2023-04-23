<?php

namespace Tests\Feature\Services;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    private TenantService $tenantService;

    public function setUp(): void
    {
        parent::setUp();
        $this->tenantService = $this->app->make(TenantService::class);
    }

    public function testGetUsersSuccess()
    {
        $res = $this->tenantService->getUsers();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetUsersAndOldUserSuccess()
    {
        $res = $this->tenantService->getUsers("edit", 3);
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetKategoriUsahaSuccess()
    {
        $res = $this->tenantService->getKategoriUsaha();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testGetTotalSuccess()
    {
        $res = $this->tenantService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->tenantService->getTotal(" AND lower(cast(t.tenant_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->tenantService->getTotal(" AND lower(cast(mu.tenant_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->tenantService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->tenantService->getData(" AND lower(cast(t.tenant_id as char)) like lower('%1%') ", " ORDER BY t.tenant_nama asc ", " LIMIT 10 OFFSET 0 ", [
            "t.tenant_id",
            "t.tenant_nama",
            "t.tenant_desc",
            "t.tenant_status",
            "t.user_id",
            "t.mku_id",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->tenantService->getData(" AND lower(cast(mu.tenant_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->tenantService->validateData([
            "act" => "add",
            "tenant_desc" => "xx",
            "tenant_nama" => "Tes",
            "tenant_status" => true,
            "mku_id" => 1,
            "user_id" => 4,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->tenantService->validateData([
            "act" => "tes",
            "tenant_desc" => "xx",
            "tenant_nama" => "Tes",
            "tenant_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->tenantService->validateData([
            "act" => "add",
            "tenant_nama" => "Tes",
            "tenant_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Nama, deskripsi, user, dan kategori usaha tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->tenantService->add([
            "tenant_desc" => "xx",
            "tenant_nama" => "Tes",
            "tenant_status" => true,
            "mku_id" => 1,
            "user_id" => 4,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->tenantService->add([
            "tenant_nama" => "Tes",
            "tenant_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = Tenant::orderBy("tenant_id", "desc")->first()->tenant_id;

        $res = $this->tenantService->edit($last_id, [
            "tenant_desc" => "xx",
            "tenant_nama" => "Tes",
            "tenant_status" => true,
            "user_id" => 4,
            "mku_id" => 3,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->tenantService->edit(0, [
            "tenant_desc" => "tes01",
            "tenant_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->tenantService->edit("", [
            "tenant_desc" => "tes01",
            "tenant_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = Tenant::orderBy("tenant_id", "desc")->first()->tenant_id;

        $res = $this->tenantService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->tenantService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->tenantService->del("");
        self::assertFalse($res['status']);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->tenantService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->tenant_id);
        self::assertEquals("User 01", $res['data']->tenant_nama);
        self::assertEquals(true, $res['data']->tenant_status);
        self::assertEquals(3, $res['data']->user_id);
        self::assertEquals(1, $res['data']->mku_id);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->tenantService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->tenantService->getById("");
        self::assertFalse($res["status"]);
    }
}
