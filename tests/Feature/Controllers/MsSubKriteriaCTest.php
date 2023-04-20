<?php

namespace Tests\Feature\Controllers;

use App\Models\MsSubKriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsSubKriteriaCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = Config::get("constants.session.admin");
    }
    public function testPage()
    {
        $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Sub Kriteria");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(1, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-sub-kriteria.save"), [
                "act" => "add",
                "msk_kode" => "xx",
                "msk_nama" => "Tes",
                "msk_status" => true,
                "msk_bobot" => 60,
                "mk_id" => 1,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-sub-kriteria.save"), [
                "act" => "add",
                "msk_nama" => "Tes",
                "msk_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_id = MsSubKriteria::orderBy("msk_id", "desc")->first()->msk_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-sub-kriteria.save"), [
                "act" => "edit",
                "msk_id" => $last_id,
                "msk_kode" => "x1",
                "msk_nama" => "Tes Update",
                "msk_status" => false,
                "msk_bobot" => 61,
                "mk_id" => 2,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-sub-kriteria.save"), [
                "act" => "edit",
                "msk_id" => 0,
                "msk_kode" => "xx",
                "msk_nama" => "Tes Update",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_id = MsSubKriteria::orderBy("msk_id", "desc")->first()->msk_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-sub-kriteria.delete", ['id' => $last_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-sub-kriteria.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.check-duplicate") . "?act=add&key=msk_kode&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.check-duplicate") . "?act=add&key[0]=msk_kode&key[1]=mk_id&val=01,1=1&old=xx");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.get", ["id" => 1]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(1, $jsonDt['data']["msk_id"]);
        self::assertEquals("01", $jsonDt['data']["msk_kode"]);
        self::assertEquals("Menetapkan Tujuan & Visi", $jsonDt['data']["msk_nama"]);
        self::assertEquals(20, $jsonDt['data']["msk_bobot"]);
        self::assertEquals(true, $jsonDt['data']["msk_status"]);
        self::assertEquals(1, $jsonDt['data']["mk_id"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-sub-kriteria.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }
}
