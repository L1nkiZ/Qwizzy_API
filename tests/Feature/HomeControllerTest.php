<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_200_on_home_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_returns_an_html_response_on_home_page()
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
