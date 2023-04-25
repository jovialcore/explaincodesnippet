<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use DI\ContainerBuilder;
use ExplainImgSnippetAi\genesis;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;

use Relay\Relay;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Narrowspark\HttpEmitter\SapiEmitter;

use function FastRoute\SimpleDispatcher;

use function Di\create;

//dependency injection but using the libray php-di
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
//$containerBuilder->useAnnotations(false); // annotations are now disabled by default no need to add them
$containerBuilder->addDefinitions([

    // handles all dependency and injection
    genesis::class => create(genesis::class)->constructor('People and my '),
]);

// understanding namespaces in php https://www.php.net/manual/en/language.namespaces.rationale.php#116280

//what does the container Builder or libray do: it automatically injects a dependecny for your class parameter so you don't have to instantiate the classes (not like the class is not being instantiated but you will not need to use new Keyword, etc)
$container = $containerBuilder->build();

// the route
$routes = SimpleDispatcher(function (RouteCollector  $route) {
    $route->get('/api', genesis::class);
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);


//dispatcher following the psr-15 and psr-7 standards
$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());






//the laminas diactoros package is to implemnet a PSR-7 compatible HTTP Messages
