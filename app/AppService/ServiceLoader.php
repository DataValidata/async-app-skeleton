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
            'injectionVisitables' => [],
        ];

        foreach ($serviceConfig as $service) {
            $this->serviceData['services'][] = $service;
            $class = new \ReflectionClass($service);
            if($class->implementsInterface('AppService\InjectionVisitable')) {
                $this->serviceData['injectionVisitables'][] = $service;
            }
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

    public function getInjectionVisitables()
    {
        return $this->serviceData['injectionVisitables'];
    }
}