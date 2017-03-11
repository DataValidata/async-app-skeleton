<?php


final class App
{
    /** @var \Aerys\Host */
    private $host;

    /** @var \Auryn\Injector  */
    private $injector;

    /** @var \Aerys\Router  */
    private $router;

    private $hostUsables = [];

    public final function __construct()
    {
        $this->injector = new \Auryn\Injector;

        $this->attachHostUsable($this->router = \Aerys\router());

        $this->loadEnvironment();
        $this->validateEnvironment();
        $this->listen();

        $this->loadServices();
    }

    private function loadServices()
    {
        Functional\map(
            \AppService\ServiceLoader::getInstance()->getInjectionVisitables(),
            function($visitable) {
                forward_static_call($visitable.'::receiveInjectionVisit', $this->injector);
            }
        );

        Functional\map(
            \AppService\ServiceLoader::getInstance()->getServices(),
            $this->getServiceBuildChain()
        );

        $fallback = function(\Aerys\Request $req, \Aerys\Response $res) {
            $res->end("<html><body><h1>Fallback \o/</h1></body></html>");
        };
        $this->attachHostUsable($fallback);

        foreach($this->hostUsables as $usable) {
            $this->host->use($usable);
        }
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
                    foreach($detail as $method => $controllerSpec) {
                        if(is_callable($controllerSpec)) {
                            $controller = $controllerSpec;
                        } else {
                            $this->injector->share($controllerSpec);
                            $controller = $this->injector->make($controllerSpec);
                        }

                        $serviceRouter->route(strtoupper($method), $route, $controller);
                    }
                }
                $serviceRouter->prefix($routes['prefix']);
                $this->router->use($serviceRouter);
            }

            if($service instanceof \AppService\ExposesStaticRouting) {
                $docRoots = $service->getDocRoots();
                foreach($docRoots as $docRoot) {
                    $this->attachHostUsable(\Aerys\root($docRoot));
                }
            }
            return $serviceName;
        };

        return Functional\compose(
            $buildService,
            $initialiseRouting
        );
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

    /**
     * @param $usable
     */
    private function attachHostUsable($usable)
    {
        $this->hostUsables[] = $usable;
    }
}