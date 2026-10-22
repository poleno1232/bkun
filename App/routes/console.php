<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean-passed-lives-command')->daily();
Schedule::command('app:update-closes-command')->everyMinute();
