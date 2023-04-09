<?php

namespace Tests\Feature\Services;

use App\Models\MsKategoriUsaha;
use App\Services\MsKategoriUsahaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsKategoriUsahaServiceTest extends

TestCase
{
    private MsKategoriUsahaService $msKategoriUsahaService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msKategoriUsahaService = $this->app->make(MsKategoriUsahaService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msKategoriUsahaService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msKategoriUsahaService->getTotal(" AND lower(cast(mku.mku_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msKategoriUsahaService->getTotal(" AND lower(cast(mu.mku_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msKategoriUsahaService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msKategoriUsahaService->getData(" AND lower(cast(mku.mku_id as char)) like lower('%1%') ", " ORDER BY mku.mku_nama asc ", " LIMIT 10 OFFSET 0 ", [
            "mku.mku_id",
            "mku.mku_kode",
            "mku.mku_nama",
            "mku.mku_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msKategoriUsahaService->getData(" AND lower(cast(mu.mku_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msKategoriUsahaService->validateData([
            "act" => "add",
            "mku_kode" => "xx",
            "mku_nama" => "Tes",
            "mku_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msKategoriUsahaService->validateData([
            "act" => "tes",
            "mku_kode" => "xx",
            "mku_nama" => "Tes",
            "mku_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msKategoriUsahaService->validateData([
            "act" => "add",
            "mku_nama" => "Tes",
            "mku_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msKategoriUsahaService->add([
            "mku_kode" => "xx",
            "mku_nama" => "Tes",
            "mku_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msKategoriUsahaService->add([
            "mku_nama" => "Tes",
            "mku_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsKategoriUsaha::orderBy("mku_id", "desc")->first()->mku_id;

        $res = $this->msKategoriUsahaService->edit($last_id, [
            "mku_kode" => "xx1",
            "mku_nama" => "Tes update",
            "mku_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msKategoriUsahaService->edit(0, [
            "mku_kode" => "tes01",
            "mku_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msKategoriUsahaService->edit("", [
            "mku_kode" => "tes01",
            "mku_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsKategoriUsaha::orderBy("mku_id", "desc")->first()->mku_id;

        $res = $this->msKategoriUsahaService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msKategoriUsahaService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msKategoriUsahaService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msKategoriUsahaService->checkDuplicate("add", "mku_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msKategoriUsahaService->checkDuplicate("add", "mku_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msKategoriUsahaService->checkDuplicate("edit", "mku_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msKategoriUsahaService->checkDuplicate("edit", "mku_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msKategoriUsahaService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msKategoriUsahaService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->mku_id);
        self::assertEquals("01", $res['data']->mku_kode);
        self::assertEquals("Kuliner/F&B", $res['data']->mku_nama);
        self::assertEquals(true, $res['data']->mku_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msKategoriUsahaService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msKategoriUsahaService->getById("");
        self::assertFalse($res["status"]);
    }
}
