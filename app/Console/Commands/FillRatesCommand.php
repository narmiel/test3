<?php

namespace App\Console\Commands;

use App\Jobs\RequestRate;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class FillRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-rates {days}';

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
        $days = (int) $this->argument('days');
        $period = new CarbonPeriod(Carbon::now()->subDays($days), Carbon::now());

        foreach ($period as $date) {
            RequestRate::dispatch($date);
        }
    }
}
