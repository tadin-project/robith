<?php

namespace Tests\Feature\Services;

use App\Models\MsKategori;
use App\Services\MsKategoriService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsKategoriServiceTest extends TestCase
{
    private MsKategoriService $msKategoriService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msKategoriService = $this->app->make(MsKategoriService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msKategoriService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msKategoriService->getTotal(" AND lower(cast(mk.mk_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msKategoriService->getTotal(" AND lower(cast(mu.mk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msKategoriService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msKategoriService->getData(" AND lower(cast(mk.mk_id as char)) like lower('%1%') ", " ORDER BY mk.mk_nama asc ", " LIMIT 10 OFFSET 0 ", [
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
        $res = $this->msKategoriService->getData(" AND lower(cast(mu.mk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msKategoriService->validateData([
            "act" => "add",
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msKategoriService->validateData([
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
        $res = $this->msKategoriService->validateData([
            "act" => "add",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msKategoriService->add([
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msKategoriService->add([
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsKategori::orderBy("mk_kode", "desc")->first()->mk_id;

        $res = $this->msKategoriService->edit($last_id, [
            "mk_kode" => "xx",
            "mk_nama" => "Tes",
            "mk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msKategoriService->edit(250, [
            "mk_kode" => "tes01",
            "mk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msKategoriService->edit("", [
            "mk_kode" => "tes01",
            "mk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsKategori::orderBy("mk_kode", "desc")->first()->mk_id;

        $res = $this->msKategoriService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msKategoriService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msKategoriService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msKategoriService->checkDuplicate("add", "mk_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msKategoriService->checkDuplicate("add", "mk_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msKategoriService->checkDuplicate("edit", "mk_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msKategoriService->checkDuplicate("edit", "mk_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msKategoriService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msKategoriService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->mk_id);
        self::assertEquals("01", $res['data']->mk_kode);
        self::assertEquals("Direction", $res['data']->mk_nama);
        self::assertEquals(true, $res['data']->mk_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msKategoriService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msKategoriService->getById("");
        self::assertFalse($res["status"]);
    }
}
