<?php

namespace Tests\Feature;

use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TitleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testListData()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Title::factory(5)->make();
        $response = $this->get('/api/title');

        $response->assertStatus(200);
    }

    public function testDetail()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $title = Title::factory()->create();

        $response = $this->get('/api/title/'.$title->id);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSelectList()
    {
        // $user = User::factory()->create();
        // $this->actingAs($user, 'api');

        $this->withoutMiddleware();

        $response = $this->postJson('api/title', [
            'name' => 'Phd'
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->call('GET', '/api/title/select-list', [
            'start' => 0,
            'length' => 10,
            'active_only' => 1,
            'search_value' => 'Phd'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAll()
    {
        Title::factory(10)->create();
        $response = $this->get('/api/public/title/');

        $this->assertCount(10, Title::All());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->postJson('api/title', [
            'name' => 'Phd'
        ]);

        $this->assertCount(1, Title::All());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->postJson('api/title', [
            'name' => 'Phd'
        ]);

        $title = Title::first();

        $response = $this->putJson('api/title/'.$title->id, [
            'name' => 'Phd'
        ]);

        $this->assertCount(1, Title::All());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        Title::factory(1)->create();
        $title = Title::first();
        $this->assertCount(1, Title::all());

        $response = $this->delete('/api/title/'.$title->id);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
