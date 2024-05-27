<?php

namespace App\Jobs;

use App\Models\Rate;
use App\Repositories\RateRepository;
use App\Services\RateService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RequestRate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Carbon $date)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(RateRepository $rateRepository, RateService $rateService): void
    {
        Cache::lock('lock_rate_request_' . $this->date->toDateString(), 10)->get(function () use ($rateRepository, $rateService) {
            $ratesCount = $rateRepository->ratesByDateCount($this->date);

            if ($ratesCount === 0) {
                $rates = $rateService->requestRate($this->date);

                $rateRepository->saveBulk($rates);
            }
        });
    }
}
