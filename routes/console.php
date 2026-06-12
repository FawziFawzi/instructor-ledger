<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command(
    'app:process-payouts'
)->monthly();

Schedule::command(
    'app:reconcile-payouts'
)->everyThirtyMinutes();
