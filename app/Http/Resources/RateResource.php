<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Rate;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(properties: [
    new OAT\Property(property: 'rate', description: 'rate', type: 'float', example: '99.11'),
    new OAT\Property(property: 'difference', description: 'difference', type: 'float', example: '1.1'),
])]
class RateResource extends JsonResource
{
    public function toArray($request): array
    {
        /**
         * @var Rate $this
         */
        return [
            'rate' => $this->value,
            'difference' => $this->previousDayDiff,
        ];
    }
}
