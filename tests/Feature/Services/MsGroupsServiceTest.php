<?php

namespace Tests\Feature\Services;

use App\Services\MsGroupsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsGroupsServiceTest extends TestCase
{
    private MsGroupsService $msGroupsService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msGroupsService = $this->app->make(MsGroupsService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msGroupsService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msGroupsService->getTotal(" AND lower(cast(mg.group_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msGroupsService->getTotal(" AND lower(cast(mg.group_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msGroupsService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msGroupsService->getData(" AND lower(cast(mg.group_id as char)) like lower('%1%') ", " ORDER BY mg.group_kode asc ", " LIMIT 10 OFFSET 0 ", [
            "mg.group_id",
            "mg.group_kode",
            "mg.group_nama",
            "mg.group_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msGroupsService->getData(" AND lower(cast(mg.group_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msGroupsService->validateData([
            "act" => "add",
            "group_kode" => "01",
            "group_nama" => "tes"
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msGroupsService->validateData([
            "act" => "tes",
            "group_kode" => "01",
            "group_nama" => "tes"
        ]);
        self::assertFalse($res['status']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msGroupsService->validateData([
            "act" => "add",
            "group_nama" => "tes"
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama grup tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msGroupsService->add([
            "group_id" => 4,
            "group_kode" => "04",
            "group_nama" => "Tes",
            "group_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msGroupsService->add([
            "group_id" => 4,
            "group_kode" => "04",
            "group_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $res = $this->msGroupsService->edit(4, [
            "group_kode" => "04",
            "group_nama" => "Tes update",
            "group_status" => false,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msGroupsService->edit(250, [
            "group_kode" => "04",
            "group_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msGroupsService->edit("", [
            "group_kode" => "04",
            "group_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $res = $this->msGroupsService->del(4);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msGroupsService->del(250);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msGroupsService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msGroupsService->checkDuplicate("add", "group_kode", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msGroupsService->checkDuplicate("add", "group_kode", "-01");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msGroupsService->checkDuplicate("edit", "group_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msGroupsService->checkDuplicate("edit", "group_kode", "-01", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msGroupsService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }
}
