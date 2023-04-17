<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AsesmenCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = Config::get("constants.session.user");
    }

    public function testPage()
    {
        $this->withSession($this->sess_user)
            ->get(route("asesmen.index"))
            ->assertStatus(200)
            ->assertSeeText("Asesmen");
    }

    public function testGetTenantNotFound()
    {
        $this->sess_user["user_data"]["user_id"] = 0;
        $res = $this->withSession($this->sess_user)
            ->get(route("asesmen.cek-data"));
        $res->assertStatus(200);
        $jsonDt = json_decode($res->getContent(), true);
        self::assertEquals(0, $jsonDt["data"]);
    }
}
