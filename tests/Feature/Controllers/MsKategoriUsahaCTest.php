<?php

namespace Tests\Feature\Controllers;

use App\Models\MsKategoriUsaha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsKategoriUsahaCTest extends TestCase
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
            ->get(route("ms-kategori-usaha.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Kategori");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kategori-usaha.save"), [
                "act" => "add",
                "mku_kode" => "xx",
                "mku_nama" => "Tes",
                "mku_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kategori-usaha.save"), [
                "act" => "add",
                "mku_nama" => "Tes",
                "mku_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_id = MsKategoriUsaha::orderBy("mku_id", "desc")->first()->mku_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kategori-usaha.save"), [
                "act" => "edit",
                "mku_id" => $last_id,
                "mku_kode" => "xx",
                "mku_nama" => "Tes Update",
                "mku_status" => false,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-kategori-usaha.save"), [
                "act" => "edit",
                "mku_id" => 0,
                "mku_kode" => "xx",
                "mku_nama" => "Tes Update",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_id = MsKategoriUsaha::orderBy("mku_id", "desc")->first()->mku_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-kategori-usaha.delete", ['id' => $last_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-kategori-usaha.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.check-duplicate") . "?act=add&key=mku_kode&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.check-duplicate") . "?act=add&key=mku_kode&val=01&old=xx");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.get", ["id" => 1]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(1, $jsonDt['data']["mku_id"]);
        self::assertEquals("01", $jsonDt['data']["mku_kode"]);
        self::assertEquals("Kuliner/F&B", $jsonDt['data']["mku_nama"]);
        self::assertEquals(true, $jsonDt['data']["mku_status"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-kategori-usaha.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }
}
