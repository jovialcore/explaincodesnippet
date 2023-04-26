<?php

declare(strict_types=1);


require_once dirname(__DIR__) . '/vendor/autoload.php';

use DI\Container;
use DI\ContainerBuilder;
use ExplainImgSnippetAi\AiController;
use FastRoute\RouteCollector;
use Relay\Relay;
use Laminas\Diactoros\ServerRequestFactory; // more like guzzlehttp client
use FastRoute\SimpleDispatcher;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Psr\Container\ContainerInterface;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

use function Di\create;
use function Di\get;
use function FastRoute\simpleDispatcher;

$containerBuilder = new  ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them

/// add definitions // register classes so that the DI can automatically create instances of those class in whatever situtatuion (contructor, methods injections, etc) they are called

// you can either use factories...I'm using objects below. Reason ebing that it is easier
$containerBuilder->addDefinitions(
    [
        AiController::class => create()->constructor('stuff to inject ', get('chatgptapikey'))

    ]
);



$container = $containerBuilder->build();



$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// defining the routes
$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/api/extract/imagetext', AiController::class);
});

// the different layers the request coming in has to pass before it gets to the application

$middleware[] = new FastRoute($routes); // middleware/layer to match url patterns
// if not for dependency injection, I would have instanciated the AiCOntroller class and pass it on the request handler, but I don't have to cos the DI is handling that
$middleware[] = new RequestHandler($container); // middleware/layer to return response from server back to the client base on the routing path/specification
$requestHandler = new Relay($middleware); //  put all the middlewares in the pipeline also know as dispatcher
$requestHandler->handle(ServerRequestFactory::fromGlobals()); // pull all middlewares in the pipeline using the current http method returned
