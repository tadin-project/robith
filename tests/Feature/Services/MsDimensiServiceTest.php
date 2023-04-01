<?php

namespace Tests\Feature\Services;

use App\Models\MsDimensi;
use App\Services\MsDimensiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MsDimensiServiceTest extends TestCase
{
    private MsDimensiService $msDimensiService;

    public function setUp(): void
    {
        parent::setUp();
        $this->msDimensiService = $this->app->make(MsDimensiService::class);
    }

    public function testGetTotalSuccess()
    {
        $res = $this->msDimensiService->getTotal("");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalByParamSuccess()
    {
        $res = $this->msDimensiService->getTotal(" AND lower(cast(md.md_id as char)) like lower('%1%') ");
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, $res['total']);
    }

    public function testGetTotalFailed()
    {
        $res = $this->msDimensiService->getTotal(" AND lower(cast(mu.md_id as char ");
        self::assertFalse($res['status']);
    }

    public function testGetDataSuccess()
    {
        $res = $this->msDimensiService->getData();
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataByParamSuccess()
    {
        $res = $this->msDimensiService->getData(" AND lower(cast(md.md_id as char)) like lower('%1%') ", " ORDER BY md.md_nama asc ", " LIMIT 10 OFFSET 0 ", [
            "md.md_id",
            "md.md_kode",
            "md.md_nama",
            "md.md_status",
        ]);
        self::assertTrue($res['status']);
        self::assertGreaterThanOrEqual(0, count($res['data']));
    }

    public function testGetDataFailed()
    {
        $res = $this->msDimensiService->getData(" AND lower(cast(mu.md_id as char ");
        self::assertFalse($res['status']);
    }

    public function testValidateDataSuccess()
    {
        $res = $this->msDimensiService->validateData([
            "act" => "add",
            "md_kode" => "xx",
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testValidateDataUnknownRequest()
    {
        $res = $this->msDimensiService->validateData([
            "act" => "tes",
            "md_kode" => "xx",
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Request tidak dikenal!", $res['msg']);
    }

    public function testValidateDataCredentialInvalid()
    {
        $res = $this->msDimensiService->validateData([
            "act" => "add",
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Kode dan nama tidak boleh kosong!", $res['msg']);
    }

    public function testAddSuccess()
    {
        $res = $this->msDimensiService->add([
            "md_kode" => "xx",
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testAddFailed()
    {
        $res = $this->msDimensiService->add([
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertFalse($res['status']);
    }

    public function testUpdateSuccess()
    {
        $last_id = MsDimensi::orderBy("md_kode", "desc")->first()->md_id;

        $res = $this->msDimensiService->edit($last_id, [
            "md_kode" => "xx",
            "md_nama" => "Tes",
            "md_status" => true,
        ]);
        self::assertTrue($res['status']);
    }

    public function testUpdateNotFound()
    {
        $res = $this->msDimensiService->edit(250, [
            "md_kode" => "tes01",
            "md_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testUpdateFailed()
    {
        $res = $this->msDimensiService->edit("", [
            "md_kode" => "tes01",
            "md_nama" => "Tes update",
        ]);
        self::assertFalse($res['status']);
    }

    public function testDeleteSuccess()
    {
        $last_id = MsDimensi::orderBy("md_kode", "desc")->first()->md_id;

        $res = $this->msDimensiService->del($last_id);
        self::assertTrue($res['status']);
    }

    public function testDeleteNotFound()
    {
        $res = $this->msDimensiService->del(0);
        self::assertFalse($res['status']);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testDeleteFailed()
    {
        $res = $this->msDimensiService->del("");
        self::assertFalse($res['status']);
    }

    public function testFoundDuplicateOnAdd()
    {
        $res = $this->msDimensiService->checkDuplicate("add", "md_kode", "01");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnAdd()
    {
        $res = $this->msDimensiService->checkDuplicate("add", "md_kode", "xx");
        self::assertEquals("true", $res);
    }

    public function testFoundDuplicateOnEdit()
    {
        $res = $this->msDimensiService->checkDuplicate("edit", "md_kode", "01", "02");
        self::assertEquals("false", $res);
    }

    public function testNotFoundDuplicateOnEdit()
    {
        $res = $this->msDimensiService->checkDuplicate("edit", "md_kode", "02", "02");
        self::assertEquals("true", $res);
    }

    public function testDuplicateFailed()
    {
        $res = $this->msDimensiService->checkDuplicate("edit", "group_kodera", "-01");
        self::assertEquals("false", $res);
    }

    public function testGetByIdSuccess()
    {
        $res = $this->msDimensiService->getById(1);
        self::assertTrue($res['status']);
        self::assertEquals(1, $res['data']->md_id);
        self::assertEquals("01", $res['data']->md_kode);
        self::assertEquals("Direction", $res['data']->md_nama);
        self::assertEquals(true, $res['data']->md_status);
    }

    public function testGetByIdNotFound()
    {
        $res = $this->msDimensiService->getById(0);
        self::assertFalse($res["status"]);
        self::assertEquals("Data tidak ditemukan!", $res['msg']);
    }

    public function testGetByIdFailed()
    {
        $res = $this->msDimensiService->getById("");
        self::assertFalse($res["status"]);
    }
}
