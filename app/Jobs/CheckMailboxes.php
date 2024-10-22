<?php

namespace App\Jobs;

use App\Models\Mailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckMailboxes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use cursor for better memory efficiency
        $mailboxes = Mailbox::cursor();
    
        foreach ($mailboxes as $mailbox) {
            try {
                CheckReplies::dispatch($mailbox);

            } catch (\Exception $e) {
                // Add this eror to the log
                // Log::error($e->getMessage());
            }
        }
    }
}
