<?php


final class App
{
    /** @var \Aerys\Host */
    private $host;

    /** @var \Auryn\Injector  */
    private $injector;

    /** @var \Aerys\Router  */
    private $router;

    public final function __construct()
    {
        $this->injector = new \Auryn\Injector;
        $this->router = \Aerys\router();

        $this->loadEnvironment();
        $this->validateEnvironment();
        $this->listen();

        $this->loadServices();
    }

    private function getServices()
    {
        return [
            \AppDefault\Service::class
        ];
    }

    private function getServiceBuildChain()
    {
        $buildService = function ($serviceName) {
            $this->injector->share($serviceName)->make($serviceName);
            return $serviceName;
        };

        $initialiseRouting = function($serviceName) {
            $service = $this->injector->make($serviceName);
            if($service instanceof \AppService\ExposesRouting) {
                $routes = $service->getRouteConfiguration();
                $serviceRouter = \Aerys\router();
                foreach($routes['routes'] as $route => $detail) {
                    foreach($detail as $method => $controller) {
                        $this->injector->share($controller);
                        $serviceRouter->route(strtoupper($method), $route, $this->injector->make($controller));
                    }
                }
                $serviceRouter->prefix($routes['prefix']);
                $this->router->use($serviceRouter);
            }
            return $serviceName;
        };

        return Functional\compose(
            $buildService,
            $initialiseRouting
        );
    }

    private function loadServices()
    {
        Functional\map($this->getServices(), $this->getServiceBuildChain());

        $this->host->use($this->router);
    }

    private function loadEnvironment()
    {
        if (file_exists(dirname(__DIR__) . '/.env')) {
            $dotenv = new Dotenv\Dotenv(dirname(__DIR__));
            $dotenv->load();
        }
    }

    private function validateEnvironment()
    {
        \Assert\Assertion::between(
            getenv('PORT'),
            1, 65535,
            "Invalid port number; integer in the range 1..65535 required"
        );
    }

    /**
     * @return \Aerys\Host
     */
    private function listen()
    {
        $this->host = (new \Aerys\Host())->expose("*", getenv('PORT'))
            ->use($this->injector->make('\Logger'));
        $this->injector->share($this->host);
        return $this->host;
    }
}