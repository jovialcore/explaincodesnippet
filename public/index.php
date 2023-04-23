<?php

declare(strict_types=1);


use DI\ContainerBuilder;
use ExplainImgSnippetAi\AiController;
use FastRoute\RouteCollector;
use Relay\Relay;
use Laminas\Diactoros\ServerRequestFactory; // more like guzzlehttp client
use FastRoute\SimpleDispatcher;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;

use function Di\create;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new  ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them

/// add definitions // register classes so that the DI can automatically create instances of those class in whatever situtatuion (contructor, methods injections, etc) they are called
$containerBuilder->addDefinitions(
    [
        AiController::class => create(AiController::class)
    ]
);
$container = $containerBuilder->build();
// let Di create the objects aka instantiate
$extractText = $container->get(AiController::class);


// defining the routes
$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/api/extract/imagetext', AiController::class);
});

// the different layers the request coming in has to pass before it gets to the application

$middleware[] = new FastRoute($routes); // middleware/layer to match url patterns
$middleware[] = new RequestHandler(); // middleware/layer to return response from server
$requestHandler = new Relay($middleware); //  put all the middlewares in the pipeline
$requestHandler->handle(ServerRequestFactory::fromGlobals()); // pull all middlewares in the pipeline using the current http method returned
