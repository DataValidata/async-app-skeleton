<?php

namespace AppService;

final class ServiceLoader
{
    private $serviceData;
    private static $instance;

    private function __construct()
    {
        $serviceConfig = require_once dirname(__DIR__) . '/services.php';
        $this->serviceData = [
            'services' => [],
        ];

        foreach ($serviceConfig as $service) {
            $this->serviceData['services'][] = $service;
        }

    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getServices()
    {
        return $this->serviceData['services'];
    }
}