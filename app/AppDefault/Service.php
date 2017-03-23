<?php

namespace AppDefault;

use AppService\ExposesRouting;
use AppService\InjectionVisitable;
use Auryn\Injector;

class Service implements ExposesRouting, InjectionVisitable
{
    public function receiveInjectionVisit(Injector $injector)
    {
        $injector
            ->define(ControllerFactory::class, [
                ':dateTime' => (new \DateTime),
                ':offset' => 20
            ])
            ->share(ControllerFactory::class)
            ->delegate(Controller::class, ControllerFactory::class)
        ;
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