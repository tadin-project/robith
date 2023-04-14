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
        $this->sess_user = Config::get("constants.session");
    }
    public function testPage()
    {
        $this->withSession($this->sess_user)
            ->get(route("asesmen.index"))
            ->assertStatus(200)
            ->assertSeeText("asesmen");
    }
}
