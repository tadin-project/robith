<?php

namespace Tests\Feature\Services;

use App\Services\MsUsersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MsUsersServiceTest extends TestCase
{
    private MsUsersService $msUsersService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msUsersService = $this->app->make(MsUsersService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msUsersService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msUsersService->getTotal(" AND lower(cast(mu.user_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msUsersService->getTotal(" AND lower(cast(mu.user_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msUsersService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msUsersService->getData(" AND lower(cast(mu.user_id as char)) like lower('%1%') ", " ORDER BY mu.user_name asc ", " LIMIT 10 OFFSET 0 ", [
            "mu.user_id",
            "mu.user_name",
            "mu.user_fullname",
            "mu.user_email",
            "mg.group_nama",
            "mu.user_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msUsersService->getData(" AND lower(cast(mu.user_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msUsersService->validateData([
            "act" => "add",
            "user_name" => "tes01",
            "user_email" => "tes01@gmail.com",
            "user_password" => "tes01",
            "group_id" => 3,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msUsersService->validateData([
            "act" => "tes",
            "user_name" => "tes01",
            "user_email" => "tes01@gmail.com",
            "group_id" => 3,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msUsersService->validateData([
            "act" => "add",
            "user_name" => "tes01",
            "group_id" => 3,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Username, email, password, dan hak akses tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msUsersService->add([
            "user_id" => 4,
            "user_name" => "tes01",
            "user_email" => "tes01@gmail.com",
            "user_fullname" => "Tes 01",
            "user_password" => Hash::make("tes01"),
            "user_status" => true,
            "group_id" => 3,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msUsersService->add([
            "user_id" => 4,
            "user_email" => "tes01@gmail.com",
            "user_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $res = $this->msUsersService->edit(4, [
            "user_name" => "tes01",
            "user_fullname" => "Tes update",
            "user_email" => "tes01-update@gmail.com",
            "user_password" => Hash::make("tes01"),
            "group_id" => 3,
            "user_status" => false,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msUsersService->edit(250, [
            "user_name" => "tes01",
            "user_fullname" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msUsersService->edit("", [
            "user_name" => "tes01",
            "user_fullname" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $res = $this->msUsersService->del(4);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msUsersService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msUsersService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msUsersService->checkDuplicate("add", "user_name", "user01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msUsersService->checkDuplicate("add", "user_name", "user02");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msUsersService->checkDuplicate("edit", "user_name", "user01", "user02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msUsersService->checkDuplicate("edit", "user_name", "user-01", "user02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msUsersService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msUsersService->getById(3);
        self::assertTrue($res['status']);
        self::assertEquals(3, $res['data']->user_id);
        self::assertEquals("user01", $res['data']->user_name);
        self::assertEquals("User 01", $res['data']->user_fullname);
        self::assertEquals("user01@gmail.com", $res['data']->user_email);
        self::assertEquals(true, $res['data']->user_status);
        self::assertEquals(3, $res['data']->group_id);
        self::assertEquals("02", $res['data']->group->group_kode);
        self::assertEquals("User", $res['data']->group->group_nama);
        self::assertEquals(true, $res['data']->group->group_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msUsersService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msUsersService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetOptGroupSuccess()
    {
        $res = $this->msUsersService->getOptGroup();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['data']->count());
    }
}
