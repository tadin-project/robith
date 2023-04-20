<?php

namespace Tests\Feature\Controllers;

use App\Models\MsUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MsUsersCTest extends TestCase
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
            ->get(route("ms-users.index"))
            ->assertStatus(200)
            ->assertSeeText("Master User")
            ->assertSeeText("Admin");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(1, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(1, $jsonDt["data"]);
    }

    public function testAddSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-users.save"), [
                "act" => "add",
                "user_name" => "tes01",
                "user_fullname" => "Tes",
                "user_email" => "tes01@gmail.com",
                "user_password" => "tes01",
                "group_id" => 3,
                "user_status" => true,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testAddError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-users.save"), [
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
        $last_user_id = MsUsers::orderBy("user_id", "desc")->first()->user_id;

        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-users.save"), [
                "act" => "edit",
                "user_id" => $last_user_id,
                "user_name" => "tes01",
                "user_fullname" => "Tes update",
                "user_email" => "tes01-update@gmail.com",
                "is_ganti_pass" => "on",
                "user_password" => "newtes01",
                "group_id" => 3,
                "user_status" => false,
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testEditError()
    {
        $dt = $this->withSession($this->sess_user)
            ->post(route("ms-users.save"), [
                "act" => "edit",
                "user_id" => 0,
                "user_name" => "tes01",
                "user_fullname" => "Tes",
                "user_email" => "tes01@gmail.com",
            ]);
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testDelSuccess()
    {
        $last_user_id = MsUsers::orderBy("user_id", "desc")->first()->user_id;

        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-users.delete", ['id' => $last_user_id]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testDelError()
    {
        $dt = $this->withSession($this->sess_user)
            ->delete(route("ms-users.delete", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }

    public function testNotFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.check-duplicate") . "?act=add&key=user_name&val=-01&old=");
        $dt->assertStatus(200);
        self::assertEquals("true", $dt->getContent());
    }

    public function testFoundDuplicate()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.check-duplicate") . "?act=add&key=user_name&val=user01&old=");
        $dt->assertStatus(200);
        self::assertEquals("false", $dt->getContent());
    }

    public function testGetByIdSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.get", ["id" => 3]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertTrue($jsonDt['status']);
        self::assertEquals(3, $jsonDt['data']["user_id"]);
        self::assertEquals("user01", $jsonDt['data']["user_name"]);
        self::assertEquals("User 01", $jsonDt['data']["user_fullname"]);
        self::assertEquals("user01@gmail.com", $jsonDt['data']["user_email"]);
        self::assertEquals(true, $jsonDt['data']["user_status"]);
        self::assertEquals(3, $jsonDt['data']["group_id"]);
    }

    public function testGetByIdNotFound()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.get", ["id" => 0]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
        self::assertEquals("Data tidak ditemukan!", $jsonDt['msg']);
    }

    public function testGetByIdFailed()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("ms-users.get", ["id" => "-"]));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertFalse($jsonDt['status']);
    }
}
