<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property-read string $id
 * @property Carbon $date
 * @property string $base_currency
 * @property string $currency
 * @property int $rate_units
 * @property int $rate_nano
 *
 * @property string $value
 * @property string $previousDayDiff
 *
 * @mixin Builder
 */
class Rate extends Model
{
    use HasFactory;

    public $timestamps = false;

    private string $previousDayDiff;

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn() => bcadd($this->rate_units, bcdiv($this->rate_nano, bcpow(10, 9))),
            set: function (string $rate) {
                $rate = str_replace(',', '.', $rate);
                $units = (int)$rate;

                return [
                    'rate_units' => $units,
                    'rate_nano' => (int)(bcsub($rate, $units) * 1000000000),
                ];
            },
        );
    }
}
