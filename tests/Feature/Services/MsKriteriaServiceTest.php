<?php

namespace Tests\Feature\Services;

use App\Models\MsKriteria;
use App\Services\MsKriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsKriteriaServiceTest extends TestCase
{
    private MsKriteriaService $msKriteriaService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msKriteriaService = $this->app->make(MsKriteriaService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msKriteriaService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msKriteriaService->getTotal(" AND lower(cast(mk.mk_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msKriteriaService->getTotal(" AND lower(cast(mu.mk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msKriteriaService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msKriteriaService->getData(" AND lower(cast(mk.mk_id as char)) like lower('%1%') ", " ORDER BY mk.mk_nama asc ", " LIMIT 10 OFFSET 0 ", [
            "mk.mk_id",
            "mk.mk_kode",
            "mk.mk_nama",
            "mk.mk_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msKriteriaService->getData(" AND lower(cast(mu.mk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msKriteriaService->validateData([
            "act" => "add",
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "md_id" => 1,
            "mk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msKriteriaService->validateData([
            "act" => "tes",
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msKriteriaService->validateData([
            "act" => "add",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode, nama, dan dimensi tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msKriteriaService->add([
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "mk_status" => true,
            "md_id" => 1,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msKriteriaService->add([
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsKriteria::orderBy("mk_id", "desc")->first()->mk_id;

        $res = $this->msKriteriaService->edit($last_id, [
            "mk_kode" => "xx",
            "mk_nama" => "Tes update",
            "mk_status" => false,
            "md_id" => 2,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msKriteriaService->edit(0, [
            "mk_kode" => "tes01",
            "mk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msKriteriaService->edit("", [
            "mk_kode" => "tes01",
            "mk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsKriteria::orderBy("mk_id", "desc")->first()->mk_id;

        $res = $this->msKriteriaService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msKriteriaService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msKriteriaService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msKriteriaService->checkDuplicate("add", ["mk_kode", "md_id"], ["01", 1]);
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msKriteriaService->checkDuplicate("add", "mk_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msKriteriaService->checkDuplicate("edit", "mk_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msKriteriaService->checkDuplicate("edit", "mk_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msKriteriaService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msKriteriaService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->mk_id);
        self::assertEquals("01", $res['data']->mk_kode);
        self::assertEquals("Purpose, Vision & Strategy", $res['data']->mk_nama);
        self::assertEquals(true, $res['data']->mk_status);
        self::assertEquals(1, $res['data']->md_id);
        self::assertEquals("01", $res['data']->dimensi->md_kode);
        self::assertEquals("Direction", $res['data']->dimensi->md_nama);
        self::assertEquals(true, $res['data']->dimensi->md_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msKriteriaService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msKriteriaService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetDimensi()
    {
        $res = $this->msKriteriaService->getDimensi();
        self::assertTrue($res["status"]);
    }
}
