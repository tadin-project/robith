<?php

namespace Tests\Feature\Services;

use App\Models\MsSubKategori;
use App\Services\MsSubKategoriService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsSubKategoriServiceTest extends TestCase
{
    private MsSubKategoriService $msSubKategoriService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msSubKategoriService = $this->app->make(MsSubKategoriService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msSubKategoriService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msSubKategoriService->getTotal(" AND lower(cast(msk.msk_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msSubKategoriService->getTotal(" AND lower(cast(mu.msk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msSubKategoriService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msSubKategoriService->getData(" AND lower(cast(msk.msk_id as char)) like lower('%1%') ", " ORDER BY msk.msk_kode asc ", " LIMIT 10 OFFSET 0 ", [
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
        $res = $this->msSubKategoriService->getData(" AND lower(cast(mu.msk_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msSubKategoriService->validateData([
            "act" => "add",
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msSubKategoriService->validateData([
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
        $res = $this->msSubKategoriService->validateData([
            "act" => "add",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msSubKategoriService->add([
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
            "mk_id" => 1,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msSubKategoriService->add([
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsSubKategori::orderBy("msk_kode", "desc")->first()->msk_id;

        $res = $this->msSubKategoriService->edit($last_id, [
            "msk_kode" => "xx",
            "msk_nama" => "Tes",
            "msk_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msSubKategoriService->edit(250, [
            "msk_kode" => "tes01",
            "msk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msSubKategoriService->edit("", [
            "msk_kode" => "tes01",
            "msk_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsSubKategori::orderBy("msk_kode", "desc")->first()->msk_id;

        $res = $this->msSubKategoriService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msSubKategoriService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msSubKategoriService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msSubKategoriService->checkDuplicate("add", "msk_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msSubKategoriService->checkDuplicate("add", "msk_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msSubKategoriService->checkDuplicate("edit", "msk_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msSubKategoriService->checkDuplicate("edit", "msk_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msSubKategoriService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msSubKategoriService->getById(1);
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
        $res = $this->msSubKategoriService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msSubKategoriService->getById("");
        self::assertFalse($res["status"]);
    }

    public function testGetKategori()
    {
        $res = $this->msSubKategoriService->getKategori();
        self::assertTrue($res["status"]);
    }
}
