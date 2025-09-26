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
        Card::factory()->count(3)->create();

        $res = $this->getJson('/api/v1/cards');

        $res->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }
}
