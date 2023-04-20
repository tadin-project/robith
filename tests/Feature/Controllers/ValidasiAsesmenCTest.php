<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ValidasiAsesmenCTest extends TestCase
{
    private $sess_user;
    public function setUp(): void
    {
        parent::setUp();
        $this->sess_user = Config::get("constants.session.validator");
    }

    public function testPage()
    {
        $response = $this->withSession($this->sess_user)->get(route('validasi-asesmen.index'));
        $response->assertStatus(200);
        $response->assertSeeText("Validasi Asesmen");
    }

    public function testGetDataSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("validasi-asesmen.get-data"));
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }

    public function testGetDataByParamSuccess()
    {
        $dt = $this->withSession($this->sess_user)
            ->get(route("validasi-asesmen.get-data") . "?search[value]=ADMIN");
        $dt->assertStatus(200);
        $jsonDt = json_decode($dt->getContent(), true);
        self::assertGreaterThanOrEqual(0, $jsonDt["recordsTotal"]);
        self::assertGreaterThanOrEqual(0, $jsonDt["data"]);
    }
}
