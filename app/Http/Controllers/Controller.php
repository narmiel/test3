<?php

namespace App\Http\Controllers;

use OpenApi\Annotations\OpenApi;
use OpenApi\Attributes as OAT;

#[OAT\OpenApi(openapi: OpenApi::VERSION_3_0_0)]
#[OAT\Info(version: '1.0.0', title: 'Rate api')]
#[OAT\Server(url: 'api', description: 'Server')]

abstract class Controller
{
    //
}
