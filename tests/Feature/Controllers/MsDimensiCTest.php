<?php

namespace Tests\Feature\Controllers;

use App\Models\MsDimensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsDimensiCTest extends TestCase
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
            ->get(route("ms-dimensi.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Dimensi");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-dimensi.save"), [
                "act" => "add",
                "md_kode" => "xx",
                "md_nama" => "Tes",
                "md_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-dimensi.save"), [
                "act" => "add",
                "md_nama" => "Tes",
                "md_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_id = MsDimensi::orderBy("md_id", "desc")->first()->md_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-dimensi.save"), [
                "act" => "edit",
                "md_id" => $last_id,
                "md_kode" => "xx",
                "md_nama" => "Tes Update",
                "md_status" => false,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-dimensi.save"), [
                "act" => "edit",
                "md_id" => 0,
                "md_kode" => "xx",
                "md_nama" => "Tes Update",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_id = MsDimensi::orderBy("md_id", "desc")->first()->md_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-dimensi.delete", ['id' => $last_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-dimensi.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.check-duplicate") . "?act=add&key=md_kode&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.check-duplicate") . "?act=add&key=md_kode&val=01&old=xx");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.get", ["id" => 1]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(1, $jsonDt['data']["md_id"]);
        self::assertEquals("01", $jsonDt['data']["md_kode"]);
        self::assertEquals("Direction", $jsonDt['data']["md_nama"]);
        self::assertEquals(true, $jsonDt['data']["md_status"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-dimensi.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }
}
