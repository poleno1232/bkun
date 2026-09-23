<?php

namespace App\Console\Commands;

use App\Models\VoteUpdate;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-closes-command')]
#[Description('Command description')]
class UpdateClosesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        VoteUpdate::where('closes_at', '>=', now()->toDateTimeString())->update(
            ['is_closed' => true],
        );
    }
}
