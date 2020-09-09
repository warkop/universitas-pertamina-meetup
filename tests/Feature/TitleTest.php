<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TitleTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testListData()
    {
        $response = $this->get('/api/title');

        $response->assertStatus(200);
    }
}
