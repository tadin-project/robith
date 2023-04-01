<?php

namespace Tests\Feature\Services;

use App\Models\MsMenus;
use App\Services\MsMenusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MsMenusServiceTest extends TestCase
{
    private MsMenusService $msMenusService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msMenusService = $this->app->make(MsMenusService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msMenusService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msMenusService->getTotal(" AND lower(cast(mm.menu_nama as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msMenusService->getTotal(" AND lower(cast(mu.user_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msMenusService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msMenusService->getData(" AND lower(cast(mm.menu_nama as char)) like lower('%admi%') ", " ORDER BY mm.menu_kode asc ", " LIMIT 10 OFFSET 0 ", [
            "mm.menu_id",
            "mm.menu_kode",
            "mm.menu_nama",
            "mm.menu_type",
            "mm.menu_link",
            "mm.menu_ikon",
            "p.menu_nama as parent_menu_nama",
            "mm.menu_status",
            "coalesce(c.tot_child) as tot_child",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msMenusService->getData(" AND lower(cast(mu.user_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msMenusService->validateData([
            "act" => "add",
            "menu_kode" => "xx1",
            "menu_nama" => "Menu tes",
            "menu_link" => "#",
            "menu_type" => 1,
            "menu_ikon" => "",
            "parent_menu_id" => 0,
            "menu_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msMenusService->validateData([
            "act" => "tes",
            "menu_kode" => "xx1",
            "menu_nama" => "Menu tes",
            "menu_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msMenusService->validateData([
            "act" => "add",
            "menu_kode" => "xx1",
            "menu_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama menu tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msMenusService->add([
            "menu_kode" => "xx1",
            "menu_nama" => "Menu tes",
            "menu_link" => "#",
            "menu_type" => 1,
            "menu_ikon" => "",
            "parent_menu_id" => 0,
            "menu_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msMenusService->add([
            "menu_link" => "#",
            "menu_type" => 1,
            "menu_ikon" => "",
            "parent_menu_id" => 0,
            "menu_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_menu_id = MsMenus::orderBy("menu_id", "desc")->first()->menu_id;

        $res = $this->msMenusService->edit($last_menu_id, [
            "menu_kode" => "xx1 update",
            "menu_nama" => "Menu tes update",
            "menu_link" => "#",
            "menu_type" => 1,
            "menu_ikon" => "",
            "parent_menu_id" => 0,
            "menu_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msMenusService->edit(250, [
            "menu_kode" => "xx1",
            "menu_nama" => "Menu tes",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msMenusService->edit("", [
            "menu_kode" => "xx1",
            "menu_nama" => "Menu tes",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_menu_id = MsMenus::orderBy("menu_id", "desc")->first()->menu_id;
        $res = $this->msMenusService->del($last_menu_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msMenusService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msMenusService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msMenusService->checkDuplicate("add", "menu_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msMenusService->checkDuplicate("add", "menu_kode", "tes");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msMenusService->checkDuplicate("edit", "menu_kode", "01", "xx1 update");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msMenusService->checkDuplicate("edit", "menu_kode", "xx1", "xx1 update");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msMenusService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msMenusService->getById(7);
        self::assertTrue($res['status']);
        self::assertEquals(7, $res['data']->menu_id);
        self::assertEquals("00", $res['data']->menu_kode);
        self::assertEquals("Dashboard", $res['data']->menu_nama);
        self::assertEquals("dashboard", $res['data']->menu_link);
        self::assertEquals(1, $res['data']->menu_type);
        self::assertEquals("", $res['data']->menu_ikon);
        self::assertEquals(true, $res['data']->menu_status);
        self::assertEquals(0, $res['data']->parent_menu_id);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msMenusService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msMenusService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetOptParentSuccess()
    {
        $res = $this->msMenusService->getOptParent();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['data']->count());
    }
}
