<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRateRequest;
use App\Http\Resources\RateResource;
use App\Services\RateService;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

class RatesController extends Controller
{
    public function __construct(protected RateService $rateService)
    {
    }

    #[OAT\Post(path: '/rates', description: 'get rates', tags: ['Rates'])]
    #[OAT\RequestBody('#/components/requestBodies/GetRateRequest')]
    #[OAT\Response(
        response: 200,
        description: 'Ok',
        content: new OAT\JsonContent(properties: [new OAT\Property(
            property: 'data',
            ref: '#/components/schemas/RateResource',
        )], type: 'object')
    )]
    public function getRates(GetRateRequest $request): JsonResource
    {
        $date = $request->date('date');
        $baseCurrencyCode = $request->input('baseCurrencyCode', 'RUR');
        $currencyCode = $request->input('currencyCode');

        $rate = $this->rateService->getRate($date, $baseCurrencyCode, $currencyCode);

        return $rate ? new RateResource($rate) : new JsonResource(null);
    }
}
