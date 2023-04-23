<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use Controller\Aicontroller;
use FastRoute\RouteCollector;
use Relay\Relay;
use Laminas\Diactoros\ServerRequestFactory; // more like guzzlehttp client
use FastRoute\SimpleDispatcher;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;

use function Di\create;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload';


$containerBuilder = new  ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them

/// add definitions // register classes so that the DI can automatically create instances of those class in whatever situtatuion (contructor, methods injections, etc) they are called
$containerBuilder->addDefinitions(
    [
        Aicontroller::class => create(Aicontroller::class)
    ]
);
$container = $containerBuilder->build();
// let Di create the objects aka instantiate
$extractText = $container->get(\Controller\Aicontroller::class);


// defining the routes
$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/api/extract/imagetext', Aicontroller::class);
});

// the different layers the request coming in has to pass before it gets to the application

$middleware[] = new FastRoute($routes);
$middleware[] = new RequestHandler();
$requestHandler = new Relay($middleware);
$requestHandler->handle(ServerRequestFactory::fromGlobals());
