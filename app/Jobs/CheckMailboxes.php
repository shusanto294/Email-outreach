<?php

namespace App\Jobs;

use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckMailboxes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        foreach (Mailbox::cursor() as $mailbox) {
            CheckMailbox::dispatch($mailbox)->onQueue('high');
        }
    }
}
