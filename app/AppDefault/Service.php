<?php

namespace AppDefault;

class Service implements \AppService\ExposesRouting
{
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