<?php

namespace AppDefault;

use AppService\ExposesRouting;
use AppService\InjectionVisitable;
use Auryn\Injector;

class Service implements ExposesRouting, InjectionVisitable
{
    public static function receiveInjectionVisit(Injector $injector)
    {

    }

    public function getRouteConfiguration()
    {
        return [
            'prefix' => '',
            'routes' => [
                '/' => [
                    'get' => Controller::class
                ]
            ]
        ];
    }
}