<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthCTest extends TestCase
{
    public function testPage()
    {
        $response = $this->get(route('auth.register'));

        $response->assertStatus(200)
            ->assertSeeText("Register");
    }
}
