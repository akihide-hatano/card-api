<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        // 決め打ちデータ（検索・動作確認しやすい）
        Card::insert([
            [
                'title'       => 'API設計メモ',
                'description' => 'RESTとステータスコードまとめ',
                'status'      => 'open',
                'due_date'    => Carbon::now()->addDays(5)->toDateString(),
                'archived_at' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Postman コレクション整備',
                'description' => 'Create→Show→Update→Archive',
                'status'      => 'in_progress',
                'due_date'    => Carbon::now()->addDays(2)->toDateString(),
                'archived_at' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'React/TS フロント繋ぎ込み',
                'description' => 'fetch + Resource で形を固定',
                'status'      => 'done',
                'due_date'    => Carbon::now()->subDay()->toDateString(),
                'archived_at' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => '古いカードのアーカイブ',
                'description' => '同期でまずはOK',
                'status'      => 'archived',
                'due_date'    => null,
                'archived_at' => Carbon::now()->subDays(3),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}