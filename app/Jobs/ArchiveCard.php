<?php

namespace App\Jobs;

use App\Models\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ArchiveCard implements ShouldQueue
{
    use Dispatchable,Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Card $card)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->card->update([
            'status'      => 'archived',
            'archived_at' => now(),
        ]);
    }
}
