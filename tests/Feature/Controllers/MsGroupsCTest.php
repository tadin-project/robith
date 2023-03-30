<?php

namespace Tests\Feature\Controllers;

use App\Models\MsGroups;
use App\Services\MsGroupsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsGroupsCTest extends TestCase
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
            ->get(route("ms-groups.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Hak Akses");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-groups.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-groups.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(1, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(1, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-groups.save"), [
                "act" => "add",
                "group_kode" => "xx",
                "group_nama" => "tes",
                "group_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-groups.save"), [
                "act" => "add",
                "group_nama" => "tes",
                "group_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_group_id = MsGroups::orderBy("group_id", "desc")->first()->group_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-groups.save"), [
                "act" => "edit",
                "group_id" => $last_group_id,
                "group_kode" => "xx edit",
                "group_nama" => "tes edit",
                "group_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-groups.save"), [
                "act" => "edit",
                "group_id" => 0,
                "group_kode" => "xx edit",
                "group_nama" => "tes edit",
                "group_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_group_id = MsGroups::orderBy("group_id", "desc")->first()->group_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-groups.delete", ['id' => $last_group_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-groups.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-groups.check-duplicate") . "?act=add&key=group_kode&val=xxx&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-groups.check-duplicate") . "?act=add&key=group_kode&val=02&old=");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }
}
