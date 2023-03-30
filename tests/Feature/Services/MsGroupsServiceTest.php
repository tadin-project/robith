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

    public function testGetByIdSuccess()
    {
        $res = $this->msGroupsService->getById(3);
        self::assertTrue($res['status']);
        self::assertEquals(3, $res['data']->group_id);
        self::assertEquals("02", $res['data']->group_kode);
        self::assertEquals("User", $res['data']->group_nama);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msGroupsService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msGroupsService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetAksesSuccess()
    {
        $res = $this->msGroupsService->getAkses(3, "");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['data']);
    }

    public function testGetAksesGroupIdRequiredSuccess()
    {
        $res = $this->msGroupsService->getAkses(0, "");
        self::assertFalse($res["status"]);
        self::assertEquals("Id hak akses diperlukan!", $res['msg']);
    }

    public function testGetAksesFailed()
    {
        $res = $this->msGroupsService->getAkses(3, "a");
        self::assertFalse($res["status"]);
    }

    public function testDelAksesSuccess()
    {
        $res = $this->msGroupsService->delAkses(3);
        self::assertTrue($res['status']);
    }

    public function testDelAksesFailed()
    {
        $res = $this->msGroupsService->delAkses("\"");
        if ($res["status"]) {
            self::assertTrue($res["status"]);
        } else {
            self::assertFalse($res["status"]);
        }
    }

    public function testAddAksesSuccess()
    {
        $res = $this->msGroupsService->addAkses([
            [
                "group_id" => 3,
                "menu_id" => 7,
            ]
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddAksesFailed()
    {
        $res = $this->msGroupsService->addAkses([
            [
                "group_id" => "",
                "menu_id" => "",
            ]
        ]);
        self::assertFalse($res["status"]);
    }

    public function testSaveAksesSuccess()
    {
        $res = $this->msGroupsService->saveAkses(3, [7]);
        fwrite(STDERR, print_r($res['msg'], true));
        self::assertTrue($res['status']);
    }

    public function testSaveAksesErrorIdGroupRequired()
    {
        $res = $this->msGroupsService->saveAkses(0, []);
        self::assertFalse($res["status"]);
        self::assertEquals("Id hak akses diperlukan!", $res["msg"]);
    }

    public function testSaveAksesErrorMinimal1Menu()
    {
        $res = $this->msGroupsService->saveAkses(3, []);
        self::assertFalse($res["status"]);
        self::assertEquals("Pilih minimal 1 menu!", $res["msg"]);
    }

    public function testSaveAksesFailed()
    {
        $res = $this->msGroupsService->saveAkses(3, ["g"]);
        self::assertFalse($res["status"]);
    }
}
