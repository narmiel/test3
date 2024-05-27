<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(request: 'GetRateRequest', content: new OAT\JsonContent(ref: '#/components/schemas/GetRateRequest'))]
#[OAT\Schema(required: ['date', 'currencyCode'], properties: [
    new OAT\Property(property: 'date', description: 'date', type: 'string', format: 'date', example: '2017-07-21'),
    new OAT\Property(property: 'currencyCode', description: 'currency code', type: 'string', format: 'currency', example: 'RUB'),
    new OAT\Property(property: 'baseCurrencyCode', description: 'base currency code', type: 'string', format: 'currency', example: 'RUB'),
])]

class GetRateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'date' => ['required', 'date'],
            'baseCurrencyCode' => ['alpha', 'min:3', 'max:3'],
            'currencyCode' => ['required', 'alpha', 'min:3', 'max:3'],
        ];
    }
}
