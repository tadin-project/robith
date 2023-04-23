<?php

namespace Tests\Feature\Controllers;

use App\Models\MsMenus;
use App\Models\MsUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsMenusCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = [
            "user_data" => [
                "user_id" => 1,
                "user_name" => "root",
                "user_email" => "root@gmail.com",
                "user_fullname" => "Root",
                "group_id" => 1,
            ]
        ];
    }
    public function testPage()
    {
        $this->withSession($this->sess_user)
            ->get(route("ms-menus.index"))
            ->assertStatus(200)
            ->assertSeeText("Master Menu");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(1, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(1, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-menus.save"), [
                "act" => "add",
                "menu_kode" => "xx1",
                "menu_nama" => "Menu tes",
                "menu_link" => "#",
                "menu_type" => 1,
                "menu_ikon" => "",
                "parent_menu_id" => 0,
                "menu_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-menus.save"), [
                "act" => "add",
                "user_name" => "tes01",
                "user_fullname" => "Tes",
                "user_email" => "tes01@gmail.com",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testEditSuccess()
    {
        $last_menu_id = MsMenus::orderBy("menu_id", "desc")->first()->menu_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-menus.save"), [
                "act" => "edit",
                "menu_id" => $last_menu_id,
                "menu_kode" => "xx1 update",
                "menu_nama" => "Menu tes update",
                "menu_link" => "#",
                "menu_type" => 1,
                "menu_ikon" => "i",
                "parent_menu_id" => 1,
                "menu_status" => false,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-menus.save"), [
                "act" => "edit",
                "menu_id" => 0,
                "menu_kode" => "xx1 update",
                "menu_nama" => "Menu tes update",
                "menu_link" => "#",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_menu_id = MsMenus::orderBy("menu_id", "desc")->first()->menu_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-menus.delete", ['id' => $last_menu_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-menus.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.check-duplicate") . "?act=add&key=menu_kode&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.check-duplicate") . "?act=add&key=menu_kode&val=01&old=xx1");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get", ["id" => 7]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(7, $jsonDt['data']["menu_id"]);
        self::assertEquals("00", $jsonDt['data']["menu_kode"]);
        self::assertEquals("Dashboard", $jsonDt['data']["menu_nama"]);
        self::assertEquals(1, $jsonDt['data']["menu_type"]);
        self::assertEquals("dashboard", $jsonDt['data']["menu_link"]);
        self::assertEquals("", $jsonDt['data']["menu_ikon"]);
        self::assertEquals(true, $jsonDt['data']["menu_status"]);
        self::assertEquals(0, $jsonDt['data']["parent_menu_id"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }

    public function testGetOptParentSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-menus.get-parent"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
    }
}
