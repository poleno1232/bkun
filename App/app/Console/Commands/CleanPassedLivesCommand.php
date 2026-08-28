<?php

namespace App\Console\Commands;

use App\Models\Quest;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:clean-passed-lives-command')]
#[Description('Nullify all next_live_at if they passed')]
class CleanPassedLivesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Quest::query()->whereDate('next_live_at', '<', now())->update(['next_live_at' => null]);
    }
}
