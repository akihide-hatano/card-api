<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Card;

class CardApiTest extends TestCase
{

    use RefreshDatabase; // 毎テストごとにmigrateリセット

    /** 一覧: 200 & ページネーションJSON */
    public function test_index_returns_paginated_cards()
    {
        Card::create(['title' => 'A']);
        Card::create(['title' => 'B']);
        Card::create(['title' => 'C']);

        $res = $this->getJson('/api/v1/cards');

        $res->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_index_can_filter_by_q()
{
    Card::create(['title' => 'Laravel API Guide']);
    Card::create(['title' => 'React API Frontend']);
    Card::create(['title' => 'API Testing with PHPUnit']);

    $res = $this->getJson('/api/v1/cards?q=React');

    $res->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertDontSee('Laravel API Guide')
        ->assertDontSee('API Testing with PHPUnit')
        ->assertSee('React API Frontend');
}

}
