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

    /** 詳細: 404（存在しないID） */
    public function test_show_returns_404_when_not_found(){

        $this->getJson('/api/v1/cards/999999')->assertStatus(404);
    }

    /** 作成: 422（バリデーション） */
    public function test_store_returns_422_when_title_missing(){
        //descriptionにtitleを入れないようにする
        $res = $this ->postJson('/api/v1/cards', ['description' => 'no title']);
        //validationのerrorで引っかかる
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }


    public function test_store_creates_card_and_returns_201_with_location()
    {
        $payload = ['title' => 'New card', 'description' => 'from test'];

        $res = $this->postJson('/api/v1/cards', $payload);

        $res->assertStatus(201)
            ->assertHeader('Location')
            ->assertJsonPath('data.title', 'New card');

        // 作成されたIDをレスポンスから取る
        $id = $res->json('data.id');

        // DBに本当に入ったか（最低限見るカラムだけでOK）
        $this->assertDatabaseHas('cards', [
            'id'    => $id,
            'title' => 'New card',
            'status'=> 'open', // デフォルト値も確認できるとGood
        ]);
    }



}
