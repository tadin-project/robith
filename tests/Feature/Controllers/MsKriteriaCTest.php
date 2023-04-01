<?php

namespace Tests\Feature\Controllers;

use App\Models\MsKriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsKriteriaCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = Config::get("constants.session");
    }
    public function testPage()
    {
        $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Kriteria");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(1, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kriteria.save"), [
                "act" => "add",
                "mk_kode" => "xx",
                "mk_nama" => "Tes",
                "mk_status" => true,
                "md_id" => 1,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kriteria.save"), [
                "act" => "add",
                "mk_nama" => "Tes",
                "mk_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_id = MsKriteria::orderBy("mk_id", "desc")->first()->mk_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kriteria.save"), [
                "act" => "edit",
                "mk_id" => $last_id,
                "mk_kode" => "x1",
                "mk_nama" => "Tes Update",
                "mk_status" => false,
                "md_id" => 1,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kriteria.save"), [
                "act" => "edit",
                "mk_id" => 0,
                "mk_kode" => "xx",
                "mk_nama" => "Tes Update",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_id = MsKriteria::orderBy("mk_id", "desc")->first()->mk_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-kriteria.delete", ['id' => $last_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-kriteria.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.check-duplicate") . "?act=add&key=mk_kode&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.check-duplicate") . "?act=add&key[0]=mk_kode&key[1]=md_id&val[0]=01&val[1]=1&old=xx");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.get", ["id" => 1]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(1, $jsonDt['data']["mk_id"]);
        self::assertEquals("01", $jsonDt['data']["mk_kode"]);
        self::assertEquals("Purpose, Vision & Strategy", $jsonDt['data']["mk_nama"]);
        self::assertEquals(true, $jsonDt['data']["mk_status"]);
        self::assertEquals(1, $jsonDt['data']["md_id"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kriteria.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }
}
