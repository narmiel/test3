<?php

namespace App\Services;

use App\Jobs\RequestRate;
use App\Models\Rate;
use App\Repositories\RateRepository;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RateService
{
    const int RATE_CACHE_TTL = 60 * 60 * 24 * 7; // 7 days
    const string DEFAULT_BASE_CURRENCY = 'RUR';

    public function __construct(protected RateRepository $rateRepository)
    {
    }

    public function getRate(Carbon $date, string $baseCurrencyCode, string $currencyCode): ?Rate
    {
        $rate = $this->rateRepository->findRate($date, self::DEFAULT_BASE_CURRENCY, $currencyCode);

        if ($rate === null) {
            RequestRate::dispatch($date);
        }

        $ratePreviousDay = $this->rateRepository->findRate((new Carbon($date))->subDay(), self::DEFAULT_BASE_CURRENCY, $currencyCode);

        if ($ratePreviousDay === null) {
            RequestRate::dispatch((new Carbon($date))->subDay());
        }

        if ($rate === null || $ratePreviousDay === null) {
            return null;
        }

        if ($baseCurrencyCode === self::DEFAULT_BASE_CURRENCY) {
            $rate->previousDayDiff = bcsub($rate->value, $ratePreviousDay->value);

            return $rate;
        } else  {
            // кросс курс
            $cross = $this->rateRepository->findRate($date, self::DEFAULT_BASE_CURRENCY, $baseCurrencyCode);
            $crossPreviousDay = $this->rateRepository->findRate((new Carbon($date))->subDay(), self::DEFAULT_BASE_CURRENCY, $baseCurrencyCode);

            $crossRate = new Rate();
            $crossRate->value = bcdiv($rate->value, $cross->value);
            $crossRate->previousDayDiff = bcsub($crossRate->value, bcdiv($ratePreviousDay->value, $crossPreviousDay->value));

            return $crossRate;
        }
    }

    public function requestRate(Carbon $date) : Collection
    {
        return Cache::remember('rates_' . $date->toDateString(), self::RATE_CACHE_TTL, function () use ($date) {
            $xml = new DOMDocument();
            $url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date->format('d.m.Y');
            if ($xml->load($url)) {
                $root = $xml->getRootNode();
                $items = $root->getElementsByTagName('Valute');
                $result = collect();

                foreach ($items as $item) {
                    $rate = new Rate();
                    $rate->date = $date;
                    $rate->base_currency = self::DEFAULT_BASE_CURRENCY;
                    $rate->currency = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
                    $rate->value = $item->getElementsByTagName('VunitRate')->item(0)->nodeValue;
                    $result->add($rate);
                }

                return $result;
            }

            return false;
        });
    }
}
