<?php

use App\Jobs\AutoReject;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Schedule::job(new AutoReject)->everyFiveSeconds();

Schedule::call(function () {
    Transaksi::where('created_at', '<=', Carbon::now()->subDays(1))->where('status', 'menunggu')->update(['status' => 'dibatalkan']);
})->everyThirtySeconds();
