<?php

namespace App\Repositories;

use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RateRepository
{
    public function save(Rate $model): Rate
    {
        $model->saveOrFail();

        return $model;
    }

    public function saveBulk(Collection $rates): bool
    {
        return Rate::insert($rates->toArray());
    }

    public function ratesByDateCount(Carbon $date): int
    {
        return Rate::where(['date' => $date->toDateString()])->count();
    }

    public function findRate(Carbon $date, string $baseCurrencyCode, string $currencyCode): ?Rate
    {
        return Rate::where(['date' => $date->toDateString(), 'base_currency' => $baseCurrencyCode, 'currency' => $currencyCode])->first();
    }
}
