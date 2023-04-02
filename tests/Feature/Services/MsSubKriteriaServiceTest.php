<?php

namespace Tests\Feature\Services;

use App\Models\MsSubKriteria;
use App\Services\MsSubKriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsSubKriteriaServiceTest extends TestCase
{
    private MsSubKriteriaService $msSubKriteriaService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msSubKriteriaService = $this->app->make(MsSubKriteriaService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msSubKriteriaService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msSubKriteriaService->getTotal(" AND lower(cast(msk.msk_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msSubKriteriaService->getTotal(" AND lower(cast(mu.msk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msSubKriteriaService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msSubKriteriaService->getData(" AND lower(cast(msk.msk_id as char)) like lower('%1%') ", " ORDER BY msk.msk_kode asc ", " LIMIT 10 OFFSET 0 ", [
            "msk.msk_id",
            "msk.msk_kode",
            "msk.msk_nama",
            "msk.msk_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msSubKriteriaService->getData(" AND lower(cast(mu.msk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msSubKriteriaService->validateData([
            "act" => "add",
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msSubKriteriaService->validateData([
            "act" => "tes",
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msSubKriteriaService->validateData([
            "act" => "add",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msSubKriteriaService->add([
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
            "mk_id" => 1,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msSubKriteriaService->add([
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsSubKriteria::orderBy("msk_kode", "desc")->first()->msk_id;

        $res = $this->msSubKriteriaService->edit($last_id, [
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msSubKriteriaService->edit(250, [
            "msk_kode" => "tes01",
            "msk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msSubKriteriaService->edit("", [
            "msk_kode" => "tes01",
            "msk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsSubKriteria::orderBy("msk_kode", "desc")->first()->msk_id;

        $res = $this->msSubKriteriaService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msSubKriteriaService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msSubKriteriaService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msSubKriteriaService->checkDuplicate("add", "msk_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msSubKriteriaService->checkDuplicate("add", "msk_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msSubKriteriaService->checkDuplicate("edit", "msk_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msSubKriteriaService->checkDuplicate("edit", "msk_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msSubKriteriaService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msSubKriteriaService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->msk_id);
        self::assertEquals("01", $res['data']->msk_kode);
        self::assertEquals("Direction", $res['data']->msk_nama);
        self::assertEquals(1, $res['data']->mk_id);
        self::assertEquals("01", $res['data']->kategori->mk_kode);
        self::assertEquals("Direction", $res['data']->kategori->mk_nama);
        self::assertEquals(true, $res['data']->msk_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msSubKriteriaService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msSubKriteriaService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetKategori()
    {
        $res = $this->msSubKriteriaService->getKategori();
        self::assertTrue($res["status"]);
    }
}
