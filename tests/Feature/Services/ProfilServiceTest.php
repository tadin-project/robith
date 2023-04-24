<?php

namespace Tests\Feature\Services;

use App\Services\ProfilService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfilServiceTest extends TestCase
{
    private ProfilService $profilService;

    public function setUp(): void
    {
        parent::setUp();
        $this->profilService = $this->app->make(ProfilService::class);
    }

    public function testGetUserSuccess()
    {
        $res = $this->profilService->getUserData(2);
        self::assertTrue($res["status"]);
        $data = $res["data"];
        self::assertEquals("admin", $data["user_name"]);
        self::assertEquals("Administrator", $data["user_fullname"]);
        self::assertEquals("admin@gmail.com", $data["user_email"]);
        self::assertEquals([], $data["tenant"]);
    }

    public function testGetUserTenantSuccess()
    {
        $res = $this->profilService->getUserData(4);
        self::assertTrue($res["status"]);
        $data = $res["data"];
        self::assertEquals("user02", $data["user_name"]);
        self::assertEquals("User 02", $data["user_fullname"]);
        self::assertEquals("user02@gmail.com", $data["user_email"]);
        $tenant = $data["tenant"];
        self::assertEquals("Tenant User 02", $tenant["tenant_nama"]);
        self::assertEquals(6, $tenant["mku_id"]);
    }

    public function testGetUserNotFound()
    {
        $res = $this->profilService->getUserData("");
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan! Silahkan relogin", $res["msg"]);
    }

    public function testGetKategoriUsahaSuccess()
    {
        $res = $this->profilService->getKategoriUsaha();
        self::assertTrue($res["status"]);
        self::assertGreaterThanOrEqual(0, count($res["data"]));
    }

    public function testSaveUserSuccess()
    {
        $res = $this->profilService->saveUser(2, ["user_fullname" => "Administrator Update"]);
        self::assertTrue($res["status"]);
    }

    public function testSaveUserFailed()
    {
        $res = $this->profilService->saveUser(2, ["user_fullna" => "Administrator Update"]);
        self::assertFalse($res["status"]);
    }

    public function testSaveTenantSuccess()
    {
        $res = $this->profilService->saveTenant(1, ["tenant_nama" => "Tenant Profil Update"]);
        self::assertTrue($res["status"]);
    }

    public function testSaveTenantFailed()
    {
        $res = $this->profilService->saveTenant(1, ["tenant_nam" => "Tenant Profil Update"]);
        self::assertFalse($res["status"]);
    }
}
