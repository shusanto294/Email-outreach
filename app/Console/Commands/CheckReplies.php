<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Reply;
use App\Models\Mailbox;
use App\Jobs\CheckMailboxes;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class CheckReplies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-replies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    CheckMailboxes::dispatch()->onQueue('high');
    echo "Queue added for checking all inboxes";
    
}


}
