<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProfilCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = Config::get("constants.session.admin");
    }

    public function testPage()
    {
        $response = $this
            ->withSession($this->sess_user)
            ->get(route('profil.index'));

        $response->assertStatus(200);
    }

    public function testSaveProfilAdminSuccess()
    {
        $sess_user = Config::get("constants.session.admin");
        $response = $this
            ->withSession($sess_user)
            ->post(route('profil.save-profil'), [
                "user_fullname" => "Administrator",
            ]);

        $response->assertStatus(200);
        $jsonDt = json_decode($response->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testSaveProfilUserSuccess()
    {
        $sess_user = Config::get("constants.session.user");
        $response = $this
            ->withSession($sess_user)
            ->post(route('profil.save-profil'), [
                "user_fullname" => "User 02",
                "tenant_nama" => "User 02",
                "tenant_desc" => "Tes user 02",
                "mku_id" => 5,
            ]);

        $response->assertStatus(200);
        $jsonDt = json_decode($response->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testSavePassSuccess()
    {
        $response = $this
            ->withSession($this->sess_user)
            ->post(route('profil.save-password'), [
                "old_user_pass" => "admin123",
                "user_pass" => "admin123",
            ]);

        $response->assertStatus(200);
        $jsonDt = json_decode($response->getContent(), true);
        self::assertTrue($jsonDt["status"]);
    }

    public function testOldPassIncorrect()
    {
        $response = $this
            ->withSession($this->sess_user)
            ->post(route('profil.save-password'), [
                "old_user_pass" => "admin456",
                "user_pass" => "admin123",
            ]);

        $response->assertStatus(200);
        $jsonDt = json_decode($response->getContent(), true);
        self::assertFalse($jsonDt["status"]);
    }
}
