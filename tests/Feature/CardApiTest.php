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

    //**作成： 422 titleが空 */
    public function test_update_return_422_when_title_empty(){
        //descriptionにtitleを入れないようにする
        $res = $this ->postJson('/api/v1/cards', ['description' => '']);

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

    public function test_destory_returns_204_ando_removes_row(){
        //ダミーデータを明示的に作成
        $card = Card::create([
            'title' => 'Temp',
            'status' => 'open',
            'archived_at' => null,
        ]);
        //実行
        $res = $this->deleteJson("/api/v1/cards/{$card->id}");

        // --- デバッグ表示したいとき（必要に応じてコメントアウトを外す） ---
        // $res->dumpHeaders();                 // ヘッダを出力
        //  dd($res->status(), $res->content()); // ステータスと本文を停止表示（204なので本文は空）
        // ------------------------------------

        // 検証：HTTP 204（No Content）
        $res->assertNoContent(); // = assertStatus(204)

        // 検証：DBから消えている
        $this->assertDatabaseMissing('cards', ['id' => $card->id]);
    }

    /** アーカイブ: 200（status/archived_at が更新） */
    public function test_archive_sets_status_and_returns_200()
    {
            $card = Card::create([
            'title' => 'Temp',
            'status' => 'open',
            'archived_at' => null,
        ]);

        $res = $this->postJson("/api/v1/cards/{$card->id}/archive");
        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'archived')
            ->assertJsonPath('data.id', $card->id);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'status' => 'archived',
        ]);
    }

    public function test_archive_return_404_when_not_found(){
        $this->postJson('/api/v1/cards/999999/archive')->assertStatus(404);
    }
}
